<?php

class Attracties
{
    private $conn;

    public function __construct()
    {
        require_once dirname(__DIR__) . '/includes/dbconnect.php';
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Haal alle attracties op
    public function getAllAttracties()
    {
        $query = "SELECT * FROM attractie";
        return $this->conn->query($query);
    }

    public function getConnection()
    {
        return $this->conn;
    }
    
    // Attractie toevoegen
    public function uploadAttractie($naam, $locatie, $type, $specificaties, $foto)
    {
        $uploadMap = '../uploads/';

        if (isset($foto) && $foto['error'] === UPLOAD_ERR_OK) {
            $fotoTmpPath = $foto['tmp_name'];
            $fotoNaam = basename($foto['name']);
            $fotoUploadPad = $uploadMap . $fotoNaam;

            if (move_uploaded_file($fotoTmpPath, $fotoUploadPad)) {
                $query = "INSERT INTO attractie (Naam, Locatie, Type, Foto, TechnischeSpecificaties) 
                          VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($query);

                if ($stmt) {
                    $stmt->bind_param('sssss', $naam, $locatie, $type, $fotoNaam, $specificaties);

                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Attractie succesvol toegevoegd.';
                    } else {
                        $_SESSION['error_message'] = 'Fout bij het opslaan in de database: ' . $stmt->error;
                    }
                } else {
                    $_SESSION['error_message'] = 'Fout bij het voorbereiden van de query.';
                }
            } else {
                $_SESSION['error_message'] = 'Fout bij het uploaden van de foto.';
            }
        } else {
            $_SESSION['error_message'] = 'Upload een geldige foto.';
        }
    }

    public function updateAttractie($id, $naam, $locatie, $type, $specificaties, $foto)
    {
        $uploadMap = '../../uploads/';
        $query = "UPDATE attractie SET Naam = ?, Locatie = ?, Type = ?, TechnischeSpecificaties = ?";

        // Controleer of er een nieuwe foto is geüpload
        if (isset($foto) && $foto['error'] === UPLOAD_ERR_OK) {
            $fotoTmpPath = $foto['tmp_name'];
            $fotoNaam = basename($foto['name']);
            $fotoUploadPad = $uploadMap . $fotoNaam;
            if (move_uploaded_file($fotoTmpPath, $fotoUploadPad)) {
                $query .= ", Foto = ?";
            } else {
                $_SESSION['error_message'] = 'Fout bij het uploaden van de foto.';
                return false;
            }
        }
        
        $query .= " WHERE ID = ?";
        $stmt = $this->conn->prepare($query);

        // Bind parameters inclusief de bestandsnaam van de foto als er een nieuwe foto is geüpload
        if (isset($foto) && $foto['error'] === UPLOAD_ERR_OK) {
            $stmt->bind_param('sssssi', $naam, $locatie, $type, $specificaties, $fotoNaam, $id);
        } else {
            $stmt->bind_param('ssssi', $naam, $locatie, $type, $specificaties, $id);
        }

        $result = $stmt->execute();

        if ($result) {
            $_SESSION['success_message'] = 'Attractie succesvol gewijzigd.';
        } else {
            $_SESSION['error_message'] = 'Fout bij het wijzigen van de attractie.';
        }

        return $result;
    }

    public function deleteAttractie($id)
    {
        $query = "DELETE FROM attractie WHERE ID = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Attractie succesvol verwijderd';
            } else {
                $_SESSION['error_message'] = 'Het verwijderen van de attractie is mislukt';
            }
        } else {
            $_SESSION['error_message'] = 'Kon de query niet voorbereiden';
        }
    }

    // Destructor om de databaseverbinding te sluiten
    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
