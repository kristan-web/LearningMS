CREATE TABLE IF NOT EXISTS `schedule_events` (
  `event_id` INT(11) NOT NULL AUTO_INCREMENT,
  `created_by_role` ENUM('Student','Teacher') NOT NULL,
  `created_by_id` INT(11) NOT NULL,
  `section_id` INT(11) DEFAULT NULL,
  `subject_id` INT(11) DEFAULT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `event_type` ENUM('Personal','Quiz','Review','Announcement') NOT NULL DEFAULT 'Personal',
  `start_datetime` DATETIME NOT NULL,
  `end_datetime` DATETIME NOT NULL,
  `status` ENUM('Scheduled','Done','Cancelled') NOT NULL DEFAULT 'Scheduled',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `schedule_events` (`event_id`, `created_by_role`, `created_by_id`, `section_id`, `subject_id`, `title`, `description`, `event_type`, `start_datetime`, `end_datetime`, `status`, `created_at`) VALUES
(1, 'Student', 1, NULL, 5, 'Math review', 'Review the functions practice set before the quiz.', 'Review', '2026-08-11 09:00:00', '2026-08-11 10:00:00', 'Scheduled', '2026-08-05 09:15:00'),
(2, 'Teacher', 1, 5, 5, 'Quiz reminder', 'Section-wide reminder for the upcoming math quiz.', 'Announcement', '2026-08-11 11:00:00', '2026-08-11 12:00:00', 'Scheduled', '2026-08-06 15:20:00')
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `description` = VALUES(`description`), `start_datetime` = VALUES(`start_datetime`), `end_datetime` = VALUES(`end_datetime`);

INSERT INTO `enrollments` (`student_id`, `section_id`, `school_year`, `school_year_id`, `semester`, `date_enrolled`, `status`)
VALUES (1, 5, '2026-2027', 1, '1st Semester', NOW(), 'Enrolled')
ON DUPLICATE KEY UPDATE `section_id` = VALUES(`section_id`), `status` = VALUES(`status`);
