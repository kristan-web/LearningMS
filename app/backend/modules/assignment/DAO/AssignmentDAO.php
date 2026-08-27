<?php
/**
 * AssignmentDAO
 *
 * Location once applied: app/backend/modules/assignment/DAO/AssignmentDAO.php
 *
 * Data-access layer for student assignments. Provides methods to:
 *   - Get all assignments for a student with optional filtering
 *   - Get a single assignment by ID with submission data
 *   - Submit an assignment (create/update submission)
 *   - Get available subjects for filtering
 */
class AssignmentDAO
{
    public function __construct(private PDO $db)
    {
    }

    /**
     * Get all assignments for a student with optional filters
     */
    public function getStudentAssignments(
        int $studentId,
        ?int $subjectId = null,
        ?string $semester = null,
        ?string $status = null,
        ?string $sortBy = 'due_date',
        ?string $sortOrder = 'ASC'
    ): array {
        $sql = "
            SELECT 
                a.assignment_id,
                a.schedule_id,
                a.title,
                a.instructions,
                a.due_date,
                a.max_score,
                a.created_at,
                s.subject_name,
                s.subject_code,
                s.subject_id,
                CONCAT(t.first_name, ' ', t.last_name) AS teacher_name,
                cs.section_name,
                cs.grade_level,
                e.semester,
                e.school_year,
                sub.submitted_score,
                sub.status AS submission_status,
                sub.submitted_at,
                sub.file_url AS submission_file_url
            FROM assignments a
            INNER JOIN schedules sch ON a.schedule_id = sch.schedule_id
            INNER JOIN subjects s ON sch.subject_id = s.subject_id
            LEFT JOIN teachers t ON sch.teacher_id = t.teacher_id
            INNER JOIN class_sections cs ON sch.section_id = cs.section_id
            INNER JOIN enrollments e ON e.section_id = cs.section_id
            LEFT JOIN submissions sub ON sub.assignment_id = a.assignment_id 
                AND sub.student_id = :student_id
            WHERE e.student_id = :student_id
              AND e.status = 'Enrolled'
        ";

        $params = [':student_id' => $studentId];

        // Apply filters
        if ($subjectId !== null) {
            $sql .= " AND s.subject_id = :subject_id";
            $params[':subject_id'] = $subjectId;
        }

        if ($semester !== null) {
            $sql .= " AND e.semester = :semester";
            $params[':semester'] = $semester;
        }

        if ($status !== null) {
            if ($status === 'pending') {
                $sql .= " AND (sub.status IS NULL OR sub.status = 'Pending') AND a.due_date >= NOW()";
            } elseif ($status === 'submitted') {
                $sql .= " AND sub.status IN ('Submitted', 'Late')";
            } elseif ($status === 'graded') {
                $sql .= " AND sub.status = 'Graded'";
            } elseif ($status === 'past_due') {
                $sql .= " AND (sub.status IS NULL OR sub.status = 'Pending') AND a.due_date < NOW()";
            }
        }

        // Apply sorting
        $allowedSortColumns = ['due_date', 'title', 'subject_name', 'created_at', 'max_score'];
        $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'due_date';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY " . $sortBy . " " . $sortOrder;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $assignments = [];
        foreach ($rows as $row) {
            $assignments[] = Assignment::hydrate($row);
        }

        return $assignments;
    }

    /**
     * Get a single assignment by ID with student submission data
     */
    public function getAssignmentById(int $assignmentId, int $studentId): ?Assignment
    {
        $sql = "
            SELECT 
                a.assignment_id,
                a.schedule_id,
                a.title,
                a.instructions,
                a.due_date,
                a.max_score,
                a.created_at,
                s.subject_name,
                s.subject_code,
                s.subject_id,
                CONCAT(t.first_name, ' ', t.last_name) AS teacher_name,
                cs.section_name,
                cs.grade_level,
                e.semester,
                e.school_year,
                sub.submitted_score,
                sub.status AS submission_status,
                sub.submitted_at,
                sub.file_url AS submission_file_url
            FROM assignments a
            INNER JOIN schedules sch ON a.schedule_id = sch.schedule_id
            INNER JOIN subjects s ON sch.subject_id = s.subject_id
            LEFT JOIN teachers t ON sch.teacher_id = t.teacher_id
            INNER JOIN class_sections cs ON sch.section_id = cs.section_id
            INNER JOIN enrollments e ON e.section_id = cs.section_id
            LEFT JOIN submissions sub ON sub.assignment_id = a.assignment_id 
                AND sub.student_id = :student_id
            WHERE a.assignment_id = :assignment_id
              AND e.student_id = :student_id
              AND e.status = 'Enrolled'
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':assignment_id' => $assignmentId,
            ':student_id' => $studentId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? Assignment::hydrate($row) : null;
    }

