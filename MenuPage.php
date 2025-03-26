<?php
session_start();

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

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Fork & Feast — Menu</title>

        <!-- Roboto Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <!-- Logo Title -->
        <link rel="icon" href="images/logo-title.jpg" type="image/x-icon/">

        <!-- CSS Links -->
        <link rel="stylesheet" href="styles/general.css">
        <link rel="stylesheet" href="styles/header.css">
        <link rel="stylesheet" href="styles/search.css">
        <link rel="stylesheet" href="styles/main.css">

    </head>
    <body>
        <!-- Header -->
        <div class="header">
            <div class="header-left-section">
                <img class="brand-logo" src="images/fork-and-feast-name-logo.png" alt="Fork & Feast">
            </div>
            <div class="header-right-section">
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

        <!-- Search -->
        <div class="background-search">
            <div class="search-section">
                <div class="search">
                    <img class="search-logo" src="images/search.png" alt="search">
                    <input class="search-bar-input" id="searchInput" type="textbox" placeholder="Search for food...">
                </div>
            </div>
        </div>
        
        <!-- Category -->
        <div class="category-grid">
            <button class="category-btn active" data-category=""><img class="category-logo-img" src="images/menu-logo.png">All</button>
            <button class="category-btn" data-category="meals"><img class="category-logo-img" src="images/meal-logo.png">Meals</button>
            <button class="category-btn" data-category="drinks"><img class="category-logo-img" src="images/drink-logo.png">Drinks</button>
            <button class="category-btn" data-category="sides"><img class="category-logo-img" src="images/sides-logo.png">Sides</button>
        </div>

        <!-- Food Grid -->
        <div id="foodGrid" class="food-container"></div>

        <!--
        Search Background Transition
        <script src="scripts/background-transition.js"></script>
        -->

        <!-- JS Links -->
        <script src="scripts/foods.js"></script>
    </body>
</html>
