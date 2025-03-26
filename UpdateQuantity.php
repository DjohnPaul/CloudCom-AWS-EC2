<?php
session_start();
include 'Database.php';

if (!isset($_SESSION["user"])) {
    header("Location: LoginPage.php");
    exit();
}

if ($_SESSION["user"] === "admin@gmail.com") {
    header("Location: AdminDashboardPage.php");
    exit();
}

$email = $_SESSION["user"];
$foodId = $_POST["foodId"];
$action = $_POST["action"];

// Get user ID
$userQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$userQuery->bind_param("s", $email);
$userQuery->execute();
$userQuery->bind_result($userId);
$userQuery->fetch();
$userQuery->close();

// Get current quantity
$checkQuery = $conn->prepare("SELECT orderId, quantity FROM orders WHERE userId = ? AND foodId = ?");
$checkQuery->bind_param("ii", $userId, $foodId);
$checkQuery->execute();
$checkQuery->bind_result($orderId, $quantity);
$checkQuery->fetch();
$checkQuery->close();

if (!$orderId) {
    echo json_encode(["status" => "error", "message" => "Order not found"]);
    exit();
}

if ($action === "increase") {
    $updateQuery = $conn->prepare("UPDATE orders SET quantity = quantity + 1 WHERE orderId = ?");
} else {
    $updateQuery = $conn->prepare("UPDATE orders SET quantity = GREATEST(quantity - 1, 1) WHERE orderId = ?");
}
$updateQuery->bind_param("i", $orderId);
$updateQuery->execute();
$updateQuery->close();

// Fetch updated quantity
$newQuantityQuery = $conn->prepare("SELECT quantity FROM orders WHERE orderId = ?");
$newQuantityQuery->bind_param("i", $orderId);
$newQuantityQuery->execute();
$newQuantityQuery->bind_result($newQuantity);
$newQuantityQuery->fetch();
$newQuantityQuery->close();

echo json_encode(["status" => "success", "quantity" => $newQuantity]);
exit();
?>
