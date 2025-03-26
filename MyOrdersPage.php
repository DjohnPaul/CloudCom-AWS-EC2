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

// Get cart items
$orderQuery = $conn->prepare("
    SELECT foods.name, foods.price, foods.imageName, foods.category, orders.quantity, orders.foodId 
    FROM orders 
    JOIN foods ON orders.foodId = foods.foodId 
    WHERE orders.userId = ?
");
$orderQuery->bind_param("i", $userId);
$orderQuery->execute();
$orderQuery->store_result();
$orderQuery->bind_result($foodName, $foodPrice, $foodImage, $foodCategory, $quantity, $foodId);

$totalAmount = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fork & Feast — My Orders</title>

    <!-- Roboto Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Logo -->
    <link rel="icon" href="images/logo-title.jpg" type="image/x-icon/">

    <!-- CSS Links -->
    <link rel="stylesheet" href="styles/orders.css">
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/header.css">
    
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
                <a href="Transactions.php">
                    <button class="transaction-button">Transaction</button>
                </a>
                <a href="Logout.php">
                    <button class="logout-button">Logout</button>
                </a>
            </div>
    </div>

    <!-- Orders Container -->
    <div class="orders-container">
        <div class="orders">
            <h2>My Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Food Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <?php
                    if ($orderQuery->num_rows > 0) {
                        while ($orderQuery->fetch()) {
                            $totalPrice = $foodPrice * $quantity;
                            $totalAmount += $totalPrice;
                            echo "<tr id='row_$foodId'>
                                    <td><img class='td-foodImage' src='images/$foodCategory/$foodImage' alt='$foodName'></td>
                                    <td class='td-foodName'>$foodName</td>
                                    <td class='td-foodPrice'>₱$foodPrice</td>
                                    <td>
                                        <button class='quantity-button' onclick=\"updateQuantity($foodId, 'decrease')\">-</button>
                                        <span class='td-foodQuantity' id='qty_$foodId'>$quantity</span>
                                        <button class='quantity-button' onclick=\"updateQuantity($foodId, 'increase')\">+</button>
                                    </td>
                                    <td class='td-totalPrice'>₱$totalPrice</td>
                                    <td>
                                        <button class='remove-btn' onclick='removeFromOrder($foodId)'>X</button>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='no-orders'>No orders found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="total-container">
                Total: ₱<span id="totalAmount"><?php echo number_format($totalAmount, 2); ?></span>
            </div>

            <?php if ($totalAmount > 0) { ?>
                <form action="Checkout.php" method="POST">
                    <input type="hidden" name="totalAmount" value="<?php echo $totalAmount; ?>">
                    <button type="submit" class="checkout-btn">Proceed to Checkout</button>
                </form>
            <?php } ?>
        </div>
    </div>
    

    <!-- JS Links -->
    <script src="scripts/myorders.js"></script>
</body>
</html>

<?php
$orderQuery->close();
$conn->close();
?>
