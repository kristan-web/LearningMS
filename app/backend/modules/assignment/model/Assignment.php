<?php
/**
 * Assignment
 *
 * Location once applied: app/backend/modules/assignment/model/Assignment.php
 *
 * Entity / data-transfer object that maps to the `assignments` table and
 * related data from `schedules`, `subjects`, `teachers`, and `class_sections`.
 * This model represents a student's view of an assignment with all necessary
 * contextual information.
 */
class Assignment
{
    private int $assignmentId;
    private int $scheduleId;
    private string $title;
    private ?string $instructions;
    private string $dueDate;
    private float $maxScore;
    private string $createdAt;
    
    // Related data from joins
    private string $subjectName;
    private string $subjectCode;
    private string $teacherName;
    private string $sectionName;
    private string $gradeLevel;
    private string $semester;
    private string $schoolYear;
    
    // Student-specific data
    private ?float $submittedScore = null;
    private ?string $submissionStatus = null;
    private ?string $submittedAt = null;
    private ?string $submissionFileUrl = null;
    
    // Status flags
    private bool $isLate = false;
    private bool $isPastDue = false;

    /* ---- Constructor -------------------------------------------------------- */
    public function __construct(
        int $assignmentId,
        string $title,
        string $dueDate,
        float $maxScore,
        string $subjectName,
        string $subjectCode
    ) {
        $this->assignmentId = $assignmentId;
        $this->title = $title;
        $this->dueDate = $dueDate;
        $this->maxScore = $maxScore;
        $this->subjectName = $subjectName;
        $this->subjectCode = $subjectCode;
    }

    /* ---- Getters and Setters ----------------------------------------------- */
    public function getAssignmentId(): int { return $this->assignmentId; }
    public function setAssignmentId(int $assignmentId): void { $this->assignmentId = $assignmentId; }

