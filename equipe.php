<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Equipe - Soir'EISTI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="equipe.css" />
        <script src="script.js"></script>
        <script src="equipe.js"></script>
    </head>
    <body>
        <?php
        include("header.php");?>
        <div id="main">
            <div id="milieu">
                <h2>Présentation de l'équipe</h2>
                <p>
                    L'association Soir'EISTI à été créée dans le but de tous vous divertir, des prépas aux ingénieurs, en vous réunissant pour des soirées et activités mémorables ! <br> Parce que études ne devrait pas rimer avec ennui, nous faisons tout notre possible pour vous faire vivre les années les plus folles de votre vie !<br> Ce site vous permet de réserver vos places aux différents évènements, retrouver toutes les photos des soirées passées, voir quelles sont les soirées les plus incroyables à venir, et même commander quelques goodies pour faire vivre l'association !
                </p>
                <h2>Les membres</h2>
                <div id="membres">
                    <?php
                    $results = $mysqli->query("SELECT * FROM utilisateurs WHERE admin = 1");
                    for($i=0; $i<$results->num_rows; $i++){
                        $result = $results->fetch_assoc();?>
                        <div class="membre">
                            <div class="pseudo">Pseudo : <?=$result["pseudo"]?></div>
                            <div class="prenom">Prénom : <?=$result["prenom"]?></div>
                            <div class="nom">Nom : <?=$result["nom"]?></div>
                            <img src="assets/utilisateurs/<?=$result["id"]?>.png" alt="<?=$result["pseudo"]?>" onerror="if (this.className.includes('unset')) return; this.src='assets/utilisateurs/unset.png'; this.className+=' unset';"/>
                        </div>
                        <?php
                    }
                    ?>
                    
                </div>
                <?php 
                if($utilisateur != null && intval($utilisateur["admin"])>0){?>
                    <h2>Ici l'admnistration</h2>
                    <?php
                    $candidatures = $mysqli->query("SELECT * FROM candidatures");
                    $nombreCandidatures = $candidatures->num_rows;
                    $candidaturesListe = [];
                    for($i=0; $i<$candidatures->num_rows; $i++){
                        $candidaturesListe[$i] = $candidatures->fetch_assoc();
                        $pseudo = $mysqli->query("SELECT pseudo FROM utilisateurs WHERE id = '".intval($candidaturesListe[$i]["idUtilisateur"])."' ");
                        $pseudo = $pseudo->fetch_assoc();
                        $pseudo = array_values($pseudo);
                        $candidaturesListe[$i]["pseudo"] = $pseudo[0];
                    }
                    if($nombreCandidatures <1){?>
                        <h3>Il n'y a pas de candidature en cours</h3>
                    <?php }
                    ?>
                    <div id="candidatures">
                        <div>
                            <span>Pseudo</span>
                            <span>Candidature</span>
                            <span>Demande examinée</span>
                        </div>
                        <script>
                            var candidatures = <?=json_encode($candidaturesListe)?>;
                            afficherCandidatures(candidatures);
                        </script>
                    </div>
                    <?php 
                }else if($utilisateur != null){
                    $result = $mysqli->query("SELECT * FROM candidatures WHERE idUtilisateur = '".$utilisateur["id"]."'  ");
                    $result = $result->fetch_assoc();
                    if($result == null){
                        ?>
                        <h2>Rejoignez-nous !</h2>
                        <h3>Remplissez ce formulaire afin que nous puissions vous intégrer parmi nos membres !</h3>
                        <script>
                            afficherPostulation();
                        </script>
                    <?php 
                    }else{ ?>
                        <h2>Votre candidature est en attente de validation. Vous receverez prochainement un mail pour vous informer de notre réponse !</h2>
                    <?php }
                }else{ ?>
                    <h2>Rejoignez-nous !</h2>
                    <h3>Connectez-vous pour avoir accès au formulaire de participation !</h3>
                    <a href="javascript: connexion()" class="button">Connecte-toi ici !</a>
                <?php }?>
            </div>
        </div>
        <?php include("footer.php"); ?>
    </body>