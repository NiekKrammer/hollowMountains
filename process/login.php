<?php
session_start();
require '../includes/dbconnect.php';

// Initialiseer de databaseklasse en haal de connectie op
$db = new Database();
$conn = $db->getConnection();

if (isset($_POST['username'], $_POST['password'])) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $_SESSION['error'] = 'Vul zowel het gebruikersnaam- als wachtwoordveld in';
        header('Location: ../index.php');
        exit;
    }

    // Bereid de query voor met de opgehaalde verbinding
    if ($stmt = $conn->prepare('SELECT id, Wachtwoord, Rol FROM personeel WHERE Gebruikersnaam = ?')) {
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $password, $role);
            $stmt->fetch();
            if (password_verify($_POST['password'], $password)) {
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['username'];
                $_SESSION['id'] = $id;
                $_SESSION['role'] = $role;

                // Stuur de gebruiker naar de juiste pagina op basis van de rol
                switch ($role) {
                    case 'Beheerder':
                        header('Location: ../rollenPaginas/beheerderPagina.php');
                        break;
                    case 'Manager':
                        header('Location: ../rollenPaginas/managerPagina.php');
                        break;
                    case 'Monteur':
                        header('Location: ../rollenPaginas/monteurPagina.php');
                        break;
                    default:
                        header('Location: ../index.php');
                }
                exit;
            }
        }
        $_SESSION['error'] = 'Ongeldige inloggegevens';
        header('Location: ../index.php');
        exit;

        $stmt->close();
    }
}

// Sluit de databaseverbinding
$db->disconnect();
?>
