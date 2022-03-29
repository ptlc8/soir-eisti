<?php

// vérification des valeurs données
if ((!isset($_POST["nom"]) || !isset($_POST["prix"]) || !isset($_POST["description"]) || !isset($_POST["date"]) || !isset($_POST["lieu"]) || !isset($_POST["lat"]) || !isset($_POST["lon"]) || !isset($_POST["image"])))
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

// Création de l'évent
$mysqli->query("INSERT INTO articles (nom, prix, description, date, lieu, lat, lon) VALUES ('".mysqli_real_escape_string($mysqli, $_POST["nom"])."', '".intval($_POST["prix"])."', '".mysqli_real_escape_string($mysqli, $_POST["description"])."', '".mysqli_real_escape_string($mysqli, $_POST["date"])."', '".mysqli_real_escape_string($mysqli, $_POST["lieu"])."', '".intval($_POST["lat"])."', '".intval($_POST["lon"])."' ");

$idEvent = $mysqli->query("SELECT id FROM events ORDER BY id DESC");
$idEvent = $idEvent->fetch_assoc()["id"];

// Ajout de l'image
if (isset($_POST["image"])) {
    try {
        $image = imagecreatefromstring(base64_decode(substr($_POST["image"], strpos($_POST['image'], ",") + 1)));
    } catch (Exception $e) {
        exit("invalid image");
    }
    imagepng($image, "assets/events/$idEvent.png");
}
exit("succes ".$idEvent);

?>