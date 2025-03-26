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
        <title>Fork & Feast — Sign Up</title>

        <!-- Roboto Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <!-- Logo -->
        <link rel="icon" href="images/logo-title.jpg" type="image/x-icon/">

        <!-- CSS Links-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <link rel="stylesheet" href="styles/general.css">
        <link rel="stylesheet" href="styles/header.css">
        <link rel="stylesheet" href="styles/login.css">
        <link rel="stylesheet" href="styles/signup.css">
        
    </head>
    <body>
        <!-- Header-->
        <div class="header">
            <div class="header-left-section">
                <a href="LandingPage.html">
                    <button class="brand-button">
                        <img class="brand-logo" src="images/fork-and-feast-name-logo.png" alt="Fork & Feast">
                    </button>
                </a>
            </div>
        </div>

        <!-- Main Singup -->
        <div class="main-login">
            <div class="login-container">
                <div class="login-left-section">
                    <div class="signup-text">
                        Create your account
                    </div>
                    <div class="signup-account-text">
                        Create an account to start feasting!
                    </div>
                    
                    <?php
                    if (isset($_POST["submit"])) {
                        $email = $_POST["email"];
                        $password = $_POST["password"];
                        $confirmPassword = $_POST["confirm_password"];
                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                        $errors = array();
                        if (empty($email) OR empty($password) OR empty($confirmPassword)) {
                            array_push($errors,"All fields are required");
                        }
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            array_push($errors, "Email is not valid");
                        }
                        if (strlen($password)<8) {
                            array_push($errors,"Password must be at least 8 charactes long");
                        }
                        if ($password!==$confirmPassword) {
                            array_push($errors,"Password does not match");
                        }
                        require_once "Database.php";
                        $sql = "SELECT * FROM users WHERE email = '$email'";
                        $result = mysqli_query($conn, $sql);
                        $rowCount = mysqli_num_rows($result);
                        if ($rowCount>0) {
                            array_push($errors,"Email already exists!");
                        }
                        if (count($errors)>0) {
                            foreach ($errors as  $error) {
                                echo "<div class='alert alert-danger'>$error</div>";
                            }
                        }                       
                        else{
                            $sql = "INSERT INTO users (email, password) VALUES ( ?, ? )";
                            $stmt = mysqli_stmt_init($conn);
                            $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
                            if ($prepareStmt) {
                                mysqli_stmt_bind_param($stmt,"ss", $email, $passwordHash);
                                mysqli_stmt_execute($stmt);
                                sleep(2);
                                header("Location: LoginPage.php");
                            }else{
                                die("Something went wrong");
                            }
                        }
                    }
                    ?>
                    
                     <!-- Signup Form -->
                    <form action="SignupPage.php" method="post">
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" placeholder="Email:">
                        </div>
                        <br>
                        <div class="form-group">
                            <input type="password" class="form-control" name="password" placeholder="Password:">
                        </div>
                        <br>
                        <div class="form-group">
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password:">
                        </div>
                        <br>
                        <div class="form-btn">
                            <input type="submit" class="btn signup-button" value="Signup" name="submit">
                        </div>

                        <hr>
                        
                         <!-- Already has account button -->
                        <div class="form-btn">
                            <a href="LoginPage.php">
                                <button type="button" class="have-account-text">Already have an account?</button>
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
