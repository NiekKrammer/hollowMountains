<?php
session_start();
ob_start();

if (!isset($_SESSION['loggedin']) && $_SESSION['role'] !== 'Beheerder') {
    header('Location: ../../index.php');
    exit;
}

require_once '../../models/Attracties.php';
require_once '../../includes/dbconnect.php';

// Maak een instantie van de Database klasse en verkrijg de verbinding
$db = new Database();
$conn = $db->getConnection();

// Maak een instantie van de Attracties klasse
$attracties = new Attracties();

// Haal de huidige gegevens op
if (isset($_GET['id'])) {
    $attractieId = $_GET['id'];
    $query = "SELECT * FROM attractie WHERE ID = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $attractieId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            $_SESSION['error_message'] = 'Attractie niet gevonden';
            header('Location: beheerderPagina.php');
            exit;
        }
    } else {
        $_SESSION['error_message'] = 'Fout bij het ophalen van de attractie: ' . $conn->error;
        header('Location: beheerderPagina.php');
        exit;
    }
} else {
    $_SESSION['error_message'] = 'Geen attractie ID opgegeven';
    header('Location: beheerderPagina.php');
    exit;
}

// Verwerk het formulier om de gegevens bij te werken
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bijwerken van een attractie
    if (isset($_POST['update_attractie'])) {
        // Verkrijg de nieuwe gegevens uit het formulier
        $naam = $_POST['naam'];
        $locatie = $_POST['locatie'];
        $type = $_POST['type'];
        $specificaties = $_POST['specificaties'];
        $foto = $_FILES['foto'];  // De geüploade foto

        // Verkrijg het ID van de attractie
        $id = $_POST['id'];

        // Update de attractie met of zonder foto
        $attracties->updateAttractie($id, $naam, $locatie, $type, $specificaties, $foto);

        // Redirect na de update
        header('Location: ../beheerderPagina.php');
        exit;
    }

    // Verwijderen van een attractie
    if (isset($_POST['delete_attractie'])) {
        $deleteAttractieId = $_POST['delete_attractie'];
        $attracties->deleteAttractie($deleteAttractieId);
        header('Location: ../beheerderPagina.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Wijzig Attractie</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../styles.css" rel="stylesheet" type="text/css">
</head>

<body>

    <nav>
        <img src="../../assets/logo.png">
        <div class="roleTag_loguitBtn">
            <span>Beheerder</span>
            <a href="../process/logout.php">uitloggen</a>
        </div>
    </nav>

    <section class="beheerderPagina">
        <h1>Wijzig Attractie</h1>
        <form action="" method="POST" enctype="multipart/form-data" class="upload_attractie_form">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['ID']); ?>">

            <label for="naam">Naam</label><br>
            <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($row['Naam']); ?>" required><br><br>

            <label for="locatie">Locatie</label><br>
            <input type="text" id="locatie" name="locatie" value="<?php echo htmlspecialchars($row['Locatie']); ?>" required><br><br>

            <label for="type">Type</label><br>
            <input type="text" id="type" name="type" value="<?php echo htmlspecialchars($row['Type']); ?>" required><br><br>

            <label for="specificaties">Technische Specificaties</label><br>
            <textarea id="specificaties" name="specificaties" rows="4" required><?php echo htmlspecialchars($row['TechnischeSpecificaties']); ?></textarea><br><br>

            <label for="foto">Foto</label><br>
            <input type="file" id="foto" name="foto" accept="image/*"><br><br>
            <img src="../../uploads/<?php echo htmlspecialchars($row['Foto']); ?>" alt="Huidige Foto"><br><br>

            <button class="submit_btn" type="submit" name="update_attractie">Voeg wijzigingen toe</button>

            <button type="submit" class="delete_attractie_btn" name="delete_attractie" value="<?php echo htmlspecialchars($row['ID']); ?>" onclick="return confirm('Weet je zeker dat je deze attractie wilt verwijderen?')">
                Verwijder Attractie
            </button>
        </form>
        
    </section>
    
    <a href="../beheerderPagina.php" class="goBackBtn"><i class="fa-solid fa-left-long"></i> Ga Terug</a>
    
    <footer>
        <p>© hollowmountains</p>
    </footer>
</body>

</html>