<?php

session_start();
include "db.php";

if (!isset($_SESSION["user_name"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: events.php");
    exit();
}

$event_id = intval($_GET["id"]);

$user_name = $_SESSION["user_name"];

$result = mysqli_query(
    $conn,
    "SELECT id FROM users WHERE name = '" . mysqli_real_escape_string($conn, $user_name) . "'"
);

$user = mysqli_fetch_assoc($result);
$user_id = $user["id"];

$check = mysqli_query(
    $conn,
    "SELECT id FROM registrations
     WHERE user_id = $user_id AND event_id = $event_id"
);

if (mysqli_num_rows($check) > 0) {
    echo "You are already registered for this event.";
    exit();
}

$event = mysqli_query(
    $conn,
    "SELECT remaining_seats FROM events WHERE id = $event_id"
);

$event_data = mysqli_fetch_assoc($event);

if (!$event_data) {
    echo "Event not found.";
    exit();
}

if ($event_data["remaining_seats"] <= 0) {
    echo "Sorry, no seats are available.";
    exit();
}

mysqli_query(
    $conn,
    "INSERT INTO registrations (user_id, event_id)
     VALUES ($user_id, $event_id)"
);

mysqli_query(
    $conn,
    "UPDATE events
     SET remaining_seats = remaining_seats - 1
     WHERE id = $event_id"
);

echo "Registration successful!";
?>