<?php
session_start();

include "db.php";

$featured_stmt = mysqli_prepare(
    $conn,
    "SELECT id,
            event_name,
            category,
            event_date,
            location,
            image,
            remaining_seats
     FROM events
     WHERE event_date >= CURDATE()
     ORDER BY event_date ASC
     LIMIT 3"
);

mysqli_stmt_execute($featured_stmt);
$featured_result = mysqli_stmt_get_result($featured_stmt);

$upcoming_stmt = mysqli_prepare(
    $conn,
    "SELECT id, event_name, category, event_date, location, image, remaining_seats, description
     FROM events
     WHERE event_date >= CURDATE()
     ORDER BY event_date ASC, id ASC"
);
mysqli_stmt_execute($upcoming_stmt);
$upcoming_result = mysqli_stmt_get_result($upcoming_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
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

<div class="nav-actions">

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
<!-- ================= HERO ================= -->
<section class="hero" id="home">

    <div class="hero-content">

        <p class="small-title">WELCOME TO EVENTHUB</p>

        <h1>
            Create Memories.<br>
            <span>Manage Events.</span>
        </h1>

        <p>
            Discover exciting events, register easily,
            and enjoy unforgettable experiences.
        </p>

        <div class="hero-buttons">
            <a href="events.php" class="btn">Explore Events</a>
            <a href="#about" class="btn-outline">Learn More</a>
        </div>

    </div>

    <div class="hero-image">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRf61tCs4SZjwmz9TlH_EwLPnhUE4LgENhZnI2_zbm92w&s=10" alt="Event Management">
    </div>

</section>

<!-- ================= FEATURED EVENTS ================= -->

<section class="featured-events" id="featured-events">

    <div class="section-title">
        <p>FEATURED</p>
        <h2>Featured Events</h2>
        <span>Discover the next events happening near you</span>
    </div>

    <div class="featured-event-container">

        <?php while ($event = mysqli_fetch_assoc($featured_result)) { ?>

            <article class="featured-event-card">

                <img
                    src="images/<?php echo htmlspecialchars($event["image"]); ?>"
                    alt="<?php echo htmlspecialchars($event["event_name"]); ?>"
                >

                <div class="featured-event-content">

                    <span class="category">
                        <?php echo htmlspecialchars($event["category"]); ?>
                    </span>

                    <h3>
                        <?php echo htmlspecialchars($event["event_name"]); ?>
                    </h3>

                    <p>
                        📅 <?php echo date("d M Y", strtotime($event["event_date"])); ?>
                    </p>

                    <p>
                        📍 <?php echo htmlspecialchars($event["location"]); ?>
                    </p>

                    <?php if ((int) $event["remaining_seats"] > 0) { ?>
                        <p>
                            🪑 <?php echo (int) $event["remaining_seats"]; ?> seats remaining
                        </p>
                    <?php } else { ?>
                        <p class="featured-sold-out">Sold Out</p>
                    <?php } ?>

                    <a
                        href="event_details.php?id=<?php echo (int) $event["id"]; ?>"
                        class="btn"
                    >
                        View Details
                    </a>

                </div>

            </article>

        <?php } ?>

    </div>

</section>

<!-- ================= EVENTS ================= -->

<section class="events" id="events">

    <div class="section-title">
        <p>DISCOVER</p>
        <h2>Upcoming Events</h2>
        <span>Find the perfect event for you</span>
    </div>


    <div class="event-container">
        <?php if (mysqli_num_rows($upcoming_result) > 0) { ?>
            <?php while ($event = mysqli_fetch_assoc($upcoming_result)) { ?>
                <article class="event-card">
                    <img src="images/<?php echo htmlspecialchars($event["image"]); ?>" alt="<?php echo htmlspecialchars($event["event_name"]); ?>">
                    <div class="event-content">
                        <span class="category"><?php echo htmlspecialchars($event["category"]); ?></span>
                        <h3><?php echo htmlspecialchars($event["event_name"]); ?></h3>
                        <p>📅 <?php echo htmlspecialchars(date("d M Y", strtotime($event["event_date"]))); ?></p>
                        <p>📍 <?php echo htmlspecialchars($event["location"]); ?></p>
                        <?php if ((int) $event["remaining_seats"] > 0) { ?>
                            <p>🪑 <?php echo (int) $event["remaining_seats"]; ?> seats remaining</p>
                        <?php } else { ?>
                            <p class="featured-sold-out">Sold Out</p>
                        <?php } ?>
                        <a href="event_details.php?id=<?php echo (int) $event["id"]; ?>" class="btn">View Details</a>
                    </div>
                </article>
            <?php } ?>
        <?php } else { ?>
            <p class="admin-empty-events">No upcoming events are available.</p>
        <?php } ?>
    </div>

</section>


<!-- ================= ABOUT ================= -->

<section class="about" id="about">

    <div class="about-image">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSUOYJylQ87HdQHieqMOOOC904vTrrAJbJ4Aq2IhwK6VYHJt9FHTVuvQ5n6&s=10" alt="Event Management">
    </div>

    <div class="about-content">

        <p class="section-label">ABOUT US</p>

        <h2>
            We Make Event Management
            <span>Simple & Smart</span>
        </h2>

        <p>
            EventHub is an event management platform designed
            to make organizing and attending events simple.
        </p>

        <p>
            From event registration to digital tickets,
            attendance and certificates, everything can be
            managed from one platform.
        </p>

        <div class="about-features">

            <div>
                <strong>500+</strong>
                <span>Participants</span>
            </div>

            <div>
                <strong>50+</strong>
                <span>Events</span>
            </div>

            <div>
                <strong>20+</strong>
                <span>Organizers</span>
            </div>

        </div>

    </div>

</section>


<!-- ================= CONTACT ================= -->

<section class="contact" id="contact">

    <div class="section-title">

        <p>GET IN TOUCH</p>

        <h2>Contact Us</h2>

        <span>
            Have a question? We would love to hear from you.
        </span>

    </div>


    <div class="contact-container">

        <div class="contact-info">

            <h3>Let's Talk</h3>

            <p>
                Contact us for event information,
                registration help or any other queries.
            </p>

            <div class="contact-item">
                📧 eventhub@gmail.com
            </div>

            <div class="contact-item">
                📞 +91 98765 43210
            </div>

            <div class="contact-item">
                📍 Mumbai, Maharashtra
            </div>

        </div>


        <form class="contact-form">

            <input type="text" placeholder="Your Name">

            <input type="email" placeholder="Your Email">

            <input type="text" placeholder="Subject">

            <textarea rows="5" placeholder="Your Message"></textarea>

            <button type="submit">
                Send Message
            </button>

        </form>

    </div>

</section>


<!-- ================= FOOTER ================= -->

<footer>

    <div class="footer-content">

        <div>
            <h2>Event<span>Hub</span></h2>

            <p>
                Making events simple, smart
                and memorable.
            </p>
        </div>


        <div>
            <h3>Quick Links</h3>

            <a href="#home">Home</a>
            <a href="#events">Events</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </div>


        <div>
            <h3>Follow Us</h3>

            <p>Facebook</p>
            <p>Instagram</p>
            <p>LinkedIn</p>
        </div>

    </div>


    <div class="copyright">

        ©    2026 EventHub | Event Management System

    </div>

</footer>

</body>
</html>