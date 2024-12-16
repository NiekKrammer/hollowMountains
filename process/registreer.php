<?php
session_start();
require '../includes/dbconnect.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_POST['gebruikersnaam'], $_POST['wachtwoord'], $_POST['adres'], $_POST['telefoonnummer'], $_POST['woonplaats'])) {
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];
    $adres = $_POST['adres'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $woonplaats = $_POST['woonplaats'];

    if (empty($gebruikersnaam) || empty($wachtwoord) || empty($adres) || empty($telefoonnummer) || empty($woonplaats)) {
        $_SESSION['error'] = 'Vul alle velden in';
        header('Location: ../index.php'); 
        exit;
    }   

    $hashedPassword = password_hash($wachtwoord, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO personeel (Naam, Gebruikersnaam, Wachtwoord, Adres, Telefoonnummer, Woonplaats) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssss', $gebruikersnaam, $gebruikersnaam, $hashedPassword, $adres, $telefoonnummer, $woonplaats );
    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Registratie voltooid! Je kunt nu inloggen.';
        header('Location: ../index.php');
        exit;
    } else {
        $_SESSION['error'] = 'Er is iets misgegaan, probeer het opnieuw';
        header('Location: ../index.php'); 
        exit;
    }

    $stmt->close();
}
?>
