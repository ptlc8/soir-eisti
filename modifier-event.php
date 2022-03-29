<?php

// vérification des valeurs données
if (!isset($_POST["id"]) || (!isset($_POST["nom"]) && !isset($_POST["prix"]) && !isset($_POST["description"]) && !isset($_POST["date"]) && !isset($_POST["lieu"]) && !isset($_POST["lat"]) && !isset($_POST["lon"]) && !isset($_POST["image"])))
    exit("need data");
$idEvent = intval($_POST["id"]);

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

// Vérification de l'existence de l'évent
if ($mysqli->query("SELECT id FROM events WHERE id = '$idEvent'")->num_rows == 0)
    exit("invalid id");

// Modification de l'évent
if (isset($_POST["nom"]) || isset($_POST["prix"]) || isset($_POST["description"]) || isset($_POST["date"]) || isset($_POST["lieu"]) || isset($_POST["lat"]) || isset($_POST["lon"])) {
    $request = "UPDATE events SET";
    if (isset($_POST["nom"]))
        $request .= " nom = '".mysqli_real_escape_string($mysqli, $_POST["nom"])."',";
    if (isset($_POST["prix"]))
        $request .= " prix = '".mysqli_real_escape_string($mysqli, $_POST["prix"])."',";
    if (isset($_POST["description"]))
        $request .= " description = '".mysqli_real_escape_string($mysqli, $_POST["description"])."',";
    if (isset($_POST["date"]))
        $request .= " date = '".mysqli_real_escape_string($mysqli, $_POST["date"])."',";
    if (isset($_POST["lieu"]))
        $request .= " lieu = '".mysqli_real_escape_string($mysqli, $_POST["lieu"])."',";
    if (isset($_POST["lat"]))
        $request .= " lat = '".mysqli_real_escape_string($mysqli, $_POST["lat"])."',";
    if (isset($_POST["lon"]))
        $request .= " lon = '".mysqli_real_escape_string($mysqli, $_POST["lon"])."',";
    $request = substr($request, 0, -1);
    $request .= " WHERE id = '$idEvent'";
    $mysqli->query($request);
}

// Modification de l'image
if (isset($_POST["image"])) {
    try {
        $image = imagecreatefromstring(base64_decode(substr($_POST["image"], strpos($_POST['image'], ",") + 1)));
    } catch (Exception $e) {
        exit("invalid image");
    }
    imagepng($image, "assets/events/$idEvent.png");
}
exit("succes");

?>