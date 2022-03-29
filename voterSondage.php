<?php
    
// vérification des données envoyées
if (!isset($_POST["vote"], $_POST["voteId"]))
    exit("need vote and voteId");

// initialisation session + BDD et vérification de la connexion à un compte
session_start();
if (!isset($_SESSION["pseudo"], $_SESSION["mdp"]))
    exit("not logged");
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');
$result = $mysqli->query("SELECT id, sondagesRepondus FROM utilisateurs WHERE pseudo = '".$_SESSION["pseudo"]."' AND mdp = '".$_SESSION["mdp"]."'");
if ($result->num_rows == 0)
    exit("not logged");
$utilisateur = $result->fetch_assoc();

$repondus = intval($utilisateur["sondagesRepondus"]);
if($repondus&pow(2, intval($_POST["voteId"]))){
    exit("déjà répondu");
}
$mysqli->query("UPDATE utilisateurs SET sondagesRepondus = '".($repondus+pow(2, intval($_POST["voteId"])))."' WHERE id = '".$utilisateur["id"]."'");
$sondage = $mysqli->query("SELECT * FROM sondages WHERE id = '".intval($_POST["voteId"])."'")->fetch_assoc();
$reponses = json_decode($sondage["reponses"]);
for($i=0; $i<count($reponses); $i++){
    if($reponses[$i]->nom==$_POST["vote"])
        $reponses[$i]->votes++;
}
$mysqli->query("UPDATE sondages SET reponses = '".json_encode($reponses)."' WHERE id = '".intval($_POST["voteId"])."'");
exit("succes");
?>