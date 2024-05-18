<?php
session_start();

include_once('../models/accounttypemodel.php');


$accountTypeModel = new AccountTypeModel();


if(isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    $accountType = $accountTypeModel->getAccountType($userId);

    
    if ($accountType == "Basic") {
        $links = [
            "<a href='loan.php'>Loans</a>",
            "<a href='billing.php'>Billing</a>",
            "<a href='accountdetails.php'>Account Details</a>"
        ];
    } elseif ($accountType == "Premium") {
        $links = [
            "<a href='loan.php'>Loans</a>",
            "<a href='savings.php'>Savings</a>",
            "<a href='billing.php'>Billing</a>",
            "<a href='accountdetails.php'>Account Details</a>"
        ];
    } else {
        
        $links = [];
    }

} else {
    
    header("Location: login.php");
    exit;
}
?>
