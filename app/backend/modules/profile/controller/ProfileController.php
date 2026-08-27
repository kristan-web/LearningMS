<?php
/**
 * ProfileController
 *
 * Location once applied: app/backend/modules/profile/controller/ProfileController.php
 *
 * HTTP entry-point / API for the "My Account" Profile module. A logged-in
 * student hits this file to view their own information and to update
 * their address, contact number and bio — nothing else. The web app (or
 * ../js/profile-validation.js) hits this file with a POST/GET request
 * carrying an `action` parameter; the controller validates the inputs,
 * delegates the work to StudentProfileDAO, and returns a JSON response.
 *
 * Conventions follow AccountController (require db.php at the top,
 * instantiate the DAO inside a try/catch, module-prefixed helper
 * functions, single error string on unhandled exceptions). Output is
 * JSON because every caller in this module is a JS fetch().
 *
 * IDENTITY: schema.sql has no `student_account` table — login lives on
 * `users` (password_hash, role) and is linked to a profile via the
 * nullable, unique `students.user_id`. This controller therefore
 * resolves "who is asking" in two steps: prefer a `student_id` already
 * cached in the session, otherwise resolve it from `users.user_id` via
 * StudentProfileDAO::findByUserId(). Adjust profile_current_student()
 * once your login controller's exact session contract is finalized.
 */

declare(strict_types=1);

session_start();

require_once dirname(__DIR__, 5) . "/config/db.php";
require_once __DIR__ . "/../model/StudentProfile.php";
require_once __DIR__ . "/../DAO/StudentProfileDAO.php";

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

/**
 * Send a JSON response and stop execution.
 *
 * @param array $payload
 * @param int   $status  HTTP status code (default 200)
 */
function profile_respond(array $payload, int $status = 200): void
{
  http_response_code($status);
  echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit;
}

/**
 * Read a trimmed string from the request body / query string.
 * Returns null when the key is missing (not present at all) so the
 * caller can distinguish "field not sent" from "field sent empty".
 */
function profile_input(string $key): ?string
{
  if (!array_key_exists($key, $_REQUEST)) {
    return null;
  }
  return trim((string) $_REQUEST[$key]);
}

/**
 * Read an integer from the request body / query string.
 * Returns null when the key is missing or not a valid integer.
 */
function profile_input_int(string $key): ?int
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
 * Resolve the logged-in student's profile.
 *
 * Precedence:
 *   1. $_SESSION["student_id"]  — trusted if the login flow already
 *      cached it there.
 *   2. $_SESSION["user_id"]     — the `users` table PK every login
 *      grants regardless of role; resolved to a student profile via
 *      the unique students.user_id FK.
 *   3. A `student_id` request param — fallback ONLY for exercising this
 *      endpoint before session wiring lands. Remove or gate this behind
 *      a staff-role check before shipping; a student must never be able
 *      to view/edit another student's profile by passing a different id.
 *
 * Returns null when no student can be resolved at all.
 */
function profile_current_student(StudentProfileDAO $dao): ?StudentProfile
{
  if (isset($_SESSION["student_id"]) && is_numeric($_SESSION["student_id"])) {
    return $dao->findByStudentId((int) $_SESSION["student_id"]);
  }
  if (isset($_SESSION["user_id"]) && is_numeric($_SESSION["user_id"])) {
    return $dao->findByUserId((int) $_SESSION["user_id"]);
  }
  $fallbackId = profile_input_int("student_id");
  return $fallbackId !== null ? $dao->findByStudentId($fallbackId) : null;
}

/**
 * Address policy. Required (students.address is NOT NULL), 5-255
 * characters.
 */
function profile_validate_address(string $address): array
{
  $errors = [];
  $value  = trim($address);
  if ($value === "") {
    $errors[] = "Address is required.";
    return $errors;
  }
  if (strlen($value) < 5) {
    $errors[] = "Address is too short.";
  }
  if (strlen($value) > 255) {
    $errors[] = "Address is too long (max 255 characters).";
  }
  return $errors;
}

