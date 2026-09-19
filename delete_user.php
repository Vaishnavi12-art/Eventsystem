<?php

session_start();
include "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: manage_users.php?error=" . urlencode("Invalid delete request."));
    exit();
}

$user_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$search = trim($_POST["search"] ?? "");
$redirect_query = $search === "" ? "" : "&search=" . urlencode($search);

if (!$user_id || $user_id <= 0) {
    header("Location: manage_users.php?error=" . urlencode("Invalid user ID.") . $redirect_query);
    exit();
}

mysqli_begin_transaction($conn);

$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE id = ? LIMIT 1 FOR UPDATE");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user) {
    mysqli_rollback($conn);
    header("Location: manage_users.php?error=" . urlencode("User not found.") . $redirect_query);
    exit();
}

$stmt = mysqli_prepare($conn, "DELETE FROM registrations WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
$registrations_deleted = mysqli_stmt_execute($stmt);

$user_deleted = false;
if ($registrations_deleted) {
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    $user_deleted = mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) === 1;
}

if (!$user_deleted) {
    mysqli_rollback($conn);
    header("Location: manage_users.php?error=" . urlencode("The user could not be deleted. Please try again.") . $redirect_query);
    exit();
}

mysqli_commit($conn);
header("Location: manage_users.php?success=" . urlencode("User deleted successfully.") . $redirect_query);
exit();

?>