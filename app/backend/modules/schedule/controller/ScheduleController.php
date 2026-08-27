<?php
require_once dirname(__DIR__, 5) . "/config/db.php";
require_once __DIR__ . "/../DAO/ScheduleDAO.php";
require_once __DIR__ . "/../model/Schedule.php";

$schedule = new Schedule();
$data = $schedule->queryData($_GET);
$data["errors"] = [];
$data["success_message"] = null;
$data["database_error"] = null;

try {
  $database = new Database();
  $connection = $database->connect();
  if (!$connection) {
    throw new RuntimeException("Unable to connect to the database.");
  }
  $dao = new ScheduleDAO($connection);
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "create";
    if ($action === "delete") {
      $dao->delete($_POST["event_id"] ?? "", $data["role"], $data["user_id"]);
      $data["success_message"] = $data["role"] === "student" ? "Note deleted." : "Event deleted.";
    } else {
      $input = $schedule->input($_POST, $data["role"]);
      $data["errors"] = $schedule->validate($input, $dao);
      if (!$data["errors"]) {
        if ($action === "update") {
          $dao->update($input, $data["role"], $data["user_id"]);
        } else {
          $dao->create($input, $data["role"], $data["user_id"]);
        }
        $data["success_message"] =
          $action === "update"
            ? "Updated successfully."
            : ($data["role"] === "student"
              ? "Note added to your schedule."
              : "Event added to your schedule.");
      }
    }
  }
  $data["subjects"] = $dao->subjects();
  $data["section_id"] = $dao->sectionId($data["role"], $data["user_id"]);
  $data["events"] = $dao->events($data["role"], $data["user_id"], $data["section_id"]);
  $data["selected_day_events"] = $dao->dayEvents(
    $data["role"],
    $data["user_id"],
    $data["section_id"],
    $data["selected_date"],
  );
  $data["calendar_events"] = $dao->calendarEvents(
    $data["events"],
    $data["role"],
    $data["user_id"],
    $data["section_id"],
  );
} catch (Throwable $exception) {
  $data["database_error"] = $exception->getMessage();
}
$data["role_label"] = ucfirst($data["role"]);
$data["switch_role"] = $data["role"] === "teacher" ? "student" : "teacher";
$data["switch_label"] =
  $data["role"] === "teacher" ? "Switch to Student View" : "Switch to Teacher View";
extract($data, EXTR_SKIP);
require dirname(__DIR__, 4) . "/views/schedule/index.php";
