<?php
session_start();

require('src/log.php');

if(isset($_SESSION['connect'])) {
	header('location: index.php');
	exit();
}

if(!empty($_POST['email']) && !empty($_POST['mdp']) && !empty($_POST['mdp2'])) {

	require('src/connect.php');

	$email 	= htmlspecialchars($_POST['email']);
	$mdp 	= htmlspecialchars($_POST['mdp']);
	$mdp2	= htmlspecialchars($_POST['mdp2']);

	if($mdp != $mdp2) {

		header('location: inscription.php?error=1&pass=1');
		exit();
		}
		
	//Test si adresse email est valide
		
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header('location: inscription.php?error=1&message=Votre adresse email est invalide.');
		exit();
	}

	//Vérifier si email déjà utilisé

	$req = $bdd->prepare("SELECT count(*) AS numberEmail FROM user WHERE email = ?");

    $req->execute([$email]);

    while ($email_verif = $req->fetch()) {
        if($email_verif['numberEmail'] != 0) {
            header('location: inscription.php?error=1&message=Votre email est déjà utilisé.');
            exit();
        }
	}
	
	//Hash

    $secreet = sha1($email).time();
	$secreet = sha1($secreet).time().time();

	//Cryptage du password
	$mdp = "iss".sha1($mdp."1254")."25";
	
	//Envoi en base de données

	$req = $bdd->prepare('INSERT INTO user(email, mdp, secreet) VALUES(?, ?, ?)');

	$req->execute([$email, $mdp, $secreet]);

	header('location: inscription.php?success&message=Vous êtes bien inscrit sur Issflix');
}

?>

<?php include('src/header.php'); ?>
	
	<section>
		<div class="text-center" id="login-body">
			<h1>S'inscrire sur Issflix</h1>
			<?php 
			if(isset($_GET['error'])) {

				if(isset($_GET['pass'])) { ?>

					<div style="color:red" class="alert alert-danger text-center">
						<?= "Les mots de passes ne sont pas identiques" ?>
					</div>
			<?php
				} else if(isset($_GET['message'])) { ?>
					<div style="color:red" class="alert alert-danger text-center">
						<?= htmlspecialchars($_GET['message']) ?>
					</div>
			<?php
				}
			}
			
			if(isset($_GET['success'])) { ?>
				<div style="color:green" class="alert alert-success text-center">
						<?= htmlspecialchars($_GET['message']) ?>
					</div>
			<?php
			}
			?>
			<form method="POST" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required/>
				<input type="password" name="mdp" placeholder="Mot de passe" required />
				<input type="password" name="mdp2" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form><br>

			<p class="grey">Déjà inscrit sur Issflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>