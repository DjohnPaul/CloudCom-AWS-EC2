<?php
session_start();
if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] === "admin@gmail.com") {
        header("Location: AdminDashboardPage.php");
    } else {
        header("Location: MenuPage.php");
    }
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Fork & Feast — Log In or Sign Up</title>

        <!-- Roboto Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <!-- Logo -->
        <link rel="icon" href="images/logo-title.jpg" type="image/x-icon/">

        <!-- CSS Links -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <link rel="stylesheet" href="styles/general.css">
        <link rel="stylesheet" href="styles/header.css">
        <link rel="stylesheet" href="styles/login.css">
        
    </head>
    <body class="login-background">
        <!-- Header -->
        <div class="header">
            <div class="header-left-section">
                <img class="brand-logo" src="images/fork-and-feast-name-logo.png" alt="Fork & Feast">
            </div>
        </div>

        <!-- Main Login -->
        <div  class="main-login">
            <div class="login-container">
                <div class="login-left-section">
                    <div class="login-text">
                        Log in to your account
                    </div>
                    <div class="signup-login-text">
                        Sign up or log in to continue
                    </div>

                    <?php
                        if (isset($_POST["login"])) {
                            $email = $_POST["email"];
                            $password = $_POST["password"];
                            
                            require_once "Database.php";

                            // Get user details from the database
                            $sql = "SELECT * FROM users WHERE email = '$email'";
                            $result = mysqli_query($conn, $sql);
                            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

                            if ($user) {
                                if (password_verify($password, $user["password"])) {
                                    session_start();
                                    $_SESSION["user"] = $email;

                                    // Insert login action into auditLogs
                                    $userId = $user["id"]; // Get user ID from database
                                    $action = "Login";
                                    $logSql = "INSERT INTO auditlogs (userId, action) VALUES (?, ?)";
                                    $stmt = mysqli_prepare($conn, $logSql);
                                    mysqli_stmt_bind_param($stmt, "is", $userId, $action);
                                    mysqli_stmt_execute($stmt);

                                    // Redirect based on user role
                                    if ($email == "admin@gmail.com") {
                                        header("Location: AdminDashboardPage.php");
                                    } else {
                                        header("Location: MenuPage.php");
                                    }
                                    die();
                                } else {
                                    echo "<div class='alert alert-danger'>Password does not match</div>";
                                }
                            } else {
                                echo "<div class='alert alert-danger'>Email does not match</div>";
                            }
                        }
                    ?>


                    <!-- Login Form -->
                    <form action="LoginPage.php" method="post">
                        <div class="form-group">
                            <input type="email" placeholder="Enter Email:" name="email" class="form-control">
                        </div>
                        <br>
                        <div class="form-group">
                            <input type="password" placeholder="Enter Password:" name="password" class="form-control">
                        </div>
                        <br>
                        <div class="form-btn">
                            <input type="submit" value="Login" name="login" class="btn login-button">
                        </div>
                        <hr>

                         <!-- Create New Account -->
                        <div class="form-btn">
                            <a href="SignupPage.php">
                                <button type="button" value="NewAccount" name="newaccount" class="btn create-new-acc-button">Create New Account!</button>
                            </a>
                        </div>

                    </form>
                </div>
                
                 <!-- Welcoming Text -->
                <div class="login-right-section">
                    <div class="welcome-text">Welcome to</div>
                    <div class="welcome-text2">FORK & FEAST</div>
                    <div class="welcome-text3">Welcome to Fork & Feast! Your trusted companion for easy and convenient meal ordering! Our website allows you to skip the hassle of waiting in line by placing your orders ahead for pick-up or dine-in at our restaurant.</div>
                </div>
            </div>
        </div>
    </body>
</html>
