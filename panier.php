<?php

// initialisation session + BDD et vérification de la connexion à un compte
session_start();
if (!isset($_SESSION["pseudo"], $_SESSION["mdp"]))
    exit("disconnected");
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');
$result = $mysqli->query("SELECT id FROM utilisateurs WHERE pseudo = '".$_SESSION["pseudo"]."' AND mdp = '".$_SESSION["mdp"]."'");
if ($result->num_rows == 0)
    exit("disconnected");
$utilisateur = $result->fetch_assoc();

//définit la quantité d'un article et renvoie le panier
if(isset($_POST['idArticle'], $_POST['quantite'])){
    $quantite = intval($_POST['quantite']);
    $idArticle = intval($_POST['idArticle']);
    if($quantite>0){
        if($mysqli->query("SELECT * FROM paniers WHERE idClient = '".$utilisateur['id']."' AND idArticle = '$idArticle'")->num_rows !==0)
            $mysqli->query("UPDATE paniers SET quantite = '$quantite' WHERE idClient = '".$utilisateur['id']."' AND idArticle = '$idArticle' ");
        else
            $mysqli->query("INSERT INTO paniers (idClient, idArticle, quantite) VALUES ('".$utilisateur['id']."', '$idArticle', 1)");
    }else{
        $mysqli->query("DELETE FROM paniers WHERE idArticle = '$idArticle' AND idClient = '".$utilisateur['id']."'");
    }
    
    //renvoyer le panier
    $panier = $mysqli->query("SELECT * FROM paniers JOIN articles ON articles.id=paniers.idArticle WHERE idClient = '".$utilisateur['id']."'");
    $panierListe = [];
    for($i=0; $i<$panier->num_rows; $i++){
        $panierListe[$i] = $panier->fetch_assoc();
    }
    exit(json_encode($panierListe));
}

?>