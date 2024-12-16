<?php

class Onderhoudstaak
{
    private $conn;

    public function __construct()
    {
        require_once dirname(__DIR__) . '/includes/dbconnect.php';
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Haal alle onderhoudstaken op
    public function getAllOnderhoudstaken()
    {
        $query = "SELECT * FROM onderhoudstaak";
        return $this->conn->query($query);
    }

    // Haal de taken op per monteur
    public function getTakenVoorMonteur($monteurID)
    {
        $query = "SELECT * FROM onderhoudstaak WHERE MonteurID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $monteurID);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Update de status van een onderhoudstaak
    public function updateStatus($task_id, $status, $opmerkingen)
    {
        $stmt = $this->conn->prepare("UPDATE onderhoudstaak SET Status = ?, Opmerkingen = ? WHERE ID = ?");
        $stmt->bind_param("ssi", $status, $opmerkingen, $task_id);
        $result = $stmt->execute();

        if ($result) {
            $_SESSION['success_message'] = 'Taak succesvol gewijzigd.';
        } else {
            $_SESSION['error_message'] = 'Fout bij het wijzigen van de taak.';
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Destructor om de databaseverbinding te sluiten
    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
