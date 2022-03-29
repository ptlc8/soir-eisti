<?php

// initialisation session + BDD
session_start();
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');

// Vérification des données envoyées
if (!isset($_POST["adresse"], $_POST["ville"], $_POST["codePostal"], $_POST["pays"]))
    exit("need all fields");

$adresse = mysqli_real_escape_string($mysqli, $_POST["adresse"]);
$ville = mysqli_real_escape_string($mysqli, $_POST["ville"]);
$codePostal = mysqli_real_escape_string($mysqli, $_POST["codePostal"]);
$pays = mysqli_real_escape_string($mysqli, $_POST["pays"]);
$pseudo = $_SESSION["pseudo"];

    // Modification de l'adresse
    $mysqli->query("UPDATE utilisateurs SET adresse = '$adresse', ville = '$ville', codePostal = '$codePostal', pays = '$pays' WHERE pseudo ='$pseudo'");
    exit("success");
?>
