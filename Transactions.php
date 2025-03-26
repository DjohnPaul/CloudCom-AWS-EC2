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

// Fetch transaction history
$transactionQuery = $conn->prepare("
    SELECT foodName, price, category, quantity, transactionDate 
    FROM transactions 
    WHERE userId = ?
    ORDER BY transactionDate DESC
");
$transactionQuery->bind_param("i", $userId);
$transactionQuery->execute();
$transactionQuery->store_result();
$transactionQuery->bind_result($foodName, $foodPrice, $foodCategory, $quantity, $transactionDate);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fork & Feast — Transaction History</title>

    <!-- Roboto Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Logo Title -->
    <link rel="icon" href="images/logo-title.jpg" type="image/x-icon/">

    <!-- CSS Links -->
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/header.css">

    <!-- Copy CSS 'orders.css' td Design -->
    <link rel="stylesheet" href="styles/orders.css">

    <link rel="stylesheet" href="styles/transaction.css">

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

    <div class="history-container">
        <div class="history">
            <h2>Transaction History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Food Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($transactionQuery->num_rows > 0) {
                        while ($transactionQuery->fetch()) {
                            echo "<tr>
                                    <td class='td-foodName'>{$foodName}</td>
                                    <td class='td-foodPrice'>₱" . number_format($foodPrice, 2) . "</td>
                                    <td class='td-foodCategory'>{$foodCategory}</td>
                                    <td class='td-foodQuantity'>{$quantity}</td>
                                    <td class='td-transactionDate'>{$transactionDate}</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='no-transactions'>No transaction history found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>

<?php
$transactionQuery->close();
$conn->close();
?>
