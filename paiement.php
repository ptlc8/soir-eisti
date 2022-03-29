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

// changement valeurs de restants et vendus lors d'un achat d'articles + actualisation du budget à la validation du panier
$panier = $mysqli->query("SELECT * FROM paniers JOIN articles ON articles.id=paniers.idArticle WHERE idClient = '".$utilisateur['id']."'");
$panierListe = [];
for($i=0; $i<$panier->num_rows; $i++){
    $panierListe[$i] = $panier->fetch_assoc();
    $restants = intval($panierListe[$i]['restants'])-intval($panierListe[$i]['quantite']);
    $vendus = intval($panierListe[$i]['vendus'])+intval($panierListe[$i]['quantite']);
    $mysqli->query("UPDATE articles SET restants = '".intval($restants)."', vendus = '".intval($vendus)."' WHERE id = '".$panierListe[$i]['idArticle']."' ");
    $mysqli->query("DELETE FROM paniers WHERE idClient = '".$utilisateur['id']."'");
    date_default_timezone_set('Europe/Paris');
    $date = date('Y-m-d H:i:s');
    $prix = $panierListe[$i]['quantite']*$panierListe[$i]['prix'];
    $mysqli->query("INSERT INTO budget (nom, date, valeur, idUtilisateur, categorie) VALUES ('".$panierListe[$i]['nom']."', '".$date."', '".$prix."', '".$utilisateur["id"]."', 'boutique')");
}

//enregistrement de l'adresse de livraison
if($_POST["enregistrer"]=="true"){
    $mysqli->query("UPDATE utilisateurs SET adresse = '".mysqli_real_escape_string($mysqli, $_POST["adresse"])."', codePostal = '".intval($_POST["codePostal"])."', ville = '".mysqli_real_escape_string($mysqli, $_POST["ville"])."', pays = '".mysqli_real_escape_string($mysqli, $_POST["pays"])."' WHERE id = '".$utilisateur["id"]."' ");
}
exit("[]");
?>