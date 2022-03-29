<?php
// initialisation session + BDD + langue + timezone
session_start();
include('credentials.php');
$mysqli = new mysqli(DATABASE_ADDRESS, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_errno)
	exit('Erreur de connexion côté serveur, veuillez réessayer plus tard');
setlocale(LC_ALL, "fr_FR.UTF-8");
date_default_timezone_set("Europe/Paris");

// fonction de requête BDD @Deprecated
function sendRequest(...$requestFrags) {
	$request = '';
	$var = false;
	foreach ($requestFrags as $frag) {
		$request .= ($var ? str_replace(array('\\', '\''), array('\\\\', '\\\''), $frag) : $frag);
		$var = !$var;
	}
	global $mysqli;
	if (!$result = $mysqli->query($request)) {
		exit('Erreur de requête côté serveur, veuillez réessayer plus tard');
	}
	return $result;
}

// Récupération d'un compte si connecté
$utilisateur = null;
if (isset($_SESSION["pseudo"], $_SESSION["mdp"])) {
    $result = sendRequest("SELECT * FROM utilisateurs WHERE pseudo = '".$_SESSION["pseudo"]."' AND mdp = '".$_SESSION["mdp"]."'");
    if ($result->num_rows != 0)
        $utilisateur = $result->fetch_assoc();
}
?>
<div id="header">
    <a href="." id="logo">Soir'EISTI</a>
    <div id="menu">
        <a href="events.php">Évents</a>
        <a href="boutique.php">Boutique</a>
        <a href="equipe.php">Équipe</a>
    </div>
    <div id="mini-profil-conteneur">
        <div id="mini-profil">
            <?php if ($utilisateur != null) { ?>
                <span class="pseudo"><?= $utilisateur["pseudo"] ?></span>
                <img src="assets/utilisateurs/<?=$utilisateur["id"]?>.png" alt="<?=$utilisateur["pseudo"]?>" onerror="if (this.className.includes('unset')) return; this.src='assets/utilisateurs/unset.png'; this.className+=' unset';" />
                <div class="menu">
                    <a href="profil.php" class="button">Mon profil</a>
                    <a href="reservations.php" class="button">Mes réservations</a>
                    <?php if (intval($utilisateur["admin"])>0) { ?>
                    <a href="gestion.php" class="button">Gestion</a>
                    <a href="tresorerie.php" class="button">Trésorerie</a>
                    <?php } ?>
                    <a href="javascript:deconnexion()" class="button">Déconnexion</a>
                </div>
                <script>var connecte = true;</script>
            <?php } else { ?>
                <a href="javascript:connexion()" class="button">Connexion</a>
                <script>var connecte = false;</script>
            <?php } ?>
        </div>
    </div>
    <span class="color-theme-switch" onclick="switchColorTheme()">
        <span>
            <span></span>
        </span>
    </span>
</div>