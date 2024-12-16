<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Login</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link
		rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
		integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
		crossorigin="anonymous"
		referrerpolicy="no-referrer" />
	<link href="styles.css" rel="stylesheet" type="text/css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

	<nav>
		<img src="assets/logo.png">
	</nav>

	<!-- Login Formulier -->
	<form action="process/login.php" method="post" class="index_page_form login_form">
		<img src="assets/user_icon.jpg">
		<h2>Inloggen</h2>
		<?php
		// error message
		if (isset($_SESSION['error'])) {
			echo '<p class="error_message">' . $_SESSION['error'] . '</p>';
			unset($_SESSION['error']);
		} elseif (isset($_SESSION['success_message'])) {
			echo '<p class="success_message">' . $_SESSION['success_message'] . '</p>';
			unset($_SESSION['success_message']);
		}
		?>
		<input type="text" name="username" placeholder="Gebruikersnaam" required>
		<input type="password" name="password" placeholder="Wachtwoord" required>
		<input type="submit" value="Inloggen">
		<button onclick="showRegisterForm()">Registreren</button>
	</form>

	<!-- Registreer Formulier -->
	<form action="process/registreer.php" method="post" class="index_page_form registreer_form">
		<img src="assets/user_icon.jpg">
		<h2>Registreren</h2>
		<?php
		// error message
		if (isset($_SESSION['error'])) {
			echo '<p class="error_message">' . $_SESSION['error'] . '</p>';
			unset($_SESSION['error']);
		}
		?>
		<input type="text" name="gebruikersnaam" placeholder="Gebruikersnaam" required>
		<input type="password" name="wachtwoord" placeholder="Wachtwoord" required>
		<input type="text" name="adres" placeholder="Adres" required>
		<input type="text" name="telefoonnummer" placeholder="Telefoonnummer" required>
		<input type="text" name="woonplaats" placeholder="Woonplaats" required>
		<!-- <select name="rol" required>
			<optgroup label="Rollen">
				<option value="Monteur">Monteur</option>
				<option value="Beheerder">Beheerder</option>
				<option value="Manager">Manager</option>
				<option value="Klantenservice">Klantenservice</option>
			</optgroup>
		</select> -->
		<input type="submit" value="Registreren">
		<button type="button" onclick="showLoginForm()">Terug naar inloggen</button>
	</form>


	<script src="script.js">
	</script>

</body>

</html>