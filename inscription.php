<?php

// initialisation session + BDD
session_start();
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');

// Vérification des données envoyées
if (!isset($_POST["pseudo"], $_POST["prenom"], $_POST["nom"], $_POST["email"], $_POST["mdp"]))
    exit("need all fields");
$pseudo = mysqli_real_escape_string($mysqli, $_POST["pseudo"]);
$prenom = mysqli_real_escape_string($mysqli, $_POST["prenom"]);
$nom = mysqli_real_escape_string($mysqli, $_POST["nom"]);
$email = mysqli_real_escape_string($mysqli, $_POST["email"]);
$mdp = mysqli_real_escape_string($mysqli, hash("sha256", $_POST["mdp"]));

// Inscription
$result = $mysqli->query("SELECT * FROM utilisateurs WHERE pseudo = '".$pseudo."' OR email = '".$email."'");
if ($result->num_rows !== 0)
    exit("invalid");
$mysqli->query("INSERT INTO utilisateurs (pseudo, prenom, nom, email, mdp) VALUES ('$pseudo', '$prenom', '$nom', '$email', '$mdp')");
$_SESSION["pseudo"] = $pseudo;
$_SESSION["mdp"] = $mdp;
exit("success");

?>