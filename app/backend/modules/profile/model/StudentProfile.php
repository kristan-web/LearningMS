<?php
/**
 * StudentProfile
 *
 * Location once applied: app/backend/modules/profile/model/StudentProfile.php
 *
 * Entity / data-transfer object that maps 1:1 to the `students` table as
 * defined in schema.sql. This entity is intentionally read-heavy: the
 * Profile module lets a student view every field below, but the DAO
 * only ever *writes* address, contact_number and bio back to the
 * database — everything else (name, lrn, student_number, grade_level,
 * status, parent/guardian/emergency-contact info) is registrar-owned
 * and never touched by this module.
 *
 * A `hydrate()` static factory builds a StudentProfile from a fetched
 * row, and `toArray()` / `toPublicArray()` expose the data back out in
 * database-friendly and browser-friendly shapes respectively.
 *
 * Conventions follow Account.php (no namespace, 2-space indentation,
 * real PHP type hints instead of PHPDoc-only types).
 *
 * NOTE: `students` has no auth columns of its own — login lives on the
 * separate `users` table (password_hash, role, ...) and is linked via
 * the nullable, unique `students.user_id`. This entity does not model
 * `users` at all; see ProfileController for how the two are joined.
 */
class StudentProfile
{
  /* ---- Properties (subset of `students` columns this module cares about) */
  private int $studentId;
  private ?int $userId = null;
  private string $lrn;
  private string $studentNumber;
  private string $firstName;
  private string $lastName;
  private ?string $middleName = null;
  private string $gender;
  private string $birthdate;
  private string $address;
  private ?string $contactNumber = null;
  private string $email;
  private string $gradeLevel;
  private string $status = "Active";
  private ?string $bio = null;
  private ?string $createdAt = null;
  private ?string $updatedAt = null;

  /* ---- Constructor -------------------------------------------------------- */
  public function __construct(
    int $studentId,
    string $firstName,
    string $lastName,
    string $email,
  ) {
    $this->setStudentId($studentId);
    $this->setFirstName($firstName);
    $this->setLastName($lastName);
    $this->setEmail($email);
  }

  /* ---- student_id (PK) ----------------------------------------------------- */
  public function getStudentId(): int
  {
    return $this->studentId;
  }

  public function setStudentId(int $studentId): void
  {
    $this->studentId = $studentId;
  }

  /* ---- user_id (nullable FK -> users.user_id) ------------------------------- */
  public function getUserId(): ?int
  {
    return $this->userId;
  }

  public function setUserId(?int $userId): void
  {
    $this->userId = $userId;
  }

  /* ---- lrn ------------------------------------------------------------------- */
  public function getLrn(): string
  {
    return $this->lrn;
  }

  public function setLrn(string $lrn): void
  {
    $this->lrn = $lrn;
  }

  /* ---- student_number ----------------------------------------------------------- */
  public function getStudentNumber(): string
  {
    return $this->studentNumber;
  }

  public function setStudentNumber(string $studentNumber): void
  {
    $this->studentNumber = $studentNumber;
  }

  /* ---- first_name ------------------------------------------------------------- */
  public function getFirstName(): string
  {
    return $this->firstName;
  }

  public function setFirstName(string $firstName): void
  {
    $this->firstName = $firstName;
  }

  /* ---- last_name --------------------------------------------------------------- */
  public function getLastName(): string
  {
    return $this->lastName;
  }

  public function setLastName(string $lastName): void
  {
    $this->lastName = $lastName;
  }

  /* ---- middle_name ---------------------------------------------------------------- */
  public function getMiddleName(): ?string
  {
    return $this->middleName;
  }

  public function setMiddleName(?string $middleName): void
  {
    $this->middleName = $middleName;
  }

  /* ---- gender ------------------------------------------------------------------------ */
  public function getGender(): string
  {
    return $this->gender;
  }

  public function setGender(string $gender): void
  {
    $this->gender = $gender;
  }

  /* ---- birthdate ------------------------------------------------------------------- */
  public function getBirthdate(): string
  {
    return $this->birthdate;
  }

  public function setBirthdate(string $birthdate): void
  {
    $this->birthdate = $birthdate;
  }

  /* ---- address (editable by this module) --------------------------------------- */
  public function getAddress(): string
  {
    return $this->address;
  }

  public function setAddress(string $address): void
  {
    $this->address = $address;
  }

  /* ---- contact_number (editable by this module) ------------------------------- */
  public function getContactNumber(): ?string
  {
    return $this->contactNumber;
  }

  public function setContactNumber(?string $contactNumber): void
  {
    $this->contactNumber = $contactNumber;
  }

  /* ---- email --------------------------------------------------------------------- */
  public function getEmail(): string
  {
    return $this->email;
  }

  public function setEmail(string $email): void
  {
    $this->email = $email;
  }

  /* ---- grade_level ------------------------------------------------------------------ */
  public function getGradeLevel(): string
  {
    return $this->gradeLevel;
  }

  public function setGradeLevel(string $gradeLevel): void
  {
    $this->gradeLevel = $gradeLevel;
  }

  /* ---- status ------------------------------------------------------------------------- */
  public function getStatus(): string
  {
    return $this->status;
  }

  public function setStatus(string $status): void
  {
    $this->status = $status;
  }

  /* ---- bio (editable by this module) --------------------------------------------------- */
  public function getBio(): ?string
  {
    return $this->bio;
  }

