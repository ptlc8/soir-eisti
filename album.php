<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Album - Soir'EISTI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="event.css" />
        <link rel="stylesheet" href="album.css" />
        <script src="script.js"></script>
        <script src="album.js"></script>
    </head>
    <body>
        <?php include("header.php"); ?>
        <div id="main">
            <?php
            $event = null;
            if (isset($_GET["id"])) { // récupération de l'évent demandé
                $id = mysqli_real_escape_string($mysqli, $_GET["id"]);
                $result = $mysqli->query("SELECT * FROM events WHERE id = '$id'");
                if ($result->num_rows != 0)
                    $event = $result->fetch_assoc();
            }
            if (!isset($event)) { // sinon récupération du précédent évent
                $result = $mysqli->query("SELECT * FROM events WHERE date < CURDATE() ORDER BY date DESC LIMIT 1");
                if ($result->num_rows > 0)
                    $event = $result->fetch_assoc();
            }
            ?>
            <div id=gauche>
                <div class="mini-event">
                    <?php if ($event !== null) { ?>
                    <span class="titre">L'évent :</span>
                    <span class="nom"><?= htmlspecialchars($event["nom"]) ?></span>
                    <span class="datelieu">Le <b><?= strftime("%A %d %B %G", strtotime($event["date"])) ?></b> à <b><?= htmlspecialchars($event["lieu"]) ?></b></span>
                    <br />
                    <img src="assets/events/<?= $event["id"] ?>.png" class="fit" alt="<?= $event["nom"] ?>" />
                    <p class="description"><?= $event["description"] ?></p>
                    <a href="event.php?id=<?= $event["id"] ?>" class="button more">Plus d'infos</a>
                    <?php } else { ?>
                    <span class="titre">Aucun évent à présenter 😯</span>
                    <?php
                        include("sondage.php");
                    } ?>
                </div>
            </div>
            <div id="milieu" class="carte-lieu">
                <?php if ($event != null) { ?>
                <script>document.title = <?=json_encode("Album de ".$event["nom"]." - Soir'EISTI")?>;</script>
                <h1><?= htmlspecialchars($event["nom"]) ?></h1>
                <span class="datelieu">Le <b><?= strftime("%A %d %B %G", strtotime($event["date"])) ?></b> à <b><?= htmlspecialchars($event["lieu"]) ?></b></span>
                <?php if (strtotime($event["date"]) > time()) { ?>
                <span>Cet évent n'est pas terminé, revenez plus tard pour voir les photos 😉</span>
                <?php } else { ?>
                <div class="album">
                    <?php
                        $pictures = array_values(array_diff(scandir("assets/events/".$event["id"]), array("..", ".")));
                        foreach ($pictures as $picture) {
                            $pictureUrl = "assets/events/".$event["id"]."/".$picture;
                    ?>
                    <img src="<?=$pictureUrl?>" onclick="magnifyPicture('<?=$pictureUrl?>')" />
                    <?php
                        }
                    ?>
                </div>
                <?php } ?>
                <h2>Commentaires</h2>
                <div class="commentaires">
                    <?php
                    $result = $mysqli->query("SELECT * FROM commentaires JOIN utilisateurs ON utilisateurs.id = idUtilisateur WHERE idEvent = '".$event["id"]."' ORDER BY date");
                    if ($result->num_rows === 0) {
                    ?>
                    <span>Aucun commentaire</span>    
                    <?php
                    } else while (($commentaire = $result->fetch_assoc()) !== null) {
                    ?>
                    <div>
                        <img src="assets/utilisateurs/<?=$commentaire["idUtilisateur"]?>.png" alt="<?=$commentaire["pseudo"]?>" class="avatar" onerror="if (this.className.includes('unset')) return; this.src='assets/utilisateurs/unset.png'; this.className+=' unset';" />
                        <div class="bulle">
                            <span class="pseudo"><?=htmlspecialchars($commentaire["pseudo"])?></span>
                            <span class="date"><?=strftime("%A %d %B %G, %Hh%M", strtotime($commentaire["date"]." UTC"))?></span>
                            <span class="commentaire"><?=htmlspecialchars($commentaire["texte"])?></span>
                        </div>
                    </div>
                    <?php } ?>
                    <button class="button" onclick="commenter(<?=$event["id"]?>)">Ajouter un commentaire</button>
                </div>
                <?php } else {?>
                <h1>Aucun évent n'a encore eu lieu on dirait... 😔</h1>
                <?php } ?>
            </div>
            <div id="droite">
                <?php include("prochaine.php"); ?>
            </div>
        </div>
        <?php include("footer.php"); ?>
    </body>
</html>