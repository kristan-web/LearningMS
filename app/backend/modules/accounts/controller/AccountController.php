<?php
/**
 * AccountController
 *
 * HTTP entry-point / API for the Accounts module. The web app (or the
 * front-end scripts under ../js/) hits this file with a POST/GET request
 * carrying an `action` parameter; the controller validates the inputs,
 * delegates the work to AccountDAO, and returns a JSON response.
 *
 * Conventions follow ScheduleController (require db.php at the top,
 * instantiate the DAO inside a try/catch, and surface any unhandled
 * exception as a single error string). Output is JSON because every
 * caller in this module is a JS fetch() (see account-validation.js).
 */

declare(strict_types=1);

require_once dirname(__DIR__, 5) . "/config/db.php";
require_once __DIR__ . "/../model/Account.php";
require_once __DIR__ . "/../DAO/AccountDAO.php";

header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

/**
 * Send a JSON response and stop execution.
 *
 * @param array $payload
 * @param int   $status  HTTP status code (default 200)
 */
function accounts_respond(array $payload, int $status = 200): void
{
  http_response_code($status);
  echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit;
}

/**
 * Read a trimmed string from the request body / query string.
 * Returns null when the key is missing or empty.
 */
function accounts_input(string $key): ?string
{
  $value = $_REQUEST[$key] ?? null;
  if ($value === null) {
    return null;
  }
  $trimmed = trim((string) $value);
  return $trimmed === "" ? null : $trimmed;
}

/**
 * Read an integer from the request body / query string.
 * Returns null when the key is missing or not a valid integer.
 */
function accounts_input_int(string $key): ?int
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
 * Best-effort client IP, safe behind reverse proxies.
 */
function accounts_client_ip(): ?string
{
  foreach (["HTTP_X_FORWARDED_FOR", "HTTP_X_REAL_IP", "REMOTE_ADDR"] as $key) {
    if (!empty($_SERVER[$key])) {
      $ip = explode(",", (string) $_SERVER[$key])[0];
      $ip = trim($ip);
      if ($ip !== "") {
        return $ip;
      }
    }
  }
  return null;
}

/**
 * Minimum password policy — mirrors account-validation.js.
 * Returns an array of error messages (empty when valid).
 */
function accounts_validate_password(string $password): array
{
  $errors = [];
  if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
  }
  if (strlen($password) > 128) {
    $errors[] = "Password must not exceed 128 characters.";
  }
  if (!preg_match("/[a-z]/", $password)) {
    $errors[] = "Password must include a lowercase letter.";
  }
  if (!preg_match("/[A-Z]/", $password)) {
    $errors[] = "Password must include an uppercase letter.";
  }
  if (!preg_match("/[0-9]/", $password)) {
    $errors[] = "Password must include a number.";
  }
  if (!preg_match("/[^A-Za-z0-9]/", $password)) {
    $errors[] = "Password must include a symbol.";
  }
  return $errors;
}

/**
 * Username policy — lowercase letters, digits, dot, underscore, dash.
 * 3-50 characters (matches the SQL VARCHAR(50)).
 */
function accounts_validate_username(string $username): array
{
  $errors = [];
  if (strlen($username) < 3) {
    $errors[] = "Username must be at least 3 characters long.";
  }
  if (strlen($username) > 50) {
    $errors[] = "Username must not exceed 50 characters.";
  }
  if (!preg_match("/^[a-z0-9._-]+$/", $username)) {
    $errors[] = "Username may only contain lowercase letters, digits, dots, underscores or dashes.";
  }
  return $errors;
}

/**
 * Basic email shape check.
 */
function accounts_validate_email(?string $email): bool
{
  if ($email === null) {
    return true; // recovery_email is optional
  }
  return (bool) preg_match(
    "/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/",
    $email
  );
}

/**
 * Person-name policy. Allows letters (including accented), spaces,
 * period, apostrophe and hyphen. Required by the signup form.
 */
function accounts_validate_name(?string $name, int $maxLength = 50): array
{
  $errors = [];
  $value  = $name === null ? "" : trim($name);
  if ($value === "") {
    $errors[] = "This field is required.";
    return $errors;
  }
  if (mb_strlen($value) > $maxLength) {
    $errors[] = "Name is too long (max {$maxLength} characters).";
  }
  if (!preg_match("/^[\p{L}\s.'-]+$/u", $value)) {
    $errors[] = "Name may only contain letters, spaces, period, apostrophe or hyphen.";
  }
  return $errors;
}

/**
 * Phone policy. Accepts digits, spaces, +, -, parentheses. Requires at
 * least 7 visible digits.
 */
