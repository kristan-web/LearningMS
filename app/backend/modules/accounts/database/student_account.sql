-- ----------------------------------------------------------------
-- Student account credentials & lifecycle table
-- Database: enrollment_management_system
--
-- Purpose:
--   The generic `users` table is shared by Admin / Staff / Registrar /
--   Accounting / Teacher / Student / Guardian. It only stores a
--   password_hash, full_name, email and role. It has no place for
--   account-lifecycle concerns (status, lockout, last login, failed
--   attempts, password-change tracking, email verification, password
--   reset tokens, 2FA, etc.).
--
--   The `students` table is the student's *personal / academic
--   record*, not their login account. It has no credentials, no
--   username, no status, no lockout, no last-login fields.
--
--   This `student_account` table therefore fills the gap and gives the
--   Accounts module a dedicated place to manage student login
--   accounts. It is keyed 1:1 on `students.student_id` (which is the
--   PK, so the same student cannot have two accounts), with an
--   optional soft-link back to `users.user_id` to stay consistent with
--   the other profile tables (teachers, guardians) in the schema.
-- ----------------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Table structure for table `student_account`
--

CREATE TABLE `student_account` (
  `student_id` int(11) NOT NULL COMMENT 'PK + 1:1 with students.student_id',
  `user_id` int(11) DEFAULT NULL COMMENT 'optional back-link to users.user_id',
  `username` varchar(50) NOT NULL COMMENT 'login id, separate from student_number / LRN / email',
  `password_hash` varchar(255) NOT NULL COMMENT 'bcrypt / argon2 hash; never store plaintext',
  `recovery_email` varchar(100) DEFAULT NULL COMMENT 'used for password-reset if personal email is inaccessible',
  `status` enum('Active','Inactive','Locked','Suspended','Pending Verification') NOT NULL DEFAULT 'Pending Verification',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'soft-disable without deleting the row',
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'forces a reset on first login',
  `password_changed_at` datetime DEFAULT NULL,
  `failed_login_count` int(11) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL COMMENT 'temporary lockout after too many failed attempts',
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL COMMENT 'IPv4 or IPv6',
  `email_verified_at` datetime DEFAULT NULL,
  `email_verification_token` varchar(100) DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `password_reset_expires_at` datetime DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `two_factor_secret` varchar(255) DEFAULT NULL COMMENT 'encrypted TOTP secret, null when 2FA is off',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'users.user_id of the staff/registrar who created the account',
  `updated_by` int(11) DEFAULT NULL COMMENT 'users.user_id of the last staff/admin who updated it',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for table `student_account`
--
ALTER TABLE `student_account`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `uq_student_account_username` (`username`),
  ADD UNIQUE KEY `uq_student_account_user_id` (`user_id`),
  ADD UNIQUE KEY `uq_student_account_reset_token` (`password_reset_token`),
  ADD UNIQUE KEY `uq_student_account_verify_token` (`email_verification_token`),
  ADD KEY `idx_student_account_status` (`status`,`is_active`),
  ADD KEY `idx_student_account_recovery_email` (`recovery_email`),
  ADD KEY `fk_student_account_created_by` (`created_by`),
  ADD KEY `fk_student_account_updated_by` (`updated_by`);

--
-- Constraints for table `student_account`
--
ALTER TABLE `student_account`
  ADD CONSTRAINT `fk_student_account_student`
      FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`)
      ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_account_user`
      FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
      ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_account_created_by`
      FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`)
      ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_account_updated_by`
      FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`)
      ON DELETE SET NULL ON UPDATE CASCADE;

