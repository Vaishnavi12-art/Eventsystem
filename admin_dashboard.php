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
$error_message = isset($_GET["error"]) ? trim($_GET["error"]) : "";

$available_seats_stmt = mysqli_prepare(
    $conn,
    "SELECT COALESCE(SUM(remaining_seats), 0) AS total_available_seats
     FROM events"
);
mysqli_stmt_execute($available_seats_stmt);
$available_seats_row = mysqli_fetch_assoc(mysqli_stmt_get_result($available_seats_stmt));
$total_available_seats = (int) $available_seats_row["total_available_seats"];

$events_stmt = mysqli_prepare(
    $conn,
    "SELECT id, event_name, category, event_date, total_seats, remaining_seats
     FROM events
     ORDER BY event_date ASC, id ASC"
);
mysqli_stmt_execute($events_stmt);
$events_result = mysqli_stmt_get_result($events_stmt);
$events = [];
while ($event = mysqli_fetch_assoc($events_result)) {
    $events[] = $event;
}

$today = date("Y-m-d");
$upcoming_events = array_filter(
    $events,
    function ($event) use ($today) {
        return $event["event_date"] >= $today;
    }
);

$recent_registrations_stmt = mysqli_prepare(
    $conn,
    "SELECT u.name AS user_name,
            u.email AS user_email,
            e.event_name,
            r.registration_date
     FROM registrations r
     JOIN users u ON r.user_id = u.id
     JOIN events e ON r.event_id = e.id
     ORDER BY r.registration_date DESC, r.id DESC
     LIMIT 5"
);
mysqli_stmt_execute($recent_registrations_stmt);
$recent_registrations_result = mysqli_stmt_get_result($recent_registrations_stmt);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EventHub</title>
    <link rel="stylesheet" href="css/style.css?v=20260919">
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
            Admin: <?php echo htmlspecialchars($_SESSION["admin_username"] ?? ""); ?>
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
    </div>

    <?php if ($success_message !== "") { ?>
        <p class="admin-message admin-message-success">
            <?php echo htmlspecialchars($success_message); ?>
        </p>
    <?php } ?>

    <?php if ($error_message !== "") { ?>
        <p class="admin-message admin-message-error">
            <?php echo htmlspecialchars($error_message); ?>
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
        <article class="admin-stat-card">
            <span>Total Available Seats</span>
            <strong><?php echo $total_available_seats; ?></strong>
        </article>
    </div>

    <div class="admin-action-panel">
        <h2>Quick Actions</h2>
        <p>Manage events, users, and registrations from one place.</p>
        <div class="admin-action-links">
            <a href="add_event.php" class="btn">Add New Event</a>
            <a href="admin_dashboard.php" class="btn btn-secondary">Manage Events</a>
            <a href="manage_users.php" class="btn btn-secondary">Manage Users</a>
            <a href="manage_registrations.php" class="btn btn-secondary">Manage Registrations</a>
        </div>
    </div>

    <div class="admin-dashboard-grid">
        <div class="admin-events-panel admin-dashboard-panel">
            <div class="admin-events-heading">
                <h2>Upcoming Events</h2>
                <span><?php echo count($upcoming_events); ?> event<?php echo count($upcoming_events) === 1 ? "" : "s"; ?></span>
            </div>

            <?php if (count($upcoming_events) > 0) { ?>
                <div class="upcoming-events-list">
                    <?php foreach ($upcoming_events as $event) { ?>
                        <article class="upcoming-event-item">
                            <div>
                                <h3><?php echo htmlspecialchars($event["event_name"]); ?></h3>
                                <span class="event-category"><?php echo htmlspecialchars($event["category"]); ?></span>
                            </div>
                            <div class="upcoming-event-details">
                                <span><?php echo htmlspecialchars(date("d M Y", strtotime($event["event_date"]))); ?></span>
                                <span><?php echo htmlspecialchars($event["location"]); ?></span>
                                <strong><?php echo (int) $event["remaining_seats"]; ?> seats remaining</strong>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p class="admin-empty-events">No upcoming events found.</p>
            <?php } ?>
        </div>

        <div class="admin-events-panel admin-dashboard-panel">
            <div class="admin-events-heading">
                <h2>Recent Registrations</h2>
                <span>Latest 5</span>
            </div>

            <?php if (mysqli_num_rows($recent_registrations_result) > 0) { ?>
                <div class="recent-registrations-list">
                    <?php while ($registration = mysqli_fetch_assoc($recent_registrations_result)) { ?>
                        <article class="recent-registration-item">
                            <div>
                                <h3><?php echo htmlspecialchars($registration["user_name"]); ?></h3>
                                <span><?php echo htmlspecialchars($registration["user_email"]); ?></span>
                            </div>
                            <div class="recent-registration-details">
                                <strong><?php echo htmlspecialchars($registration["event_name"]); ?></strong>
                                <span><?php echo htmlspecialchars(date("d M Y H:i", strtotime($registration["registration_date"]))); ?></span>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p class="admin-empty-events">No registrations found.</p>
            <?php } ?>
        </div>
    </div>

    <div class="admin-events-panel admin-statistics-panel">
        <div class="admin-events-heading">
            <h2>Event Registration Statistics</h2>
            <span>Seat usage by event</span>
        </div>

        <?php if (count($events) > 0) { ?>
            <div class="event-statistics-list">
                <?php foreach ($events as $event) {
                    $total_seats = (int) $event["total_seats"];
                    $remaining_seats = (int) $event["remaining_seats"];
                    $registered_seats = max(0, $total_seats - $remaining_seats);
                    $registration_percentage = $total_seats > 0
                        ? ($registered_seats / $total_seats) * 100
                        : 0;
                    $registration_percentage = min(100, max(0, $registration_percentage));
                ?>
                    <article class="event-statistic-item">
                        <div class="event-statistic-heading">
                            <div>
                                <h3><?php echo htmlspecialchars($event["event_name"]); ?></h3>
                                <span><?php echo htmlspecialchars($event["category"]); ?></span>
                            </div>
                            <strong><?php echo number_format($registration_percentage, 1); ?>%</strong>
                        </div>
                        <div class="event-statistic-values">
                            <span>Registered: <?php echo $registered_seats; ?> / <?php echo $total_seats; ?></span>
                            <span>Remaining: <?php echo $remaining_seats; ?></span>
                        </div>
                        <div class="event-progress-track" role="progressbar" aria-label="<?php echo htmlspecialchars($event["event_name"]); ?> registration percentage" aria-valuenow="<?php echo number_format($registration_percentage, 1, ".", ""); ?>" aria-valuemin="0" aria-valuemax="100">
                            <span class="event-progress-bar" style="width: <?php echo number_format($registration_percentage, 1, ".", ""); ?>%;"></span>
                        </div>
                    </article>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p class="admin-empty-events">No event statistics available.</p>
        <?php } ?>
    </div>

    <div class="admin-events-panel">
        <div class="admin-events-heading">
            <h2>All Events</h2>
            <span><?php echo $total_events; ?> event<?php echo $total_events === 1 ? "" : "s"; ?></span>
        </div>

        <?php if (count($events) > 0) { ?>
            <div class="admin-events-table-wrapper">
                <table class="admin-events-table">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Seats</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event) { ?>
                            <tr>
                                <td data-label="Event Name"><?php echo htmlspecialchars($event["event_name"]); ?></td>
                                <td data-label="Date"><?php echo htmlspecialchars(date("d M Y", strtotime($event["event_date"]))); ?></td>
                                <td data-label="Category"><?php echo htmlspecialchars($event["category"]); ?></td>
                                <td data-label="Seats"><?php echo (int) $event["remaining_seats"]; ?> / <?php echo (int) $event["total_seats"]; ?></td>
                                <td data-label="Edit">
                                    <a href="edit_event.php?id=<?php echo (int) $event["id"]; ?>" class="admin-edit-btn">Edit</a>
                                </td>
                                <td data-label="Delete">
                                    <form method="post" action="delete_event.php" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                        <input type="hidden" name="id" value="<?php echo (int) $event["id"]; ?>">
                                        <button type="submit" class="admin-delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="admin-empty-events">No events have been created yet.</p>
        <?php } ?>
    </div>
</section>

<footer>
    <div class="copyright">© 2026 EventHub | Event Management System</div>
</footer>

</body>
</html>
