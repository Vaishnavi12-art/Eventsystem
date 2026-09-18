<?php

function show_registration_result($page_title, $message, $message_type, $buttons, $event = null)
{
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($page_title); ?> - EventHub</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    <nav class="navbar">
        <div class="logo">Event<span>Hub</span></div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="index.php#about">About</a></li>
            <li><a href="index.php#contact">Contact</a></li>
        </ul>
        <div>
            <span class="username">
                Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
            </span>
            <a href="my_registrations.php" class="my-registrations-link">
                My Registrations
            </a>
            <a href="logout.php" class="register">Logout</a>
        </div>
    </nav>

    <section class="contact registration-message-page">
        <div class="registration-message <?php echo htmlspecialchars($message_type); ?>">
            <div class="registration-message-icon" aria-hidden="true">
                <?php echo htmlspecialchars($message_type === "success" ? "✓" : "!"); ?>
            </div>
            <h2><?php echo htmlspecialchars($page_title); ?></h2>
            <p class="message-box"><?php echo htmlspecialchars($message); ?></p>

            <?php if ($event !== null) { ?>
                <div class="registration-event-summary">
                    <strong><?php echo htmlspecialchars($event["event_name"]); ?></strong>
                    <span>
                        <?php echo htmlspecialchars(date("d M Y", strtotime($event["event_date"]))); ?>
                    </span>
                    <span><?php echo htmlspecialchars($event["location"]); ?></span>
                </div>
            <?php } ?>

            <div class="registration-message-actions">
                <?php foreach ($buttons as $button) { ?>
                    <a
                        href="<?php echo htmlspecialchars($button["url"]); ?>"
                        class="btn <?php echo htmlspecialchars($button["class"] ?? ""); ?>"
                    >
                        <?php echo htmlspecialchars($button["label"]); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="copyright">© 2026 EventHub | Event Management System</div>
    </footer>
    </body>
    </html>
    <?php
}
