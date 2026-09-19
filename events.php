<?php

session_start();
include "db.php";

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "All");
$categories = ["Technology", "Cultural", "Education", "Sports"];

if ($category !== "All" && !in_array($category, $categories, true)) {
    $category = "All";
}

$search_pattern = "%" . $search . "%";
$stmt = mysqli_prepare(
    $conn,
    "SELECT id, event_name, category, event_date, location, remaining_seats, image
     FROM events
     WHERE (? = '' OR event_name LIKE ?)
       AND (? = 'All' OR category = ?)
     ORDER BY event_date ASC, id ASC"
);
mysqli_stmt_bind_param($stmt, "ssss", $search, $search_pattern, $category, $category);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Events - EventHub</title>

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

        <p>DISCOVER EVENTS</p>

        <h2>Upcoming Events</h2>

        <span>Find an event and create unforgettable memories</span>

    </div>


    <!-- Search and Filter -->

    <form method="GET" class="event-filter">

        <input
            type="text"
            name="search"
            placeholder="Search event..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <select name="category">

            <option value="All">All Categories</option>

            <option value="Technology"
                <?php if ($category == "Technology") echo "selected"; ?>>
                Technology
            </option>

            <option value="Cultural"
                <?php if ($category == "Cultural") echo "selected"; ?>>
                Cultural
            </option>

            <option value="Education"
                <?php if ($category == "Education") echo "selected"; ?>>
                Education
            </option>

            <option value="Sports"
                <?php if ($category == "Sports") echo "selected"; ?>>
                Sports
            </option>

        </select>

        <button type="submit">Search</button>

    </form>


    <!-- Event Cards -->

    <div class="event-container">

        <?php while ($event = mysqli_fetch_assoc($result)) { ?>

            <div class="event-card">

                <img
                    src="images/<?php echo htmlspecialchars($event["image"]); ?>"
                    alt="<?php echo htmlspecialchars($event["event_name"]); ?>"
                >

                <div class="event-content">

                    <p class="event-category">
                        <?php echo htmlspecialchars($event["category"]); ?>
                    </p>

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
                        <?php echo $event["remaining_seats"]; ?>
                        seats remaining
                    </p>

                    <a
                        href="event_details.php?id=<?php echo (int) $event["id"]; ?>"
                        class="btn"
                    >
                        View Details
                    </a>

                </div>

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