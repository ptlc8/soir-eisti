<?php

// initialisation session + BDD
session_start();
if (!isset($_SESSION["pseudo"], $_SESSION["mdp"]))
    exit("not logged");
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');

// Recherche de l'utilisateur
$pseudo = mysqli_real_escape_string($mysqli, $_REQUEST["pseudo"]);
$resultat = $mysqli->query("SELECT pseudo, id FROM utilisateurs WHERE pseudo LIKE '%$pseudo%' ORDER BY LENGTH(pseudo) LIMIT 5");
$utilisateurs = [];
while (($utilisateur = $resultat->fetch_assoc()) != null) {
    array_push($utilisateurs, $utilisateur);
}
echo json_encode($utilisateurs);

?>