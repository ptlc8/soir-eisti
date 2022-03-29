<?php

// vérification des données envoyées
if (!isset($_POST["texte"], $_POST["a"]))
    exit("need texte and a");

// initialisation session + BDD et vérification de la connexion à un compte
session_start();
if (!isset($_SESSION["pseudo"], $_SESSION["mdp"]))
    exit("not logged");
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');
$result = $mysqli->query("SELECT id FROM utilisateurs WHERE pseudo = '".$_SESSION["pseudo"]."' AND mdp = '".$_SESSION["mdp"]."'");
if ($result->num_rows == 0)
    exit("not logged");
$utilisateur = $result->fetch_assoc();

// vérification de l'existence de l'évent
// TODO

// Ajout du message
$texte = mysqli_real_escape_string($mysqli, $_POST["texte"]);
$a = mysqli_real_escape_string($mysqli, $_POST["a"]);
$mysqli->query("INSERT INTO messagerie (de, a, texte, date) VALUES (".$utilisateur["id"].", '$a', '$texte', NOW())");
exit("succes");

?>