    /**
     * Submit an assignment (create or update submission)
     */
    public function submitAssignment(
        int $assignmentId,
        int $studentId,
        ?string $fileUrl = null,
        ?string $originalFilename = null,
        ?int $fileSize = null,
        ?string $mimeType = null
    ): bool {
        // Check if submission exists
        $checkSql = "SELECT submission_id FROM submissions WHERE assignment_id = :assignment_id AND student_id = :student_id";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([
            ':assignment_id' => $assignmentId,
            ':student_id' => $studentId
        ]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update existing submission
            $sql = "
                UPDATE submissions 
                SET submitted_at = NOW(),
                    file_url = :file_url,
                    status = 'Submitted'
                WHERE assignment_id = :assignment_id 
                  AND student_id = :student_id
            ";
        } else {
            // Create new submission
            $sql = "
                INSERT INTO submissions (
                    assignment_id,
                    student_id,
                    submitted_at,
                    file_url,
                    status
                ) VALUES (
                    :assignment_id,
                    :student_id,
                    NOW(),
                    :file_url,
                    'Submitted'
                )
            ";
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':assignment_id' => $assignmentId,
            ':student_id' => $studentId,
            ':file_url' => $fileUrl
        ]);
    }

    /**
     * Get all subjects for the student to use in filtering
     */
    public function getStudentSubjects(int $studentId): array
    {
        $sql = "
            SELECT DISTINCT
                s.subject_id,
                s.subject_name,
                s.subject_code
            FROM subjects s
            INNER JOIN schedules sch ON sch.subject_id = s.subject_id
            INNER JOIN class_sections cs ON sch.section_id = cs.section_id
            INNER JOIN enrollments e ON e.section_id = cs.section_id
            WHERE e.student_id = :student_id
              AND e.status = 'Enrolled'
            ORDER BY s.subject_name
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get available semesters for the student
     */
    public function getStudentSemesters(int $studentId): array
    {
        $sql = "
            SELECT DISTINCT
                e.semester,
                e.school_year
            FROM enrollments e
            WHERE e.student_id = :student_id
              AND e.status = 'Enrolled'
            ORDER BY e.school_year DESC, e.semester DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get assignment count by status for dashboard
     */
    public function getAssignmentStats(int $studentId): array
    {
        $sql = "
            SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN sub.status IS NULL OR sub.status = 'Pending' AND a.due_date >= NOW() THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN sub.status IN ('Submitted', 'Late') THEN 1 ELSE 0 END) AS submitted,
                SUM(CASE WHEN sub.status = 'Graded' THEN 1 ELSE 0 END) AS graded,
                SUM(CASE WHEN (sub.status IS NULL OR sub.status = 'Pending') AND a.due_date < NOW() THEN 1 ELSE 0 END) AS past_due
            FROM assignments a
            INNER JOIN schedules sch ON a.schedule_id = sch.schedule_id
            INNER JOIN class_sections cs ON sch.section_id = cs.section_id
            INNER JOIN enrollments e ON e.section_id = cs.section_id
            LEFT JOIN submissions sub ON sub.assignment_id = a.assignment_id 
                AND sub.student_id = :student_id
            WHERE e.student_id = :student_id
              AND e.status = 'Enrolled'
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total' => (int) ($result['total'] ?? 0),
            'pending' => (int) ($result['pending'] ?? 0),
            'submitted' => (int) ($result['submitted'] ?? 0),
            'graded' => (int) ($result['graded'] ?? 0),
            'past_due' => (int) ($result['past_due'] ?? 0)
        ];
    }
}