<?php
class Schedule
{
  public function queryData(array $query): array
  {
    $role = ($query["role"] ?? "student") === "teacher" ? "teacher" : "student";
    $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $query["date"] ?? "")
      ? $query["date"]
      : date("Y-m-d");
    return [
      "role" => $role,
      "user_id" => 1,
      "selected_date" => $date,
      "subjects" => [],
      "events" => [],
      "calendar_events" => [],
      "selected_day_events" => [],
      "section_id" => null,
    ];
  }
  public function input(array $post, string $role): array
  {
    $fields = [
      "event_id",
      "subject_id",
      "title",
      "description",
      "event_type",
      "start_datetime",
      "end_datetime",
      "share_to_section",
    ];
    $input = [];
    foreach ($fields as $field) {
      $input[$field] = trim((string) ($post[$field] ?? ""));
    }
    $input["event_type"] = $role === "student" ? "Personal" : $input["event_type"];
    return $input;
  }
  public function validate(array $input, ScheduleDAO $dao): array
  {
    $errors = [];
    $subjectId = filter_var($input["subject_id"], FILTER_VALIDATE_INT);
    $start = DateTime::createFromFormat("Y-m-d\TH:i", $input["start_datetime"]);
    $end = DateTime::createFromFormat("Y-m-d\TH:i", $input["end_datetime"]);
    if ($input["title"] === "") {
      $errors[] = "Title is required.";
    } elseif (mb_strlen($input["title"]) > 150) {
      $errors[] = "Title must not exceed 150 characters.";
    }
    if (!$subjectId || !$dao->subjectExists($subjectId)) {
      $errors[] = "Please select a valid subject.";
    }
    if (!$start || $start->format("Y-m-d\TH:i") !== $input["start_datetime"]) {
      $errors[] = "Start date and time must be valid.";
    }
    if (!$end || $end->format("Y-m-d\TH:i") !== $input["end_datetime"]) {
      $errors[] = "End date and time must be valid.";
    }
    if ($start && $end && $start >= $end) {
      $errors[] = "Start date and time must be before the end date and time.";
    }
    if (!in_array($input["event_type"], ["Personal", "Quiz", "Review", "Announcement"], true)) {
      $errors[] = "Please select a valid event type.";
    }
    return $errors;
  }
}
