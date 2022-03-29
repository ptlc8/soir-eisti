<?php

// initialisation session + BDD
session_start();
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');

// Vérification des données envoyées
if (!isset($_POST["ancienMdp"], $_POST["nouveauMdp"], $_POST["confirmMdp"]))
    exit("need all fields");

$ancienMdp = mysqli_real_escape_string($mysqli, hash("sha256", $_POST["ancienMdp"]));
$nouveauMdp = mysqli_real_escape_string($mysqli, hash("sha256", $_POST["nouveauMdp"]));
$confirmMdp = mysqli_real_escape_string($mysqli, hash("sha256", $_POST["confirmMdp"]));
$pseudo = $_SESSION["pseudo"] ;
$mdp=$_SESSION["mdp"];

if($nouveauMdp==$confirmMdp && $mdp==$ancienMdp ){
    // Modification du mot de passe
    $mysqli->query("UPDATE utilisateurs SET mdp = '$nouveauMdp' WHERE pseudo ='$pseudo'");

    $_SESSION["pseudo"] = $pseudo;
    $_SESSION["mdp"] = $nouveauMdp;
    exit("success");
}
else{
    exit("invalid");
}
?>
