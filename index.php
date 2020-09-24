
<?php

	session_start();

	require_once('src/log.php');

	if (!empty($_POST)
	&& !empty($_POST['password']
	&& !empty($_POST['email'])))
	{
		require_once('src/connect.php');
		$email = htmlspecialchars($_POST['email']);
		
		//On verifie l'adresse Email
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			header('location: index.php?error=1&message=Le format de l\'adresse email est invalide.');
			exit();
		}
		//On crypt le password 
		$password = htmlspecialchars($_POST['password']);
		$password = "ak1".sha1($password)."95";

		//On verifie si l'email est utilisé
		$req = $db->prepare('SELECT count(*) as numberEmail FROM users WHERE email = ?');
		$req->execute(array($email));
		while ($email_verification = $req->fetch())
		{
			if ($email_verification['numberEmail'] != 1)
			{
				header('location: index.php?error=1&message=Impossible de vous authentifier.');
				exit();
			}
		}

		// Connexion
		$req = $db->prepare("SELECT * FROM users WHERE email = ?");
		$req->execute(array($email));

		while($user = $req->fetch())
		{
			if($password == $user['password'])
			{
				$_SESSION['connect'] = 1;
				$_SESSION['email']	 = $user['email'];
				if(isset($_POST['auto']))
				{
					setcookie('auth', $user['secret'], time() + 364*24*3600, '/', null, false, true);

				}
				header('location: index.php?success=1');
				exit();
			}
			else
			{
				header('location: index.php?error=1&message=Impossible de vous authentifier.');
				exit();
			}
		}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>
<body>
	<?php include('src/header.php'); ?>	
	<section>
		<div id="login-body">
			<?php if (isset($_SESSION['connect'])) { ?>
				<h1>Bienvenue</h1>
				<?php
					if (isset($_GET['success']))
					{
						echo '<div class="alert success">Vous êtes désormais connecté ! </div>';
					}
				?>
				<p>Que voulez vous regarder aujourd'hui ?</p>
				<small><a href="logout.php">Déconnexion</a></small>
				<?php } else { ?>
					<h1>S'identifier</h1>

						<?php
							if (isset($_GET['error']))
							{
								if (isset($_GET['message']))
								{
									echo '<div class="alert error">' . htmlspecialchars($_GET['message']) . '</div>';
								}
							}
							
						?>

					<form method="post" action="index.php">
						<input type="email" name="email" placeholder="Votre adresse email" required />
						<input type="password" name="password" placeholder="Mot de passe" required />
						<button type="submit">S'identifier</button>
						<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
					</form>
					<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
				<?php } ?>
		</div>
	</section>
	<?php include('src/footer.php'); ?>
</body>
</html>