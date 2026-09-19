<?php

session_start();
include "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_dashboard.php?error=" . urlencode("Invalid delete request."));
    exit();
}

$event_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
if (!$event_id || $event_id <= 0) {
    header("Location: admin_dashboard.php?error=" . urlencode("Invalid event ID."));
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT image FROM events WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$event = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$event) {
    header("Location: admin_dashboard.php?error=" . urlencode("Event not found."));
    exit();
}

mysqli_begin_transaction($conn);

$stmt = mysqli_prepare($conn, "DELETE FROM registrations WHERE event_id = ?");
mysqli_stmt_bind_param($stmt, "i", $event_id);
$registrations_deleted = mysqli_stmt_execute($stmt);

$event_deleted = false;
if ($registrations_deleted) {
    $stmt = mysqli_prepare($conn, "DELETE FROM events WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    $event_deleted = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1;
}

if (!$event_deleted) {
    mysqli_rollback($conn);
    header("Location: admin_dashboard.php?error=" . urlencode("The event could not be deleted. Please try again."));
    exit();
}

mysqli_commit($conn);

$image_path = __DIR__ . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . basename($event["image"]);
if (is_file($image_path)) {
    unlink($image_path);
}

header("Location: admin_dashboard.php?success=" . urlencode("Event deleted successfully."));
exit();

?>