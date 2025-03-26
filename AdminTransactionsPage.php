<?php
session_start();
include 'Database.php';

if (!isset($_SESSION["user"])) {
    header("Location: LoginPage.php");
    exit();
}

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] !== "admin@gmail.com") {
        header("Location: MenuPage.php");
        exit();
    }
}


// Fetch transaction history
$adminTransactionQuery = $conn->prepare("
    SELECT transactions.userId, users.email, transactions.foodId, transactions.foodName, transactions.price, transactions.category, transactions.quantity, transactions.transactionDate
    FROM transactions
    JOIN users ON transactions.userId = users.Id
    ORDER BY transactions.Id DESC
");
$adminTransactionQuery->execute();
$adminTransactionQuery->store_result();
$adminTransactionQuery->bind_result($userId, $email, $foodId,$foodName, $foodPrice, $foodCategory, $quantity, $transactionDate);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fork & Feast — Admin Transaction</title>

    <!-- Roboto Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <!-- Logo Title -->
    <link rel="icon" href="images/logo-title.jpg" type="image/x-icon/">

    <!-- CSS Links -->
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/transaction-admin.css">

</head>
<body>
    <!-- Header -->
    <div class="header">
            <div class="header-left-section">
                <img class="brand-logo" src="images/fork-and-feast-name-logo.png" alt="Fork & Feast">
            </div>
            <div class="header-right-section">
                <a href="AdminDashboardPage.php">
                    <button class="home-button">Dashboard</button>
                </a>
                <a href="AdminAuditLogsPage.php">
                    <button class="myorders-button">AuditLogs</button>
                </a>
                <a href="AdminTransactionsPage.php">
                    <button class="transaction-button">Transaction</button>
                </a>
                <a href="Logout.php">
                    <button class="logout-button">Logout</button>
                </a>
            </div>
    </div>

    <div class="history-admin-container">
        <div class="history-admin">
            <h2>Transaction Histories</h2>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Email</th>
                        <th>Food ID</th>
                        <th>Food Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($adminTransactionQuery->num_rows > 0) {
                        while ($adminTransactionQuery->fetch()) {
                            echo "<tr>
                                    <td class='td-userId'>{$userId}</td>
                                    <td class='td-email'>{$email}</td>
                                    <td class='td-foodId'>{$foodId}</td>
                                    <td class='td-foodName'>{$foodName}</td>
                                    <td class='td-foodPrice'>₱" . number_format($foodPrice, 2) . "</td>
                                    <td class='td-foodCategory'>{$foodCategory}</td>
                                    <td class='td-quantity'>{$quantity}</td>
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
$adminTransactionQuery->close();
$conn->close();
?>