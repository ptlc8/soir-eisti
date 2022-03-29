<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Évent - Soir'EISTI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="event.css" />
        <script src="script.js"></script>
        <script src="event.js"></script>
        <!-- Mapbox -->
        <script src="https://api.mapbox.com/mapbox-gl-js/v1.11.1/mapbox-gl.js"></script>
        <link href="https://api.mapbox.com/mapbox-gl-js/v1.11.1/mapbox-gl.css" rel="stylesheet">
    </head>
    <body>
        <?php include("header.php"); ?>
        <div id="main">
            <?php
            /*$event = null;
            $previousEvent = null;
            $nextEvent = null;*/
            if (isset($_GET["id"])) { // récupération de l'évent demandé
                $id = mysqli_real_escape_string($mysqli, $_GET["id"]);
                $result = $mysqli->query("SELECT * FROM events WHERE id = '$id'");
                if ($result->num_rows != 0)
                    $event = $result->fetch_assoc();
            }
            if (!isset($event)) { // sinon récupération du prochain évent
                $result = $mysqli->query("SELECT * FROM events WHERE date > CURDATE() ORDER BY date ASC LIMIT 1");
                if ($result->num_rows > 0)
                    $event = $result->fetch_assoc();
                if ($result->num_rows > 1)
                    $nextEvent = $result->fetch_assoc();
            }
            if (!isset($event)) { // sinon récupération du dernier évent
                $result = $mysqli->query("SELECT * FROM events ORDER BY date DESC");
                if ($result->num_rows > 0)
                    $event = $result->fetch_assoc();
                if ($result->num_rows > 1)
                    $previousEvent = $result->fetch_assoc();
                $nextEvent = null;
            }
            // récupération de l'évent suivant
            if (!isset($nextEvent)) {
                $result = $mysqli->query("SELECT * FROM events WHERE date > '".$event["date"]."' ORDER BY date ASC");
                $nextEvent = $result ? $result->fetch_assoc() : null;
            }
            // récupération de l'évent précédent
            if (!isset($previousEvent)) {
                $result = $mysqli->query("SELECT * FROM events WHERE date < '".$event["date"]."' ORDER BY date DESC");
                $previousEvent = $result ? $result->fetch_assoc() : null;
            }
            ?>
            <div id=gauche>
                <div class="mini-event">
                    <?php if ($previousEvent !== null) { ?>
                    <span class="titre">Précédent évent :</span>
                    <span class="nom"><?= htmlspecialchars($previousEvent["nom"]) ?></span>
                    <span class="datelieu">Le <b><?= strftime("%A %d %B %G", strtotime($previousEvent["date"])) ?></b> à <b><?= htmlspecialchars($previousEvent["lieu"]) ?></b></span>
                    <br />
                    <img src="assets/events/<?= $previousEvent["id"] ?>.png" class="fit" alt="<?= $previousEvent["nom"] ?>" />
                    <p class="description"><?= $previousEvent["description"] ?></p>
                    <a href="event.php?id=<?= $previousEvent["id"] ?>" class="button more">Plus d'infos</a>
                    <?php } else { ?>
                    <span class="titre">Aucun évent précédent 😯</span>
                    <?php
                        include("sondage.php");
                    } ?>
                </div>
            </div>
            <div id="milieu">
                <?php
                if ($event != null) {
                    $inscrits = $mysqli->query("SELECT COUNT(id) AS inscrits FROM reservations WHERE idEvent = '".$event["id"]."'")->fetch_assoc()["inscrits"];    
                ?>
                <script>document.title = <?=json_encode($event["nom"]." - Soir'EISTI")?>;</script>
                <h1><?= htmlspecialchars($event["nom"]) ?></h1>
                <span class="datelieu">Le <b><?= strftime("%A %d %B %G", strtotime($event["date"])) ?></b> à <b><?= htmlspecialchars($event["lieu"]) ?></b></span>
                <img src="assets/events/<?= $event["id"] ?>.png" alt="<?= $event["nom"] ?>" class="main-image" />
                <p><?= str_replace(["\n"], ["<br />"], htmlspecialchars($event["description"])) ?></p>
                <h2>Lieu</h2>
                <div id="carte" class="carte-lieu">
                    <script defer>
                        initMap("carte", <?=$event["lon"]?>, <?=$event["lat"]?>);
                    </script>
                </div>
                <h2>Inscription</h2>
                <?php if (strtotime($event["date"]) > time()) { ?>
                Prix par personne : <?=$event["prix"]==0?"Gratuit":($event["prix"]."€")?>. Déjà <?=$inscrits?> inscrits !
                <button onclick="inscriptionEvent(<?=$event["id"]?>, <?=$event["prix"]?>)" class="button">S'inscrire à l'évent</button>
                <?php } else { ?>
                Cet évent est terminé.
                <?php } ?>
                <h2>Photos</h2>
                <?php if (strtotime($event["date"]) > time()) { ?>
                <span>Cet évent n'est pas terminé, revenez plus tard pour voir les photos 😉</span>
                <?php } else { ?>
                <div class="mini-album">
                    <?php
                    $pictures = array_values(array_diff(scandir("assets/events/".$event["id"]), array("..", ".")));
                    if ($pictures !== null) {
                        for ($i = 0; $i < 4 && $i < count($pictures); $i++) {
                            $pictureUrl = "assets/events/".$event["id"]."/".$pictures[$i];
                    ?>
                    <div style="background-image:url(<?=$pictureUrl?>)"<?=$i==3?" class=\"more\"":""?> onclick="<?=$i==3?"window.location.href='album.php?id=".$event["id"]."'":"magnifyPicture('".$pictureUrl."')"?>"><?=$i==3?"+".(count($pictures)-3)." photos":""?></div>
                    <?php
                        }
                    } else {
                    ?>
                        Aucune photo pour l'instant 😿
                    <?php } ?>
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
                <h1>Aucun évent prévu prochainement... 😔</h1>
                <?php } ?>
            </div>
            <div id="droite">
                <div class="mini-event">
                    <?php if ($nextEvent !== null) { ?>
                    <span class="titre">Évent suivant :</span>
                    <span class="nom"><?= htmlspecialchars($nextEvent["nom"]) ?></span>
                    <span class="datelieu">Le <b><?= strftime("%A %d %B %G", strtotime($nextEvent["date"])) ?></b> à <b><?= htmlspecialchars($nextEvent["lieu"]) ?></b></span>
                    <br />
                    <img src="assets/events/<?= $nextEvent["id"] ?>.png" class="fit" alt="<?= $nextEvent["nom"] ?>" />
                    <p class="description"><?= $nextEvent["description"] ?></p>
                    <a href="event.php?id=<?= $nextEvent["id"] ?>" class="button more">Plus d'infos</a>
                    <?php } else { ?>
                    <span class="titre">Aucun évent suivant, pour l'instant 😏</span>
                    <?php
                        include("sondage.php");
                    } ?>
                </div>
            </div>
        </div>
        <?php include("footer.php"); ?>
    </body>
</html>