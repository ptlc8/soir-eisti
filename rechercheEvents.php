<?php

// initialisation session + BDD
session_start();
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');

$recherche = mysqli_real_escape_string($mysqli, $_REQUEST['recherche']);
$dateDebut = mysqli_real_escape_string($mysqli, $_REQUEST['dateDebut']);
$dateFin = mysqli_real_escape_string($mysqli, $_REQUEST['dateFin']);
$Lati = mysqli_real_escape_string($mysqli, $_REQUEST['Lati']);
$Long = mysqli_real_escape_string($mysqli, $_REQUEST['Long']);
$Dist = mysqli_real_escape_string($mysqli, $_REQUEST['Dist']);

// recherche
$requete = "SELECT * from events WHERE nom LIKE '%".$recherche."%'";

if ($dateDebut != ''){
    $requete = $requete." AND date > '".$dateDebut."'";
}

if($dateFin != ''){
    $requete = $requete." AND date < '".$dateFin."'";
}


if (isset ($_REQUEST['Lati'], $_REQUEST['Long'],$_REQUEST['Dist']) && $Dist !== ''){
   $requete = $requete." AND ACOS(SIN(RADIANS($Lati))*SIN(RADIANS(lat))+COS(RADIANS($Lati))*COS(RADIANS(lat))*COS(RADIANS($Long-lon)))*6371<$Dist";
}
// ACOS(SIN(RADIANS(B2))*SIN(RADIANS(B3))+COS(RADIANS(B2))*COS(RADIANS(B3))*COS(RADIANS(C2-C3)))*6371.
// B2 C2 - B3 C3

$resultat = $mysqli -> query($requete);
$resultliste = [];
for ($i=0; $i<$resultat -> num_rows; $i++)
{
    $resultliste[$i]=$resultat -> fetch_assoc();
}

$json = json_encode($resultliste);
echo($json);

?>