function accounts_validate_phone(?string $phone): array
{
  $errors = [];
  $value  = $phone === null ? "" : trim($phone);
  if ($value === "") {
    $errors[] = "Phone number is required.";
    return $errors;
  }
  if (strlen($value) > 25) {
    $errors[] = "Phone number is too long (max 25 characters).";
  }
  $digits = preg_replace("/\D/", "", $value);
  if ($digits === null || strlen($digits) < 7) {
    $errors[] = "Phone number must contain at least 7 digits.";
  }
  if (!preg_match("/^[+0-9()\-\s]+$/", $value)) {
    $errors[] = "Phone number may only contain digits, spaces, +, -, or parentheses.";
  }
  return $errors;
}

/**
 * Address policy. Required, 5-255 characters.
 */
function accounts_validate_address(?string $address): array
{
  $errors = [];
  $value  = $address === null ? "" : trim($address);
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
 * Birthdate policy. Returns errors for empty, malformed, out-of-range
 * dates. Min age 10 (Junior / Senior high school range), max age 100.
 */
function accounts_validate_birthdate(?string $birthdate): array
{
  $errors = [];
  $value  = $birthdate === null ? "" : trim($birthdate);
  if ($value === "") {
    $errors[] = "Birthdate is required.";
    return $errors;
  }
  $dt = DateTime::createFromFormat("Y-m-d", $value);
  if (!$dt || $dt->format("Y-m-d") !== $value) {
    $errors[] = "Please enter a valid date (YYYY-MM-DD).";
    return $errors;
  }
  $today = new DateTime("today");
  $age   = (int) $today->diff($dt)->y;
  if ($age < 10) {
    $errors[] = "You must be at least 10 years old to register.";
  }
  if ($age > 100) {
    $errors[] = "Please enter a valid birthdate.";
  }
  return $errors;
}

/**
 * Enum-style allowed values for select inputs. Returns the list of
 * allowed values (caller compares with ===).
 */
function accounts_allowed_genders(): array
{
  return ["Male", "Female", "Other"];
}

function accounts_allowed_grade_levels(): array
{
  return ["11", "12"];
}

/**
 * Convert an Account to a safe-for-the-browser array. The
 * password_hash and remember_token are never sent to the client.
 */
function accounts_account_to_public_array(?Account $account): ?array
{
  if ($account === null) {
    return null;
  }
  return [
    "account_id"           => $account->getAccountId(),
    "user_id"              => $account->getUserId(),
    "entity_id"            => $account->getEntityId(),
    "entity_type"          => $account->getEntityType(),
    "username"             => $account->getUsername(),
    "recovery_email"       => $account->getRecoveryEmail(),
    "status"               => $account->getStatus(),
    "is_active"            => $account->getIsActive(),
    "must_change_password" => $account->getMustChangePassword(),
    "failed_login_count"   => $account->getFailedLoginCount(),
    "locked_until"         => $account->getLockedUntil(),
    "last_login_at"        => $account->getLastLoginAt(),
    "last_login_ip"        => $account->getLastLoginIp(),
    "email_verified_at"    => $account->getEmailVerifiedAt(),
    "two_factor_enabled"   => $account->getTwoFactorEnabled(),
    "created_at"           => $account->getCreatedAt(),
    "updated_at"           => $account->getUpdatedAt(),
  ];
}

/* ====================================================================
 *  Boot: connect to the DB and pick an action.
 * ================================================================== */

try {
  $database   = new Database();
  $connection = $database->connect();
  if (!$connection) {
    accounts_respond(
      ["success" => false, "error" => "Unable to connect to the database."],
      500
    );
  }

  $dao = new AccountDAO($connection);

  $action = accounts_input("action") ?? "login";


  /* ====================================================================
   *  LOGIN
   * ================================================================== */
  if ($action === "login") {
    $identifier = accounts_input("identifier") ?? accounts_input("username") ?? accounts_input("email");
    $password   = $_POST["password"] ?? $_REQUEST["password"] ?? null;

    if ($identifier === null || $password === null || $password === "") {
      accounts_respond([
        "success" => false,
        "reason"  => "missing_fields",
        "errors"  => ["Please enter your username/email and password."],
      ]);
    }

    $result = $dao->authenticate(
      $identifier,
      (string) $password,
      accounts_client_ip()
    );

    // Don't echo the password_hash or remember_token to the browser.
    $public = accounts_account_to_public_array($result["account"]);

    // Look up the entity's name on a successful authentication
    if ($result["success"] && is_array($public) && $public["entity_id"]) {
      try {
        $entityType = $public["entity_type"] ?? Account::ENTITY_TYPE_STUDENT;
        $entityId   = (int) $public["entity_id"];

        if ($entityType === Account::ENTITY_TYPE_STUDENT) {
          $nameStmt = $connection->prepare(
            "SELECT first_name, middle_name, last_name " .
            "FROM students WHERE student_id = :id LIMIT 1"
          );
          $nameStmt->execute([":id" => $entityId]);
          $nameRow = $nameStmt->fetch(PDO::FETCH_ASSOC);
          if ($nameRow !== false) {
            $public["first_name"]  = $nameRow["first_name"] ?? null;
            $public["middle_name"] = $nameRow["middle_name"] ?? null;
            $public["last_name"]   = $nameRow["last_name"] ?? null;
          }
        } elseif ($entityType === Account::ENTITY_TYPE_TEACHER) {
          $nameStmt = $connection->prepare(
            "SELECT first_name, last_name " .
            "FROM teachers WHERE teacher_id = :id LIMIT 1"
          );
          $nameStmt->execute([":id" => $entityId]);
          $nameRow = $nameStmt->fetch(PDO::FETCH_ASSOC);
          if ($nameRow !== false) {
            $public["first_name"]  = $nameRow["first_name"] ?? null;
            $public["last_name"]   = $nameRow["last_name"] ?? null;
          }
        }
      } catch (Throwable $ignored) {
        // The name lookup is purely cosmetic; never let it block a successful login.
      }
    }

    $httpStatus = $result["success"] ? 200 : 401;
    if ($result["reason"] === "locked") {
      $httpStatus = 423; // Locked
    }

    accounts_respond([
      "success"            => $result["success"],
      "reason"             => $result["reason"],
      "message"            => $result["message"],
      "account"            => $public,
      "attempts_remaining" => $result["attempts_remaining"],
      "lockout_until"      => $result["lockout_until"],
    ], $httpStatus);
  }

  /* ====================================================================
   *  REGISTER STUDENT (self-service signup)
   * ================================================================== */
  if ($action === "register_student") {
    // ---- Collect + validate every field. ---------------------------------
    $firstName  = accounts_input("first_name");
    $middleName = accounts_input("middle_name");
    $lastName   = accounts_input("last_name");
    $email      = accounts_input("email");
    $phone      = accounts_input("phone");
    $gender     = accounts_input("gender");
    $birthdate  = accounts_input("birthdate");
    $address    = accounts_input("address");
    $gradeLevel = accounts_input("grade_level");
    $username   = accounts_input("username");
    $password   = $_POST["password"] ?? $_REQUEST["password"] ?? null;

    $errors = [];
    $errors = array_merge($errors, accounts_validate_name($firstName, 50));
    if ($middleName !== null && trim($middleName) !== "") {
      $errors = array_merge($errors, accounts_validate_name($middleName, 50));
    }
    $errors = array_merge($errors, accounts_validate_name($lastName, 50));

    if ($email === null || trim($email) === "") {
      $errors[] = "Email is required.";
    } elseif (!accounts_validate_email($email)) {
      $errors[] = "Please enter a valid email address.";
    }
    $errors = array_merge($errors, accounts_validate_phone($phone));
    if ($gender === null || !in_array($gender, accounts_allowed_genders(), true)) {
      $errors[] = "Gender is required.";
    }
    $errors = array_merge($errors, accounts_validate_birthdate($birthdate));
    $errors = array_merge($errors, accounts_validate_address($address));
    if ($gradeLevel === null || !in_array($gradeLevel, accounts_allowed_grade_levels(), true)) {
      $errors[] = "Grade level is required.";
    }
    $errors = array_merge($errors, accounts_validate_username($username ?? ""));
    if ($password === null || $password === "") {
      $errors[] = "Password is required.";
    } else {
      $errors = array_merge($errors, accounts_validate_password((string) $password));
    }
    if (!empty($errors)) {
      accounts_respond([
        "success" => false,
        "reason"  => "missing_fields",
        "errors"  => $errors,
      ], 422);
    }

    // ---- Pre-check uniqueness --------------------------------------------
    $reason  = null;
    $message = null;
    if ($dao->existsByUsername($username, null)) {
      $reason  = "duplicate_username";
      $message = "That username is already taken.";
    } else {
      try {
        $stmt = $connection->prepare("SELECT 1 FROM students WHERE email = :e LIMIT 1");
        $stmt->execute([":e" => $email]);
        if ($stmt->fetchColumn()) {
          $reason  = "duplicate_email";
          $message = "An account with that email already exists.";
        }
      } catch (PDOException $e) {
        // Table might not exist yet
      }
    }
    if ($reason !== null) {
      accounts_respond([
        "success" => false,
        "reason"  => $reason,
        "errors"  => [$message],
      ], 409);
    }

    // ---- Insert both rows in a single transaction. ----------------------
    try {
      $connection->beginTransaction();

      // Auto-generate LRN and Student Number
      $lrn = "PEND" . strtoupper(bin2hex(random_bytes(4)));
      
      $year = date("Y");
      $randomNum = random_int(0, 99999);
      $studentNumber = $year . "-" . str_pad((string) $randomNum, 5, "0", STR_PAD_LEFT);
      
      // Ensure student number is unique
      $checkStmt = $connection->prepare("SELECT 1 FROM students WHERE student_number = :sn LIMIT 1");
      $checkStmt->execute([":sn" => $studentNumber]);
      while ($checkStmt->fetchColumn()) {
        $randomNum = random_int(0, 99999);
        $studentNumber = $year . "-" . str_pad((string) $randomNum, 5, "0", STR_PAD_LEFT);
        $checkStmt->execute([":sn" => $studentNumber]);
      }

      // Get the active school year
      $schoolYear = "2026-2027";
      $syStmt = $connection->prepare("SELECT year FROM school_years WHERE status = 'active' LIMIT 1");
      $syStmt->execute();
      $syRow = $syStmt->fetch(PDO::FETCH_ASSOC);
      if ($syRow !== false) {
        $schoolYear = $syRow["year"];
      }

      // STEP 1: Insert into students table
      $studentStmt = $connection->prepare(
        "INSERT INTO students
           (lrn, student_number, first_name, last_name, middle_name,
            gender, birthdate, address, contact_number, email,
            grade_level, status,
            emergency_contact_name, emergency_contact_relationship,
            emergency_contact_number)
         VALUES
           (:lrn, :student_number, :first_name, :last_name, :middle_name,
            :gender, :birthdate, :address, :contact_number, :email,
            :grade_level, 'Active',
            :emergency_name, :emergency_relationship, :emergency_number)"
      );
      $studentStmt->execute([
        ":lrn"                    => $lrn,
        ":student_number"         => $studentNumber,
        ":first_name"             => trim($firstName),
        ":middle_name"            => $middleName !== null ? trim($middleName) : null,
        ":last_name"              => trim($lastName),
        ":gender"                 => $gender,
        ":birthdate"              => $birthdate,
        ":address"                => trim($address),
        ":contact_number"         => trim($phone),
        ":email"                  => trim($email),
        ":grade_level"            => $gradeLevel,
        ":emergency_name"         => "(To be updated)",
        ":emergency_relationship" => "(To be updated)",
        ":emergency_number"       => "(To be updated)",
      ]);

      // Get the auto-generated student_id
      $newStudentId = (int) $connection->lastInsertId();

      // STEP 2: Create the account with the student_id as entity_id
      $account = new Account(
        $newStudentId,                       // entity_id
        Account::ENTITY_TYPE_STUDENT,        // entity_type
        $username,
        password_hash((string) $password, PASSWORD_DEFAULT)
      );
      $account->setRecoveryEmail(trim($email));
      $account->setStatus(Account::STATUS_ACTIVE);
      $account->setMustChangePassword(false);
      $account->setPasswordChangedAt(date("Y-m-d H:i:s"));

      // STEP 3: Insert into lms_accounts table using the DAO
      $saved = $dao->create($account);
      
      $connection->commit();

      accounts_respond([
        "success"    => true,
        "message"    => "Student account created successfully! You can now log in.",
        "student_id" => $newStudentId,
        "account"    => accounts_account_to_public_array($saved),
      ], 201);
      
    } catch (Throwable $e) {
      if ($connection->inTransaction()) {
        $connection->rollBack();
      }
      
      error_log("Student registration error: " . $e->getMessage());
      error_log("Student registration trace: " . $e->getTraceAsString());
      
      accounts_respond([
        "success" => false,
        "reason"  => "server_error",
        "message" => "Could not create the student account. Please try again later.",
        "errors"  => ["Server error: " . $e->getMessage()],
      ], 500);
    }
  }

  /* ====================================================================
   *  REGISTER TEACHER (self-service signup)
   * ================================================================== */
  if ($action === "register_teacher") {
    // ---- Collect + validate every field. ---------------------------------
    $firstName   = accounts_input("first_name");
    $lastName    = accounts_input("last_name");
    $email       = accounts_input("email");
    $phone       = accounts_input("phone");
    $specialization = accounts_input("specialization");
    $username    = accounts_input("username");
    $password    = $_POST["password"] ?? $_REQUEST["password"] ?? null;

    $errors = [];
    $errors = array_merge($errors, accounts_validate_name($firstName, 50));
    $errors = array_merge($errors, accounts_validate_name($lastName, 50));

    if ($email === null || trim($email) === "") {
      $errors[] = "Email is required.";
    } elseif (!accounts_validate_email($email)) {
      $errors[] = "Please enter a valid email address.";
    }
    $errors = array_merge($errors, accounts_validate_phone($phone));
    $errors = array_merge($errors, accounts_validate_username($username ?? ""));
    if ($password === null || $password === "") {
      $errors[] = "Password is required.";
    } else {
      $errors = array_merge($errors, accounts_validate_password((string) $password));
    }
    if (!empty($errors)) {
      accounts_respond([
        "success" => false,
        "reason"  => "missing_fields",
        "errors"  => $errors,
      ], 422);
    }

    // ---- Pre-check uniqueness --------------------------------------------
    $reason  = null;
    $message = null;
    if ($dao->existsByUsername($username, null)) {
      $reason  = "duplicate_username";
      $message = "That username is already taken.";
    } else {
      try {
        $stmt = $connection->prepare("SELECT 1 FROM teachers WHERE email = :e LIMIT 1");
        $stmt->execute([":e" => $email]);
        if ($stmt->fetchColumn()) {
          $reason  = "duplicate_email";
          $message = "A teacher account with that email already exists.";
        }
      } catch (PDOException $e) {
        // Table might not exist yet
      }
    }
    if ($reason !== null) {
      accounts_respond([
        "success" => false,
        "reason"  => $reason,
        "errors"  => [$message],
      ], 409);
    }

    // ---- Insert both rows in a single transaction. ----------------------
    try {
      $connection->beginTransaction();

      // Auto-generate Teacher Number
      $year = date("Y");
      $randomNum = random_int(0, 99999);
      $teacherNumber = "TCH-" . $year . "-" . str_pad((string) $randomNum, 4, "0", STR_PAD_LEFT);
      
      // Ensure teacher number is unique
      $checkStmt = $connection->prepare("SELECT 1 FROM teachers WHERE teacher_number = :tn LIMIT 1");
      $checkStmt->execute([":tn" => $teacherNumber]);
      while ($checkStmt->fetchColumn()) {
        $randomNum = random_int(0, 99999);
        $teacherNumber = "TCH-" . $year . "-" . str_pad((string) $randomNum, 4, "0", STR_PAD_LEFT);
        $checkStmt->execute([":tn" => $teacherNumber]);
      }

      // STEP 1: Insert into teachers table
      $teacherStmt = $connection->prepare(
        "INSERT INTO teachers
           (teacher_number, first_name, last_name, email, contact_number, specialization, status)
         VALUES
           (:teacher_number, :first_name, :last_name, :email, :contact_number, :specialization, 'Active')"
      );
      $teacherStmt->execute([
        ":teacher_number"  => $teacherNumber,
        ":first_name"      => trim($firstName),
        ":last_name"       => trim($lastName),
        ":email"           => trim($email),
        ":contact_number"  => trim($phone),
        ":specialization"  => $specialization ?? null,
      ]);

      // Get the auto-generated teacher_id
      $newTeacherId = (int) $connection->lastInsertId();

      // STEP 2: Create the account with the teacher_id as entity_id
      $account = new Account(
        $newTeacherId,                       // entity_id
        Account::ENTITY_TYPE_TEACHER,        // entity_type
        $username,
        password_hash((string) $password, PASSWORD_DEFAULT)
      );
      $account->setRecoveryEmail(trim($email));
      $account->setStatus(Account::STATUS_ACTIVE);
      $account->setMustChangePassword(false);
      $account->setPasswordChangedAt(date("Y-m-d H:i:s"));

      // STEP 3: Insert into lms_accounts table using the DAO
      $saved = $dao->create($account);
      
      $connection->commit();

      accounts_respond([
        "success"    => true,
        "message"    => "Teacher account created successfully! You can now log in.",
        "teacher_id" => $newTeacherId,
        "account"    => accounts_account_to_public_array($saved),
      ], 201);
      
    } catch (Throwable $e) {
      if ($connection->inTransaction()) {
        $connection->rollBack();
      }
      
      error_log("Teacher registration error: " . $e->getMessage());
      error_log("Teacher registration trace: " . $e->getTraceAsString());
      
      accounts_respond([
        "success" => false,
        "reason"  => "server_error",
        "message" => "Could not create the teacher account. Please try again later.",
        "errors"  => ["Server error: " . $e->getMessage()],
      ], 500);
    }
  }

  /* ====================================================================
   *  USERNAME AVAILABILITY (live check from account-validation.js)
   * ================================================================== */
  if ($action === "check_username") {
    $username  = accounts_input("username") ?? "";
    $excludeId = accounts_input_int("exclude_account_id");
    $errors    = accounts_validate_username($username);
    $available = empty($errors) && !$dao->existsByUsername($username, $excludeId);

    accounts_respond([
      "success"   => empty($errors),
      "available" => $available,
      "errors"    => $errors,
    ]);
  }
  
  if ($action === "forgot_password") {
    $email = accounts_input("email") ?? accounts_input("recovery_email");
    if ($email === null) {
      accounts_respond([
        "success" => false,
        "errors"  => ["Please provide an email address."],
      ]);
    }

    $ok      = true;
    $message = "If an account exists for that email, a password-reset link has been sent.";

    $account = $dao->findByRecoveryEmail($email);
    if ($account !== null) {
      try {
        $token = bin2hex(random_bytes(32));
        $dao->setPasswordResetToken($account->getAccountId(), $token);
      } catch (Throwable $tokenError) {
        $ok      = false;
        $message = "Could not issue a reset token. Please try again later.";
      }
    }

    accounts_respond([
      "success" => $ok,
      "message" => $message,
    ]);
  }

  /* ====================================================================
   *  RESET PASSWORD
   * ================================================================== */
  if ($action === "reset_password") {
    $token       = accounts_input("token");
    $newPassword = $_POST["password"] ?? null;

    if ($token === null || $newPassword === null) {
      accounts_respond([
        "success" => false,
        "errors"  => ["Reset token and new password are required."],
      ]);
    }

    $account = $dao->findByResetToken($token);
    if ($account === null) {
      accounts_respond([
        "success" => false,
        "reason"  => "invalid_token",
        "errors"  => ["This password-reset link is invalid or has expired."],
      ], 400);
    }

    $errors = accounts_validate_password((string) $newPassword);
    if (!empty($errors)) {
      accounts_respond([
        "success" => false,
        "reason"  => "weak_password",
        "errors"  => $errors,
      ], 422);
    }

    $hash = password_hash((string) $newPassword, PASSWORD_DEFAULT);
    $dao->updatePassword($account->getAccountId(), $hash);

    accounts_respond([
      "success" => true,
      "message" => "Your password has been reset. You can now log in.",
    ]);
  }


  /* ====================================================================
   *  CHANGE PASSWORD
   * ================================================================== */
  if ($action === "change_password") {
    $accountId   = accounts_input_int("account_id");
    $oldPassword = $_POST["old_password"] ?? null;
    $newPassword = $_POST["new_password"] ?? null;

    if ($accountId === null || $oldPassword === null || $newPassword === null) {
      accounts_respond([
        "success" => false,
        "errors"  => ["account_id, old_password and new_password are all required."],
      ]);
    }

    $account = $dao->findByAccountId($accountId);
    if ($account === null) {
      accounts_respond([
        "success" => false,
        "reason"  => "not_found",
        "errors"  => ["Account not found."],
      ], 404);
    }
    if (!password_verify((string) $oldPassword, $account->getPasswordHash())) {
      accounts_respond([
        "success" => false,
        "reason"  => "wrong_password",
        "errors"  => ["The current password is incorrect."],
      ], 401);
    }

    $errors = accounts_validate_password((string) $newPassword);
    if (!empty($errors)) {
      accounts_respond([
        "success" => false,
        "reason"  => "weak_password",
        "errors"  => $errors,
      ], 422);
    }
    if (hash_equals((string) $oldPassword, (string) $newPassword)) {
      accounts_respond([
        "success" => false,
        "reason"  => "same_password",
        "errors"  => ["Your new password must be different from your current one."],
      ], 422);
    }

    $hash = password_hash((string) $newPassword, PASSWORD_DEFAULT);
    $dao->updatePassword($accountId, $hash);

    accounts_respond([
      "success" => true,
      "message" => "Password changed successfully.",
    ]);
  }

  /* ====================================================================
   *  VERIFY EMAIL
   * ================================================================== */
  if ($action === "verify_email") {
    $token = accounts_input("token");
    if ($token === null) {
      accounts_respond([
        "success" => false,
        "errors"  => ["Verification token is required."],
      ]);
    }

    $account = $dao->findByVerificationToken($token);
    if ($account === null) {
      accounts_respond([
        "success" => false,
        "reason"  => "invalid_token",
        "errors"  => ["This verification link is invalid or has already been used."],
      ], 400);
    }

    $dao->markEmailVerified($account->getAccountId());

    accounts_respond([
      "success" => true,
      "message" => "Email verified successfully. You can now log in.",
    ]);
  }


  /* ====================================================================
   *  CREATE — staff/registrar provisions a new account
   * ================================================================== */
  if ($action === "create") {
    $entityId    = accounts_input_int("entity_id");
    $entityType  = accounts_input("entity_type") ?? Account::ENTITY_TYPE_STUDENT;
    $username    = accounts_input("username");
    $password    = $_POST["password"] ?? null;
    $recovery    = accounts_input("recovery_email");
    $userId      = accounts_input_int("user_id");
    $createdBy   = accounts_input_int("created_by");
    $status      = accounts_input("status") ?? Account::STATUS_PENDING_VERIFICATION;

    $errors = [];
    if ($entityId === null) {
      $errors[] = "entity_id is required.";
    }
    if (!in_array($entityType, Account::ENTITY_TYPES, true)) {
      $errors[] = "entity_type must be one of: " . implode(", ", Account::ENTITY_TYPES);
    }
    if ($username === null) {
      $errors[] = "username is required.";
    } else {
      $errors = array_merge($errors, accounts_validate_username($username));
    }
    if ($password === null) {
      $errors[] = "password is required.";
    } else {
      $errors = array_merge($errors, accounts_validate_password((string) $password));
    }
    if (!accounts_validate_email($recovery)) {
      $errors[] = "recovery_email is not a valid email address.";
    }
    if (!empty($errors)) {
      accounts_respond(["success" => false, "errors" => $errors], 422);
    }

    // Check if account already exists for this entity
    $existing = $dao->findByEntity($entityId, $entityType);
    if ($existing !== null) {
      accounts_respond([
        "success" => false,
        "reason"  => "duplicate",
        "errors"  => ["An account already exists for this " . $entityType . "."],
      ], 409);
    }
    if ($dao->existsByUsername($username)) {
      accounts_respond([
        "success" => false,
        "reason"  => "duplicate_username",
        "errors"  => ["That username is already taken."],
      ], 409);
    }

    $account = new Account(
      $entityId,
      $entityType,
      $username,
      password_hash((string) $password, PASSWORD_DEFAULT)
    );
    $account->setUserId($userId);
    $account->setRecoveryEmail($recovery);
    $account->setStatus($status);
    $account->setCreatedBy($createdBy);
    $account->setUpdatedBy($createdBy);

    $created = $dao->create($account);

    accounts_respond([
      "success" => true,
      "message" => "Account created successfully.",
      "account" => accounts_account_to_public_array($created),
    ], 201);
  }

  /* ====================================================================
   *  UPDATE
   * ================================================================== */
  if ($action === "update") {
    $accountId = accounts_input_int("account_id");
    if ($accountId === null) {
      accounts_respond([
        "success" => false,
        "errors"  => ["account_id is required."],
      ]);
    }

    $account = $dao->findByAccountId($accountId);
    if ($account === null) {
      accounts_respond([
        "success" => false,
        "reason"  => "not_found",
        "errors"  => ["Account not found."],
      ], 404);
    }

    if (($username = accounts_input("username")) !== null) {
      $errors = accounts_validate_username($username);
      if (!empty($errors)) {
        accounts_respond(["success" => false, "errors" => $errors], 422);
      }
      if ($dao->existsByUsername($username, $accountId)) {
        accounts_respond([
          "success" => false,
          "reason"  => "duplicate_username",
          "errors"  => ["That username is already taken."],
        ], 409);
      }
      $account->setUsername($username);
    }
    if (($recovery = accounts_input("recovery_email")) !== null) {
      if (!accounts_validate_email($recovery)) {
        accounts_respond([
          "success" => false,
          "errors"  => ["recovery_email is not a valid email address."],
        ], 422);
      }
      $account->setRecoveryEmail($recovery);
    }
    if (isset($_POST["user_id"])) {
      $account->setUserId(accounts_input_int("user_id"));
    }
    if (($status = accounts_input("status")) !== null) {
      try {
        $account->setStatus($status);
      } catch (InvalidArgumentException $e) {
        accounts_respond([
          "success" => false,
          "reason"  => "invalid_status",
          "errors"  => [$e->getMessage()],
        ], 422);
      }
    }
    if (isset($_POST["is_active"])) {
      $account->setIsActive(filter_var(
        $_POST["is_active"],
        FILTER_VALIDATE_BOOLEAN,
        FILTER_NULL_ON_FAILURE
      ) ?? false);
    }
    if (isset($_POST["must_change_password"])) {
      $account->setMustChangePassword(filter_var(
        $_POST["must_change_password"],
        FILTER_VALIDATE_BOOLEAN,
        FILTER_NULL_ON_FAILURE
      ) ?? false);
    }
    if (($updatedBy = accounts_input_int("updated_by")) !== null) {
      $account->setUpdatedBy($updatedBy);
    }

    $updated = $dao->update($account);

    accounts_respond([
      "success" => true,
      "message" => "Account updated successfully.",
      "account" => accounts_account_to_public_array($updated),
    ]);
  }


  /* ====================================================================
   *  DELETE
   * ================================================================== */
  if ($action === "delete") {
    $accountId = accounts_input_int("account_id");
    if ($accountId === null) {
      accounts_respond([
        "success" => false,
        "errors"  => ["account_id is required."],
      ]);
    }
    $account = $dao->findByAccountId($accountId);
    if ($account === null) {
      accounts_respond([
        "success" => false,
        "reason"  => "not_found",
        "errors"  => ["Account not found."],
      ], 404);
    }
    $dao->delete($accountId);

    accounts_respond([
      "success" => true,
      "message" => "Account deleted successfully.",
    ]);
  }

  /* ====================================================================
   *  GET (single)
   * ================================================================== */
  if ($action === "get") {
    $accountId = accounts_input_int("account_id");
    $username  = accounts_input("username");

    $account = null;
    if ($accountId !== null) {
      $account = $dao->findByAccountId($accountId);
    } elseif ($username !== null) {
      $account = $dao->findByUsername($username);
    } else {
      accounts_respond([
        "success" => false,
        "errors"  => ["account_id or username is required."],
      ]);
    }

    if ($account === null) {
      accounts_respond([
        "success" => false,
        "reason"  => "not_found",
        "errors"  => ["Account not found."],
      ], 404);
    }

    accounts_respond([
      "success" => true,
      "account" => accounts_account_to_public_array($account),
    ]);
  }

  /* ====================================================================
   *  LIST (paginated)
   * ================================================================== */
  if ($action === "list") {
    $status     = accounts_input("status");
    $entityType = accounts_input("entity_type");
    $limit      = accounts_input_int("limit");
    $offset     = accounts_input_int("offset");

    if ($status !== null && !in_array($status, Account::STATUSES, true)) {
      accounts_respond([
        "success" => false,
        "reason"  => "invalid_status",
        "errors"  => ["Unknown status filter: \"{$status}\"."],
      ], 422);
    }

    if ($entityType !== null && !in_array($entityType, Account::ENTITY_TYPES, true)) {
      accounts_respond([
        "success" => false,
        "reason"  => "invalid_entity_type",
        "errors"  => ["Unknown entity type filter: \"{$entityType}\"."],
      ], 422);
    }

    $accounts = $dao->listAll($status, $entityType, $limit, $offset);

    accounts_respond([
      "success" => true,
      "count"   => count($accounts),
      "accounts" => array_map(
        fn(Account $a) => accounts_account_to_public_array($a),
        $accounts
      ),
    ]);
  }

  /* ====================================================================
   *  UNLOCK / RESET COUNTERS
   * ================================================================== */
  if ($action === "unlock") {
    $accountId = accounts_input_int("account_id");
    if ($accountId === null) {
      accounts_respond([
        "success" => false,
        "errors"  => ["account_id is required."],
      ]);
    }
    $dao->clearFailedLogins($accountId);
    $dao->updateStatus($accountId, Account::STATUS_ACTIVE);

    accounts_respond([
      "success" => true,
      "message" => "Account unlocked.",
    ]);
  }

  /* ====================================================================
   *  LOGOUT
   * ================================================================== */
  if ($action === "logout") {
    $accountId = accounts_input_int("account_id");
    if ($accountId !== null) {
      $dao->setRememberToken($accountId, null);
    }
    accounts_respond([
      "success" => true,
      "message" => "Logged out.",
    ]);
  }

  /* ====================================================================
   *  Fallback — unknown action
   * ================================================================== */
  accounts_respond([
    "success" => false,
    "reason"  => "unknown_action",
    "errors"  => ["Unknown action: \"{$action}\"."],
  ], 400);
} catch (Throwable $exception) {
  error_log("AccountController fatal: " . $exception->getMessage());
  error_log("AccountController trace: " . $exception->getTraceAsString());
  accounts_respond([
    "success" => false,
    "reason"  => "server_error",
    "error"   => $exception->getMessage(),
  ], 500);
}