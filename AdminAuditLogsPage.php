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
$auditQuery = $conn->prepare("
    SELECT auditlogs.userId, users.email, auditlogs.action, auditlogs.timestamp
    FROM auditlogs 
    JOIN users ON auditLogs.userId = users.id
    ORDER BY auditlogs.Id DESC
");
$auditQuery->execute();
$auditQuery->store_result();
$auditQuery->bind_result($userId, $email, $action,$timestamp);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fork & Feast — Audit Logs</title>

    <!-- Roboto Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <!-- Logo Title -->
    <link rel="icon" href="images/logo-title.jpg" type="image/x-icon/">

    <!-- CSS Links -->
    <link rel="stylesheet" href="styles/general.css">
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/auditlogs.css">

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

    <div class="auditlogs-container">
        <div class="auditlogs">
            <h2>Audit Logs</h2>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Email</th>
                        <th>Action</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    if ($auditQuery->num_rows > 0) {
                        while ($auditQuery->fetch()) {
                            // Determine the color based on the action
                            $color = ($action === "Logout") ? "lightcoral" : (($action === "Login") ? "rgb(0, 233, 89);" : "black");

                            echo "<tr>
                                    <td class='td-user-id'>{$userId}</td>
                                    <td class='td-user-email'>{$email}</td>
                                    <td style='
                                        font-family: Roboto, Arial, Helvetica, sans-serif;
                                        color: {$color}; 
                                        font-weight: bold;
                                    '>{$action}</td>
                                    <td class='td-user-timestamp'>{$timestamp}</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='no-transactions'>No Audit Logs found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>

<?php
$auditQuery->close();
$conn->close();
?>