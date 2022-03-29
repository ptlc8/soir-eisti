<?php
if(!isset($_POST["email"]))
    exit("invalidEmail");
$email = $_POST["email"];

// initialisation BDD
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');

$result = $mysqli->query("SELECT * FROM utilisateurs WHERE email = '".mysqli_real_escape_string($mysqli, $email)."'");
if ($result->num_rows == 0)
    exit("invalid");
    
$code = "";
for($i=0; $i<12; $i++){
   $code.="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_"[random_int(0, 63)];
}
$mysqli->query("UPDATE utilisateurs SET codeMdpOublie = '$code' WHERE email = '".mysqli_real_escape_string($mysqli, $email)."'");
mail($email, "Perte de mot de passe", 'Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href="http://'.$_SERVER["HTTP_HOST"].str_replace("mdp-oublie", "reinitialisation", $_SERVER["REQUEST_URI"]).'?'.$code.'">Réinitialiser le mot de passe</a>', "From:contact@ayaya.cy-hub.fr\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n");
exit("success");
?>