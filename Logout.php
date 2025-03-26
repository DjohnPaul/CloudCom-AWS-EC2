<?php
session_start();
require_once "Database.php";

if (isset($_SESSION["user"])) {
    $email = $_SESSION["user"];

    // Fetch user ID before destroying session
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if ($user) {
        $userId = $user["id"];
        $action = "Logout";

        // Insert into auditLogs
        $logSql = "INSERT INTO auditlogs (userId, action) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $logSql);
        mysqli_stmt_bind_param($stmt, "is", $userId, $action);
        mysqli_stmt_execute($stmt);
    }
}

// Destroy session and redirect
session_destroy();
header("Location: LoginPage.php");
exit();
?>
