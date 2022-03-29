<?php

// initialisation session + BDD
session_start();
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');

// Vérification des données envoyées
if (!isset($_POST["pseudo"], $_POST["mdp"]))
    exit("need pseudo and mdp");
$pseudo = mysqli_real_escape_string($mysqli, $_POST["pseudo"]);
$mdp = mysqli_real_escape_string($mysqli, hash("sha256", $_POST["mdp"]));

// Connexion
$result = $mysqli->query("SELECT * FROM utilisateurs WHERE pseudo = '".$pseudo."' AND mdp = '".$mdp."'");
    if ($result->num_rows == 0)
        exit("invalid");
$utilisateur = $result->fetch_assoc();
$_SESSION["pseudo"] = $utilisateur["pseudo"];
$_SESSION["mdp"] = $utilisateur["mdp"];
exit("success");

?>