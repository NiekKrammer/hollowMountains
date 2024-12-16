<?php
session_start();
ob_start();

if (!isset($_SESSION['loggedin']) && $_SESSION['role'] !== 'Beheerder') {
	header('Location: ../index.php');
	exit;
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Beheerder</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link
		rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
		integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
		crossorigin="anonymous"
		referrerpolicy="no-referrer" />
	<link href="../styles.css" rel="stylesheet" type="text/css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

	<nav>
		<img src="../assets/logo.png">
		<div class="roleTag_loguitBtn">
			<span>Beheerder</span>
			<a href="../process/logout.php">uitloggen</a>
		</div>
	</nav>

	<section class="beheerderPagina">
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

		<h1>
			<p>Welkom bij</p> Hollow Mountains
		</h1>

		<div class="herosection">
			<img src="../assets/herosection_img.jpg" />
			<img src="../assets/herosection_img2.jpg" />
			<img src="../assets/herosection_img3.jpg" />
		</div>

		<h2 class="attractiesTitle">Attracties</h2>

		<div class="attracties">
			<?php
			require_once '../models/Attracties.php';

			$attracties = new Attracties();
			$attractiesContent = $attracties->getAllAttracties();

			if ($attractiesContent->num_rows > 0) {
				while ($row = $attractiesContent->fetch_assoc()) {
					echo '<div class="attractieCard">';
					echo '<img src="../uploads/' . htmlspecialchars($row['Foto']) . '" alt="Attractie">';
					echo '<div class="attractieCardBottom">';
					echo '<h2>' . htmlspecialchars($row['Naam']) . '</h2>';
					echo '<p><strong><i class="fa-solid fa-tag iconCard"></i> Type:</strong> ' . htmlspecialchars($row['Type']) . '</p>';
					echo '<p><strong><i class="fa-solid fa-location-dot iconCard"></i> Locatie:</strong> ' . htmlspecialchars($row['Locatie']) . '</p>';
					echo '<p><strong><i class="fa-solid fa-gear iconCard"></i> Technische Specificaties:</strong> ' . htmlspecialchars($row['TechnischeSpecificaties']) . '</p>';
					echo '<a href="wijzigPaginas/wijzig_attractie.php?id=' . htmlspecialchars($row['ID']) . '" class="CardEditLink"><i class="fa-solid fa-pen-to-square"></i>Bewerken</a>';
					echo '<a href="wijzigPaginas/onderhoudsschema.php?id=' . htmlspecialchars($row['ID']) . '" class="CardEditSchema"><i class="fa-regular fa-calendar-days"></i>Onderhoudsschema</a>';
					echo '</div>';
					echo '</div>';
				}
			} else {
				echo '<p>Geen attracties gevonden.</p>';
			}

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				// Uploaden van een attractie
				if (isset($_POST['upload_attractie'])) {
					$naam = $_POST['naam'];
					$locatie = $_POST['locatie'];
					$type = $_POST['type'];
					$specificaties = $_POST['specificaties'];
					$foto = $_FILES['foto'];

					$result = $attracties->uploadAttractie($naam, $locatie, $type, $specificaties, $foto);
					header('Location: beheerderPagina.php');
					exit;
				}
			}

			?>
		</div>

		<div class="form_text_section">

			<form action="" method="POST" enctype="multipart/form-data" class="upload_attractie_form">
				<h3>Attractie toevoegen</h3>
				<label for="naam">Naam</label><br>
				<input type="text" id="naam" name="naam" required><br><br>

				<label for="locatie">Locatie</label><br>
				<input type="text" id="locatie" name="locatie" required><br><br>

				<label for="type">Type</label><br>
				<input type="text" id="type" name="type" required><br><br>

				<label for="specificaties">Technische Specificaties</label><br>
				<textarea id="specificaties" name="specificaties" rows="4" required></textarea><br><br>

				<label for="foto">Foto</label><br>
				<input type="file" id="foto" name="foto" accept="image/*" required><br><br>

				<button class="upload_attractie_btn" type="submit" name="upload_attractie">Upload Attractie</button>
			</form>

			<div class="introSection">
				<i class="fa-solid fa-circle-info infoIcon"></i>
				<p class="introtext">Hier kunt u een overzicht bekijken van alle attracties in ons systeem. U heeft de mogelijkheid om nieuwe attracties toe te voegen of bestaande informatie bij te werken. Zorg ervoor dat alles up-to-date is, zodat bezoekers altijd de juiste informatie hebben.</p>
			</div>
		</div>

	</section>

	<footer>
		<p>© hollowmountains</p>
	</footer>

</body>

</html>