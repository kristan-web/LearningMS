<?php
/**
 * Account
 *
 * Entity / data-transfer object that maps 1:1 to the `student_account`
 * table defined in
 *   app/backend/modules/accounts/database/student_account.sql
 *
 * The class exposes one private property per SQL column, together with
 * strict typed getters and setters. A `hydrate()` static factory is
 * provided so the DAO layer can build an Account from a fetched row,
 * and a `toArray()` helper exposes the data back in a database-friendly
 * shape (snake_case keys) so it can be used in INSERT/UPDATE statements
 * without manual mapping.
 *
 * Conventions follow the rest of the codebase (no namespace, 2-space
 * indentation, real PHP type hints instead of PHPDoc-only types).
 */
class Account
{
  /* ---- Status enum (matches the SQL ENUM definition) -------------------- */
  public const STATUS_ACTIVE               = "Active";
  public const STATUS_INACTIVE             = "Inactive";
  public const STATUS_LOCKED               = "Locked";
  public const STATUS_SUSPENDED            = "Suspended";
  public const STATUS_PENDING_VERIFICATION = "Pending Verification";

  public const STATUSES = [
    self::STATUS_ACTIVE,
    self::STATUS_INACTIVE,
    self::STATUS_LOCKED,
    self::STATUS_SUSPENDED,
    self::STATUS_PENDING_VERIFICATION,
  ];

  /* ---- Properties (one per student_account column) --------------------- */
  private int $studentId;
  private ?int $userId = null;
  private string $username;
  private string $passwordHash;
  private ?string $recoveryEmail = null;
  private string $status = self::STATUS_PENDING_VERIFICATION;
  private bool $isActive = true;
  private bool $mustChangePassword = true;
  private ?string $passwordChangedAt = null;
  private int $failedLoginCount = 0;
  private ?string $lockedUntil = null;
  private ?string $lastLoginAt = null;
  private ?string $lastLoginIp = null;
  private ?string $emailVerifiedAt = null;
  private ?string $emailVerificationToken = null;
  private ?string $passwordResetToken = null;
  private ?string $passwordResetExpiresAt = null;
  private bool $twoFactorEnabled = false;
  private ?string $twoFactorSecret = null;
  private ?string $rememberToken = null;
  private ?int $createdBy = null;
  private ?int $updatedBy = null;
  private ?string $createdAt = null;
  private ?string $updatedAt = null;

  /* ---- Constructor ------------------------------------------------------ */
  public function __construct(int $studentId, string $username, string $passwordHash)
  {
    $this->setStudentId($studentId);
    $this->setUsername($username);
    $this->setPasswordHash($passwordHash);
  }

  /* ---- student_id (PK) -------------------------------------------------- */
  public function getStudentId(): int
  {
    return $this->studentId;
  }

  public function setStudentId(int $studentId): void
  {
    $this->studentId = $studentId;
  }

  /* ---- user_id ---------------------------------------------------------- */
  public function getUserId(): ?int
  {
    return $this->userId;
  }

  public function setUserId(?int $userId): void
  {
    $this->userId = $userId;
  }

  /* ---- username --------------------------------------------------------- */
  public function getUsername(): string
  {
    return $this->username;
  }

  public function setUsername(string $username): void
  {
    $this->username = $username;
  }

  /* ---- password_hash ---------------------------------------------------- */
  public function getPasswordHash(): string
  {
    return $this->passwordHash;
  }

  public function setPasswordHash(string $passwordHash): void
  {
    $this->passwordHash = $passwordHash;
  }

  /* ---- recovery_email --------------------------------------------------- */
  public function getRecoveryEmail(): ?string
  {
    return $this->recoveryEmail;
  }

  public function setRecoveryEmail(?string $recoveryEmail): void
  {
    $this->recoveryEmail = $recoveryEmail;
  }

  /* ---- status ----------------------------------------------------------- */
  public function getStatus(): string
  {
    return $this->status;
  }

  public function setStatus(string $status): void
  {
    if (!in_array($status, self::STATUSES, true)) {
      throw new InvalidArgumentException(
        "Invalid account status: \"{$status}\"."
      );
    }
    $this->status = $status;
  }

  /* ---- is_active -------------------------------------------------------- */
  public function getIsActive(): bool
  {
    return $this->isActive;
  }

  public function setIsActive(bool $isActive): void
  {
    $this->isActive = $isActive;
  }

  /* ---- must_change_password -------------------------------------------- */
  public function getMustChangePassword(): bool
  {
    return $this->mustChangePassword;
  }

  public function setMustChangePassword(bool $mustChangePassword): void
  {
    $this->mustChangePassword = $mustChangePassword;
  }

  /* ---- password_changed_at --------------------------------------------- */
  public function getPasswordChangedAt(): ?string
  {
    return $this->passwordChangedAt;
  }

  public function setPasswordChangedAt(?string $passwordChangedAt): void
  {
    $this->passwordChangedAt = $passwordChangedAt;
  }

  /* ---- failed_login_count ---------------------------------------------- */
  public function getFailedLoginCount(): int
  {
    return $this->failedLoginCount;
  }

