<?php
session_start();
ob_start();

if (!isset($_SESSION['loggedin']) && $_SESSION['role'] !== 'Monteur') {
	header('Location: ../index.php');
	exit;
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Monteur</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link
		rel="stylesheet"
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
			<span>Monteur</span>
			<a href="../process/logout.php">uitloggen</a>
		</div>
	</nav>
	<section class="monteurPagina">

		<h1>Onderhoudstaken</h1>

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

		<div class="taken-container">
			<?php
			require_once '../models/Onderhoudstaak.php';

			// Maak een Onderhoudstaak object
			$onderhoudstaak = new Onderhoudstaak();

			$monteurID = $_SESSION['id'];
			$onderhoudstakenContent = $onderhoudstaak->getTakenVoorMonteur($monteurID);
 
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['wijzig_taak'])) {
				$task_id = $_POST['task_id'];
				$status = $_POST['status'];
				$opmerkingen = $_POST['opmerkingen'];

				// Roep functie in het Onderhoudstaak model aan
				$onderhoudstaak->updateStatus($task_id, $status, $opmerkingen);
			}

			// Toon de onderhoudstaken als divs
			if ($onderhoudstakenContent->num_rows > 0) {
				while ($row = $onderhoudstakenContent->fetch_assoc()) {
					echo '<div class="taak">';
					echo '<p class="task-datum"><strong>Datum:</strong> ' . $row['Datum'] . '</p>';
					echo '<p><strong>Monteur ID:</strong> ' . $row['MonteurID'] . '</p>';
					echo '<p><strong>Attractie ID:</strong> ' . $row['AttractieID'] . '</p>';
					echo '<p class="task-datum"><strong>Status:</strong> ' . $row['Status'] . '</p>';
					echo '<p><strong>Opmerkingen:</strong> ' . $row['Opmerkingen'] . '</p>';
					echo '<hr>';
					echo '<p class="strong">Breng hier wijzigingen toe:</p>';
					echo '<form method="post" action="" class="form_wijzig_dropdown">
							<input type="hidden" name="task_id" value="' . $row['ID'] . '">
						    <label>Status</label>
							<br>					
							<select name="status">
								<option value="In Behandeling"' . ($row['Status'] == 'In Behandeling' ? ' selected' : '') . '>In Behandeling</option>
								<option value="Voltooid"' . ($row['Status'] == 'Voltooid' ? ' selected' : '') . '>Voltooid</option>
							</select>
							<br>
							<label>Opmerkingen</label>							
            				<textarea name="opmerkingen" placeholder="Voer opmerkingen in..."></textarea>
							<br>
							<button type="submit" name="wijzig_taak" class="updateTaskBtn">Update</button>
							</form>';
					echo '</div>';
				}
			} else {
				echo 'Geen onderhoudstaken gevonden.';
			}
			?>
		</div>
	</section>

	<footer>© hollowmountains</footer>

</body>

</html>