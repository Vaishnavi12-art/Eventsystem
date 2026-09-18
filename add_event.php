<?php

session_start();
include "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$categories = ["Technology", "Cultural", "Education", "Sports"];
$message = "";
$message_type = "";
$event_name = "";
$category = "";
$event_date = "";
$location = "";
$total_seats = "";
$description = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $event_name = trim($_POST["event_name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $event_date = trim($_POST["event_date"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $total_seats = trim($_POST["total_seats"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $uploaded_image = $_FILES["image"] ?? null;

    $date_object = DateTime::createFromFormat("Y-m-d", $event_date);
    $valid_date = $date_object && $date_object->format("Y-m-d") === $event_date;
    $seat_count = filter_var($total_seats, FILTER_VALIDATE_INT);

    if ($event_name === "" || $location === "" || $description === "") {
        $message = "Please complete all required fields.";
    } elseif (!in_array($category, $categories, true)) {
        $message = "Please select a valid event category.";
    } elseif (!$valid_date) {
        $message = "Please enter a valid event date.";
    } elseif ($seat_count === false || $seat_count <= 0) {
        $message = "Total seats must be greater than 0.";
    } elseif (!$uploaded_image || $uploaded_image["error"] !== UPLOAD_ERR_OK) {
        $message = "Please upload a valid event image.";
    } elseif ($uploaded_image["size"] > 5 * 1024 * 1024) {
        $message = "The event image must be 5 MB or smaller.";
    } else {
        $image_info = @getimagesize($uploaded_image["tmp_name"]);
        $allowed_mimes = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];
        $image_mime = $image_info["mime"] ?? "";

        if (!$image_info || !isset($allowed_mimes[$image_mime])) {
            $message = "Only JPG, JPEG, PNG, and WEBP images are allowed.";
        } else {
            $image_extension = $allowed_mimes[$image_mime];
            $image_filename = bin2hex(random_bytes(12)) . "." . $image_extension;
            $image_path = __DIR__ . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . $image_filename;

            if (!move_uploaded_file($uploaded_image["tmp_name"], $image_path)) {
                $message = "The event image could not be saved.";
            } else {
                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO events
                     (event_name, category, event_date, location, total_seats,
                      remaining_seats, description, image)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "ssssiiss",
                    $event_name,
                    $category,
                    $event_date,
                    $location,
                    $seat_count,
                    $seat_count,
                    $description,
                    $image_filename
                );

                if (mysqli_stmt_execute($stmt)) {
                    header("Location: admin_dashboard.php?success=" . urlencode("Event added successfully."));
                    exit();
                }

                unlink($image_path);
                $message = "The event could not be added. Please try again.";
            }
        }
    }

    if ($message !== "") {
        $message_type = "error";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - EventHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">Event<span>Hub</span></div>
    <ul class="nav-links">
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="index.php">View Site</a></li>
    </ul>
    <div class="nav-actions">
        <span class="admin-username">
            Admin: <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>
        </span>
        <a href="admin_logout.php" class="register">Logout</a>
    </div>
</nav>

<section class="admin-page">
    <div class="section-title">
        <p>EVENT MANAGEMENT</p>
        <h2>Add New Event</h2>
        <span>Create an event for the EventHub community</span>
    </div>

    <?php if ($message !== "") { ?>
        <p class="admin-message admin-message-error">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php } ?>

    <form class="admin-form event-form" method="post" action="add_event.php" enctype="multipart/form-data">
        <label for="event_name">Event Name</label>
        <input id="event_name" type="text" name="event_name" value="<?php echo htmlspecialchars($event_name); ?>" required>

        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="">Select category</option>
            <?php foreach ($categories as $option) { ?>
                <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $category === $option ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($option); ?>
                </option>
            <?php } ?>
        </select>

        <label for="event_date">Event Date</label>
        <input id="event_date" type="date" name="event_date" value="<?php echo htmlspecialchars($event_date); ?>" required>

        <label for="location">Location</label>
        <input id="location" type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" required>

        <label for="total_seats">Total Seats</label>
        <input id="total_seats" type="number" name="total_seats" min="1" value="<?php echo htmlspecialchars($total_seats); ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($description); ?></textarea>

        <label for="image">Event Image</label>
        <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
        <small class="form-help">Accepted formats: JPG, JPEG, PNG, WEBP. Maximum size: 5 MB.</small>

        <div class="admin-form-actions">
            <button type="submit">Create Event</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>

<footer>
    <div class="copyright">© 2026 EventHub | Event Management System</div>
</footer>

</body>
</html>
