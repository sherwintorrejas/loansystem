<?php
include_once('../models/registrationmodel.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $registrationModel = new RegistrationModel();

   
    $registrationModel->registerUser($_POST);
    

    header("Location: ../view/login.php");
    exit(); 
}
?>
