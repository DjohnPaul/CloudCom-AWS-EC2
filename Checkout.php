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

// Fetch ordered items from the database
$orderQuery = $conn->prepare("
    SELECT foods.name, foods.price, orders.quantity 
    FROM orders 
    JOIN foods ON orders.foodId = foods.foodId 
    WHERE orders.userId = ?
");
$orderQuery->bind_param("i", $userId);
$orderQuery->execute();
$orderQuery->store_result();
$orderQuery->bind_result($foodName, $foodPrice, $quantity);

$totalAmount = 0;
$receiptItems = [];

while ($orderQuery->fetch()) {
    $totalPrice = $foodPrice * $quantity;
    $totalAmount += $totalPrice;
    $receiptItems[] = [
        'name' => $foodName,
        'price' => $foodPrice,
        'quantity' => $quantity,
        'total' => $totalPrice
    ];
}

$orderQuery->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>

    <!-- Roboto Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Logo -->
    <link rel="icon" href="images/logo-title.jpg" type="image/x-icon/">
    <!-- CSS Links -->
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/header.css">

    <!-- Copy CSS 'orders.css' td Design -->
    <link rel="stylesheet" href="styles/orders.css">

    <link rel="stylesheet" href="styles/checkout.css">

</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left-section">
            <img class="brand-logo" src="images/fork-and-feast-name-logo.png" alt="Fork & Feast">
        </div>
        <div class="header-right-section">
            <a href="MenuPage.php">
                <button class="home-button">Menu</button>
            </a>
            <a href="MyOrdersPage.php">
                <button class="myorders-button">Cart</button>
            </a>
            <a href="Logout.php">
                <button class="logout-button">Logout</button>
            </a>
        </div>
    </div>

    <!-- Receipt Container -->
    <div class="receipt-container">
        <div class="receipt">
            <h2>Order Receipt</h2>
            <table>
                <thead>
                    <tr>
                        <th>Food Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($receiptItems as $item): ?>
                        <tr>
                            <td class='td-foodName'><?php echo htmlspecialchars($item['name']); ?></td>
                            <td class='td-foodPrice'>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td class='td-foodQuantity'><?php echo $item['quantity']; ?></td>
                            <td  class='td-totalPrice'>₱<?php echo number_format($item['total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-container">
                Total Amount: ₱<span><?php echo number_format($totalAmount, 2); ?></span>
            </div>
        </div>
    </div>

    <div class='confirm-orders-button'>
        <form action="ClearOrders.php" method="POST">
            <button type="submit" class="back-btn">Confirm Orders</button>
        </form>
    </div>

</body>
</html>

<?php
$conn->close();
?>