/**
 * Contact number policy. students.contact_number is nullable, so an
 * empty value is how a student clears it. Accepts digits, spaces, +,
 * -, parentheses; when non-empty requires at least 7 visible digits.
 * Matches the varchar(20) column width.
 */
function profile_validate_contact_number(string $contactNumber): array
{
  $errors = [];
  $value  = trim($contactNumber);
  if ($value === "") {
    return $errors; // optional — empty clears the field
  }
  if (strlen($value) > 20) {
    $errors[] = "Contact number is too long (max 20 characters).";
  }
  $digits = preg_replace("/\D/", "", $value);
  if ($digits === null || strlen($digits) < 7) {
    $errors[] = "Contact number must contain at least 7 digits.";
  }
  if (!preg_match("/^[+0-9()\-\s]+$/", $value)) {
    $errors[] = "Contact number may only contain digits, spaces, +, -, or parentheses.";
  }
  return $errors;
}

/**
 * Bio policy. Optional — an empty string is how a student clears their
 * bio. Free text, capped at 500 characters to match the `bio` column
 * added by profile_columns_migration.sql.
 */
function profile_validate_bio(string $bio): array
{
  $errors = [];
  if (mb_strlen($bio) > 500) {
    $errors[] = "Bio is too long (max 500 characters).";
  }
  return $errors;
}

/* ====================================================================
 *  Boot: connect to the DB and pick an action.
 * ================================================================== */

try {
  $database   = new Database();
  $connection = $database->connect();
  if (!$connection) {
    profile_respond(
      ["success" => false, "error" => "Unable to connect to the database."],
      500
    );
  }

  $dao = new StudentProfileDAO($connection);

  $action = profile_input("action") ?? "get";

  /* ====================================================================
   *  AUTH GUARD — every action below needs a resolved student.
   * ================================================================== */
  $profile = profile_current_student($dao);
  if ($profile === null) {
    profile_respond([
      "success" => false,
      "reason"  => "not_authenticated",
      "errors"  => ["Please log in to view your account."],
    ], 401);
  }
  $studentId = $profile->getStudentId();

  /* ====================================================================
   *  GET — view the logged-in student's own profile
   * ================================================================== */
  if ($action === "get") {
    profile_respond([
      "success" => true,
      "profile" => $profile->toPublicArray(),
    ]);
  }

  /* ====================================================================
   *  UPDATE — student edits their own address / contact number / bio
   *
   *  Each field is read independently and applied only when the request
   *  actually carries the key, so a caller can PATCH just one field
   *  (e.g. only "bio") without resending the others.
   * ================================================================== */
  if ($action === "update") {
    $address       = profile_input("address");
    $contactNumber = profile_input("contact_number");
    $bio           = profile_input("bio");

    $errors = [];
    if ($address !== null) {
      $errors = array_merge($errors, profile_validate_address($address));
    }
    if ($contactNumber !== null) {
      $errors = array_merge($errors, profile_validate_contact_number($contactNumber));
    }
    if ($bio !== null) {
      $errors = array_merge($errors, profile_validate_bio($bio));
    }
    if (!empty($errors)) {
      profile_respond(["success" => false, "errors" => $errors], 422);
    }

    // Fall back to the existing value for any field the request didn't
    // send, so a partial PATCH never accidentally blanks a field out.
    // address is NOT NULL in the schema, so it always resolves to a
    // non-null string here.
    $updated = $dao->updateContactInfo(
      $studentId,
      $address ?? $profile->getAddress(),
      $contactNumber ?? $profile->getContactNumber(),
      $bio ?? $profile->getBio(),
    );

    profile_respond([
      "success" => true,
      "message" => "Profile updated successfully.",
      "profile" => $updated->toPublicArray(),
    ]);
  }

  /* ====================================================================
   *  Fallback — unknown action
   * ================================================================== */
  profile_respond([
    "success" => false,
    "reason"  => "unknown_action",
    "errors"  => ["Unknown action: \"{$action}\"."],
  ], 400);
} catch (Throwable $exception) {
  profile_respond([
    "success" => false,
    "reason"  => "server_error",
    "error"   => $exception->getMessage(),
  ], 500);
}
