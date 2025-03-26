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

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Prepare SQL query
$sql = "SELECT * FROM foods WHERE 1";

if (!empty($search)) {
    $sql .= " AND name LIKE ?";
}

if (!empty($category)) {
    $sql .= " AND category = ?";
}

// Prepare and execute statement
$stmt = $conn->prepare($sql);

if (!empty($search) && !empty($category)) {
    $searchParam = "%" . $search . "%";
    $stmt->bind_param("ss", $searchParam, $category);
} elseif (!empty($search)) {
    $searchParam = "%" . $search . "%";
    $stmt->bind_param("s", $searchParam);
} elseif (!empty($category)) {
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$result = $stmt->get_result();

$foods = [];

while ($row = $result->fetch_assoc()) {
    $foods[] = $row;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($foods);

$conn->close();

?>
