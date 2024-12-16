<?php
session_start();
if (!isset($_SESSION['loggedin']) && $_SESSION['role'] !== 'Manager') {
	header('Location: ../index.php');
	exit;
}

require_once '../includes/dbconnect.php';

$db = new Database();
$conn = $db->getConnection();
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Manager</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
		integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
		crossorigin="anonymous"
		referrerpolicy="no-referrer" />
	<link href="../styles.css" rel="stylesheet" type="text/css">
</head>

<body>
	<nav>
		<img src="../assets/logo.png">
		<div class="roleTag_loguitBtn">
			<span>Manager</span>
			<a href="../process/logout.php">Uitloggen</a>
		</div>
	</nav>

	<section class="managerPagina">

		<form method="GET" action="" class="formGenereerOverzichtAttractie">
			<h2><i class="fa-solid fa-wrench"></i> Attracties Onderhoud</h2>
			<p class="genereerOverzichtTekst">Genereer hier een overzicht van het onderhoud van de attracties.</p>
			<label for="start">Startdatum:</label>
			<input type="date" name="start" required>
			<label for="end">Einddatum:</label>
			<input type="date" name="end" required>
			<button type="submit">Toon Overzicht</button>
		</form>

		<p class="overzicht_p"><i class="fa-solid fa-table"></i> Overzicht</p>
		<div class="overzichtAttractieTabel">
			<table>
				<thead>
					<tr>
						<th>Attractie</th>
						<th>Onderhoudsdatum</th>
						<th>Status</th>
						<th>Opmerkingen</th>
						<th>Monteur</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if (isset($_GET['start']) && isset($_GET['end'])) {
						$start = $_GET['start'];
						$end = $_GET['end'];

						$startDate = new DateTime($start);
						$endDate = new DateTime($end);

						$formattedStart = $startDate->format('d-m-Y');
						$formattedEnd = $endDate->format('d-m-Y');

						echo "<p class='gegenereerdeOverzicht_p'>Van {$formattedStart} tot {$formattedEnd}.</p>";

						$sql = "
                    SELECT 
                        a.Naam AS AttractieNaam,
                        o.Datum AS Onderhoudsdatum,
                        o.Status AS Onderhoudsstatus,
                        o.Opmerkingen,
                        p.Naam AS MonteurNaam
                    FROM
                        attractie a
                    JOIN 
                        onderhoudstaak o ON a.ID = o.AttractieID
                    LEFT JOIN 
                        personeel p ON o.MonteurID = p.ID
                    WHERE 
                        o.Datum BETWEEN '$start' AND '$end'
                    ORDER BY 
                        o.Datum ASC
                    ";

						if ($result = $conn->query($sql)) {
							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									echo "<tr>
                                    <td>{$row['AttractieNaam']}</td>
                                    <td>{$row['Onderhoudsdatum']}</td>
                                    <td>{$row['Onderhoudsstatus']}</td>
                                    <td>{$row['Opmerkingen']}</td>
                                    <td>{$row['MonteurNaam']}</td>
                                  </tr>";
								}
							} else {
								echo "<tr><td colspan='5'>Geen resultaten gevonden</td></tr>";
							}
						} else {
							echo "<tr><td colspan='5'>Er is een fout opgetreden bij het ophalen van de gegevens.</td></tr>";
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
		if (isset($_SESSION['success_message'])) {
			echo '<div class="success_message">' . $_SESSION['success_message'] . '</div>';
			unset($_SESSION['success_message']);
		}

		if (isset($_SESSION['error_message'])) {
			echo '<div class="error_message">' . $_SESSION['error_message'] . '</div>';
			unset($_SESSION['error_message']);
		}
		?>
		<h2 class="personeelH2"><i class="fa-solid fa-users"></i> Personeel</h2>
		<input type="text" class="search_field" placeholder="Zoek naar..." onkeyup="zoekPersoneel()">

		<div class="personeelContainer">
			<?php
			require_once '../models/Personeel.php';

			$personeel = new Personeel();

			$personeelContent = $personeel->getAllPersoneel();

			// Zorg ervoor dat het formulier correct wordt verwerkt
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				// Controleer of de 'wijzig_rol' knop is ingedrukt
				if (isset($_POST['wijzig_rol'])) {
					$role_id = $_POST['ID']; // Haal de ID van de medewerker op
					$new_role = $_POST['rol']; // Haal de nieuwe rol op

					// Voer de update van de rol uit
					if ($personeel->updateRole($role_id, $new_role)) {
						$_SESSION['success_message'] = 'Rol succesvol gewijzigd.';
						header('Location: managerPagina.php');
						exit;
					} else {
						$_SESSION['error_message'] = 'Fout bij het wijzigen van de rol.';
					}
				}

				// Verwijder medewerker
				if (isset($_POST['verwijder_medewerker'])) {
					$delete_id = $_POST['delete_id'];

					if ($personeel->deletePersoneel($delete_id)) {
						$_SESSION['success_message'] = 'Medewerker succesvol verwijderd.';
						header('Location: managerPagina.php');
						exit;
					} else {
						$_SESSION['error_message'] = 'Fout bij het verwijderen van de medewerker.';
					}
				}
			}

			// Loop door de medewerkers en voeg de wijzig- en verwijderknoppen toe
			if ($personeelContent->num_rows > 0) {
				while ($row = $personeelContent->fetch_assoc()) {
					echo '<div class="personeelKaart">';
					echo '<h3>' . '<i class="fa-solid fa-circle-user"></i>' . htmlspecialchars($row['Naam']) . '</h3>';
					echo '<p><strong>Gebruikersnaam:</strong> ' . htmlspecialchars($row['Gebruikersnaam']) . '</p>';
					echo '<p><strong>Wachtwoord:</strong> <span class="password">' . htmlspecialchars($row['Wachtwoord']) . '</span></p>';
					echo '<p><strong>Adres:</strong> ' . htmlspecialchars($row['Adres']) . '</p>';
					echo '<p><strong>Telefoonnummer:</strong> ' . htmlspecialchars($row['Telefoonnummer']) . '</p>';
					echo '<p><strong>Woonplaats:</strong> ' . htmlspecialchars($row['Woonplaats']) . '</p>';
					echo '<p><strong>Rol:</strong> ' . htmlspecialchars($row['Rol']) . '</p>';

					// Wijzigen van de rol
					echo '<form method="POST" action="" class="form_wijzig_rol">';
					echo '<input type="hidden" name="ID" value="' . htmlspecialchars($row['ID']) . '">';
					echo '<select name="rol" required>';
					echo '<option value="Beheerder" ' . ($row['Rol'] == 'Beheerder' ? 'selected' : '') . '>Beheerder</option>';
					echo '<option value="Monteur" ' . ($row['Rol'] == 'Monteur' ? 'selected' : '') . '>Monteur</option>';
					echo '<option value="Manager" ' . ($row['Rol'] == 'Manager' ? 'selected' : '') . '>Manager</option>';
					echo '</select>';
					echo '<button type="submit" name="wijzig_rol" class="wijzig_rol_btn">Wijzig rol</button>';
					echo '</form>';

					// Verwijderen van de medewerker
					echo '<form method="POST" action="" class="form_verwijder_personeel">';
					echo '<input type="hidden" name="delete_id" value="' . htmlspecialchars($row['ID']) . '">';
					echo '<button type="submit" name="verwijder_medewerker" class="verwijder_medewerker_btn" onclick="return confirm(\'Weet je zeker dat je deze medewerker wilt verwijderen?\')">Verwijder medewerker</button>';
					echo '</form>';
					echo '</div>';
				}
			} else {
				echo '<p>Geen personeel gevonden.</p>';
			}
			?>

		</div>

	</section>

	<footer>© hollowmountains</footer>

	<script src="../script.js"></script>
</body>

</html>