<?php
class ScheduleDAO
{
  public function __construct(private PDO $db)
  {
    $this->db->exec(
      "CREATE TABLE IF NOT EXISTS schedule_events (event_id INT AUTO_INCREMENT PRIMARY KEY, created_by_role ENUM('Student','Teacher') NOT NULL, created_by_id INT NOT NULL, section_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, title VARCHAR(150) NOT NULL, description TEXT DEFAULT NULL, event_type ENUM('Personal','Quiz','Review','Announcement') NOT NULL DEFAULT 'Personal', start_datetime DATETIME NOT NULL, end_datetime DATETIME NOT NULL, status ENUM('Scheduled','Cancelled','Done') NOT NULL DEFAULT 'Scheduled', created_at DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    );
  }
  public function subjects(): array
  {
    return $this->db
      ->query(
        "SELECT subject_id, subject_code, subject_name FROM subjects WHERE status = 'Active' ORDER BY subject_name",
      )
      ->fetchAll(PDO::FETCH_ASSOC);
  }
  public function subjectExists(int $id): bool
  {
    $stmt = $this->db->prepare("SELECT 1 FROM subjects WHERE subject_id = ? AND status = 'Active'");
    $stmt->execute([$id]);
    return (bool) $stmt->fetchColumn();
  }
  public function sectionId(string $role, int $userId): ?int
  {
    $sql =
      $role === "student"
        ? "SELECT section_id FROM enrollments WHERE student_id = ? AND status = 'Enrolled' ORDER BY enrollment_id DESC LIMIT 1"
        : "SELECT section_id FROM schedules WHERE teacher_id = ? ORDER BY schedule_id LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$userId]);
    $value = $stmt->fetchColumn();
    return $value === false ? null : (int) $value;
  }
  private function eventQuery(
    string $role,
    int $userId,
    ?int $sectionId,
    ?string $date = null,
  ): array {
    $sql =
      "SELECT e.*, sub.subject_code, sub.subject_name FROM schedule_events e LEFT JOIN subjects sub ON sub.subject_id = e.subject_id WHERE ";
    $params = [];
    if ($role === "student") {
      $sql .=
        "((e.created_by_role = 'Student' AND e.created_by_id = ? AND e.section_id IS NULL) OR (e.created_by_role = 'Teacher' AND e.section_id = ?))";
      $params = [$userId, $sectionId];
    } else {
      $sql .= "e.created_by_role = 'Teacher' AND e.created_by_id = ?";
      $params = [$userId];
    }
    if ($date !== null) {
      $sql .= " AND DATE(e.start_datetime) = ?";
      $params[] = $date;
    }
    return [$sql, $params];
  }
  private function fetch(string $sql, array $params): array
  {
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function events(string $role, int $userId, ?int $sectionId): array
  {
    [$sql, $params] = $this->eventQuery($role, $userId, $sectionId);
    $sql .= " ORDER BY e.start_datetime";
    return $this->fetch($sql, $params);
  }
  public function dayEvents(
    string $role,
    int $userId,
    ?int $sectionId,
    string $date,
    string $status,
  ): array {
    [$sql, $params] = $this->eventQuery($role, $userId, $sectionId, $date);
    if ($status !== "all") {
      $sql .= " AND e.status = ?";
      $params[] = $status;
    }
    $sql .= " ORDER BY e.start_datetime";
    return $this->fetch($sql, $params);
  }
  private function owner(string $role): string
  {
    return $role === "teacher" ? "Teacher" : "Student";
  }
  private function assertOwner(string $eventId, string $role, int $userId): void
  {
    $stmt = $this->db->prepare(
      "SELECT event_id FROM schedule_events WHERE event_id = ? AND created_by_role = ? AND created_by_id = ?",
    );
    $stmt->execute([(int) $eventId, $this->owner($role), $userId]);
    if (!$stmt->fetchColumn()) {
      throw new RuntimeException("You can only edit notes or events that you created.");
    }
  }
  public function create(array $input, string $role, int $userId): void
  {
    $section =
      $role === "teacher" && $input["share_to_section"] !== ""
        ? $this->sectionId($role, $userId)
        : null;
    $stmt = $this->db->prepare(
      "INSERT INTO schedule_events (created_by_role, created_by_id, section_id, subject_id, title, description, event_type, start_datetime, end_datetime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
    );
    $stmt->execute([
      $this->owner($role),
      $userId,
      $section,
      (int) $input["subject_id"],
      $input["title"],
      $input["description"] ?: null,
      $input["event_type"],
      str_replace("T", " ", $input["start_datetime"]) . ":00",
      str_replace("T", " ", $input["end_datetime"]) . ":00",
    ]);
  }
  public function update(array $input, string $role, int $userId): void
  {
    $this->assertOwner($input["event_id"], $role, $userId);
    $section =
      $role === "teacher" && $input["share_to_section"] !== ""
        ? $this->sectionId($role, $userId)
        : null;
    $stmt = $this->db->prepare(
      "UPDATE schedule_events SET section_id = ?, subject_id = ?, title = ?, description = ?, event_type = ?, start_datetime = ?, end_datetime = ? WHERE event_id = ?",
    );
    $stmt->execute([
      $section,
      (int) $input["subject_id"],
      $input["title"],
      $input["description"] ?: null,
      $input["event_type"],
      str_replace("T", " ", $input["start_datetime"]) . ":00",
      str_replace("T", " ", $input["end_datetime"]) . ":00",
      (int) $input["event_id"],
    ]);
  }
  public function delete(string $eventId, string $role, int $userId): void
  {
    $this->assertOwner($eventId, $role, $userId);
    $this->db->prepare("DELETE FROM schedule_events WHERE event_id = ?")->execute([(int) $eventId]);
  }
  public function calendarEvents(array $events, string $role, int $userId, ?int $sectionId): array
  {
    $calendar = [];
    foreach ($events as $event) {
      $isOwner =
        $event["created_by_role"] === $this->owner($role) &&
        (int) $event["created_by_id"] === $userId;
      $calendar[] = [
        "id" => "event-" . $event["event_id"],
        "title" => $event["title"],
        "start" => date(DATE_ATOM, strtotime($event["start_datetime"])),
        "end" => date(DATE_ATOM, strtotime($event["end_datetime"])),
        "className" => "calendar-event-" . strtolower($event["event_type"]),
        "extendedProps" => ["editable" => $isOwner, "event" => $event],
      ];
    }
    $sql =
      $role === "student"
        ? "SELECT s.schedule_id, s.day_of_week, s.start_time, s.end_time, sub.subject_code, r.room_name, cs.section_name FROM schedules s JOIN subjects sub ON sub.subject_id = s.subject_id JOIN rooms r ON r.room_id = s.room_id JOIN class_sections cs ON cs.section_id = s.section_id WHERE s.section_id = ?"
        : "SELECT s.schedule_id, s.day_of_week, s.start_time, s.end_time, sub.subject_code, r.room_name, cs.section_name FROM schedules s JOIN subjects sub ON sub.subject_id = s.subject_id JOIN rooms r ON r.room_id = s.room_id JOIN class_sections cs ON cs.section_id = s.section_id WHERE s.teacher_id = ?";
    $schedules = $this->fetch($sql, [$role === "student" ? $sectionId : $userId]);
    $start = new DateTime("first day of this month");
    $end = (clone $start)->modify("+12 months");
    foreach ($schedules as $item) {
      for ($cursor = clone $start; $cursor < $end; $cursor->modify("+1 day")) {
        if ($cursor->format("l") === $item["day_of_week"]) {
          $calendar[] = [
            "id" => "schedule-" . $item["schedule_id"] . "-" . $cursor->format("Ymd"),
            "title" => $item["subject_code"] . " - " . $item["room_name"],
            "start" => $cursor->format("Y-m-d") . "T" . substr($item["start_time"], 0, 5),
            "end" => $cursor->format("Y-m-d") . "T" . substr($item["end_time"], 0, 5),
            "className" => "calendar-schedule",
            "extendedProps" => ["section" => $item["section_name"]],
          ];
        }
      }
    }
    return $calendar;
  }
}
