<?php
require_once 'config.php';

class withdraw{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function requestWithdrawal($savingsId, $amount) {
        // Check if user has exceeded the daily withdrawal limit
        $withdrawalsToday = $this->getWithdrawalsToday($savingsId);
        if ($withdrawalsToday >= 5) {
            return "You have reached the maximum number of withdrawals for today.";
        }

        // Check if withdrawal amount is within the allowed range
        if ($amount < 500 || $amount > 5000) {
            return "Withdrawal amount must be between 500 and 5000.";
        }

        // Insert withdrawal request into the savingstransaction table
        $transactionId = uniqid(); // Generate unique transaction ID
        $status = 'Pending';
        $dateTime = date('Y-m-d');
        $stmt = $this->db->prepare("INSERT INTO savingstransaction (transaction_id, savings_id, transaction_type, amount, last_amount, status, date_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([$transactionId, $savingsId, 'Withdrawal', $amount, null, $status, $dateTime]);

        if ($success) {
            return "Withdrawal request submitted successfully.";
        } else {
            return "Failed to submit withdrawal request.";
        }
    }

    private function getWithdrawalsToday($savingsId) {
        // Get the number of withdrawals made today
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM savingstransaction WHERE savings_id = ? AND transaction_type = 'Withdrawal' AND date_time = ?");
        $stmt->execute([$savingsId, $today]);
        return $stmt->fetchColumn();
    }
}
?>
