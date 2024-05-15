<?php
include_once('../models/LoginModel.php');

// Start session
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $loginModel = new LoginModel();
    $userData = $loginModel->authenticateUser($username, $password);

    if ($userData) {
        // Check if the 'account_type' key exists in the user data
        if (isset($userData['account_type'])) {
            header("Location: ../view/userdashboard.php");
            exit();
        } else {
            header("Location: ../view/admindashboard.php");
            exit();
        }
    } else {
        echo "Invalid username or password.";
    }
} 


?>
