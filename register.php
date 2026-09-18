<?php

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password != $confirm_password) {

        $message = "Passwords do not match!";

    } else {

        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, mobile, password)
                VALUES ('$name', '$email', '$mobile', '$password')";

        if (mysqli_query($conn, $sql)) {
            $message = "Registration successful!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EventHub</title>
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

    <div class="nav-buttons">
        <a href="login.php" class="login">Login</a>
    </div>

</nav>

<section class="contact">

    <div class="section-title">
        <p>JOIN EVENTHUB</p>
        <h2>Create Account</h2>
        <span>Register to participate in events</span>
    </div>

    <?php if ($message != "") { ?>
        <p style="text-align:center;">
            <?php echo $message; ?>
        </p>
    <?php } ?>

    <form class="contact-form"
          method="post"
          action="register.php"
          style="max-width:500px; margin:auto;">

        <input type="text" name="name" placeholder="Full Name" required>

        <input type="email" name="email" placeholder="Email Address" required>

        <input type="tel" name="mobile" placeholder="Mobile Number" required>

        <input type="password" name="password" placeholder="Password" required>

        <input type="password"
               name="confirm_password"
               placeholder="Confirm Password"
               required>

        <button type="submit">Create Account</button>

        <p style="text-align:center;">
            Already have an account?
            <a href="login.php">Login</a>
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
