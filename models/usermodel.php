<?php
include_once('config.php');

class UserModel {
    private $db;

    public function __construct() {
        // Get the existing database connection
        $this->db = Database::getInstance()->getConnection();
    }

    // Method to fetch user data by user ID
    public function getUserData($userId) {
        try {
            // Prepare the SQL statement
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        
            $stmt->bindParam(':id', $userId);
           
            $stmt->execute();
           
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            return $userData;
        } catch(PDOException $e) {
           
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
?>
