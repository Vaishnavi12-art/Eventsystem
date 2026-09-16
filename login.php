<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - EventHub</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar">

    <div class="logo">
        Event<span>Hub</span>
    </div>

    <ul class="nav-links">

<?php
// filepath: c:\xampp\htdocs\eventsystem\db.php

$host = 'localhost';
$dbname = 'eventhub';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}<?php
// filepath: c:\xampp\htdocs\eventsystem\register.php

require_once __DIR__ . '/db.php';

$message = '';
$messageType = '';

$name = '';
$email = '';
$mobile = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $userPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $mobile === '' || $userPassword === '' || $confirmPassword === '') {
        $message = 'Please fill in all fields.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } elseif ($userPassword !== $confirmPassword) {
        $message = 'Password and confirm password do not match.';
        $messageType = 'error';
    } else {
        try {
            $checkUser = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $checkUser->execute([$email]);

            if ($checkUser->fetch()) {
                $message = 'An account with this email already exists.';
                $messageType = 'error';
            } else {
                $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

                $insertUser = $pdo->prepare(
                    'INSERT INTO users (name, email, mobile, password)
                     VALUES (?, ?, ?, ?)'
                );

                $insertUser->execute([
                    $name,
                    $email,
                    $mobile,
                    $hashedPassword
                ]);

                $message = 'Account created successfully.';
                $messageType = 'success';

                $name = '';
                $email = '';
                $mobile = '';
            }
        } catch (PDOException $e) {
            $message = 'Registration failed. Please try again.';
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="register-container">
        <h2>Create Account</h2>

        <?php if ($message !== ''): ?>
            <p class="<?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label for="name">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                maxlength="100"
                value="<?php echo htmlspecialchars($name); ?>"
                required
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="100"
                value="<?php echo htmlspecialchars($email); ?>"
                required
            >

            <label for="mobile">Mobile</label>
            <input
                type="text"
                id="mobile"
                name="mobile"
                maxlength="15"
                value="<?php echo htmlspecialchars($mobile); ?>"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
            >

            <label for="confirm_password">Confirm Password</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                required
            >

            <button type="submit">Register</button>
        </form>
    </div>

</body>
</html><?php
// filepath: c:\xampp\htdocs\eventsystem\register.php

require_once __DIR__ . '/db.php';

$message = '';
$messageType = '';

$name = '';
$email = '';
$mobile = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $userPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $mobile === '' || $userPassword === '' || $confirmPassword === '') {
        $message = 'Please fill in all fields.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } elseif ($userPassword !== $confirmPassword) {
        $message = 'Password and confirm password do not match.';
        $messageType = 'error';
    } else {
        try {
            $checkUser = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $checkUser->execute([$email]);

            if ($checkUser->fetch()) {
                $message = 'An account with this email already exists.';
                $messageType = 'error';
            } else {
                $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

                $insertUser = $pdo->prepare(
                    'INSERT INTO users (name, email, mobile, password)
                     VALUES (?, ?, ?, ?)'
                );

                $insertUser->execute([
                    $name,
                    $email,
                    $mobile,
                    $hashedPassword
                ]);

                $message = 'Account created successfully.';
                $messageType = 'success';

                $name = '';
                $email = '';
                $mobile = '';
            }
        } catch (PDOException $e) {
            $message = 'Registration failed. Please try again.';
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="register-container">
        <h2>Create Account</h2>

        <?php if ($message !== ''): ?>
            <p class="<?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label for="name">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                maxlength="100"
                value="<?php echo htmlspecialchars($name); ?>"
                required
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="100"
                value="<?php echo htmlspecialchars($email); ?>"
                required
            >

            <label for="mobile">Mobile</label>
            <input
                type="text"
                id="mobile"
                name="mobile"
                maxlength="15"
                value="<?php echo htmlspecialchars($mobile); ?>"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
            >

            <label for="confirm_password">Confirm Password</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                required
            >

            <button type="submit">Register</button>
        </form>
    </div>

</body>
</html>CREATE DATABASE eventhub;

USE eventhub;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL
);

    </ul>

</nav>


<!-- LOGIN FORM -->

<section class="contact">

    <div class="section-title">

        <p>WELCOME BACK</p>

        <h2>Login</h2>

        <span>Login to your EventHub account</span>

    </div>


    <form class="contact-form"
          method="post"
          action="login.php"
          style="max-width:500px; margin:auto;">

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


<!-- FOOTER -->

<footer>

    <div class="copyright">

        © 2026 EventHub | Event Management System

    </div>

</footer>

</body>
</html>