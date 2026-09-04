<?php
/**
 * AccountDAO
 *
 * Data-access layer for the `lms_accounts` table. Provides:
 *   - Standard CRUD: create, read (one / by-field / list), update, delete
 *   - Login authentication with built-in rate-limiting (failed_login_count,
 *     locked_until) and lockout semantics
 *   - Token helpers: remember-me, password-reset, email-verification
 *   - Login bookkeeping: last login IP/timestamp, clearing/incrementing
 *     failed-login counters
 *
 * Every method takes its inputs as primitives / arrays / Account objects
 * and returns either an Account, a list of Accounts, or a structured
 * result array. Database errors are converted to RuntimeException so the
 * controller can surface a single, user-friendly message.
 *
 * Conventions follow ScheduleDAO (no namespace, 2-space indentation,
 * `__construct(private PDO $db)`, prepared statements everywhere).
 */
class AccountDAO
{
  /** Lockout threshold — after this many failed attempts, lock the row. */
  public const MAX_FAILED_ATTEMPTS = 5;

  /** How long a temporary lockout lasts (minutes). */
  public const LOCKOUT_MINUTES = 15;

  /** The table name used by this DAO */
  private const TABLE = "lms_accounts";

  public function __construct(private PDO $db)
  {
  }


  /* ====================================================================
   *  CREATE
   * ================================================================== */

  /**
   * Insert a new lms_accounts row. The Account must carry at minimum
   * entity_id, entity_type, username and password_hash; everything else defaults.
   * Returns the same Account, now stamped with the DB-generated
   * created_at / updated_at values.
   */
  public function create(Account $account): Account
  {
    $data = $account->toArray();
    // The DB owns these two timestamps; let it fill them in.
    unset($data["created_at"], $data["updated_at"]);
    // account_id is auto-generated
    unset($data["account_id"]);

    $columns        = array_keys($data);
    $placeholders   = array_map(fn($c) => ":{$c}", $columns);

    $sql =
      "INSERT INTO " . self::TABLE . " (" .
      implode(", ", $columns) .
      ") VALUES (" .
      implode(", ", $placeholders) .
      ")";

    $stmt = $this->db->prepare($sql);
    foreach ($data as $column => $value) {
      $stmt->bindValue(":{$column}", $value);
    }
    $stmt->execute();

    // Get the auto-generated account_id
    $accountId = (int) $this->db->lastInsertId();
    $account->setAccountId($accountId);

    // Fetch the fresh record to get timestamps
    $fresh = $this->findByAccountId($accountId);
    return $fresh ?? $account;
  }

  /* ====================================================================
   *  READ
   * ================================================================== */

  /** Find a single account by its primary key (account_id). */
  public function findByAccountId(int $accountId): ?Account
  {
    $row = $this->fetchOne(
      "SELECT * FROM " . self::TABLE . " WHERE account_id = :id",
      [":id" => $accountId]
    );
    return $row ? Account::hydrate($row) : null;
  }

  /**
   * Find a single account by entity_id and entity_type.
   * This is the primary way to find a student or teacher account.
   */
  public function findByEntity(int $entityId, string $entityType): ?Account
  {
    $row = $this->fetchOne(
      "SELECT * FROM " . self::TABLE . " WHERE entity_id = :id AND entity_type = :type",
      [":id" => $entityId, ":type" => $entityType]
    );
    return $row ? Account::hydrate($row) : null;
  }

  /** Find a single account by login username. */
  public function findByUsername(string $username): ?Account
  {
    $row = $this->fetchOne(
      "SELECT * FROM " . self::TABLE . " WHERE username = :u",
      [":u" => $username]
    );
    return $row ? Account::hydrate($row) : null;
  }

  /** Find a single account by optional back-link to users.user_id. */
  public function findByUserId(int $userId): ?Account
  {
    $row = $this->fetchOne(
      "SELECT * FROM " . self::TABLE . " WHERE user_id = :u",
      [":u" => $userId]
    );
    return $row ? Account::hydrate($row) : null;
  }

  /** Find a single account by recovery email. */
  public function findByRecoveryEmail(string $email): ?Account
  {
    $row = $this->fetchOne(
      "SELECT * FROM " . self::TABLE . " WHERE recovery_email = :e",
      [":e" => $email]
    );
    return $row ? Account::hydrate($row) : null;
  }

  /**
   * Find a single account by a password-reset token. Only returns rows
   * whose token has not expired.
   */
  public function findByResetToken(string $token): ?Account
  {
    $row = $this->fetchOne(
      "SELECT * FROM " . self::TABLE . "
        WHERE password_reset_token = :t
          AND password_reset_expires_at IS NOT NULL
          AND password_reset_expires_at >= NOW()
        LIMIT 1",
      [":t" => $token]
    );
    return $row ? Account::hydrate($row) : null;
  }

