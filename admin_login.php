<?php

session_start();
include "db.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: admin_dashboard.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $message = "Please enter both email and password.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, email, password
             FROM admins
             WHERE email = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);

        if ($admin && password_verify($password, $admin["password"])) {
            session_regenerate_id(true);
            $_SESSION["admin_id"] = (int) $admin["id"];
            $_SESSION["admin_username"] = $admin["name"];

            header("Location: admin_dashboard.php");
            exit();
        }

        $message = "Invalid admin email or password.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - EventHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">Event<span>Hub</span></div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="events.php">Events</a></li>
    </ul>
</nav>

<section class="contact admin-page">
    <div class="section-title">
        <p>EVENTHUB ADMIN</p>
        <h2>Admin Login</h2>
        <span>Sign in to manage EventHub events</span>
    </div>

    <?php if ($message !== "") { ?>
        <p class="admin-message admin-message-error">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php } ?>

    <form class="contact-form admin-form" method="post" action="admin_login.php">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" required autocomplete="email">

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">

        <button type="submit">Login</button>
    </form>
</section>

<footer>
    <div class="copyright">© 2026 EventHub | Event Management System</div>
</footer>

</body>
</html>
