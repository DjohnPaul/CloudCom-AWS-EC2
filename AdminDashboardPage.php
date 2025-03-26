<?php
session_start();
include 'Database.php';

if (!isset($_SESSION["user"])) {
    header("Location: LoginPage.php");
    exit();
}
// Fetch total sales
$adminTotalSalesQuery = $conn->prepare("
    SELECT SUM(price * quantity) 
    FROM transactions;
");
$adminTotalSalesQuery->execute();
$adminTotalSalesQuery->store_result();
$adminTotalSalesQuery->bind_result($totalSales);
$adminTotalSalesQuery->fetch();
$adminTotalSalesQuery->close();

//Fetch total orders
$adminOrdersQuery = $conn->prepare("
    SELECT COUNT(id) 
    FROM transactions;
");
$adminOrdersQuery->execute();
$adminOrdersQuery->store_result();
$adminOrdersQuery->bind_result($totalOrders);
$adminOrdersQuery->fetch();
$adminOrdersQuery->close();

//Fetch average order value
$adminAverageOrdersQuery = $conn->prepare("
    SELECT SUM(price * quantity)/COUNT(id) 
    FROM transactions;
");
$adminAverageOrdersQuery->execute();
$adminAverageOrdersQuery->store_result();
$adminAverageOrdersQuery->bind_result($averageOrders);
$adminAverageOrdersQuery->fetch();
$adminAverageOrdersQuery->close();

// Fetch total Users
$adminTotalUsersQuery = $conn->prepare("
    SELECT COUNT(*)  
    FROM users;
");
$adminTotalUsersQuery->execute();
$adminTotalUsersQuery->store_result();
$adminTotalUsersQuery->bind_result($totalUsers);
$adminTotalUsersQuery->fetch();
$adminTotalUsersQuery->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fork & Feast — Admin Dashboard</title>

    <!-- Roboto Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <!-- Logo -->
    <link rel="icon" href="images/logo-title.jpg" type="image/x-icon/">

    <!-- CSS Links -->
    <link rel="stylesheet" href="styles/dashboard.css">
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
    <!--Dashboard-->
    <div class="container">
        <div class="dashboard">
            <div class="dashboard-header">
                <div class="dashboard-title">Sales Dashboard</div>
            </div>

            <div class="quick-metrics">
                <!--Total Sales-->
                <div class="card metric-card" style="background-color: var(--primary);">
                    <div class="metric-title">Total Sales</div>
                    <div class="metric-value">
                        <?php
                            echo"<h1>₱" . number_format($totalSales, 2) . "</h1> ";
                        ?> 
                    </div>
                </div>    
                <!--Orders-->
                <div class="card metric-card" style="background-color: var(--success);">
                    <div class="metric-title">Orders</div>
                    <div class="metric-value">
                        <?php
                            echo"<h1>{$totalOrders}</h1>";
                        ?> 
                    </div>
                </div>
                <!--Average Order Value-->
                <div class="card metric-card" style="background-color: var(--warning);">
                    <div class="metric-title">Average Order Value</div>
                    <div class="metric-value">
                        <?php
                            echo"<h1>₱" . number_format($averageOrders, 2) . "</h1> ";
                        ?> 
                    </div>
                </div>
                <!--Users-->
                <div class="card metric-card" style="background-color: var(--info);">
                    <div class="metric-title">Users</div>
                    <div class="metric-value">
                        <?php
                            echo"<h1>{$totalUsers}</h1>";
                        ?> 
                    </div>
                </div>        
            </div>

            <!--Weekly Sales Trend-->
            <div class="card sales-trend-chart">
                <div class="chart-header">
                    <div class="chart-title">Sales Trend</div>
                    <div class="chart-actions">
                        <select class='select-filter' id="chart-filter">
                            <option value="daily">Daily</option>
                            <option value="weekly" selected>Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    </div>
                    <div class="chart-canvas"></div>
                    <div class="chart-x-axis"></div>
                </div>
            </div>

            <!--Top Selling Items--> 
            <div class="card top-items">
                <div class="chart-header">
                <div class="chart-title">Top-Selling Items</div>
                </div>

                <table class="table top-items-table">
                <thead>
                    <tr>
                    <th>Item</th>
                    <th>Sales</th>
                    <th>Orders</th>
                    
                    </tr>
                </thead>
                <tbody>
                    <!-- JavaScript will populate this -->
                </tbody>
                </table>
            </div>
            
        </div>
        <script src="scripts/dashboard.js"></script>
    </body>
</body>
</html>
