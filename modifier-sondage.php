<?php

// vérification des valeurs données
if (!isset($_POST["id"]) || (!isset($_POST["question"]) && !isset($_POST["reponses"])))
    exit("need data");
$idSondage = intval($_POST["id"]);

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

// Vérification de l'existence du sondage
if ($mysqli->query("SELECT id FROM sondages WHERE id = '$idSondage'")->num_rows == 0)
    exit("invalid id");

// Modification du sondage
$request = "UPDATE sondages SET";
if (isset($_POST["question"]))
    $request .= " question = '".mysqli_real_escape_string($mysqli, $_POST["question"])."',";
if (isset($_POST["reponses"]))
    $request .= " reponses = '".mysqli_real_escape_string($mysqli, $_POST["reponses"])."',";
$request = substr($request, 0, -1);
$request .= " WHERE id = '$idSondage'";
$mysqli->query($request);
echo("succes");

?>