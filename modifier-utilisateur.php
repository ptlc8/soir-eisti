<?php

// vérification des valeurs données
if (!isset($_POST["id"]) || (!isset($_POST["pseudo"]) && !isset($_POST["prenom"]) && !isset($_POST["nom"]) && !isset($_POST["email"]) && !isset($_POST["admin"]) && !isset($_POST["adresse"]) && !isset($_POST["codePostal"]) && !isset($_POST["ville"]) && !isset($_POST["pays"]) && !isset($_POST["image"])))
    exit("need data");
$idUtilisateur = intval($_POST["id"]);

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

// Vérification de l'existence de l'utilisateur
if ($mysqli->query("SELECT id FROM utilisateurs WHERE id = '$idUtilisateur'")->num_rows == 0)
    exit("invalid id");

// Modification de l'utilisateur
if (isset($_POST["pseudo"]) || isset($_POST["prenom"]) || isset($_POST["nom"]) || isset($_POST["email"]) || isset($_POST["admin"]) || isset($_POST["adresse"]) || isset($_POST["codePostal"]) || isset($_POST["ville"]) || isset($_POST["pays"])) {
    $request = "UPDATE utilisateurs SET";
    if (isset($_POST["pseudo"]))
        $request .= " pseudo = '".mysqli_real_escape_string($mysqli, $_POST["pseudo"])."',";
    if (isset($_POST["prenom"]))
        $request .= " prenom = '".mysqli_real_escape_string($mysqli, $_POST["prenom"])."',";
    if (isset($_POST["nom"]))
        $request .= " nom = '".mysqli_real_escape_string($mysqli, $_POST["nom"])."',";
    if (isset($_POST["email"]))
        $request .= " email = '".mysqli_real_escape_string($mysqli, $_POST["email"])."',";
    if (isset($_POST["admin"]))
        $request .= " admin = '".mysqli_real_escape_string($mysqli, $_POST["admin"])."',";
    if (isset($_POST["adresse"]))
        $request .= " adresse = '".mysqli_real_escape_string($mysqli, $_POST["adresse"])."',";
    if (isset($_POST["codePostal"]))
        $request .= " codePostal = '".mysqli_real_escape_string($mysqli, $_POST["codePostal"])."',";
    if (isset($_POST["ville"]))
        $request .= " ville = '".mysqli_real_escape_string($mysqli, $_POST["ville"])."',";
    if (isset($_POST["pays"]))
        $request .= " pays = '".mysqli_real_escape_string($mysqli, $_POST["pays"])."',";
    $request = substr($request, 0, -1);
    $request .= " WHERE id = '$idUtilisateur'";
    $mysqli->query($request);
}

// Modification de l'image
if (isset($_POST["image"])) {
    try {
        $image = imagecreatefromstring(base64_decode(substr($_POST["image"], strpos($_POST['image'], ",") + 1)));
    } catch (Exception $e) {
        exit("invalid image");
    }
    imagepng($image, "assets/utilisateurs/$idUtilisateur.png");
}
exit("succes");

?>