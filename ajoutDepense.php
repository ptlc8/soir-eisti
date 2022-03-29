<?php
// initialisation session + BDD et vérification de la connexion à un compte
session_start();
if (!isset($_SESSION["pseudo"], $_SESSION["mdp"]))
    exit("disconnected");
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');
$result = $mysqli->query("SELECT id FROM utilisateurs WHERE admin > 0 AND pseudo = '".$_SESSION["pseudo"]."' AND mdp = '".$_SESSION["mdp"]."'");
if ($result->num_rows == 0)
    exit("disconnected");
$utilisateur = $result->fetch_assoc();

//ajout d'une recette
date_default_timezone_set('Europe/Paris');
$date = date('Y-m-d H:i:s');
$prix = intval($_POST["prix"])*(-1);
$mysqli->query("INSERT INTO budget (nom, date, valeur, categorie, idUtilisateur) VALUES ('".mysqli_real_escape_string($mysqli, $_POST["nom"])."', '".mysqli_real_escape_string($mysqli, $date)."', '".$prix."', '".mysqli_real_escape_string($mysqli, $_POST["categorie"])."', '".$utilisateur["id"]."')");
?>
succes