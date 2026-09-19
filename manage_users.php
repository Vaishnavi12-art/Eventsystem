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
    "SELECT id, name, email, mobile
     FROM users
     WHERE name LIKE ? OR email LIKE ?
     ORDER BY id DESC"
);
mysqli_stmt_bind_param($stmt, "ss", $search_pattern, $search_pattern);
mysqli_stmt_execute($stmt);
$users_result = mysqli_stmt_get_result($stmt);

$success_message = trim($_GET["success"] ?? "");
$error_message = trim($_GET["error"] ?? "");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - EventHub</title>
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

<section class="admin-page admin-users-page">
    <div class="admin-heading">
        <div>
            <p class="section-label">EVENTHUB ADMIN</p>
            <h1>Manage Users</h1>
            <p>View registered users and manage their accounts.</p>
        </div>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if ($success_message !== "") { ?>
        <p class="admin-message admin-message-success"><?php echo htmlspecialchars($success_message); ?></p>
    <?php } ?>

    <?php if ($error_message !== "") { ?>
        <p class="admin-message admin-message-error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php } ?>

    <form method="get" action="manage_users.php" class="admin-search-form">
        <label for="user-search">Search users</label>
        <input id="user-search" class="admin-search-input" type="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or email">
        <button type="submit" class="admin-search-button">Search</button>
        <?php if ($search !== "") { ?>
            <a href="manage_users.php" class="admin-clear-search">Clear</a>
        <?php } ?>
    </form>

    <div class="admin-events-panel admin-users-panel">
        <div class="admin-events-heading">
            <h2>Registered Users</h2>
            <span><?php echo mysqli_num_rows($users_result); ?> result<?php echo mysqli_num_rows($users_result) === 1 ? "" : "s"; ?></span>
        </div>

        <?php if (mysqli_num_rows($users_result) > 0) { ?>
            <div class="admin-events-table-wrapper">
                <table class="admin-events-table admin-users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($users_result)) { ?>
                            <tr>
                                <td data-label="ID"><?php echo (int) $user["id"]; ?></td>
                                <td data-label="Name"><?php echo htmlspecialchars($user["name"]); ?></td>
                                <td data-label="Email"><?php echo htmlspecialchars($user["email"]); ?></td>
                                <td data-label="Mobile"><?php echo htmlspecialchars($user["mobile"]); ?></td>
                                <td data-label="Delete">
                                    <form method="post" action="delete_user.php" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="id" value="<?php echo (int) $user["id"]; ?>">
                                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                        <button type="submit" class="admin-delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="admin-empty-events">No users matched your search.</p>
        <?php } ?>
    </div>
</section>

<footer>
    <div class="copyright">© 2026 EventHub | Event Management System</div>
</footer>

</body>
</html>