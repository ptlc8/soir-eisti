<?php

// vérification des données envoyées
if (!isset($_POST["commentaire"], $_POST["event"]))
    exit("need commentaire and event");

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

// Ajout du commentaire
$commentaire = mysqli_real_escape_string($mysqli, $_POST["commentaire"]);
$event = intval($_POST["event"]);
$mysqli->query("INSERT INTO commentaires (idUtilisateur, idEvent, texte, date) VALUES (".$utilisateur["id"].", $event, '$commentaire', NOW())");
exit("success");

?>