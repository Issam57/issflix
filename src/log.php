<?php

if(isset($_COOKIE['auth']) && !isset($_SESSION['connect'])) {

    $secret = htmlspecialchars($_COOKIE['auth']);

    //Vérifier le code secret lié à un compte

    require('src/connect.php');

    $req = $bdd->prepare('SELECT count(*) AS numberAccount FROM user WHERE secreet = ?');

    $req->execute([$secret]);

    while($user = $req->fetch()) {

        if($user['numberAccount'] == 1) {

            $reqUser = $bdd->prepare('SELECT * FROM user WHERE secreet = ?');
            $reqUser->execute([$secret]);

            while($userAccount = $reqUser->fetch()) {

                $_SESSION['connect'] 	= 1;
			    $_SESSION['email']		= $userAccount['email'];
            }
        }
    }
}

if (isset($_SESSION['connect'])) {

    require('src/connect.php');

    $reqUser = $bdd->prepare('SELECT * FROM user WHERE email = ?');
            $reqUser->execute([$_SESSION['email']]);

            while($userAccount = $reqUser->fetch()) {

                if($userAccount['blocked'] == 1) {

                    header('location: ../ISSFLIX/logout.php');
                    exit();
                }
            }
}

?>