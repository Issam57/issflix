<?php include('src/header.php'); 

session_start();

require('src/log.php');

if(!empty($_POST['email']) && !empty($_POST['mdp'])) {

	require('src/connect.php');

	$email 	= htmlspecialchars($_POST['email']);
	$mdp 	= htmlspecialchars($_POST['mdp']);

	//Test si adresse email est valide
		
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header('location: index.php?error=1&message=Votre adresse email est invalide.');
		exit();
	}
	//cryptage mdp
	$mdp = "iss".sha1($mdp."1254")."25";

	//Vérifier si email déjà utilisé

	$req = $bdd->prepare("SELECT count(*) AS numberEmail FROM user WHERE email = ?");

    $req->execute([$email]);

    while ($email_verif = $req->fetch()) {
        if($email_verif['numberEmail'] != 1) {
            header('location: index.php?error=1&message=Impossible de vous authentifier correctement.');
            exit();
        }
	}
	//Connexion
	
	$req = $bdd->prepare("SELECT * FROM user WHERE email = ?");

	$req->execute([$email]);
	
	while($user = $req->fetch()) {

		if($mdp == $user['mdp'] && $user['blocked'] == 0) {

			$_SESSION['connect'] 	= 1;
			$_SESSION['email']		= $user['email'];

			if(isset($_POST['auto'])) {
                setcookie('auth', $user['secreet'], time() + 365*24*3600, '/', null, false, true);
            }

			header('location: index.php?success=1');
			exit();

		} else {

			header('location: index.php?error=1&message=Impossible de vous authentifier correctement.');
			exit();
		}
	}
}
?>
	
	<section>
		<div class="text-center" id="login-body">
			<?php
				if(isset($_SESSION['connect'])) { ?>

				<h1>Bienvenue <?= $_SESSION['email'] ?></h1><br>
					<p>Qu'allez vous regarder aujourd'hui ?</p>
					<small><a href="logout.php">Déconnexion</a></small>

			<?php	} else { ?>
				<h1>S'identifier sur Issflix</h1>
				<?php

			if(isset($_GET['error'])) {

				if(isset($_GET['message'])) { ?>

				<div style="color:red" class="alert alert-danger text-center">
					<?= htmlspecialchars($_GET['message']) ?>
				</div>
				<?php
				} 
			}
			if(isset($_GET['success'])) { ?>

				<div style="color:green" class="alert alert-success text-center">
					<?= "Vous êtes connecté à Issflix." ?>
				</div>
				<?php
			}
			
			?>
				<form method="post" action="index.php">
					<input type="email" name="email" placeholder="Votre adresse email" required />
					<input type="password" name="mdp" placeholder="Mot de passe" required />
					<button class="mb-2" type="submit">S'identifier</button>
					<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
				</form>
			

				<p class="grey">Première visite sur Issflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
		</div>
		<?php } ?>
	</section>

<?php include('src/footer.php'); ?>
