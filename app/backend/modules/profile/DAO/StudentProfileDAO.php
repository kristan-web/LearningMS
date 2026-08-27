<?php
/**
 * StudentProfileDAO
 *
 * Location once applied: app/backend/modules/profile/DAO/StudentProfileDAO.php
 *
 * Data-access layer for the read/update slice of the `students` table
 * that the Profile module owns (see schema.sql). Provides:
 *   - findByStudentId() / findByUserId() to load a profile for display
 *   - updateContactInfo() to patch ONLY address, contact_number and bio
 *
 * Deliberately narrower than a generic update(StudentProfile $profile)
 * on purpose. Name, lrn, student_number, grade_level, status and the
 * parent/guardian/emergency-contact fields are registrar-owned (see
 * `applicants` -> enrollment conversion) and must never be mutated
 * through the self-service Profile module, so the DAO simply doesn't
 * expose a way to do it.
 *
 * Conventions follow AccountDAO (no namespace, 2-space indentation,
 * `__construct(private PDO $db)`, prepared statements everywhere).
 */
class StudentProfileDAO
{
  public function __construct(private PDO $db)
  {
  }

  /* ====================================================================
   *  READ
   * ================================================================== */

  /** Find a single student's profile by primary key. */
  public function findByStudentId(int $studentId): ?StudentProfile
  {
    $row = $this->fetchOne(
      "SELECT * FROM students WHERE student_id = :id",
      [":id" => $studentId]
    );
    return $row ? StudentProfile::hydrate($row) : null;
  }

  /**
   * Find a single student's profile by the linked `users.user_id`.
   * `students.user_id` is nullable and unique, so this is safe to use
   * as the session -> profile lookup once a student account exists.
   */
  public function findByUserId(int $userId): ?StudentProfile
  {
    $row = $this->fetchOne(
      "SELECT * FROM students WHERE user_id = :uid",
      [":uid" => $userId]
    );
    return $row ? StudentProfile::hydrate($row) : null;
  }

  /** Whether a student row exists at all (cheap existence check). */
  public function exists(int $studentId): bool
  {
    $stmt = $this->db->prepare("SELECT 1 FROM students WHERE student_id = :id");
    $stmt->execute([":id" => $studentId]);
    return (bool) $stmt->fetchColumn();
  }

  /* ====================================================================
   *  UPDATE — intentionally the only write path this DAO exposes
   * ================================================================== */

  /**
   * Patch address, contact_number and bio for one student. Any of the
   * three may be passed as null to explicitly clear that field — the
   * controller is responsible for distinguishing "field not sent" from
   * "field sent as empty" before calling this (address is NOT NULL in
   * the schema, so the controller must never pass null for it).
   *
   * Requires profile_columns_migration.sql to have been run (adds
   * `bio` and `updated_at`, neither of which existed on `students`
   * originally).
   *
   * Returns the freshly re-hydrated profile so the controller can echo
   * back the saved state (including the new updated_at timestamp).
   */
  public function updateContactInfo(
    int $studentId,
    string $address,
    ?string $contactNumber,
    ?string $bio,
  ): StudentProfile {
    $stmt = $this->db->prepare(
      "UPDATE students
          SET address        = :address,
              contact_number = :contact_number,
              bio            = :bio,
              updated_at     = NOW()
        WHERE student_id     = :id"
    );
    $stmt->bindValue(":address", $address);
    $stmt->bindValue(":contact_number", $contactNumber);
    $stmt->bindValue(":bio", $bio);
    $stmt->bindValue(":id", $studentId, PDO::PARAM_INT);
    $stmt->execute();

    $updated = $this->findByStudentId($studentId);
    if ($updated === null) {
      // Row existed moments ago (the controller checks first) — this
      // would only happen on a race with a hard delete. Surface it as a
      // clear server error rather than returning a stale/fake object.
      throw new RuntimeException(
        "Student {$studentId} could not be re-read after update."
      );
    }
    return $updated;
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
}
