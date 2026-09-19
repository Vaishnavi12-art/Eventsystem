<?php

session_start();
include "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$categories = ["Technology", "Cultural", "Education", "Sports"];
$event_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $event_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
}

if (!$event_id || $event_id <= 0) {
    header("Location: admin_dashboard.php?error=" . urlencode("Invalid event ID."));
    exit();
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, event_name, category, event_date, location, total_seats,
            remaining_seats, description, image
     FROM events
     WHERE id = ?
     LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$event = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$event) {
    header("Location: admin_dashboard.php?error=" . urlencode("Event not found."));
    exit();
}

$message = "";
$event_name = $event["event_name"];
$category = $event["category"];
$event_date = $event["event_date"];
$location = $event["location"];
$total_seats = (string) $event["total_seats"];
$remaining_seats = (string) $event["remaining_seats"];
$description = $event["description"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $event_name = trim($_POST["event_name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $event_date = trim($_POST["event_date"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $total_seats = trim($_POST["total_seats"] ?? "");
    $remaining_seats = trim($_POST["remaining_seats"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $date_object = DateTime::createFromFormat("Y-m-d", $event_date);
    $valid_date = $date_object && $date_object->format("Y-m-d") === $event_date;
    $total_seat_count = filter_var($total_seats, FILTER_VALIDATE_INT);
    $remaining_seat_count = filter_var($remaining_seats, FILTER_VALIDATE_INT);
    $uploaded_image = $_FILES["image"] ?? null;

    if ($event_name === "" || $location === "" || $description === "") {
        $message = "Please complete all required fields.";
    } elseif (!in_array($category, $categories, true)) {
        $message = "Please select a valid event category.";
    } elseif (!$valid_date) {
        $message = "Please enter a valid event date.";
    } elseif ($total_seat_count === false || $total_seat_count <= 0 || $remaining_seat_count === false || $remaining_seat_count < 0 || $remaining_seat_count > $total_seat_count) {
        $message = "Please enter valid seat totals and availability.";
    } else {
        $registration_stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM registrations WHERE event_id = ?");
        mysqli_stmt_bind_param($registration_stmt, "i", $event_id);
        mysqli_stmt_execute($registration_stmt);
        $registration_count = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($registration_stmt))["total"];

        if ($total_seat_count < $registration_count || $remaining_seat_count > $total_seat_count - $registration_count) {
            $message = "Seats cannot be reduced below the current registrations.";
        } else {
            $new_image_filename = $event["image"];
            $new_image_path = "";
            $has_new_image = $uploaded_image && $uploaded_image["error"] !== UPLOAD_ERR_NO_FILE;

            if ($has_new_image) {
                if ($uploaded_image["error"] !== UPLOAD_ERR_OK || $uploaded_image["size"] > 5 * 1024 * 1024) {
                    $message = "The event image must be a valid file no larger than 5 MB.";
                } else {
                    $image_info = @getimagesize($uploaded_image["tmp_name"]);
                    $allowed_mimes = ["image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp"];
                    $image_mime = $image_info["mime"] ?? "";

                    if (!$image_info || !isset($allowed_mimes[$image_mime])) {
                        $message = "Only JPG, JPEG, PNG, and WEBP images are allowed.";
                    } else {
                        $new_image_filename = bin2hex(random_bytes(12)) . "." . $allowed_mimes[$image_mime];
                        $new_image_path = __DIR__ . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . $new_image_filename;
                        if (!move_uploaded_file($uploaded_image["tmp_name"], $new_image_path)) {
                            $message = "The event image could not be saved.";
                        }
                    }
                }
            }

            if ($message === "") {
                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE events
                     SET event_name = ?, category = ?, event_date = ?, location = ?,
                         total_seats = ?, remaining_seats = ?, description = ?, image = ?
                     WHERE id = ?"
                );
                mysqli_stmt_bind_param(
                    $stmt,
                    "ssssiiisi",
                    $event_name,
                    $category,
                    $event_date,
                    $location,
                    $total_seat_count,
                    $remaining_seat_count,
                    $description,
                    $new_image_filename,
                    $event_id
                );

                if (mysqli_stmt_execute($stmt)) {
                    if ($new_image_path !== "" && $event["image"] !== $new_image_filename) {
                        $old_image_path = __DIR__ . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . basename($event["image"]);
                        if (is_file($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                    header("Location: admin_dashboard.php?success=" . urlencode("Event updated successfully."));
                    exit();
                }

                if ($new_image_path !== "" && is_file($new_image_path)) {
                    unlink($new_image_path);
                }
                $message = "The event could not be updated. Please try again.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - EventHub</title>
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
        <span class="admin-username">Admin: <?php echo htmlspecialchars($_SESSION["admin_username"] ?? ""); ?></span>
        <a href="admin_logout.php" class="register">Logout</a>
    </div>
</nav>
<section class="admin-page">
    <div class="section-title">
        <p>EVENT MANAGEMENT</p>
        <h2>Edit Event</h2>
        <span>Update the event information</span>
    </div>
    <?php if ($message !== "") { ?>
        <p class="admin-message admin-message-error"><?php echo htmlspecialchars($message); ?></p>
    <?php } ?>
    <form class="admin-form event-form" method="post" action="edit_event.php?id=<?php echo (int) $event_id; ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo (int) $event_id; ?>">
        <label for="event_name">Event Name</label>
        <input id="event_name" type="text" name="event_name" value="<?php echo htmlspecialchars($event_name); ?>" required>
        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="">Select category</option>
            <?php foreach ($categories as $option) { ?>
                <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $category === $option ? "selected" : ""; ?>><?php echo htmlspecialchars($option); ?></option>
            <?php } ?>
        </select>
        <label for="event_date">Event Date</label>
        <input id="event_date" type="date" name="event_date" value="<?php echo htmlspecialchars($event_date); ?>" required>
        <label for="location">Location</label>
        <input id="location" type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
        <label for="total_seats">Total Seats</label>
        <input id="total_seats" type="number" name="total_seats" min="1" value="<?php echo htmlspecialchars($total_seats); ?>" required>
        <label for="remaining_seats">Remaining Seats</label>
        <input id="remaining_seats" type="number" name="remaining_seats" min="0" value="<?php echo htmlspecialchars($remaining_seats); ?>" required>
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($description); ?></textarea>
        <label>Current Image</label>
        <div class="current-event-image">
            <img src="images/<?php echo htmlspecialchars($event["image"]); ?>" alt="<?php echo htmlspecialchars($event["event_name"]); ?>">
        </div>
        <label for="image">Replace Image</label>
        <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        <small class="form-help">Leave blank to keep the current image. Maximum size: 5 MB.</small>
        <div class="admin-form-actions">
            <button type="submit">Update Event</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>
<footer><div class="copyright">© 2026 EventHub | Event Management System</div></footer>
</body>
</html>