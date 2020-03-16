<?php

//Initialiser la session
session_start();

//Désactive la session
session_unset();

//Détruire la session
session_destroy();

//Supprimer cookie
setcookie('auth', '', time()-1, '/', null, false, true);

header('location: index.php');
exit();

?>
