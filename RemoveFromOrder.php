<?php
session_start();
include 'Database.php';

if (!isset($_SESSION["user"])) {
    header("Location: LoginPage.php");
    exit();
}

if (!isset($_POST["foodId"])) {
    echo "Invalid request.";
    exit();
}

$foodId = $_POST["foodId"];
$email = $_SESSION["user"];

// Get user ID
$userQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$userQuery->bind_param("s", $email);
$userQuery->execute();
$userQuery->bind_result($userId);
$userQuery->fetch();
$userQuery->close();

// Delete the order item for the user
$deleteQuery = $conn->prepare("DELETE FROM orders WHERE foodId = ? AND userId = ?");
$deleteQuery->bind_param("ii", $foodId, $userId);
if ($deleteQuery->execute()) {
    echo "Item removed successfully!";
} else {
    echo "Error removing item.";
}

$deleteQuery->close();
$conn->close();
?>
