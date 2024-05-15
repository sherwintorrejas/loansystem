<?php
include_once('config.php');

class LoanTransacModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Method to fetch all transactions with associated loan amount and payable months
    public function getAllTransactionsWithLoanAmountAndMonths() {
        try {
            // Prepare the SQL statement to fetch all loan transactions with associated loan amount, payable months, and status
            $stmt = $this->db->prepare("SELECT lt.lntranid, lt.loan_id, lt.transaction_type, lt.amount_increase, lt.payable_months_increase, lt.admin_remarks, lt.transaction_date, l.loan_amount AS LoanAmount, l.payable_months AS PayableMonths, l.status AS LoanStatus
                                        FROM loan_transactions lt
                                        INNER JOIN loans l ON lt.loan_id = l.loanid");
            // Execute the query
            $stmt->execute();
            // Fetch all loan transactions with associated loan amount, payable months, and status
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $transactions;
        } catch(PDOException $e) {
            // Handle database errors
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    

    // Method to record a new transaction
    public function recordTransaction($loanId, $userId, $transactionType, $transactionDate, $status, $details) {
        try {
            // Prepare the SQL statement to insert a new transaction
            $stmt = $this->db->prepare("INSERT INTO loan_transactions (lntranid, loan_id, transaction_type, amount_increase, payable_months_increase, admin_remarks, transaction_date) VALUES (NULL, :LoanID, :TransactionType, NULL, NULL, NULL, :TransactionDate)");
            // Bind parameters
            $stmt->bindParam(':LoanID', $loanId);
            $stmt->bindParam(':TransactionType', $transactionType);
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
