<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, password FROM users WHERE email = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_name"] = $user["name"];

            header("Location: index.php");
            exit();

        } else {
            $message = "Invalid email or password.";
        }

    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - EventHub</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

    <div class="logo">
        Event<span>Hub</span>
    </div>

    <ul class="nav-links">

        <li><a href="index.php">Home</a></li>

        <li><a href="events.php">Events</a></li>

        <li><a href="index.php#about">About</a></li>

        <li><a href="index.php#contact">Contact</a></li>

    </ul>

</nav>


<section class="contact">

    <div class="section-title">

        <p>WELCOME BACK</p>

        <h2>Login</h2>

        <span>Login to your EventHub account</span>

    </div>


    <?php if ($message != "") { ?>

        <p style="text-align:center;">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>


    <form
        class="contact-form"
        method="post"
        action="login.php"
        style="max-width:500px; margin:auto;"
    >

        <input
            type="email"
            name="email"
            placeholder="Email Address"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >

        <button type="submit">
            Login
        </button>

        <p style="text-align:center;">

            Don't have an account?

            <a href="register.php">
                Register
            </a>

        </p>

    </form>

</section>


<footer>

    <div class="copyright">
        © 2026 EventHub | Event Management System
    </div>

</footer>

</body>

</html>