  public function setFailedLoginCount(int $failedLoginCount): void
  {
    if ($failedLoginCount < 0) {
      throw new InvalidArgumentException(
        "Failed login count cannot be negative."
      );
    }
    $this->failedLoginCount = $failedLoginCount;
  }




  /* ---- locked_until ----------------------------------------------------- */
  public function getLockedUntil(): ?string
  {
    return $this->lockedUntil;
  }

  public function setLockedUntil(?string $lockedUntil): void
  {
    $this->lockedUntil = $lockedUntil;
  }

  /* ---- last_login_at ---------------------------------------------------- */
  public function getLastLoginAt(): ?string
  {
    return $this->lastLoginAt;
  }

  public function setLastLoginAt(?string $lastLoginAt): void
  {
    $this->lastLoginAt = $lastLoginAt;
  }

  /* ---- last_login_ip ---------------------------------------------------- */
  public function getLastLoginIp(): ?string
  {
    return $this->lastLoginIp;
  }

  public function setLastLoginIp(?string $lastLoginIp): void
  {
    $this->lastLoginIp = $lastLoginIp;
  }

  /* ---- email_verified_at ------------------------------------------------ */
  public function getEmailVerifiedAt(): ?string
  {
    return $this->emailVerifiedAt;
  }

  public function setEmailVerifiedAt(?string $emailVerifiedAt): void
  {
    $this->emailVerifiedAt = $emailVerifiedAt;
  }

  /* ---- email_verification_token ---------------------------------------- */
  public function getEmailVerificationToken(): ?string
  {
    return $this->emailVerificationToken;
  }

  public function setEmailVerificationToken(?string $emailVerificationToken): void
  {
    $this->emailVerificationToken = $emailVerificationToken;
  }

  /* ---- password_reset_token -------------------------------------------- */
  public function getPasswordResetToken(): ?string
  {
    return $this->passwordResetToken;
  }

  public function setPasswordResetToken(?string $passwordResetToken): void
  {
    $this->passwordResetToken = $passwordResetToken;
  }

  /* ---- password_reset_expires_at --------------------------------------- */
  public function getPasswordResetExpiresAt(): ?string
  {
    return $this->passwordResetExpiresAt;
  }

  public function setPasswordResetExpiresAt(?string $passwordResetExpiresAt): void
  {
    $this->passwordResetExpiresAt = $passwordResetExpiresAt;
  }

  /* ---- two_factor_enabled ---------------------------------------------- */
  public function getTwoFactorEnabled(): bool
  {
    return $this->twoFactorEnabled;
  }

  public function setTwoFactorEnabled(bool $twoFactorEnabled): void
  {
    $this->twoFactorEnabled = $twoFactorEnabled;
  }

  /* ---- two_factor_secret ------------------------------------------------ */
  public function getTwoFactorSecret(): ?string
  {
    return $this->twoFactorSecret;
  }

  public function setTwoFactorSecret(?string $twoFactorSecret): void
  {
    $this->twoFactorSecret = $twoFactorSecret;
  }



  /* ---- remember_token --------------------------------------------------- */
  public function getRememberToken(): ?string
  {
    return $this->rememberToken;
  }

  public function setRememberToken(?string $rememberToken): void
  {
    $this->rememberToken = $rememberToken;
  }

  /* ---- created_by ------------------------------------------------------- */
  public function getCreatedBy(): ?int
  {
    return $this->createdBy;
  }

  public function setCreatedBy(?int $createdBy): void
  {
    $this->createdBy = $createdBy;
  }

  /* ---- updated_by ------------------------------------------------------- */
  public function getUpdatedBy(): ?int
  {
    return $this->updatedBy;
  }

  public function setUpdatedBy(?int $updatedBy): void
  {
    $this->updatedBy = $updatedBy;
  }

  /* ---- created_at ------------------------------------------------------- */
  public function getCreatedAt(): ?string
  {
    return $this->createdAt;
  }

  public function setCreatedAt(?string $createdAt): void
  {
    $this->createdAt = $createdAt;
  }

  /* ---- updated_at ------------------------------------------------------- */
  public function getUpdatedAt(): ?string
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(?string $updatedAt): void
  {
    $this->updatedAt = $updatedAt;
  }


  /* ---- Helpers ---------------------------------------------------------- */

