<?php

// vérification des valeurs données
if ((!isset($_POST["question"]) || !isset($_POST["reponses"])))
    exit("need data");

// initialisation session + BDD et vérification de la connexion à un compte
session_start();
if (!isset($_SESSION["pseudo"], $_SESSION["mdp"]))
    exit("disconnected");
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');
$result = $mysqli->query("SELECT id, admin FROM utilisateurs WHERE pseudo = '".$_SESSION["pseudo"]."' AND mdp = '".$_SESSION["mdp"]."'");
if ($result->num_rows == 0)
    exit("disconnected");
$utilisateur = $result->fetch_assoc();

// Vérification d'admin
if($utilisateur["admin"] <= 0)
    exit("not admin");

// Création du sondage
$mysqli->query("INSERT INTO sondages (question, reponses) VALUES ('".mysqli_real_escape_string($mysqli, $_POST["question"])."', '".mysqli_real_escape_string($mysqli, $_POST["reponses"])."') ");
exit("succes ".$idSondage);
?>