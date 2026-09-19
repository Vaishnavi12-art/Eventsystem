<?php

session_start();
include "db.php";
include "registration_message.php";

function show_removal_error($message)
{
    show_registration_result(
        "⚠️ Registration Not Found",
        $message,
        "error",
        [["label" => "My Registrations", "url" => "my_registrations.php"]]
    );
    exit();
}

if (!isset($_SESSION["user_name"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    show_removal_error("Registration not found.");
}

$registration_id = isset($_POST["registration_id"])
    ? intval($_POST["registration_id"])
    : 0;

if ($registration_id <= 0) {
    show_removal_error("Registration not found.");
}

$user_id = isset($_SESSION["user_id"]) ? (int) $_SESSION["user_id"] : 0;

if ($user_id <= 0) {
    $user_name = $_SESSION["user_name"];
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE name = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $user_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        show_removal_error("User account not found.");
    }

    $user_id = (int) $user["id"];
    $_SESSION["user_id"] = $user_id;
}

mysqli_begin_transaction($conn);

/* Lock only this user's registration before deleting it. */
$stmt = mysqli_prepare(
    $conn,
    "SELECT r.event_id,
            e.event_name,
            e.event_date,
            e.location
     FROM registrations r
     JOIN events e ON r.event_id = e.id
     WHERE r.id = ? AND r.user_id = ?
     LIMIT 1
     FOR UPDATE"
);

mysqli_stmt_bind_param($stmt, "ii", $registration_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$registration = mysqli_fetch_assoc($result);

if (!$registration) {
    mysqli_rollback($conn);
    show_removal_error("Registration not found.");
}

$event_id = (int) $registration["event_id"];

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM registrations
     WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($stmt, "ii", $registration_id, $user_id);

if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) !== 1) {
    mysqli_rollback($conn);
    show_removal_error("Registration could not be removed.");
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE events
    SET remaining_seats = LEAST(remaining_seats + 1, total_seats)
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $event_id);

if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) !== 1) {
    mysqli_rollback($conn);
    show_removal_error("Registration could not be removed.");
}

mysqli_commit($conn);
show_registration_result(
    "✓ Registration Removed",
    "Your registration has been removed successfully.",
    "success",
    [["label" => "My Registrations", "url" => "my_registrations.php"]]
);

?>
