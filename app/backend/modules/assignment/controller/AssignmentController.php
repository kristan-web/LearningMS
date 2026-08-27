<?php
/**
 * AssignmentController
 *
 * Location once applied: app/backend/modules/assignment/controller/AssignmentController.php
 *
 * HTTP entry-point / API for the Student Assignments module. A logged-in
 * student hits this file to view their assignments, filter them, and
 * submit work. Returns JSON responses for all endpoints.
 */

declare(strict_types=1);

session_start();

require_once dirname(__DIR__, 5) . "/config/db.php";
require_once __DIR__ . "/../model/Assignment.php";
require_once __DIR__ . "/../DAO/AssignmentDAO.php";

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

/**
 * Send a JSON response and stop execution.
 */
function assignment_respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Read a trimmed string from the request body / query string.
 */
function assignment_input(string $key): ?string
{
    if (!array_key_exists($key, $_REQUEST)) {
        return null;
    }
    return trim((string) $_REQUEST[$key]);
}

/**
 * Read an integer from the request body / query string.
 */
function assignment_input_int(string $key): ?int
{
    $value = $_REQUEST[$key] ?? null;
    if ($value === null || $value === "") {
        return null;
    }
    if (is_numeric($value)) {
        return (int) $value;
    }
    return null;
}

/**
 * Resolve the logged-in student's ID.
 */
function assignment_current_student(): ?int
{
    if (isset($_SESSION["student_id"]) && is_numeric($_SESSION["student_id"])) {
        return (int) $_SESSION["student_id"];
    }
    if (isset($_SESSION["user_id"]) && is_numeric($_SESSION["user_id"])) {
        // If we only have user_id, we need to look up the student
        // This could be done here or we could require student_id in session
        // For now, we'll require student_id
        return null;
    }
    return null;
}

/* ====================================================================
 *  Boot: connect to the DB and pick an action.
 * ================================================================== */

try {
    $database = new Database();
    $connection = $database->connect();
    if (!$connection) {
        assignment_respond(
            ["success" => false, "error" => "Unable to connect to the database."],
            500
        );
    }

    $dao = new AssignmentDAO($connection);

    $action = assignment_input("action") ?? "list";

    /* ====================================================================
     *  AUTH GUARD — every action below needs a resolved student.
     * ================================================================== */
    $studentId = assignment_current_student();
    if ($studentId === null) {
        assignment_respond([
            "success" => false,
            "reason" => "not_authenticated",
            "errors" => ["Please log in to view your assignments."],
        ], 401);
    }

    /* ====================================================================
     *  LIST — get assignments with filters
     * ================================================================== */
    if ($action === "list") {
        $subjectId = assignment_input_int("subject_id");
        $semester = assignment_input("semester");
        $status = assignment_input("status");
        $sortBy = assignment_input("sort_by") ?? "due_date";
        $sortOrder = assignment_input("sort_order") ?? "ASC";

        $assignments = $dao->getStudentAssignments(
            $studentId,
            $subjectId,
            $semester,
            $status,
            $sortBy,
            $sortOrder
        );

        $assignmentData = array_map(function ($assignment) {
            return $assignment->toArray();
        }, $assignments);

        // Get filter options
        $subjects = $dao->getStudentSubjects($studentId);
        $semesters = $dao->getStudentSemesters($studentId);
        $stats = $dao->getAssignmentStats($studentId);

        assignment_respond([
            "success" => true,
            "assignments" => $assignmentData,
            "filters" => [
                "subjects" => $subjects,
                "semesters" => $semesters
            ],
            "stats" => $stats
        ]);
    }

    /* ====================================================================
     *  VIEW — get a single assignment with submission data
     * ================================================================== */
    if ($action === "view") {
        $assignmentId = assignment_input_int("assignment_id");
        if ($assignmentId === null) {
            assignment_respond([
                "success" => false,
                "errors" => ["Assignment ID is required."]
            ], 400);
        }

        $assignment = $dao->getAssignmentById($assignmentId, $studentId);
        if ($assignment === null) {
            assignment_respond([
                "success" => false,
                "errors" => ["Assignment not found or you don't have access to it."]
            ], 404);
        }

        assignment_respond([
            "success" => true,
            "assignment" => $assignment->toArray()
        ]);
    }

    /* ====================================================================
     *  SUBMIT — submit an assignment
     * ================================================================== */
    if ($action === "submit") {
        $assignmentId = assignment_input_int("assignment_id");
        if ($assignmentId === null) {
            assignment_respond([
                "success" => false,
                "errors" => ["Assignment ID is required."]
            ], 400);
        }

        // Check if assignment exists and student has access
        $assignment = $dao->getAssignmentById($assignmentId, $studentId);
        if ($assignment === null) {
            assignment_respond([
                "success" => false,
                "errors" => ["Assignment not found or you don't have access to it."]
            ], 404);
        }

        // Check if assignment is past due
        if ($assignment->isPastDue() && $assignment->getSubmissionStatus() !== 'Graded') {
            // Allow submission with late flag
            // The DAO will handle status correctly
        }

        // Handle file upload
        $fileUrl = null;
        if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 6) . "/public/uploads/submissions/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $file = $_FILES['submission_file'];
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $destination = $uploadDir . $filename;

            // Validate file type
            $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file['type'], $allowedTypes)) {
                assignment_respond([
                    "success" => false,
                    "errors" => ["Invalid file type. Please upload PDF, DOC, DOCX, or image files."]
                ], 400);
            }

            // Validate file size (max 10MB)
            if ($file['size'] > 10 * 1024 * 1024) {
                assignment_respond([
                    "success" => false,
                    "errors" => ["File size must be less than 10MB."]
                ], 400);
            }

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $fileUrl = "/uploads/submissions/" . $filename;
            } else {
                assignment_respond([
                    "success" => false,
                    "errors" => ["Failed to upload file."]
                ], 500);
            }
        } else {
            assignment_respond([
                "success" => false,
                "errors" => ["Please upload a file."]
            ], 400);
        }

        $result = $dao->submitAssignment(
            $assignmentId,
            $studentId,
            $fileUrl
        );

        if ($result) {
            // Get updated assignment data
            $updatedAssignment = $dao->getAssignmentById($assignmentId, $studentId);
            assignment_respond([
                "success" => true,
                "message" => "Assignment submitted successfully!",
                "assignment" => $updatedAssignment ? $updatedAssignment->toArray() : null
            ]);
        } else {
            assignment_respond([
                "success" => false,
                "errors" => ["Failed to submit assignment. Please try again."]
            ], 500);
        }
    }

    /* ====================================================================
     *  STATS — get assignment statistics for dashboard
     * ================================================================== */
    if ($action === "stats") {
        $stats = $dao->getAssignmentStats($studentId);
        assignment_respond([
            "success" => true,
            "stats" => $stats
        ]);
    }

    /* ====================================================================
     *  FILTERS — get available filter options
     * ================================================================== */
    if ($action === "filters") {
        $subjects = $dao->getStudentSubjects($studentId);
        $semesters = $dao->getStudentSemesters($studentId);

        assignment_respond([
            "success" => true,
            "subjects" => $subjects,
            "semesters" => $semesters
        ]);
    }

    /* ====================================================================
     *  Fallback — unknown action
     * ================================================================== */
    assignment_respond([
        "success" => false,
        "reason" => "unknown_action",
        "errors" => ["Unknown action: \"{$action}\"."],
    ], 400);

} catch (Throwable $exception) {
    assignment_respond([
        "success" => false,
        "reason" => "server_error",
        "error" => $exception->getMessage(),
    ], 500);
}