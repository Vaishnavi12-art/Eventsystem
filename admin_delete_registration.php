<?php

session_start();
include "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: manage_registrations.php?error=" . urlencode("Invalid delete request."));
    exit();
}

$registration_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$search = trim($_POST["search"] ?? "");
$redirect_query = $search === "" ? "" : "&search=" . urlencode($search);

if (!$registration_id || $registration_id <= 0) {
    header("Location: manage_registrations.php?error=" . urlencode("Invalid registration ID.") . $redirect_query);
    exit();
}

if (($_POST["confirm"] ?? "") !== "yes") {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT r.id AS registration_id,
                u.name AS user_name,
                e.event_name,
                e.event_date
         FROM registrations r
         JOIN users u ON r.user_id = u.id
         JOIN events e ON r.event_id = e.id
         WHERE r.id = ?
         LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "i", $registration_id);
    mysqli_stmt_execute($stmt);
    $registration = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$registration) {
        header("Location: manage_registrations.php?error=" . urlencode("Registration not found.") . $redirect_query);
        exit();
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirm Registration Cancellation - EventHub</title>
        <link rel="stylesheet" href="css/style.css?v=20260919">
    </head>
    <body>
    <nav class="navbar">
        <div class="logo">Event<span>Hub</span></div>
        <ul class="nav-links">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_registrations.php<?php echo $search === "" ? "" : "?search=" . urlencode($search); ?>">Registrations</a></li>
        </ul>
        <div class="nav-actions">
            <span class="admin-username">Admin: <?php echo htmlspecialchars($_SESSION["admin_username"] ?? ""); ?></span>
            <a href="admin_logout.php" class="register">Logout</a>
        </div>
    </nav>

    <section class="admin-page admin-confirmation-page">
        <div class="admin-confirmation-panel">
            <p class="section-label">REGISTRATION MANAGEMENT</p>
            <h1>Confirm Cancellation</h1>
            <p class="admin-confirmation-message">
                Are you sure you want to cancel this registration?<br>
                This will remove the user's booking for this event and make the seat available again.
            </p>
            <div class="admin-registration-summary">
                <p><strong>User:</strong> <?php echo htmlspecialchars($registration["user_name"]); ?></p>
                <p><strong>Event:</strong> <?php echo htmlspecialchars($registration["event_name"]); ?></p>
                <p><strong>Event Date:</strong> <?php echo htmlspecialchars(date("d M Y", strtotime($registration["event_date"]))); ?></p>
            </div>
            <div class="admin-confirmation-actions">
                <form method="post" action="admin_delete_registration.php">
                    <input type="hidden" name="id" value="<?php echo (int) $registration["registration_id"]; ?>">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="confirm" value="yes">
                    <button type="submit" class="admin-delete-btn">Yes, Cancel Registration</button>
                </form>
                <a href="manage_registrations.php<?php echo $search === "" ? "" : "?search=" . urlencode($search); ?>" class="btn btn-secondary">No, Keep Registration</a>
            </div>
        </div>
    </section>

    <footer><div class="copyright">© 2026 EventHub | Event Management System</div></footer>
    </body>
    </html>
    <?php
    exit();
}

mysqli_begin_transaction($conn);

/* Lock the registration and event before changing either record. */
$stmt = mysqli_prepare(
    $conn,
    "SELECT r.id AS registration_id,
            r.event_id,
            e.total_seats,
            e.remaining_seats
     FROM registrations r
     JOIN events e ON r.event_id = e.id
     WHERE r.id = ?
     LIMIT 1
     FOR UPDATE"
);
mysqli_stmt_bind_param($stmt, "i", $registration_id);
mysqli_stmt_execute($stmt);
$registration = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$registration) {
    mysqli_rollback($conn);
    header("Location: manage_registrations.php?error=" . urlencode("Registration not found.") . $redirect_query);
    exit();
}

$event_id = (int) $registration["event_id"];

$stmt = mysqli_prepare($conn, "DELETE FROM registrations WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $registration_id);
$registration_deleted = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1;

$seats_updated = false;
if ($registration_deleted) {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE events
         SET remaining_seats = LEAST(remaining_seats + 1, total_seats)
         WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    $seats_updated = mysqli_stmt_execute($stmt);
}

if (!$registration_deleted || !$seats_updated) {
    mysqli_rollback($conn);
    header("Location: manage_registrations.php?error=" . urlencode("The registration could not be deleted. Please try again.") . $redirect_query);
    exit();
}

mysqli_commit($conn);
header("Location: manage_registrations.php?success=" . urlencode("Registration cancelled successfully.\nThe seat has been made available again.") . $redirect_query);
exit();

?>