    public function getScheduleId(): int { return $this->scheduleId; }
    public function setScheduleId(int $scheduleId): void { $this->scheduleId = $scheduleId; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getInstructions(): ?string { return $this->instructions; }
    public function setInstructions(?string $instructions): void { $this->instructions = $instructions; }

    public function getDueDate(): string { return $this->dueDate; }
    public function setDueDate(string $dueDate): void { $this->dueDate = $dueDate; }

    public function getMaxScore(): float { return $this->maxScore; }
    public function setMaxScore(float $maxScore): void { $this->maxScore = $maxScore; }

    public function getCreatedAt(): string { return $this->createdAt; }
    public function setCreatedAt(string $createdAt): void { $this->createdAt = $createdAt; }

    public function getSubjectName(): string { return $this->subjectName; }
    public function setSubjectName(string $subjectName): void { $this->subjectName = $subjectName; }

    public function getSubjectCode(): string { return $this->subjectCode; }
    public function setSubjectCode(string $subjectCode): void { $this->subjectCode = $subjectCode; }

    public function getTeacherName(): string { return $this->teacherName; }
    public function setTeacherName(string $teacherName): void { $this->teacherName = $teacherName; }

    public function getSectionName(): string { return $this->sectionName; }
    public function setSectionName(string $sectionName): void { $this->sectionName = $sectionName; }

    public function getGradeLevel(): string { return $this->gradeLevel; }
    public function setGradeLevel(string $gradeLevel): void { $this->gradeLevel = $gradeLevel; }

    public function getSemester(): string { return $this->semester; }
    public function setSemester(string $semester): void { $this->semester = $semester; }

    public function getSchoolYear(): string { return $this->schoolYear; }
    public function setSchoolYear(string $schoolYear): void { $this->schoolYear = $schoolYear; }

    public function getSubmittedScore(): ?float { return $this->submittedScore; }
    public function setSubmittedScore(?float $submittedScore): void { $this->submittedScore = $submittedScore; }

    public function getSubmissionStatus(): ?string { return $this->submissionStatus; }
    public function setSubmissionStatus(?string $submissionStatus): void { $this->submissionStatus = $submissionStatus; }

    public function getSubmittedAt(): ?string { return $this->submittedAt; }
    public function setSubmittedAt(?string $submittedAt): void { $this->submittedAt = $submittedAt; }

    public function getSubmissionFileUrl(): ?string { return $this->submissionFileUrl; }
    public function setSubmissionFileUrl(?string $submissionFileUrl): void { $this->submissionFileUrl = $submissionFileUrl; }

    public function isLate(): bool { return $this->isLate; }
    public function setIsLate(bool $isLate): void { $this->isLate = $isLate; }

    public function isPastDue(): bool { return $this->isPastDue; }
    public function setIsPastDue(bool $isPastDue): void { $this->isPastDue = $isPastDue; }

    /**
     * Get the status text for display
     */
    public function getStatusText(): string {
        if ($this->submissionStatus === 'Graded') {
            return 'Graded';
        } elseif ($this->submissionStatus === 'Submitted' || $this->submissionStatus === 'Late') {
            return 'Submitted';
        } elseif ($this->isPastDue) {
            return 'Past Due';
        } else {
            return 'Pending';
        }
    }

    /**
     * Get the CSS class for status badge
     */
    public function getStatusBadgeClass(): string {
        if ($this->submissionStatus === 'Graded') {
            return 'bg-green-100 text-green-800';
        } elseif ($this->submissionStatus === 'Submitted' || $this->submissionStatus === 'Late') {
            return 'bg-blue-100 text-blue-800';
        } elseif ($this->isPastDue) {
            return 'bg-red-100 text-red-800';
        } else {
            return 'bg-yellow-100 text-yellow-800';
        }
    }

    /**
     * Build an Assignment from a database row
     */
    public static function hydrate(array $row): self
    {
        $assignment = new self(
            (int) $row['assignment_id'],
            (string) $row['title'],
            (string) $row['due_date'],
            (float) $row['max_score'],
            (string) $row['subject_name'],
            (string) $row['subject_code']
        );

        if (array_key_exists('schedule_id', $row)) {
            $assignment->setScheduleId((int) $row['schedule_id']);
        }
        if (array_key_exists('instructions', $row)) {
            $assignment->setInstructions($row['instructions'] === null ? null : (string) $row['instructions']);
        }
        if (array_key_exists('created_at', $row)) {
            $assignment->setCreatedAt((string) $row['created_at']);
        }
        if (array_key_exists('teacher_name', $row) && $row['teacher_name'] !== null) {
            $assignment->setTeacherName((string) $row['teacher_name']);
        }
        if (array_key_exists('section_name', $row) && $row['section_name'] !== null) {
            $assignment->setSectionName((string) $row['section_name']);
        }
        if (array_key_exists('grade_level', $row) && $row['grade_level'] !== null) {
            $assignment->setGradeLevel((string) $row['grade_level']);
        }
        if (array_key_exists('semester', $row) && $row['semester'] !== null) {
            $assignment->setSemester((string) $row['semester']);
        }
        if (array_key_exists('school_year', $row) && $row['school_year'] !== null) {
            $assignment->setSchoolYear((string) $row['school_year']);
        }
        if (array_key_exists('submitted_score', $row)) {
            $assignment->setSubmittedScore($row['submitted_score'] === null ? null : (float) $row['submitted_score']);
        }
        if (array_key_exists('submission_status', $row)) {
            $assignment->setSubmissionStatus($row['submission_status'] === null ? null : (string) $row['submission_status']);
        }
        if (array_key_exists('submitted_at', $row)) {
            $assignment->setSubmittedAt($row['submitted_at'] === null ? null : (string) $row['submitted_at']);
        }
        if (array_key_exists('submission_file_url', $row)) {
            $assignment->setSubmissionFileUrl($row['submission_file_url'] === null ? null : (string) $row['submission_file_url']);
        }

        // Calculate if past due
        $now = new DateTime();
        $due = new DateTime($row['due_date']);
        $assignment->setIsPastDue($due < $now && $assignment->getSubmissionStatus() !== 'Graded');

        // Check if submission was late
        if ($assignment->getSubmittedAt() !== null) {
            $submitted = new DateTime($assignment->getSubmittedAt());
            $assignment->setIsLate($submitted > $due);
        }

        return $assignment;
    }

    /**
     * Convert to array for JSON response
     */
    public function toArray(): array
    {
        return [
            'assignment_id' => $this->assignmentId,
            'schedule_id' => $this->scheduleId,
            'title' => $this->title,
            'instructions' => $this->instructions,
            'due_date' => $this->dueDate,
            'max_score' => $this->maxScore,
            'created_at' => $this->createdAt,
            'subject_name' => $this->subjectName,
            'subject_code' => $this->subjectCode,
            'teacher_name' => $this->teacherName,
            'section_name' => $this->sectionName,
            'grade_level' => $this->gradeLevel,
            'semester' => $this->semester,
            'school_year' => $this->schoolYear,
            'submitted_score' => $this->submittedScore,
            'submission_status' => $this->submissionStatus,
            'submitted_at' => $this->submittedAt,
            'submission_file_url' => $this->submissionFileUrl,
            'is_late' => $this->isLate,
            'is_past_due' => $this->isPastDue,
            'status_text' => $this->getStatusText(),
            'status_badge_class' => $this->getStatusBadgeClass()
        ];
    }
}