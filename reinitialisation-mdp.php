<?php
// initialisation BDD
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');

// Récupération des paramètres
if(!isset($_POST["code"], $_POST["mdp"]))
    exit("need code and mdp");
$code = mysqli_real_escape_string($mysqli, $_POST["code"]);
$mdp = mysqli_real_escape_string($mysqli, hash("sha256", $_POST["mdp"]));

// Vérification de l'existence du code
$result = $mysqli->query("SELECT * FROM utilisateurs WHERE codeMdpOublie = '$code'");
if ($result->num_rows == 0)
    exit("invalid");
    
// Modification du mot de passe
$mysqli->query("UPDATE utilisateurs SET mdp = '$mdp', codeMdpOublie = NULL WHERE codeMdpOublie = '$code'");
exit("success");

?>