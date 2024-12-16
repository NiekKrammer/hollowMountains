<?php
session_start();
ob_start();

if (!isset($_SESSION['loggedin']) && $_SESSION['role'] !== 'Beheerder') {
    header('Location: ../../index.php');
    exit;
}

require_once '../../models/Attracties.php';
require_once '../../models/Personeel.php';
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

$personeel = new Personeel();
$monteurs = $personeel->getMonteurs();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['onderhoudschema_toevoegen'])) {
    // Verkrijg de ingevoerde gegevens
    $taken = $_POST['taken'];
    $monteurId = $_POST['monteur'];
    $datum = $_POST['Datum'];
    $frequentie = $_POST['frequentie'];  // Verkrijg de frequentie uit het formulier
    $attractieId = $_GET['id']; // Het attractie ID komt uit de URL

    // Voeg een nieuwe onderhoudstaak toe
    $insertTaskQuery = "INSERT INTO onderhoudstaak (Datum, Status, Opmerkingen, MonteurID, AttractieID) 
                        VALUES (?, 'In Behandeling', ?, ?, ?)";
    $stmt = $conn->prepare($insertTaskQuery);
    $stmt->bind_param('ssii', $datum, $taken, $monteurId, $attractieId);

    if ($stmt->execute()) {
        // Voeg een nieuwe onderhoudschema in de onderhoudschema tabel toe
        $taskId = $stmt->insert_id; // Haal het ID van de net ingevoerde taak op

        $insertSchemaQuery = "INSERT INTO onderhoudschema (Frequentie, AttractieID, OnderhoudstaakID)
                              VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertSchemaQuery);
        $stmt->bind_param('sii', $frequentie, $attractieId, $taskId); // Voeg frequentie toe aan query

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Onderhoudsschema succesvol toegevoegd.';
        } else {
            $_SESSION['error_message'] = 'Fout bij het toevoegen van onderhoudschema.';
        }
    } else {
        $_SESSION['error_message'] = 'Fout bij het toevoegen van onderhoudstaak.';
    }

    // Redirect naar de beheerderpagina of de huidige pagina
    header('Location: ../beheerderPagina.php');
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Wijzig Schema</title>
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

    <h1>Onderhoudsschema</h1>

    <section class="beheerderPagina onderhoudsschema">
    
        <div>
            <form action="" method="POST" enctype="multipart/form-data" class="upload_attractie_form">
                <h2>Onderhoudsschema aanmaken:</h2>
                <p class="nameRide"><?php echo htmlspecialchars($row['Naam']); ?></p>
                <p>Attractie ID: <?php echo htmlspecialchars($attractieId); ?></p>
                <img src="../../uploads/<?php echo htmlspecialchars($row['Foto']); ?>" class="attractieFotoOnderhoudsschema">
                <br>
                <br>
                <label for="taken">Taken</label><br>
                <textarea id="taken" name="taken" rows="5" cols="50" required></textarea><br>
                <label for="monteur">Selecteer een monteur</label><br>
                <select id="monteur" name="monteur" required>
                    <option value="">-- Kies een monteur --</option>
                    <?php while ($monteur = $monteurs->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($monteur['ID']); ?>">
                            <?php echo htmlspecialchars($monteur['Naam']); ?>
                        </option>
                    <?php endwhile; ?>
                </select><br>

                <label for="startdatum">Datum</label>
                <input type="date" id="startdatum" name="Datum" required>
                <label for="frequentie">Frequentie</label><br>
                <select id="frequentie" name="frequentie" required>
                    <option value="dagelijks">Dagelijks</option>
                    <option value="wekelijks">Wekelijks</option>
                    <option value="maandelijks">Maandelijks</option>
                </select><br><br>
                <button class="submit_btn" type="submit" name="onderhoudschema_toevoegen">Voeg onderhoudschema toe</button>
            </form>
        </div>

        <div>
            <h3>Onderhoudsoverzicht <span>'<?php echo htmlspecialchars($row['Naam']); ?>'</span>
            </h3>
            <?php
            $queryOnderhoudstaken = "SELECT * FROM onderhoudstaak WHERE AttractieID = ?";
            $stmtOnderhoud = $conn->prepare($queryOnderhoudstaken);

            if ($stmtOnderhoud) {
                $stmtOnderhoud->bind_param('i', $attractieId);
                $stmtOnderhoud->execute();
                $resultOnderhoud = $stmtOnderhoud->get_result();
            } else {
                $_SESSION['error_message'] = 'Fout bij het ophalen van onderhoudstaken: ' . $conn->error;
            }

            if ($resultOnderhoud->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Status</th>
                            <th>Opmerkingen</th>
                            <th>Monteur ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($taak = $resultOnderhoud->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($taak['Datum']); ?></td>
                                <td><?php echo htmlspecialchars($taak['Status']); ?></td>
                                <td><?php echo htmlspecialchars($taak['Opmerkingen']); ?></td>
                                <td><?php echo htmlspecialchars($taak['MonteurID']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Geen onderhoudstaken gevonden voor deze attractie.</p>
            <?php endif; ?>
        </div>

    </section>
    <a href="../beheerderPagina.php" class="goBackBtn"><i class="fa-solid fa-left-long"></i> Ga Terug</a>

    <footer>
        <p>© hollowmountains</p>
    </footer>

</body>

</html>