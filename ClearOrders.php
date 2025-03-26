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

$email = $_SESSION["user"];

// Get user ID
$userQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$userQuery->bind_param("s", $email);
$userQuery->execute();
$userQuery->bind_result($userId);
$userQuery->fetch();
$userQuery->close();

// Get all ordered items before deleting them
$orderQuery = $conn->prepare("
    SELECT foods.foodId, foods.name, foods.price, foods.category, orders.quantity 
    FROM orders 
    JOIN foods ON orders.foodId = foods.foodId 
    WHERE orders.userId = ?
");
$orderQuery->bind_param("i", $userId);
$orderQuery->execute();
$orderQuery->store_result();
$orderQuery->bind_result($foodId, $foodName, $foodPrice, $foodCategory, $quantity);

// Insert transactions into the transactions table
$insertTransaction = $conn->prepare("
    INSERT INTO transactions (userId, foodId, foodName, price, category, quantity) 
    VALUES (?, ?, ?, ?, ?, ?)
");

while ($orderQuery->fetch()) {
    $insertTransaction->bind_param("iisdsi", $userId, $foodId, $foodName, $foodPrice, $foodCategory, $quantity);
    $insertTransaction->execute();
}

$orderQuery->close();
$insertTransaction->close();

// Delete all orders for this user after storing them as transactions
$deleteQuery = $conn->prepare("DELETE FROM orders WHERE userId = ?");
$deleteQuery->bind_param("i", $userId);
$deleteQuery->execute();
$deleteQuery->close();

// Redirect back to the orders page
header("Location: MenuPage.php");
?>