  /** Find a single account by email-verification token. */
  public function findByVerificationToken(string $token): ?Account
  {
    $row = $this->fetchOne(
      "SELECT * FROM " . self::TABLE . " WHERE email_verification_token = :t",
      [":t" => $token]
    );
    return $row ? Account::hydrate($row) : null;
  }

  /** Find a single account by remember-me token. */
  public function findByRememberToken(string $token): ?Account
  {
    $row = $this->fetchOne(
      "SELECT * FROM " . self::TABLE . " WHERE remember_token = :t",
      [":t" => $token]
    );
    return $row ? Account::hydrate($row) : null;
  }

  /**
   * List accounts, optionally filtered by status. Use $limit/$offset for
   * pagination. Returns an array of Account objects (possibly empty).
   *
   * @return Account[]
   */
  public function listAll(
    ?string $status = null,
    ?string $entityType = null,
    ?int $limit = null,
    ?int $offset = null,
  ): array {
    $sql    = "SELECT * FROM " . self::TABLE;
    $params = [];
    $where  = [];

    if ($status !== null) {
      $where[]        = "status = :s";
      $params[":s"]   = $status;
    }
    if ($entityType !== null) {
      $where[]         = "entity_type = :et";
      $params[":et"]   = $entityType;
    }

    if (!empty($where)) {
      $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY created_at DESC, account_id DESC";

    if ($limit !== null) {
      $sql .= " LIMIT " . (int) $limit;
      if ($offset !== null) {
        $sql .= " OFFSET " . (int) $offset;
      }
    }

    $rows = $this->fetchAll($sql, $params);
    return array_map(fn(array $row) => Account::hydrate($row), $rows);
  }

  /**
   * Check whether a username is already taken. Pass $excludeAccountId
   * when validating a username change on an existing account so the
   * account's own row is ignored.
   */
  public function existsByUsername(string $username, ?int $excludeAccountId = null): bool
  {
    $sql    = "SELECT 1 FROM " . self::TABLE . " WHERE username = :u";
    $params = [":u" => $username];

    if ($excludeAccountId !== null) {
      $sql                  .= " AND account_id <> :aid";
      $params[":aid"]        = $excludeAccountId;
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return (bool) $stmt->fetchColumn();
  }


  /* ====================================================================
   *  UPDATE
   * ================================================================== */

  /**
   * Persist any mutable field on the account. The primary key
   * (account_id) is intentionally NOT updated. Returns the freshly
   * re-hydrated row from the DB.
   */
  public function update(Account $account): Account
  {
    $accountId = $account->getAccountId();
    $data      = $account->toArray();
    unset($data["account_id"], $data["created_at"], $data["updated_at"]);

    $assignments = [];
    foreach (array_keys($data) as $column) {
      $assignments[] = "{$column} = :{$column}";
    }

    $sql =
      "UPDATE " . self::TABLE . " SET " .
      implode(", ", $assignments) .
      " WHERE account_id = :account_id";

    $stmt = $this->db->prepare($sql);
    foreach ($data as $column => $value) {
      $stmt->bindValue(":{$column}", $value);
    }
    $stmt->bindValue(":account_id", $accountId, PDO::PARAM_INT);
    $stmt->execute();

    return $this->findByAccountId($accountId) ?? $account;
  }

  /** Update only the password hash + bookkeeping timestamps. */
  public function updatePassword(int $accountId, string $passwordHash): void
  {
    $stmt = $this->db->prepare(
      "UPDATE " . self::TABLE . "
          SET password_hash = :h,
              password_changed_at = NOW(),
              must_change_password = 0,
              failed_login_count = 0,
              locked_until = NULL,
              password_reset_token = NULL,
              password_reset_expires_at = NULL
        WHERE account_id = :id"
    );
    $stmt->execute([":h" => $passwordHash, ":id" => $accountId]);
  }

  /**
   * Change the account's status (Active / Inactive / Locked / Suspended /
   * Pending Verification).
   */
  public function updateStatus(int $accountId, string $status): void
  {
    $stmt = $this->db->prepare(
      "UPDATE " . self::TABLE . " SET status = :s WHERE account_id = :id"
    );
    $stmt->execute([":s" => $status, ":id" => $accountId]);
  }

  /** Replace the remember-me cookie token. */
  public function setRememberToken(int $accountId, ?string $token): void
  {
    $stmt = $this->db->prepare(
      "UPDATE " . self::TABLE . " SET remember_token = :t WHERE account_id = :id"
    );
    $stmt->execute([":t" => $token, ":id" => $accountId]);
  }

  /** Issue (or replace) a password-reset token. */
  public function setPasswordResetToken(
    int $accountId,
    string $token,
    int $expiresInMinutes = 60,
  ): void {
    $stmt = $this->db->prepare(
      "UPDATE " . self::TABLE . "
          SET password_reset_token = :t,
              password_reset_expires_at = DATE_ADD(NOW(), INTERVAL :m MINUTE)
        WHERE account_id = :id"
    );
    $stmt->execute([
      ":t" => $token,
      ":m" => $expiresInMinutes,
      ":id" => $accountId,
    ]);
  }

  /** Issue (or replace) an email-verification token. */
  public function setEmailVerificationToken(int $accountId, string $token): void
  {
    $stmt = $this->db->prepare(
      "UPDATE " . self::TABLE . "
          SET email_verification_token = :t
        WHERE account_id = :id"
    );
    $stmt->execute([":t" => $token, ":id" => $accountId]);
  }


  /** Mark an account's email as verified and clear the verification token. */
  public function markEmailVerified(int $accountId): void
  {
    $stmt = $this->db->prepare(
      "UPDATE " . self::TABLE . "
          SET email_verified_at = NOW(),
              email_verification_token = NULL,
              status = CASE
                WHEN status = 'Pending Verification' THEN 'Active'
                ELSE status
              END
        WHERE account_id = :id"
    );
    $stmt->execute([":id" => $accountId]);
  }

  /**
   * Stamp a successful login. Resets failed-login counters and unlocks
   * the row if it was temporarily locked.
   */
  public function recordLogin(int $accountId, ?string $ipAddress = null): void
  {
    $stmt = $this->db->prepare(
      "UPDATE " . self::TABLE . "
          SET last_login_at = NOW(),
              last_login_ip = :ip,
              failed_login_count = 0,
              locked_until = NULL
        WHERE account_id = :id"
    );
    $stmt->execute([
      ":ip" => $ipAddress,
      ":id" => $accountId,
    ]);
  }

  /**
   * Increment failed_login_count. If the new value reaches
   * MAX_FAILED_ATTEMPTS, set status='Locked' and locked_until accordingly.
   * Returns the new count.
   */
  public function incrementFailedLogins(int $accountId): int
  {
    $stmt = $this->db->prepare(
      "UPDATE " . self::TABLE . "
          SET failed_login_count = failed_login_count + 1
        WHERE account_id = :id"
    );
    $stmt->execute([":id" => $accountId]);

    $row   = $this->fetchOne(
      "SELECT failed_login_count FROM " . self::TABLE . " WHERE account_id = :id",
      [":id" => $accountId]
    );
    $count = (int) ($row["failed_login_count"] ?? 0);

    if ($count >= self::MAX_FAILED_ATTEMPTS) {
      $lock = $this->db->prepare(
        "UPDATE " . self::TABLE . "
            SET status = 'Locked',
                locked_until = DATE_ADD(NOW(), INTERVAL :m MINUTE)
          WHERE account_id = :id"
      );
      $lock->execute([":m" => self::LOCKOUT_MINUTES, ":id" => $accountId]);
    }

    return $count;
  }

  /** Reset the failed-login counter (e.g. after manual unlock). */
  public function clearFailedLogins(int $accountId): void
  {
    $stmt = $this->db->prepare(
      "UPDATE " . self::TABLE . "
          SET failed_login_count = 0,
              locked_until = NULL
        WHERE account_id = :id"
    );
    $stmt->execute([":id" => $accountId]);
  }

  /* ====================================================================
   *  DELETE
   * ================================================================== */

  /**
   * Hard-delete a lms_accounts row.
   */
  public function delete(int $accountId): void
  {
    $stmt = $this->db->prepare(
      "DELETE FROM " . self::TABLE . " WHERE account_id = :id"
    );
    $stmt->execute([":id" => $accountId]);
  }


  /* ====================================================================
   *  LOGIN / AUTHENTICATION
   * ================================================================== */

  /**
   * Authenticate a login attempt.
   *
   * The $identifier can be either a username or a recovery email — the
   * DAO looks up both. The returned array always has the shape:
   *
   *   [
   *     "success"            => bool,
   *     "reason"             => string,   // short machine code
   *     "message"            => string,   // human-friendly explanation
   *     "account"            => ?Account, // populated on success
   *     "attempts_remaining" => int,
   *     "lockout_until"      => ?string,  // "Y-m-d H:i:s" when locked
   *   ]
   *
   * Possible $reason values:
   *   "ok"                  — credentials correct
   *   "no_user"             — no account with that identifier
   *   "wrong_password"      — identifier found, but password didn't match
   *   "locked"              — account is locked (status or locked_until)
   *   "inactive"            — account exists but is not Active
   *   "must_change_password"— success but the user must reset first
   *   "pending_verification"— success but email must be verified first
   *
   * Failed attempts are recorded against the account. After
   * MAX_FAILED_ATTEMPTS, the row is locked automatically.
   */
  public function authenticate(
    string $identifier,
    string $password,
    ?string $ipAddress = null,
  ): array {
    $empty = [
      "success"            => false,
      "reason"             => "no_user",
      "message"            => "Invalid username/email or password.",
      "account"            => null,
      "attempts_remaining" => 0,
      "lockout_until"      => null,
    ];

    $account =
      $this->findByUsername($identifier) ??
      $this->findByRecoveryEmail($identifier);

    if (!$account) {
      // Run a dummy verify to keep timing roughly constant.
      password_verify(
        $password,
        '$2y$10$invalidinvalidinvalidinvalidinvalidinvalidinvalidinvalida'
      );
      return $empty;
    }

    $accountId = $account->getAccountId();

    // Lockout-by-time check (e.g. previously locked for 15 minutes).
    $lockedUntil = $account->getLockedUntil();
    if ($lockedUntil !== null && strtotime($lockedUntil) > time()) {
      return [
        "success"            => false,
        "reason"             => "locked",
        "message"            => "This account is temporarily locked. Try again later.",
        "account"            => $account,
        "attempts_remaining" => 0,
        "lockout_until"      => $lockedUntil,
      ];
    }
    if ($account->getStatus() === Account::STATUS_LOCKED) {
      return [
        "success"            => false,
        "reason"             => "locked",
        "message"            => "This account is locked. Please contact the registrar.",
        "account"            => $account,
        "attempts_remaining" => 0,
        "lockout_until"      => $lockedUntil,
      ];
    }

    if (!password_verify($password, $account->getPasswordHash())) {
      $newCount           = $this->incrementFailedLogins($accountId);
      $attemptsRemaining  = max(0, self::MAX_FAILED_ATTEMPTS - $newCount);
      $justLocked          = $newCount >= self::MAX_FAILED_ATTEMPTS;

      return [
        "success"            => false,
        "reason"             => "wrong_password",
        "message"            => $justLocked
          ? "Too many failed attempts. Your account is now locked."
          : "Invalid username/email or password.",
        "account"            => $account,
        "attempts_remaining" => $attemptsRemaining,
        "lockout_until"      => $justLocked
          ? date("Y-m-d H:i:s", time() + self::LOCKOUT_MINUTES * 60)
          : null,
      ];
    }

    // Credentials OK — record success and reset counters.
    $this->recordLogin($accountId, $ipAddress);

    // Refresh the in-memory copy so callers see the new last_login_*.
    $account = $this->findByAccountId($accountId) ?? $account;

    if ($account->getMustChangePassword()) {
      return [
        "success"            => true,
        "reason"             => "must_change_password",
        "message"            => "Please change your temporary password before continuing.",
        "account"            => $account,
        "attempts_remaining" => self::MAX_FAILED_ATTEMPTS,
        "lockout_until"      => null,
      ];
    }
    if ($account->getStatus() === Account::STATUS_PENDING_VERIFICATION) {
      return [
        "success"            => true,
        "reason"             => "pending_verification",
        "message"            => "Please verify your email before continuing.",
        "account"            => $account,
        "attempts_remaining" => self::MAX_FAILED_ATTEMPTS,
        "lockout_until"      => null,
      ];
    }
    if (!$account->getIsActive() || $account->getStatus() !== Account::STATUS_ACTIVE) {
      return [
        "success"            => true,
        "reason"             => "inactive",
        "message"            => "This account is not active. Please contact the registrar.",
        "account"            => $account,
        "attempts_remaining" => self::MAX_FAILED_ATTEMPTS,
        "lockout_until"      => null,
      ];
    }

    return [
      "success"            => true,
      "reason"             => "ok",
      "message"            => "Login successful.",
      "account"            => $account,
      "attempts_remaining" => self::MAX_FAILED_ATTEMPTS,
      "lockout_until"      => null,
    ];
  }

  /* ====================================================================
   *  Internal helpers
   * ================================================================== */

  /** Fetch a single row as an associative array, or null. */
  private function fetchOne(string $sql, array $params): ?array
  {
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row === false ? null : $row;
  }

  /** Fetch all rows as a list of associative arrays (possibly empty). */
  private function fetchAll(string $sql, array $params): array
  {
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}