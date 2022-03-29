<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8" />
		<title>Profil - Soir'EISTI</title>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="style.css" />
		<link rel="stylesheet" href="profil.css" />
		<script src="script.js"></script>
        <script src="profil.js"></script>
    </head>
    
	<body>
		<?php
		include("header.php");
		// redirection si non connecté
		if ($utilisateur==null) {
			header("Location: .");
			exit("</body></html>");
        }
        ?>

        <div id="mainx">
            <div id="milieuy">
                <h1>Profil de <?= $utilisateur["pseudo"] ?></h1>
                <div class="triptyque">
                    <div id="gauchex">
                        <div class="pdp-conteneur" onclick="selectPdp()">
                            <img id="pdp" class="pdp" src="assets/utilisateurs/<?=$utilisateur["id"]?>.png" alt="<?=$utilisateur["pseudo"]?>" onerror="if (this.className.includes('unset')) return; this.src='assets/utilisateurs/unset.png'; this.className+=' unset';" />
                            <span class="hover-texte">Changer l'image</span>
                            <span class="chargement-texte">Chargement...</span>
                            <input type="file" id="pdp-input" onchange="changePdp(this)" accept=".jpg, .png, .jpeg, .webp, .bmp">
                        </div>
                    </div>
    
                    <div id="milieux">
                        <h3>
                        Pseudo :
                        <?= $utilisateur["pseudo"] ?><br><br>
                        Nom :
                        <?= $utilisateur["nom"] ?><br><br>
                        Prénom :
                        <?= $utilisateur["prenom"] ?><br><br>
                        Email :
                        <?= $utilisateur["email"] ?><br>
                        <a href="javascript:changeInfo()" class="button">Changer les informations</a><br>
    
                        Mot de passe : ****
                        <a href="javascript:changeMdp()" class="button">Changer le mot de passe</a>
                        </h3>
                    </div>
    
                    <div id="droitex">
                        <h3>
                        Adresse de livraison :
                        <?= $utilisateur["adresse"] ?><br><br>
                        Ville :
                        <?= $utilisateur["ville"] ?><br><br>
                        Code Postal :
                        <?= $utilisateur["codePostal"] ?><br><br>
                        Pays :
                        <?= $utilisateur["pays"] ?><br>
                        <a href="javascript:changeAdresse()" class="button">Changer les informations de livraison</a><br>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <?php include("footer.php"); ?>
	</body>
</html>