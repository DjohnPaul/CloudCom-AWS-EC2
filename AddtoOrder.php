<?php
session_start();
include 'Database.php'; 

if (!isset($_SESSION["user"])) {
    header("Location: LoginPage.php");
    exit();
}

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] === "admin@gmail.com") {
        header("Location: AdminDashboardPage.php");
        exit();
    } 
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

// Check if the food is already in the cart
$checkQuery = $conn->prepare("SELECT orderId, quantity FROM orders WHERE userId = ? AND foodId = ?");
$checkQuery->bind_param("ii", $userId, $foodId);
$checkQuery->execute();
$checkQuery->store_result();

if ($checkQuery->num_rows > 0) {
    // If item exists, increase quantity
    $checkQuery->bind_result($orderId, $quantity);
    $checkQuery->fetch();
    $newQuantity = $quantity + 1;
    $updateQuery = $conn->prepare("UPDATE orders SET quantity = ? WHERE orderId = ?");
    $updateQuery->bind_param("ii", $newQuantity, $orderId);
    $updateQuery->execute();
    echo "Updated quantity!";
} else {
    // Insert new item into cart
    $insertQuery = $conn->prepare("INSERT INTO orders (userId, foodId, quantity) VALUES (?, ?, 1)");
    $insertQuery->bind_param("ii", $userId, $foodId);
    $insertQuery->execute();
    echo "Item added to cart!";
}

$checkQuery->close();
$conn->close();
?>