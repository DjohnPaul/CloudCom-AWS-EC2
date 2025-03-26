<?php
include 'Database.php'; 

header("Content-Type: application/json");

if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// SQL Query to get the top 4 selling items
$sql = "SELECT 
    foods.foodId, 
    foods.name AS foodName, 
    foods.category, 
    foods.imageName, 
    SUM(transactions.quantity) AS totalSold, 
    SUM(transactions.price * transactions.quantity) AS totalSales
FROM transactions 
JOIN foods  ON transactions.foodId = foods.foodId
GROUP BY foods.foodId, foods.name, foods.category, foods.imageName
ORDER BY  totalSold DESC, totalSales DESC
LIMIT 4";

$result = mysqli_query($conn, $sql);
$items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

echo json_encode($items);
mysqli_close($conn);
?>
