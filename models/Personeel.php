<?php
class Personeel
{
    private $conn;

    public function __construct()
    {
        require_once dirname(__DIR__) . '/includes/dbconnect.php';
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Haal alle personeel gegevens op 
    public function getAllPersoneel()
    {
        $query = "SELECT * FROM personeel";
        return $this->conn->query($query);
    }

    // Update de rol in de database
    public function updateRole($role_id, $new_role)
    {
        $updateQuery = "UPDATE personeel SET Rol = ? WHERE ID = ?";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->bind_param('si', $new_role, $role_id);
        $result = $stmt->execute();

        if ($result) {
            $_SESSION['success_message'] = 'Rol succesvol gewijzigd.';
        } else {
            $_SESSION['error_message'] = 'Fout bij het wijzigen van de rol.';
        }

        return $result;
    }

    // Verwijderen medewerker
    public function deletePersoneel($id)
    {
        $query = "DELETE FROM personeel WHERE ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Haal alleen monteurs op
    public function getMonteurs()
    {
        $query = "SELECT ID, Naam FROM personeel WHERE Rol = 'Monteur'";
        return $this->conn->query($query);
    }

    // Destructor om de databaseverbinding te sluiten
    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
