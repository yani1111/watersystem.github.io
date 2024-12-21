<?php
require_once '../phpFolder/database.php';

class Client {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    // Add a new client to the database
    public function addClient($fname, $lname, $address, $email, $prepaidLimit) {
        try {
            $query = "INSERT INTO clients (fname, lname, address, email, prepaid_limit, remaining_water) 
                      VALUES (?, ?, ?, ?, ?, prepaid_limit)";
            $stmt = $this->conn->prepare($query);
            
            // Ensure the values are correctly bound to the placeholders
            return $stmt->execute([$fname, $lname, $address, $email, $prepaidLimit]);
        } catch (PDOException $e) {
            echo "Error adding client: " . $e->getMessage();
            return false;
        }
    }

    // Fetch all clients from the database
    public function getClients() {
        $query = "SELECT * FROM clients";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to add amount to a client for admin-home & admin-view
    public function addAmount($clientId, $amountPaid) {
        try {
            // Conversion rate: 5 cubic meters = 250 pesos
            $conversionRate = 250 / 5; // 50 pesos per cubic meter
            $cubicMeters = $amountPaid / $conversionRate; // Calculate cubic meters
            
            // Begin transaction to ensure atomicity of both operations
            $this->conn->beginTransaction();
            
            // Update the client's prepaid_limit in the clients table
            $sqlClientUpdate = "UPDATE clients
                                SET prepaid_limit = prepaid_limit + :cubicMeters, amount_paid = amount_paid + :amountPaid,
                                    last_payment = NOW()
                                WHERE client_id = :clientId";
            
            // Prepare the query to update client’s prepaid limit
            $stmtClientUpdate = $this->conn->prepare($sqlClientUpdate);
            
            // Bind parameters for client update
            $stmtClientUpdate->bindParam(':cubicMeters', $cubicMeters, PDO::PARAM_STR);
            $stmtClientUpdate->bindParam(':amountPaid', $amountPaid, PDO::PARAM_STR);
            $stmtClientUpdate->bindParam(':clientId', $clientId, PDO::PARAM_INT);
            
            // Execute the query to update the client's prepaid limit
            $stmtClientUpdate->execute();

            // Insert payment details into the payments table
            $sqlPayment = "INSERT INTO payment_history (client_id, ph_date, amount_paid)
                           VALUES (:clientId, NOW(), :amountPaid)";
            
            // Prepare the query for payment insertion
            $stmtPayment = $this->conn->prepare($sqlPayment);
            
            // Bind parameters for payment insertion
            $stmtPayment->bindParam(':clientId', $clientId, PDO::PARAM_INT);  // Foreign key from clients table
            $stmtPayment->bindParam(':amountPaid', $amountPaid, PDO::PARAM_STR);
            
            // Execute the query to insert payment
            $stmtPayment->execute();

            // Commit the transaction to ensure both queries are executed together
            $this->conn->commit();
            
            return true; // Indicating the operation was successful
        } catch (PDOException $e) {
            // Rollback in case of any errors
            $this->conn->rollBack();
            // Handle any exceptions
            echo "Error adding payment and updating client: " . $e->getMessage();
            return false;
        }
    }






    public function recordWaterUsage($clientId, $waterUsed) {
    try {
        // Begin transaction to ensure atomicity
        $this->conn->beginTransaction();

        // Retrieve current values for remaining_water, prepaid_limit, and water_used
        $sqlSelect = "SELECT remaining_water, prepaid_limit, water_used FROM clients WHERE client_id = :clientId";
        $stmtSelect = $this->conn->prepare($sqlSelect);
        $stmtSelect->bindParam(':clientId', $clientId, PDO::PARAM_INT);
        $stmtSelect->execute();
        $clientData = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if (!$clientData) {
            throw new Exception("Client not found.");
        }

        $remainingWater = $clientData['remaining_water'];
        $prepaidLimit = $clientData['prepaid_limit'];
        $totalWaterUsed = $clientData['water_used'];

        // If the remaining water is still greater than 0, deduct from it
        if ($remainingWater > 0) {
            // Deduct the used water from the remaining water
            $newRemainingWater = $remainingWater - $waterUsed;

            if ($newRemainingWater < 0) {
                // If remaining water goes negative, calculate how much prepaid limit to deduct
                $newPrepaidLimit = $prepaidLimit + $newRemainingWater; // Adjust prepaid limit accordingly
                $newRemainingWater = 0; // Set remaining water to 0
            } else {
                // If there is still remaining water, no need to adjust prepaid limit
                $newPrepaidLimit = $prepaidLimit;
            }
        } else {
            // If remaining water is 0, deduct water usage from prepaid limit
            $newRemainingWater = 0;
            $newPrepaidLimit = $prepaidLimit - $waterUsed; // Deduct directly from prepaid limit
        }

        // Update the total water used
        $updatedWaterUsed = $totalWaterUsed + $waterUsed;

        // Update the client's remaining water, prepaid limit, water used, and usage date
        $sqlUpdate = "UPDATE clients
                      SET remaining_water = :newRemainingWater,
                          prepaid_limit = :newPrepaidLimit,
                          water_used = :updatedWaterUsed,
                          usage_date = NOW()
                      WHERE client_id = :clientId";

        $stmtUpdate = $this->conn->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':newRemainingWater', $newRemainingWater, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':newPrepaidLimit', $newPrepaidLimit, PDO::PARAM_INT); // or PDO::PARAM_DECIMAL if applicable
        $stmtUpdate->bindParam(':updatedWaterUsed', $updatedWaterUsed, PDO::PARAM_INT); // or PDO::PARAM_DECIMAL if applicable
        $stmtUpdate->bindParam(':clientId', $clientId, PDO::PARAM_INT);

        // Execute the update query
        $stmtUpdate->execute();

        // Commit the transaction
        $this->conn->commit();

        return true; // Indicating the operation was successful
    } catch (Exception $e) {
        // Rollback in case of any errors
        $this->conn->rollBack();
        // Handle exceptions
        echo "Error recording water usage: " . $e->getMessage();
        return false;
    }
}

    
    
    
    
    // for admin-view
    public function getClientById($id) {
        $sql = "SELECT * FROM clients WHERE client_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateClient($id, $fname, $mname, $lname, $username, $password, $address, $email, $phone_num) {
        $sql = "UPDATE clients 
                SET fname = ?, mname = ?, lname = ?, username = ?, password = ?, address = ?, email = ?, phone_num = ?
                WHERE client_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$fname, $mname, $lname, $username, password_hash($password, PASSWORD_DEFAULT), $address, $email, $phone_num, $id]);
    }

    // Get payment history for a specific client
    public function getPaymentHistory($clientId) {
        $sql = "SELECT ph_date, amount_paid, ph_status FROM payment_history WHERE client_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteClient($id) {
        $sql = "DELETE FROM clients WHERE client_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
    
}