  /**
   * Build an Account from a database row (snake_case keys, as returned by
   * PDO::FETCH_ASSOC). Unknown keys are ignored so the method is safe to
   * call with the result of any SELECT on `student_account`.
   */
  public static function hydrate(array $row): self
  {
    $studentId    = (int) ($row["student_id"] ?? 0);
    $username     = (string) ($row["username"] ?? "");
    $passwordHash = (string) ($row["password_hash"] ?? "");

    $account = new self($studentId, $username, $passwordHash);

    if (array_key_exists("user_id", $row)) {
      $account->setUserId(
        $row["user_id"] === null ? null : (int) $row["user_id"]
      );
    }
    if (array_key_exists("recovery_email", $row)) {
      $account->setRecoveryEmail(
        $row["recovery_email"] === null ? null : (string) $row["recovery_email"]
      );
    }
    if (array_key_exists("status", $row) && $row["status"] !== null) {
      $account->setStatus((string) $row["status"]);
    }
    if (array_key_exists("is_active", $row) && $row["is_active"] !== null) {
      $account->setIsActive((bool) $row["is_active"]);
    }
    if (
      array_key_exists("must_change_password", $row) &&
      $row["must_change_password"] !== null
    ) {
      $account->setMustChangePassword((bool) $row["must_change_password"]);
    }
    if (array_key_exists("password_changed_at", $row)) {
      $account->setPasswordChangedAt(
        $row["password_changed_at"] === null
          ? null
          : (string) $row["password_changed_at"]
      );
    }
    if (
      array_key_exists("failed_login_count", $row) &&
      $row["failed_login_count"] !== null
    ) {
      $account->setFailedLoginCount((int) $row["failed_login_count"]);
    }
    if (array_key_exists("locked_until", $row)) {
      $account->setLockedUntil(
        $row["locked_until"] === null ? null : (string) $row["locked_until"]
      );
    }
    if (array_key_exists("last_login_at", $row)) {
      $account->setLastLoginAt(
        $row["last_login_at"] === null ? null : (string) $row["last_login_at"]
      );
    }
    if (array_key_exists("last_login_ip", $row)) {
      $account->setLastLoginIp(
        $row["last_login_ip"] === null ? null : (string) $row["last_login_ip"]
      );
    }
    if (array_key_exists("email_verified_at", $row)) {
      $account->setEmailVerifiedAt(
        $row["email_verified_at"] === null
          ? null
          : (string) $row["email_verified_at"]
      );
    }
    if (array_key_exists("email_verification_token", $row)) {
      $account->setEmailVerificationToken(
        $row["email_verification_token"] === null
          ? null
          : (string) $row["email_verification_token"]
      );
    }
    if (array_key_exists("password_reset_token", $row)) {
      $account->setPasswordResetToken(
        $row["password_reset_token"] === null
          ? null
          : (string) $row["password_reset_token"]
      );
    }
    if (array_key_exists("password_reset_expires_at", $row)) {
      $account->setPasswordResetExpiresAt(
        $row["password_reset_expires_at"] === null
          ? null
          : (string) $row["password_reset_expires_at"]
      );
    }
    if (
      array_key_exists("two_factor_enabled", $row) &&
      $row["two_factor_enabled"] !== null
    ) {
      $account->setTwoFactorEnabled((bool) $row["two_factor_enabled"]);
    }
    if (array_key_exists("two_factor_secret", $row)) {
      $account->setTwoFactorSecret(
        $row["two_factor_secret"] === null
          ? null
          : (string) $row["two_factor_secret"]
      );
    }
    if (array_key_exists("remember_token", $row)) {
      $account->setRememberToken(
        $row["remember_token"] === null ? null : (string) $row["remember_token"]
      );
    }
    if (array_key_exists("created_by", $row)) {
      $account->setCreatedBy(
        $row["created_by"] === null ? null : (int) $row["created_by"]
      );
    }
    if (array_key_exists("updated_by", $row)) {
      $account->setUpdatedBy(
        $row["updated_by"] === null ? null : (int) $row["updated_by"]
      );
    }
    if (array_key_exists("created_at", $row)) {
      $account->setCreatedAt(
        $row["created_at"] === null ? null : (string) $row["created_at"]
      );
    }
    if (array_key_exists("updated_at", $row)) {
      $account->setUpdatedAt(
        $row["updated_at"] === null ? null : (string) $row["updated_at"]
      );
    }

    return $account;
  }


  /**
   * Convert the entity to a database-friendly associative array
   * (snake_case keys matching the `student_account` columns).
   * Booleans are normalized to 0/1 so they can be bound directly to
   * PDO statements that expect integer TINYINT values.
   */
  public function toArray(): array
  {
    return [
      "student_id"                => $this->studentId,
      "user_id"                   => $this->userId,
      "username"                  => $this->username,
      "password_hash"             => $this->passwordHash,
      "recovery_email"            => $this->recoveryEmail,
      "status"                    => $this->status,
      "is_active"                 => $this->isActive ? 1 : 0,
      "must_change_password"      => $this->mustChangePassword ? 1 : 0,
      "password_changed_at"       => $this->passwordChangedAt,
      "failed_login_count"        => $this->failedLoginCount,
      "locked_until"              => $this->lockedUntil,
      "last_login_at"             => $this->lastLoginAt,
      "last_login_ip"             => $this->lastLoginIp,
      "email_verified_at"         => $this->emailVerifiedAt,
      "email_verification_token"  => $this->emailVerificationToken,
      "password_reset_token"      => $this->passwordResetToken,
      "password_reset_expires_at" => $this->passwordResetExpiresAt,
      "two_factor_enabled"        => $this->twoFactorEnabled ? 1 : 0,
      "two_factor_secret"         => $this->twoFactorSecret,
      "remember_token"            => $this->rememberToken,
      "created_by"                => $this->createdBy,
      "updated_by"                => $this->updatedBy,
      "created_at"                => $this->createdAt,
      "updated_at"                => $this->updatedAt,
    ];
  }
}

