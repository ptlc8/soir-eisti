<?php

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

// Récupération des messages
$resultat = $mysqli->query("SELECT messagerie.*, utilisateurs.pseudo FROM messagerie JOIN utilisateurs WHERE (messagerie.de = '".$utilisateur["id"]."' AND messagerie.a = utilisateurs.id) OR (messagerie.a = '".$utilisateur["id"]."' AND messagerie.de = utilisateurs.id) OR (messagerie.a = '0' AND messagerie.de = utilisateurs.id) ORDER BY date DESC");
$messages = [];
$messages[0] = array("pseudo"=>"Chat général", "messages"=>[]);
while (($message = $resultat->fetch_assoc()) != null) {
    if ($message["a"] == "0") {
        array_push($messages["0"]["messages"], array("texte"=>$message["texte"], "date"=>$message["date"], "moi"=>$message["de"]==$utilisateur["id"], "pseudo"=>$message["pseudo"]));
    } else if ($message["de"] == $utilisateur["id"]) {
        if (!isset($messages[$message["a"]]))
            $messages[$message["a"]] = array("pseudo"=>$message["pseudo"], "messages"=>[]);
        array_push($messages[$message["a"]]["messages"], array("texte"=>$message["texte"], "date"=>$message["date"], "moi"=>true));
    } else {
        if (!isset($messages[$message["de"]]))
            $messages[$message["de"]] = array("pseudo"=>$message["pseudo"], "messages"=>[]);
        array_push($messages[$message["de"]]["messages"], array("texte"=>$message["texte"], "date"=>$message["date"], "moi"=>false));
    }
}
exit(json_encode(($messages)));

?>