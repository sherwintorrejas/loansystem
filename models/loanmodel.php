<?php
include_once('config.php');

class LoanModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getLoansByUserId($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM loans WHERE user_id = :UserID");
            $stmt->bindParam(':UserID', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    public function applyLoan($userId, $amount, $duration) {
        try {
            // Calculate the remaining payment based on the loan amount
            $remainingPayment = $amount;
    
            // Prepare the SQL statement to insert loan application
            $stmt = $this->db->prepare("INSERT INTO loans (loanid, user_id, loan_amount, payable_months, status) VALUES (NULL, :UserID, :LoanAmount, :PayableMonths, 'Pending')");
            // Bind parameters
            $stmt->bindParam(':UserID', $userId);
            $stmt->bindParam(':LoanAmount', $amount);
            $stmt->bindParam(':PayableMonths', $duration);
            // Execute the query
            $stmt->execute();
            // Return the inserted loan ID
            $loanId = $this->db->lastInsertId();
    
            // Record the loan application as a transaction
            $transactionType = 'Increase'; // Assuming 'Increase' represents a loan application
            $amountIncrease = $amount;
            $payableMonthsIncrease = $duration;
            $adminRemarks = 'Loan application submitted';
            $transactionDate = date('Y-m-d H:i:s');
    
            $this->recordLoanTransaction($loanId, $transactionType, $amountIncrease, $payableMonthsIncrease, $adminRemarks, $transactionDate);
    
            return $loanId;
        } catch(PDOException $e) {
            // Handle database errors
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    
    
    public function getLoanDetailsByUserId($userId) {
        try {
            // Prepare the SQL statement to fetch loan details by user ID
            $stmt = $this->db->prepare("SELECT * FROM loans WHERE user_id = :UserID");
            // Bind parameters
            $stmt->bindParam(':UserID', $userId);
            // Execute the query
            $stmt->execute();
            // Fetch loan details
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // Handle database errors
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    public function getLoanById($loanId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM loans WHERE loanid = :LoanID");
            $stmt->bindParam(':LoanID', $loanId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    public function updateRemainingPayment($loanId, $remainingPayment) {
        try {
            $stmt = $this->db->prepare("UPDATE loans SET RemainingPayment = :RemainingPayment WHERE loanid = :LoanID");
            $stmt->bindParam(':RemainingPayment', $remainingPayment);
            $stmt->bindParam(':LoanID', $loanId);
            $stmt->execute();
            return true;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    private function recordLoanTransaction($loanId, $transactionType, $amountIncrease, $payableMonthsIncrease, $adminRemarks, $transactionDate) {
        try {
            // Prepare the SQL statement to insert a new transaction
            $stmt = $this->db->prepare("INSERT INTO loan_transactions (loan_id, transaction_type, amount_increase, payable_months_increase, admin_remarks, transaction_date) VALUES (:LoanID, :TransactionType, :AmountIncrease, :PayableMonthsIncrease, :AdminRemarks, :TransactionDate)");
            // Bind parameters
            $stmt->bindParam(':LoanID', $loanId);
            $stmt->bindParam(':TransactionType', $transactionType);
            $stmt->bindParam(':AmountIncrease', $amountIncrease);
            $stmt->bindParam(':PayableMonthsIncrease', $payableMonthsIncrease);
            $stmt->bindParam(':AdminRemarks', $adminRemarks);
            $stmt->bindParam(':TransactionDate', $transactionDate);
            // Execute the query
            $stmt->execute();
            // Return true if successful
            return true;
        } catch(PDOException $e) {
            // Handle database errors
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    
}
?>
