<?php

session_start();
include "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$search = trim($_GET["search"] ?? "");
$search_pattern = "%" . $search . "%";

$stmt = mysqli_prepare(
    $conn,
    "SELECT r.id AS registration_id,
            u.name AS user_name,
            u.email AS user_email,
            e.event_name,
            e.category,
            e.event_date,
            e.location,
            r.registration_date
     FROM registrations r
     JOIN users u ON r.user_id = u.id
     JOIN events e ON r.event_id = e.id
     WHERE u.name LIKE ? OR u.email LIKE ? OR e.event_name LIKE ?
     ORDER BY r.registration_date DESC, r.id DESC"
);
mysqli_stmt_bind_param($stmt, "sss", $search_pattern, $search_pattern, $search_pattern);
mysqli_stmt_execute($stmt);
$registrations_result = mysqli_stmt_get_result($stmt);

$success_message = trim($_GET["success"] ?? "");
$error_message = trim($_GET["error"] ?? "");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Registrations - EventHub</title>
    <link rel="stylesheet" href="css/style.css?v=20260919">
</head>
<body>

<nav class="navbar">
    <div class="logo">Event<span>Hub</span></div>
    <ul class="nav-links">
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="index.php">View Site</a></li>
    </ul>
    <div class="nav-actions">
        <span class="admin-username">Admin: <?php echo htmlspecialchars($_SESSION["admin_username"] ?? ""); ?></span>
        <a href="admin_logout.php" class="register">Logout</a>
    </div>
</nav>

<section class="admin-page admin-registrations-page">
    <div class="admin-heading">
        <div>
            <p class="section-label">EVENTHUB ADMIN</p>
            <h1>Manage Registrations</h1>
            <p>Review event registrations and cancel them when needed.</p>
        </div>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if ($success_message !== "") { ?>
        <p class="admin-message admin-message-success"><?php echo htmlspecialchars($success_message); ?></p>
    <?php } ?>

    <?php if ($error_message !== "") { ?>
        <p class="admin-message admin-message-error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php } ?>

    <form method="get" action="manage_registrations.php" class="admin-search-form">
        <label for="registration-search">Search registrations</label>
        <input id="registration-search" class="admin-search-input" type="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by user name, email, or event">
        <button type="submit" class="admin-search-button">Search</button>
        <?php if ($search !== "") { ?>
            <a href="manage_registrations.php" class="admin-clear-search">Clear</a>
        <?php } ?>
    </form>

    <div class="admin-events-panel admin-registrations-panel">
        <div class="admin-events-heading">
            <h2>All Registrations</h2>
            <span><?php echo mysqli_num_rows($registrations_result); ?> result<?php echo mysqli_num_rows($registrations_result) === 1 ? "" : "s"; ?></span>
        </div>

        <?php if (mysqli_num_rows($registrations_result) > 0) { ?>
            <div class="admin-events-table-wrapper">
                <table class="admin-events-table admin-registrations-table">
                    <thead>
                        <tr>
                            <th>Registration ID</th>
                            <th>User Name</th>
                            <th>User Email</th>
                            <th>Event Name</th>
                            <th>Category</th>
                            <th>Event Date</th>
                            <th>Location</th>
                            <th>Registration Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($registration = mysqli_fetch_assoc($registrations_result)) { ?>
                            <tr>
                                <td data-label="Registration ID"><?php echo (int) $registration["registration_id"]; ?></td>
                                <td data-label="User Name"><?php echo htmlspecialchars($registration["user_name"]); ?></td>
                                <td data-label="User Email"><?php echo htmlspecialchars($registration["user_email"]); ?></td>
                                <td data-label="Event Name"><?php echo htmlspecialchars($registration["event_name"]); ?></td>
                                <td data-label="Category"><?php echo htmlspecialchars($registration["category"]); ?></td>
                                <td data-label="Event Date"><?php echo htmlspecialchars(date("d M Y", strtotime($registration["event_date"]))); ?></td>
                                <td data-label="Location"><?php echo htmlspecialchars($registration["location"]); ?></td>
                                <td data-label="Registration Date"><?php echo htmlspecialchars(date("d M Y H:i", strtotime($registration["registration_date"]))); ?></td>
                                <td data-label="Action">
                                    <form method="post" action="admin_delete_registration.php">
                                        <input type="hidden" name="id" value="<?php echo (int) $registration["registration_id"]; ?>">
                                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                        <button type="submit" class="admin-delete-btn">Cancel Registration</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="admin-empty-events">No registrations matched your search.</p>
        <?php } ?>
    </div>
</section>

<footer>
    <div class="copyright">© 2026 EventHub | Event Management System</div>
</footer>

</body>
</html>