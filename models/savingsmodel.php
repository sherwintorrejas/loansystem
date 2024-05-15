<?php
require_once 'config.php';
require_once 'accounttypemodel.php';
require_once 'SavingsTransactionModel.php';
session_start();
$userId = $_SESSION['id'];

// Create instance of SavingsController
$controller = new SavingsController($userId);

// Check if form is submitted and action is set
// Check if form is submitted and action is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    // Get savings ID
    $savingsId = $controller->getSavingsId();
    
    // Handle deposit action
    if ($_POST['action'] == 'deposit') {
        $depositAmount = $_POST['deposit_amount'];
        if ($controller->deposit($savingsId, $depositAmount)) {
            echo "Deposit successful.";
            header("Location: ../view/savings.php");
                exit();
        } else {
            echo "Failed to deposit.";
        }
    }
    // Handle withdrawal action
    // elseif ($_POST['action'] == 'withdraw') {
    //     $withdrawAmount = $_POST['withdraw_amount'];
    //     if ($controller->withdraw($savingsId, $withdrawAmount)) {
    //         echo "Withdrawal successful.";
    //     } else {
    //         echo "Failed to withdraw.";
    //     }
    // }
    // Add more actions if needed
}
class SavingsModel {
    private $db;
    private $transactionModel;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->transactionModel = new SavingsTransactionModel();
    }

    public function getSavingsId($userId) {
    $stmt = $this->db->prepare("SELECT savings_id FROM savingdatabase WHERE user_id = ?");
    if ($stmt->execute([$userId])) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && isset($result['savings_id'])) {
            return $result['savings_id'];
        } else {
            // If no savings_id is found and the user is a premium member, generate a new one
            $accountTypeModel = new AccountTypeModel();
            $accountType = $accountTypeModel->getAccountType($userId);
            if ($accountType === 'Premium') {
                $savingsId = $this->generateSavingsId();
                $this->createSavingsAccount($userId, $savingsId);
                return $savingsId;
            }
        }
    }
    return null; // Return null if query failed or no savings ID found
}

    
    public function generateSavingsId() {
        // Generate a random savings_id
        return mt_rand(100000, 999999); // Example: Generating a 6-digit random number
    }
    
    public function createSavingsAccount($userId, $savingsId) {
        try {
            // Insert a new savings account into the database
            $stmt = $this->db->prepare("INSERT INTO savingdatabase (savings_id, user_id, savings_amount, last_activity_date) VALUES (?, ?, 0.00, NOW())");
            $stmt->execute([$savingsId, $userId]);
            return true; // Savings account created successfully
        } catch (PDOException $e) {
            // Handle database error
            error_log("Error in createSavingsAccount(): " . $e->getMessage());
            return false;
        }
    }
    
    
    public function getAvailableBalance($userId) {
        $stmt = $this->db->prepare("SELECT savings_amount FROM savingdatabase WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result['savings_amount'])) {
                return $result['savings_amount'];
            }
        }
        return null;
    }
    
    public function deposit($savingsId, $amount) {
        try {
            if ($amount < 100 || $amount > 1000) {
                return false; 
            }
            $stmt = $this->db->prepare("UPDATE savingdatabase SET savings_amount = savings_amount + ?, last_activity_date = NOW() WHERE savings_id = ?");
            $success = $stmt->execute([$amount, $savingsId]);
            if (!$success) {
                throw new Exception("Failed to update savings amount.");
            }
            $transactionId = uniqid(); 
            $status = 'Completed';
            $dateTime = date('Y-m-d');
            $this->transactionModel->logTransaction($savingsId, 'Deposit', $amount, $transactionId, $status, $dateTime);
            
            return true;
        } catch (Exception $e) {

            error_log("Error in deposit(): " . $e->getMessage());
            return false;
        }
    }  
    // public function withdraw($userId, $amount) {
    //     try {
    //         // Check if the withdrawal amount is within the allowed range
    //         if ($amount < 500 || $amount > 5000) {
    //             return false; // Withdrawal amount out of range
    //         }
    
    //         // Log the withdrawal request transaction with 'Pending' status
    //         $transactionId = uniqid();
    //         $status = 'Pending';
    //         $dateTime = date('Y-m-d');
    //         $transactionType = 'Withdrawal';
    //         $savingsId = $this->getSavingsId($userId);
    //         $lastAmount = $this->getAvailableBalance($userId);
    
    //         // Insert the withdrawal transaction into the savingstransaction table
    //         $stmt = $this->db->prepare("INSERT INTO savingstransaction (transaction_id, savings_id, transaction_type, amount, last_amount, status, date_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
    //         $success = $stmt->execute([$transactionId, $savingsId, $transactionType, $amount, $lastAmount, $status, $dateTime]);
    
    //         if (!$success) {
    //             throw new Exception("Failed to insert withdrawal transaction.");
    //         }
    
    //         return true; // Withdrawal request successful
    //     } catch (Exception $e) {
    //         // Handle the exception (e.g., log error, return false, etc.)
    //         error_log("Error in withdraw(): " . $e->getMessage());
    //         return false;
    //     }
    // }
    
    
    public function approveWithdrawal($transactionId) {
        try {
            // Get the transaction details
            $stmt = $this->db->prepare("SELECT * FROM savingstransaction WHERE transaction_id = ?");
            $stmt->execute([$transactionId]);
            $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$transaction || $transaction['status'] !== 'Pending') {
                return false; // Transaction not found or not pending
            }
    
            // Deduct the withdrawal amount from the current balance
            $newBalance = $transaction['last_amount'] - $transaction['amount'];
    
            // Update the savings amount and transaction status
            $stmt = $this->db->prepare("UPDATE savingdatabase SET savings_amount = ?, last_activity_date = NOW() WHERE savings_id = ?");
            $stmt->execute([$newBalance, $transaction['savings_id']]);
            $stmt = $this->db->prepare("UPDATE savingstransaction SET status = 'Completed' WHERE transaction_id = ?");
            $stmt->execute([$transactionId]);
    
            return true; // Withdrawal approved successfully
        } catch (Exception $e) {
            // Handle the exception (e.g., log error, return false, etc.)
            error_log("Error in approveWithdrawal(): " . $e->getMessage());
            return false;
        }
    }
    
    

    private function downgradeAccountTypeToBasic($userId) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET account_type = 'Basic' WHERE user_id = ?");
            $success = $stmt->execute([$userId]);
            if (!$success) {
                throw new Exception("Failed to downgrade account type to Basic.");
            }
            // Additional actions (if any) after downgrading the account type
        } catch (Exception $e) {
            // Handle the exception (e.g., log error, return false, etc.)
            error_log("Error in downgradeAccountTypeToBasic(): " . $e->getMessage());
        }
    }
    

    public function addPremiumIncomeToSavings($incomePerUser) {
        $stmt = $this->db->prepare("UPDATE savingdatabase SET savings_amount = savings_amount + ?");
        return $stmt->execute([$incomePerUser]);
    }

    public function checkSavingsStatus($userId) {
        $stmt = $this->db->prepare("SELECT last_activity_date, savings_amount FROM savingdatabase WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastActivityDate = strtotime($result['last_activity_date']);
        $savingsAmount = $result['savings_amount'];

        $threeMonthsAgo = strtotime('-3 months');
        if ($lastActivityDate < $threeMonthsAgo && $savingsAmount == 0) {
            $stmt = $this->db->prepare("UPDATE users SET account_type = 'Basic' WHERE user_id = ?");
            return $stmt->execute([$userId]);
        }
        return true;
    }
    // public function canRequestWithdrawal($userId) {
    //     try {
    //         $stmt = $this->db->prepare("SELECT COUNT(*) AS withdrawal_count FROM savings_transactions WHERE user_id = ? AND DATE(date_time) = CURDATE() AND transaction_type = 'Withdrawal'");
    //         $stmt->execute([$userId]);
    //         $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //         $withdrawalCount = $result['withdrawal_count'];
    //         return $withdrawalCount < 5;
    //     } catch (PDOException $e) {
    //         error_log("Error in canRequestWithdrawal(): " . $e->getMessage());
    //         return false;
    //     }
    // }

    // public function exceedsDailyWithdrawalLimit($userId, $amount) {
    //     try {
    //         // Get the total withdrawal amount made by the user today
    //         $stmt = $this->db->prepare("SELECT SUM(amount) AS total_withdrawal_amount FROM savings_transactions WHERE user_id = ? AND DATE(date_time) = CURDATE() AND transaction_type = 'Withdrawal'");
    //         $stmt->execute([$userId]);
    //         $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //         $totalWithdrawalAmount = $result['total_withdrawal_amount'];

    //         // Check if the total withdrawal amount plus the requested amount exceeds the daily limit ($5000)
    //         return ($totalWithdrawalAmount + $amount) > 5000;
    //     } catch (PDOException $e) {
    //         // Handle database error
    //         error_log("Error in exceedsDailyWithdrawalLimit(): " . $e->getMessage());
    //         return false;
    //     }
    // }
}
?>
