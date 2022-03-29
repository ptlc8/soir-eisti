<?php

// vérification des données envoyées
if (!isset($_POST["pdp"]))
    exit("need pdp");

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

// Changement de l'image de profil (avec redimensionnement en carré)
try {
    $image = imagecreatefromstring(base64_decode(substr($_POST["pdp"], strpos($_POST['pdp'], ",") + 1)));
} catch (Exception $e) {
    exit("invalid image");
}
$w = imagesx($image);
$h = imagesy($image);
if ($w > $h) {
    $image = imagecrop($image, array("y"=>0, "height"=>$h, "width"=>$h, "x"=>($w-$h)/2));
} else if ($w < $h) {
    $image = imagecrop($image, array("x"=>0, "width"=>$w, "height"=>$w, "y"=>($h-$w)/2));
}
imagepng($image, "assets/utilisateurs/".$utilisateur["id"].".png");
exit("success");

?>