<?php

// vérification des données envoyées
if (!isset($_POST["event"]))
    exit("need event");

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

// vérification de l'existence future de l'évent
$eventId = intval($_POST["event"]);
$eventResultat = $mysqli->query("SELECT * FROM events WHERE id = $eventId AND date > NOW()");
if ($eventResultat->num_rows == 0)
    exit("invalid event");
$event = $eventResultat->fetch_assoc();

// Création d'un code unique de 20 chiffres en base 64
do {
    $code = "";
    for ($i = 0; $i < 20; $i++) {
        $code .= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_"[random_int(0,63)];
    }
} while($mysqli->query("SELECT * FROM reservations WHERE code = '$code'")->num_rows != 0);

// Ajout de la reservation
$mysqli->query("INSERT INTO reservations (idUtilisateur, idEvent, code, date) VALUES ('".$utilisateur["id"]."', $eventId, '$code', NOW())");
$mysqli->query("INSERT INTO budget (nom, date, valeur, idUtilisateur, categorie) VALUES ('".mysqli_real_escape_string($mysqli, "Place ".$event["nom"])."', NOW(), '".$event['prix']."', '".$utilisateur["id"]."', 'place évent')");
exit("succes");

?>