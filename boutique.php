<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Boutique - Soir'EISTI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="boutique.css" />
        <script src="script.js"></script>
        <script src="boutique.js"></script>
    </head>
    <body>
        <?php include("header.php");
        
        $articles = $mysqli->query("SELECT * FROM articles");
        $prixMax=0;
        $articlesListe = [];
        for($i=0; $i<$articles->num_rows; $i++){
            $articlesListe[$i] = $articles->fetch_assoc();
            if(intval($articlesListe[$i]["prix"])>$prixMax)
                $prixMax = intval($articlesListe[$i]["prix"]);
        }
        if($utilisateur!=null){
            $id = $utilisateur["id"];
            $achats = $mysqli->query("SELECT * FROM paniers JOIN articles ON articles.id = idArticle WHERE idClient = '".$id."'");
            $achatsListe = [];
            for($i=0; $i<$achats->num_rows; $i++){
                $achatsListe[$i] = $achats->fetch_assoc();
            }
        }
        
        ?>
        <div id="main">
            <div id=gauche>
                <div class="prix-conteneur">
                    <label for="prix">Prix : </label>
                    <input type="range" min="1" max="<?=$prixMax?>" step="1" value="<?=$prixMax?>" id="prix" onchange="
                           var prixmax = this.value;
                           document.getElementsByClassName('prix-max')[0].innerText = prixmax+'€';
                    "/>
                    <div class="prix-valeurs">
                        <div class="prix-min">0€</div>
                        <div class="prix-max"><?=$prixMax?>€</div>
                    </div>
                </div>
                <div id="panier">
                    <?php if(!isset($achatsListe)){ ?>
                    <div>Connectez-vous pour ajouter des articles à votre panier !</div>
                    <a href="javascript: connexion()" class="button">Connecte-toi ici</a>
                    <?php } ?>
                </div>
            </div>
            <script>
                <?php
                if(isset($achatsListe)){
                ?>
                    var achats = <?=json_encode($achatsListe)?>;
                    afficherPanier(achats);
                <?php }else{?>
                    var achats = [];
                <?php } ?>
                var adresseEnregistre = <?= json_encode($utilisateur["adresse"])?>;
                var codePostalEnregistre = <?= json_encode($utilisateur["codePostal"])?>;
                var villeEnregistre = <?= json_encode($utilisateur["ville"])?>;
                var paysEnregistre = <?= json_encode($utilisateur["pays"])?>;
            </script>
            <div id="milieu">
                <h1>Bienvenue dans la boutique !</h1>
                <div id="articles">
                    
                </div>
            </div>
            <script>
                var articles = <?=json_encode($articlesListe)?>;
                afficherArticles(articles);
            </script>
            <div id="droite">
                <?php include("prochaine.php"); ?>
            </div>
            
        </div>
        
        <?php include("footer.php"); ?>
    </body>
</html>