  public function setBio(?string $bio): void
  {
    $this->bio = $bio;
  }

  /* ---- created_at ------------------------------------------------------------------------ */
  public function getCreatedAt(): ?string
  {
    return $this->createdAt;
  }

  public function setCreatedAt(?string $createdAt): void
  {
    $this->createdAt = $createdAt;
  }

  /* ---- updated_at (added by profile_columns_migration.sql) ------------------------------- */
  public function getUpdatedAt(): ?string
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(?string $updatedAt): void
  {
    $this->updatedAt = $updatedAt;
  }

  /* ---- Helpers ---------------------------------------------------------------- */

  /** Full name, "First Middle Last" (middle omitted when not on file). */
  public function getFullName(): string
  {
    $parts = array_filter([$this->firstName, $this->middleName, $this->lastName]);
    return implode(" ", $parts);
  }

  /**
   * Build a StudentProfile from a database row (snake_case keys, as
   * returned by PDO::FETCH_ASSOC). Unknown keys are ignored so the
   * method is safe to call with the result of any SELECT on `students`.
   */
  public static function hydrate(array $row): self
  {
    $studentId = (int) ($row["student_id"] ?? 0);
    $firstName = (string) ($row["first_name"] ?? "");
    $lastName  = (string) ($row["last_name"] ?? "");
    $email     = (string) ($row["email"] ?? "");

    $profile = new self($studentId, $firstName, $lastName, $email);

    if (array_key_exists("user_id", $row)) {
      $profile->setUserId(
        $row["user_id"] === null ? null : (int) $row["user_id"]
      );
    }
    if (array_key_exists("lrn", $row) && $row["lrn"] !== null) {
      $profile->setLrn((string) $row["lrn"]);
    }
    if (array_key_exists("student_number", $row) && $row["student_number"] !== null) {
      $profile->setStudentNumber((string) $row["student_number"]);
    }
    if (array_key_exists("middle_name", $row)) {
      $profile->setMiddleName(
        $row["middle_name"] === null ? null : (string) $row["middle_name"]
      );
    }
    if (array_key_exists("gender", $row) && $row["gender"] !== null) {
      $profile->setGender((string) $row["gender"]);
    }
    if (array_key_exists("birthdate", $row) && $row["birthdate"] !== null) {
      $profile->setBirthdate((string) $row["birthdate"]);
    }
    if (array_key_exists("address", $row) && $row["address"] !== null) {
      $profile->setAddress((string) $row["address"]);
    }
    if (array_key_exists("contact_number", $row)) {
      $profile->setContactNumber(
        $row["contact_number"] === null ? null : (string) $row["contact_number"]
      );
    }
    if (array_key_exists("grade_level", $row) && $row["grade_level"] !== null) {
      $profile->setGradeLevel((string) $row["grade_level"]);
    }
    if (array_key_exists("status", $row) && $row["status"] !== null) {
      $profile->setStatus((string) $row["status"]);
    }
    if (array_key_exists("bio", $row)) {
      $profile->setBio($row["bio"] === null ? null : (string) $row["bio"]);
    }
    if (array_key_exists("created_at", $row)) {
      $profile->setCreatedAt(
        $row["created_at"] === null ? null : (string) $row["created_at"]
      );
    }
    if (array_key_exists("updated_at", $row)) {
      $profile->setUpdatedAt(
        $row["updated_at"] === null ? null : (string) $row["updated_at"]
      );
    }

    return $profile;
  }

  /**
   * Database-friendly associative array (snake_case keys matching the
   * `students` columns this module reads). Not used for a generic
   * UPDATE — the DAO writes address/contact_number/bio individually so
   * registrar-owned fields can never be overwritten through this module.
   */
  public function toArray(): array
  {
    return [
      "student_id"     => $this->studentId,
      "user_id"        => $this->userId,
      "lrn"            => $this->lrn,
      "student_number" => $this->studentNumber,
      "first_name"     => $this->firstName,
      "last_name"      => $this->lastName,
      "middle_name"    => $this->middleName,
      "gender"         => $this->gender,
      "birthdate"      => $this->birthdate,
      "address"        => $this->address,
      "contact_number" => $this->contactNumber,
      "email"          => $this->email,
      "grade_level"    => $this->gradeLevel,
      "status"         => $this->status,
      "bio"            => $this->bio,
      "created_at"     => $this->createdAt,
      "updated_at"     => $this->updatedAt,
    ];
  }

  /**
   * Browser-safe shape. `students` carries no auth secrets (those live
   * on `users`), so this is effectively toArray() minus user_id, kept
   * separately so a future field can be redacted without touching the
   * DB-facing shape.
   */
  public function toPublicArray(): array
  {
    return [
      "student_id"     => $this->studentId,
      "full_name"      => $this->getFullName(),
      "first_name"     => $this->firstName,
      "middle_name"    => $this->middleName,
      "last_name"      => $this->lastName,
      "lrn"            => $this->lrn,
      "student_number" => $this->studentNumber,
      "email"          => $this->email,
      "contact_number" => $this->contactNumber,
      "gender"         => $this->gender,
      "birthdate"      => $this->birthdate,
      "address"        => $this->address,
      "grade_level"    => $this->gradeLevel,
      "status"         => $this->status,
      "bio"            => $this->bio,
      "updated_at"     => $this->updatedAt,
    ];
  }
}
