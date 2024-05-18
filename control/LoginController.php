<?php
include_once('../models/LoginModel.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $loginModel = new LoginModel();
    $userData = $loginModel->authenticateUser($username, $password);

    if ($userData) {
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
