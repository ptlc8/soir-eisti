<?php

// initialisation session + BDD
session_start();
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');

// Vérification des données envoyées
if (!isset($_POST["pseudo"], $_POST["nom"], $_POST["prenom"], $_POST["email"]))
    exit("need all fields");

$nouveauPseudo = mysqli_real_escape_string($mysqli, $_POST["pseudo"]);
$nom = mysqli_real_escape_string($mysqli, $_POST["nom"]);
$prenom = mysqli_real_escape_string($mysqli, $_POST["prenom"]);
$nouveauEmail = mysqli_real_escape_string($mysqli, $_POST["email"]);
$pseudo = $_SESSION["pseudo"];

$email = mysqli_real_escape_string($mysqli, $mysqli->query("SELECT email FROM utilisateurs WHERE pseudo ='".$pseudo."'")->fetch_assoc()["email"]);

if ($nouveauPseudo != $pseudo){
    $result = $mysqli->query("SELECT * FROM utilisateurs WHERE pseudo = '".$nouveauPseudo."'");
        if ($result->num_rows !== 0)
            exit("pseudoincorrect");
}
if ($nouveauEmail != $email){
    $result = $mysqli->query("SELECT * FROM utilisateurs WHERE email = '".$nouveauEmail."'");
        if ($result->num_rows !== 0)
            exit("emailincorrect");
}
    // Modification de l'adresse
    $mysqli->query("UPDATE utilisateurs SET pseudo = '$nouveauPseudo', nom = '$nom', prenom = '$prenom', email = '$nouveauEmail' WHERE pseudo ='$pseudo'");
    exit("success");
?>

