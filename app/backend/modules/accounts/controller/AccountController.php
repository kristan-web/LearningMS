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
    "student_id"           => $account->getStudentId(),
    "user_id"              => $account->getUserId(),
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

    // Look up the student's first/last name on a successful authentication
    // so the front-end can greet the user by their real name (e.g. "Good
    // morning, Maria") on the next page. We only do this for successful
    // logins (including the soft-success cases like must_change_password)
    // to avoid leaking profile data on a wrong-password attempt. The DAO
    // hydrates only `student_account` columns, so a small targeted SELECT
    // here is the cleanest way to attach the name without restructuring
    // the entity / DAO layers.
    if (
      $result["success"] &&
      is_array($public) &&
      isset($public["student_id"])
    ) {
      try {
        $nameStmt = $connection->prepare(
          "SELECT first_name, middle_name, last_name " .
          "FROM students WHERE student_id = :sid LIMIT 1"
        );
        $nameStmt->execute([":sid" => (int) $public["student_id"]]);
        $nameRow = $nameStmt->fetch(PDO::FETCH_ASSOC);
        if ($nameRow !== false) {
          $public["first_name"]  = isset($nameRow["first_name"])
            ? (string) $nameRow["first_name"]
            : null;
          $public["middle_name"] = isset($nameRow["middle_name"])
            ? (string) $nameRow["middle_name"]
            : null;
          $public["last_name"]   = isset($nameRow["last_name"])
            ? (string) $nameRow["last_name"]
            : null;
        }
      } catch (Throwable $ignored) {
        // The name lookup is purely cosmetic; never let it block a
        // successful login. Fall back to whatever the account row gives us
        // (e.g. the username) if the students table is unreachable.
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
   *  REGISTER (self-service signup)
   *
   *  Public action called from public/student-signup.php. Creates BOTH a
   *  new `students` row and a matching `student_account` row inside a
   *  single transaction so the FK on student_account.student_id is never
   *  left dangling. The student's email is also stored as
   *  student_account.recovery_email so a future password-reset flow has
   *  a target.
   *
   *  Expects POST: first_name, last_name, middle_name, email, phone,
   *                gender, birthdate, address, grade_level, username,
   *                password. (password_confirm is UI-only.)
   *
   *  Returns 201 on success with { student_id, account }, 422 with a
   *  structured `reason` and per-field `errors` on validation failures,
   *  409 on duplicates, 500 on unexpected DB errors.
   * ================================================================== */
  if ($action === "register") {
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

    // ---- Pre-check uniqueness on the columns that have UNIQUE indexes. ---
    $reason  = null;
    $message = null;
    if ($dao->existsByUsername($username, null)) {
      $reason  = "duplicate_username";
      $message = "That username is already taken.";
    } else {
      $stmt = $connection->prepare("SELECT 1 FROM students WHERE email = :e LIMIT 1");
      $stmt->execute([":e" => $email]);
      if ($stmt->fetchColumn()) {
        $reason  = "duplicate_email";
        $message = "An account with that email already exists.";
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

      // Auto-generate the two NOT NULL UNIQUE student identifiers. The
      // students PK is auto-increment, so the only constraint is unique.
      // `lrn` is VARCHAR(12) so we need a 12-char placeholder: "PEND" (4)
      // + 8 hex digits (8) = 12. `student_number` is VARCHAR(20) so a
      // year + dash + 5 digits is comfortably under the cap.
      $lrn           = "PEND" . strtoupper(bin2hex(random_bytes(4)));
      $studentNumber = date("Y") . "-" . str_pad((string) random_int(0, 99999), 5, "0", STR_PAD_LEFT);

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
        // The form doesn't ask for these, but the schema requires NOT
        // NULL. We persist "(To be updated)" placeholders that the
        // student / registrar can fill in later.
        ":emergency_name"         => "(To be updated)",
        ":emergency_relationship" => "(To be updated)",
        ":emergency_number"       => "(To be updated)",
      ]);

      $newStudentId = (int) $connection->lastInsertId();

      $account = new Account(
        $newStudentId,
        $username,
        password_hash((string) $password, PASSWORD_BCRYPT)
      );
      $account->setRecoveryEmail(trim($email));

      // Self-service signup is "ready to use" out of the box: the student
      // just chose their own password (it already passed the strength
      // policy), they have no temporary credential to rotate, and the
      // email they provided is the one we'll use for recovery. We therefore
      // flip the two flags that would otherwise force a re-route on
      // first login (`must_change_password = 1` and
      // `status = 'Pending Verification'`) and stamp password_changed_at
      // so audit/lockout logic that reads it gets a real timestamp.
      //
      // The "must change your temporary password" / "pending verification"
      // branches of authenticate() are kept intact for any future
      // admin-issued-temp-password flow that creates an Account with
      // a generated credential.
      $account->setStatus(Account::STATUS_ACTIVE);
      $account->setMustChangePassword(false);
      $account->setPasswordChangedAt(date("Y-m-d H:i:s"));

      $saved = $dao->create($account);
      $connection->commit();

      accounts_respond([
        "success"    => true,
        "message"    => "Account created. You can now log in.",
        "student_id" => $newStudentId,
        "account"    => accounts_account_to_public_array($saved),
      ], 201);
    } catch (Throwable $e) {
      if ($connection->inTransaction()) {
        $connection->rollBack();
      }
      accounts_respond([
        "success" => false,
        "reason"  => "server_error",
        "message" => "Could not create the account. Please try again later.",
        "errors"  => ["Server error: " . $e->getMessage()],
      ], 500);
    }
  }

  /* ====================================================================
   *  USERNAME AVAILABILITY (live check from account-validation.js)
   * ================================================================== */
  if ($action === "check_username") {
    $username  = accounts_input("username") ?? "";
    $excludeId = accounts_input_int("exclude_student_id");
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

    // Always respond success to avoid leaking which emails exist.
    $ok      = true;
    $message = "If an account exists for that email, a password-reset link has been sent.";

    $account = $dao->findByRecoveryEmail($email);
    if ($account !== null) {
      try {
        $token = bin2hex(random_bytes(32));
        $dao->setPasswordResetToken($account->getStudentId(), $token);
        // Real implementation: hand $token off to a mailer here.
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
   *  RESET PASSWORD — consume a reset token + set a new password
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

    $hash = password_hash((string) $newPassword, PASSWORD_BCRYPT);
    $dao->updatePassword($account->getStudentId(), $hash);

    accounts_respond([
      "success" => true,
      "message" => "Your password has been reset. You can now log in.",
    ]);
  }


  /* ====================================================================
   *  CHANGE PASSWORD — authenticated user swaps their own password
   * ================================================================== */
  if ($action === "change_password") {
    $studentId   = accounts_input_int("student_id");
    $oldPassword = $_POST["old_password"] ?? null;
    $newPassword = $_POST["new_password"] ?? null;

    if ($studentId === null || $oldPassword === null || $newPassword === null) {
      accounts_respond([
        "success" => false,
        "errors"  => ["student_id, old_password and new_password are all required."],
      ]);
    }

    $account = $dao->findByStudentId($studentId);
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

    $hash = password_hash((string) $newPassword, PASSWORD_BCRYPT);
    $dao->updatePassword($studentId, $hash);

    accounts_respond([
      "success" => true,
      "message" => "Password changed successfully.",
    ]);
  }

  /* ====================================================================
   *  VERIFY EMAIL — consume a verification token
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

    $dao->markEmailVerified($account->getStudentId());

    accounts_respond([
      "success" => true,
      "message" => "Email verified successfully. You can now log in.",
    ]);
  }


  /* ====================================================================
   *  CREATE — staff/registrar provisions a new account
   * ================================================================== */
  if ($action === "create") {
    $studentId    = accounts_input_int("student_id");
    $username     = accounts_input("username");
    $password     = $_POST["password"] ?? null;
    $recovery     = accounts_input("recovery_email");
    $userId       = accounts_input_int("user_id");
    $createdBy    = accounts_input_int("created_by");
    $status       = accounts_input("status") ?? Account::STATUS_PENDING_VERIFICATION;

    $errors = [];
    if ($studentId === null) {
      $errors[] = "student_id is required.";
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
    if ($dao->findByStudentId($studentId) !== null) {
      accounts_respond([
        "success" => false,
        "reason"  => "duplicate",
        "errors"  => ["An account already exists for this student."],
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
      $studentId,
      $username,
      password_hash((string) $password, PASSWORD_BCRYPT)
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
   *  UPDATE — staff/registrar updates one or more fields
   * ================================================================== */
  if ($action === "update") {
    $studentId = accounts_input_int("student_id");
    if ($studentId === null) {
      accounts_respond([
        "success" => false,
        "errors"  => ["student_id is required."],
      ]);
    }

    $account = $dao->findByStudentId($studentId);
    if ($account === null) {
      accounts_respond([
        "success" => false,
        "reason"  => "not_found",
        "errors"  => ["Account not found."],
      ], 404);
    }

    // Patchable fields. Each is read independently and applied only
    // when the request actually carries the key.
    if (($username = accounts_input("username")) !== null) {
      $errors = accounts_validate_username($username);
      if (!empty($errors)) {
        accounts_respond(["success" => false, "errors" => $errors], 422);
      }
      if ($dao->existsByUsername($username, $studentId)) {
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
    $studentId = accounts_input_int("student_id");
    if ($studentId === null) {
      accounts_respond([
        "success" => false,
        "errors"  => ["student_id is required."],
      ]);
    }
    $account = $dao->findByStudentId($studentId);
    if ($account === null) {
      accounts_respond([
        "success" => false,
        "reason"  => "not_found",
        "errors"  => ["Account not found."],
      ], 404);
    }
    $dao->delete($studentId);

    accounts_respond([
      "success" => true,
      "message" => "Account deleted successfully.",
    ]);
  }

  /* ====================================================================
   *  GET (single)
   * ================================================================== */
  if ($action === "get") {
    $studentId = accounts_input_int("student_id");
    $username  = accounts_input("username");

    $account = null;
    if ($studentId !== null) {
      $account = $dao->findByStudentId($studentId);
    } elseif ($username !== null) {
      $account = $dao->findByUsername($username);
    } else {
      accounts_respond([
        "success" => false,
        "errors"  => ["student_id or username is required."],
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
    $status = accounts_input("status");
    $limit  = accounts_input_int("limit");
    $offset = accounts_input_int("offset");

    if ($status !== null && !in_array($status, Account::STATUSES, true)) {
      accounts_respond([
        "success" => false,
        "reason"  => "invalid_status",
        "errors"  => ["Unknown status filter: \"{$status}\"."],
      ], 422);
    }

    $accounts = $dao->listAll($status, $limit, $offset);

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
   *  UNLOCK / RESET COUNTERS — manual override
   * ================================================================== */
  if ($action === "unlock") {
    $studentId = accounts_input_int("student_id");
    if ($studentId === null) {
      accounts_respond([
        "success" => false,
        "errors"  => ["student_id is required."],
      ]);
    }
    $dao->clearFailedLogins($studentId);
    $dao->updateStatus($studentId, Account::STATUS_ACTIVE);

    accounts_respond([
      "success" => true,
      "message" => "Account unlocked.",
    ]);
  }

  /* ====================================================================
   *  LOGOUT — clear the remember-me token server-side
   * ================================================================== */
  if ($action === "logout") {
    $studentId = accounts_input_int("student_id");
    if ($studentId !== null) {
      $dao->setRememberToken($studentId, null);
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
  accounts_respond([
    "success" => false,
    "reason"  => "server_error",
    "error"   => $exception->getMessage(),
  ], 500);
}

