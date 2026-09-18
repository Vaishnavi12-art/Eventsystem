<?php

session_start();
include "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

function get_count($conn, $table)
{
    $allowed_tables = ["events", "users", "registrations"];

    if (!in_array($table, $allowed_tables, true)) {
        return 0;
    }

    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM `$table`");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return (int) $row["total"];
}

$total_events = get_count($conn, "events");
$total_users = get_count($conn, "users");
$total_registrations = get_count($conn, "registrations");
$success_message = isset($_GET["success"]) ? trim($_GET["success"]) : "";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EventHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">Event<span>Hub</span></div>
    <ul class="nav-links">
        <li><a href="index.php">View Site</a></li>
        <li><a href="events.php">Events</a></li>
    </ul>
    <div class="nav-actions">
        <span class="admin-username">
            Admin: <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>
        </span>
        <a href="admin_logout.php" class="register">Logout</a>
    </div>
</nav>

<section class="admin-page admin-dashboard-page">
    <div class="admin-heading">
        <div>
            <p class="section-label">EVENTHUB ADMIN</p>
            <h1>Admin Dashboard</h1>
            <p>Manage your events and view platform activity.</p>
        </div>
        <a href="add_event.php" class="btn">Add New Event</a>
    </div>

    <?php if ($success_message !== "") { ?>
        <p class="admin-message admin-message-success">
            <?php echo htmlspecialchars($success_message); ?>
        </p>
    <?php } ?>

    <div class="admin-stat-grid">
        <article class="admin-stat-card">
            <span>Total Events</span>
            <strong><?php echo $total_events; ?></strong>
        </article>
        <article class="admin-stat-card">
            <span>Total Users</span>
            <strong><?php echo $total_users; ?></strong>
        </article>
        <article class="admin-stat-card">
            <span>Total Registrations</span>
            <strong><?php echo $total_registrations; ?></strong>
        </article>
    </div>

    <div class="admin-action-panel">
        <h2>Event Management</h2>
        <p>Create a new event and make it available to EventHub users.</p>
        <a href="add_event.php" class="btn">Add New Event</a>
    </div>
</section>

<footer>
    <div class="copyright">© 2026 EventHub | Event Management System</div>
</footer>

</body>
</html>
