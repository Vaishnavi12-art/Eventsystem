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

        <li>
            <a href="index.php">Home</a>
        </li>

        <li>
            <a href="events.php">Events</a>
        </li>

        <li>
            <a href="about.php">About</a>
        </li>

        <li>
            <a href="contact.php">Contact</a>
        </li>

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