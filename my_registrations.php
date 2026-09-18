<?php

session_start();
include "db.php";

if (!isset($_SESSION["user_name"])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION["user_name"];
$user_id = isset($_SESSION["user_id"]) ? (int) $_SESSION["user_id"] : 0;

if ($user_id > 0) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT r.id AS registration_id,
            r.registration_date,
                e.id,
                e.event_name,
                e.category,
                e.event_date,
                e.location,
                e.image,
                e.remaining_seats
         FROM registrations r
         JOIN events e ON r.event_id = e.id
         WHERE r.user_id = ?
         ORDER BY r.registration_date DESC"
    );

    mysqli_stmt_bind_param($stmt, "i", $user_id);
} else {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT r.id AS registration_id,
            r.registration_date,
                e.id,
                e.event_name,
                e.category,
                e.event_date,
                e.location,
                e.image,
                e.remaining_seats
         FROM registrations r
         JOIN users u ON r.user_id = u.id
         JOIN events e ON r.event_id = e.id
         WHERE u.name = ?
         ORDER BY r.registration_date DESC"
    );

    mysqli_stmt_bind_param($stmt, "s", $user_name);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$registration_message = $_SESSION["registration_message"] ?? "";
unset($_SESSION["registration_message"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Registrations - EventHub</title>

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

        <span class="username">
            Welcome, <?php echo htmlspecialchars($user_name); ?>
        </span>

        <a href="my_registrations.php" class="my-registrations-link">
            My Registrations
        </a>

        <a href="logout.php" class="register">Logout</a>

    </div>

</nav>


<section class="contact registrations-page">

    <div class="section-title">

        <p>MY EVENTS</p>

        <h2>My Registrations</h2>

        <span>Events you have registered for</span>

    </div>

    <?php if ($registration_message != "") { ?>

        <p class="registration-notice">
            <?php echo htmlspecialchars($registration_message); ?>
        </p>

    <?php } ?>


    <div class="registration-grid">

        <?php if (mysqli_num_rows($result) > 0) { ?>

            <?php while ($event = mysqli_fetch_assoc($result)) { ?>

                <article class="registration-card">

                    <img
                        src="images/<?php echo htmlspecialchars($event["image"]); ?>"
                        alt="<?php echo htmlspecialchars($event["event_name"]); ?>"
                    >

                    <div class="registration-info">

                        <span class="category">
                            <?php echo htmlspecialchars($event["category"]); ?>
                        </span>

                        <h3>
                            <?php echo htmlspecialchars($event["event_name"]); ?>
                        </h3>

                        <p>
                            📅
                            <?php echo date("d M Y", strtotime($event["event_date"])); ?>
                        </p>

                        <p>
                            📍
                            <?php echo htmlspecialchars($event["location"]); ?>
                        </p>

                        <p>
                            🪑
                            <?php echo (int) $event["remaining_seats"]; ?>
                            seats remaining
                        </p>

                        <p>
                            📝 Registered:
                            <?php echo date("d M Y", strtotime($event["registration_date"])); ?>
                        </p>

                        <div class="registration-actions">

                            <a
                                href="event_details.php?id=<?php echo (int) $event["id"]; ?>"
                                class="btn"
                            >
                                View Event
                            </a>

                            <form
                                method="post"
                                action="remove_registration.php"
                                class="remove-registration-form"
                                onsubmit="return confirm('Are you sure you want to remove this registration?');"
                            >
                                <input
                                    type="hidden"
                                    name="registration_id"
                                    value="<?php echo (int) $event["registration_id"]; ?>"
                                >
                                <button type="submit" class="remove-registration-button">
                                    Remove Registration
                                </button>
                            </form>

                        </div>

                    </div>

                </article>

            <?php } ?>

        <?php } else { ?>

            <div class="empty-registrations">

                <h3>No Registrations Found</h3>

                <p>You have not registered for any events yet.</p>

                <a href="events.php" class="btn">
                    Explore Events
                </a>

            </div>

        <?php } ?>

    </div>

</section>


<footer>

    <div class="copyright">
        © 2026 EventHub | Event Management System
    </div>

</footer>

</body>

</html>