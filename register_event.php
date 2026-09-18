<?php

session_start();
include "db.php";
include "registration_message.php";

if (!isset($_SESSION["user_name"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: events.php");
    exit();
}

$event_id = intval($_GET["id"]);

if ($event_id <= 0) {
    $message = "The requested event could not be found.";
    $message_type = "error";
    $page_title = "⚠️ Event Not Found";
    show_registration_result($page_title, $message, $message_type, [
        ["label" => "Back to Events", "url" => "events.php"]
    ]);
    exit();
}

/* Use the logged-in user's ID when available. */
$user_id = isset($_SESSION["user_id"]) ? (int) $_SESSION["user_id"] : 0;

if ($user_id <= 0) {
    $user_name = $_SESSION["user_name"];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id FROM users WHERE name = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $user_name);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        $message = "User account not found.";
        $message_type = "error";
        $page_title = "⚠️ Registration Error";
    } else {
        $user_id = (int) $user["id"];
        $_SESSION["user_id"] = $user_id;
    }
}

if ($user_id <= 0) {
    show_registration_result($page_title, $message, $message_type, [
        ["label" => "Back to Events", "url" => "events.php"]
    ]);
    exit();
}

mysqli_begin_transaction($conn);

/* Lock the event while checking and changing its remaining seats. */
$stmt = mysqli_prepare(
    $conn,
    "SELECT event_name, event_date, location, remaining_seats
     FROM events
     WHERE id = ?
     FOR UPDATE"
);

mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);

if (!$event) {
    mysqli_rollback($conn);
    $message = "The requested event could not be found.";
    $message_type = "error";
    $page_title = "⚠️ Event Not Found";
    show_registration_result($page_title, $message, $message_type, [
        ["label" => "Back to Events", "url" => "events.php"]
    ]);
    exit();
}

/* Check duplicate registration while the event row is locked. */
$stmt = mysqli_prepare(
    $conn,
    "SELECT id FROM registrations
     WHERE user_id = ? AND event_id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "ii", $user_id, $event_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    mysqli_rollback($conn);
    $message = "You are already registered for this event.";
    $message_type = "info";
    $page_title = "⚠️ Already Registered";
    show_registration_result($page_title, $message, $message_type, [
        ["label" => "My Registrations", "url" => "my_registrations.php"]
    ]);
    exit();
}

if ((int) $event["remaining_seats"] <= 0) {
    mysqli_rollback($conn);
    $message = "Sorry, no seats are available for this event.";
    $message_type = "error";
    $page_title = "⚠️ Event Sold Out";
    show_registration_result($page_title, $message, $message_type, [
        ["label" => "Back to Events", "url" => "events.php"]
    ]);
    exit();
}

/* Insert the registration and decrease the seat in one transaction. */
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO registrations (user_id, event_id)
     VALUES (?, ?)"
);

mysqli_stmt_bind_param($stmt, "ii", $user_id, $event_id);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_rollback($conn);
    $message = "Registration could not be completed.";
    $message_type = "error";
    $page_title = "⚠️ Registration Error";
    show_registration_result($page_title, $message, $message_type, [
        ["label" => "Back to Events", "url" => "events.php"]
    ]);
    exit();
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE events
     SET remaining_seats = remaining_seats - 1
     WHERE id = ? AND remaining_seats > 0"
);

mysqli_stmt_bind_param($stmt, "i", $event_id);

if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) !== 1) {
    mysqli_rollback($conn);
    $message = "Registration could not be completed.";
    $message_type = "error";
    $page_title = "⚠️ Registration Error";
    show_registration_result($page_title, $message, $message_type, [
        ["label" => "Back to Events", "url" => "events.php"]
    ]);
    exit();
}

mysqli_commit($conn);
show_registration_result(
    "🎉 Registration Successful!",
    "You have successfully registered for:",
    "success",
    [
        ["label" => "My Registrations", "url" => "my_registrations.php"],
        ["label" => "Back to Events", "url" => "events.php", "class" => "btn-secondary"]
    ],
    $event
);
exit();

?>