<?php

session_start();
include "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    echo "Event not found.";
    exit();
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM events WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "Event not found.";
    exit();
}

$event = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($event["event_name"]); ?> - EventHub
    </title>

    <link rel="stylesheet" href="css/style.css">

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

    <div>

        <?php if (isset($_SESSION["user_name"])) { ?>

            <span class="username">
                Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
            </span>

            <a href="my_registrations.php" class="my-registrations-link">
                My Registrations
            </a>

            <a href="logout.php" class="register">Logout</a>

        <?php } else { ?>

            <a href="login.php" class="login">Login</a>
            <a href="register.php" class="register">Register</a>

        <?php } ?>

    </div>

</nav>


<section class="contact">

    <div class="section-title">

        <p><?php echo htmlspecialchars($event["category"]); ?></p>

        <h2>
            <?php echo htmlspecialchars($event["event_name"]); ?>
        </h2>

        <span>Event Details</span>

    </div>


    <div class="event-details">

        <img
            src="images/<?php echo htmlspecialchars($event["image"]); ?>"
            alt="<?php echo htmlspecialchars($event["event_name"]); ?>"
        >

        <div class="event-info">

            <h3>
                <?php echo htmlspecialchars($event["event_name"]); ?>
            </h3>

            <p>
                📅 <strong>Date:</strong>
                <?php echo date("d M Y", strtotime($event["event_date"])); ?>
            </p>

            <p>
                📍 <strong>Location:</strong>
                <?php echo htmlspecialchars($event["location"]); ?>
            </p>

            <p>
                🎫 <strong>Total Seats:</strong>
                <?php echo $event["total_seats"]; ?>
            </p>

            <p>
                🪑 <strong>Seats Remaining:</strong>
                <?php echo $event["remaining_seats"]; ?>
            </p>

            <p>
                <?php echo htmlspecialchars($event["description"]); ?>
            </p>

            <?php if ((int) $event["remaining_seats"] > 0) { ?>

                <a href="register_event.php?id=<?php echo (int) $event['id']; ?>" class="btn">
                    Register for Event
                </a>

            <?php } else { ?>

                <span class="btn sold-out">Sold Out</span>

            <?php } ?>

        </div>

    </div>

</section>


<footer>

    <div class="copyright">
        © 2026 EventHub | Event Management System
    </div>

</footer>

</body>
</html>