<?php
session_start();
include 'Database.php'; 

if (!isset($_SESSION["user"])) {
    header("Location: LoginPage.php");
}

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] !== "admin@gmail.com") {
        header("Location: MenuPage.php");
        exit();
    } 
}
// Get the timeframe from the AJAX request
$timeframe = isset($_GET['timeframe']) ? $_GET['timeframe'] : 'daily';

// Prepare SQL based on the selected timeframe
if ($timeframe === "monthly") {
    $sql = "SELECT 
    months.label AS label,
    months.monthNumber AS monthNumber,
    COALESCE(SUM(t.price * t.quantity), 0) AS totalSales
FROM (
    SELECT 1 AS monthNumber, 'January' AS label UNION ALL
    SELECT 2, 'February' UNION ALL
    SELECT 3, 'March' UNION ALL
    SELECT 4, 'April' UNION ALL
    SELECT 5, 'May' UNION ALL
    SELECT 6, 'June' UNION ALL
    SELECT 7, 'July' UNION ALL
    SELECT 8, 'August' UNION ALL
    SELECT 9, 'September' UNION ALL
    SELECT 10, 'October' UNION ALL
    SELECT 11, 'November' UNION ALL
    SELECT 12, 'December'
) AS months
LEFT JOIN transactions t
    ON months.monthNumber = MONTH(t.transactionDate)
    AND YEAR(t.transactionDate) = YEAR(CURDATE())
GROUP BY months.label, months.monthNumber
ORDER BY months.monthNumber";
} elseif ($timeframe === "weekly") {
    $sql = "WITH weeks AS (
    SELECT 1 AS weekNumber UNION ALL
    SELECT 2 UNION ALL
    SELECT 3 UNION ALL
    SELECT 4 UNION ALL
    SELECT 5
)
SELECT 
    CONCAT('Week ', weeks.weekNumber) AS label,
    COALESCE(SUM(t.price * t.quantity), 0) AS totalSales
FROM weeks
LEFT JOIN transactions t 
    ON weeks.weekNumber = WEEK(t.transactionDate, 1) - WEEK(DATE_FORMAT(t.transactionDate, '%Y-%m-01'), 1) + 1
    AND YEAR(t.transactionDate) = YEAR(CURDATE())
    AND MONTH(t.transactionDate) = MONTH(CURDATE())
GROUP BY weeks.weekNumber
ORDER BY weeks.weekNumber";
} else { // Default to daily
    $sql = "WITH week_days AS (
    SELECT CURDATE() - INTERVAL (WEEKDAY(CURDATE()) - 0) DAY AS saleDate UNION ALL
    SELECT CURDATE() - INTERVAL (WEEKDAY(CURDATE()) - 1) DAY UNION ALL
    SELECT CURDATE() - INTERVAL (WEEKDAY(CURDATE()) - 2) DAY UNION ALL
    SELECT CURDATE() - INTERVAL (WEEKDAY(CURDATE()) - 3) DAY UNION ALL
    SELECT CURDATE() - INTERVAL (WEEKDAY(CURDATE()) - 4) DAY UNION ALL
    SELECT CURDATE() - INTERVAL (WEEKDAY(CURDATE()) - 5) DAY UNION ALL
    SELECT CURDATE() - INTERVAL (WEEKDAY(CURDATE()) - 6) DAY
)
SELECT 
    DATE(wd.saleDate) AS saleDate,
    DAYNAME(wd.saleDate) AS label,
    COALESCE(SUM(t.price * t.quantity), 0) AS totalSales
FROM week_days wd
LEFT JOIN transactions t 
    ON DATE(t.transactionDate) = wd.saleDate
    AND YEARWEEK(t.transactionDate, 1) = YEARWEEK(CURDATE(), 1)
GROUP BY saleDate, label
ORDER BY saleDate ASC";
}

$result = $conn->query($sql);

// Prepare arrays for Chart.js
$labels = [];
$sales = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['label'];
        $sales[] = $row['totalSales'];
    }
}

// Convert to JSON for Chart.js
echo json_encode(["labels" => $labels, "sales" => $sales]);

$conn->close();
?>
