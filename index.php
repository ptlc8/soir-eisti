<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Soir'EISTI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="index.css" />
        <script src="script.js"></script>
        <script src="index.js"></script>
    </head>
    <body>
        <?php include("header.php"); ?>
        <div id="main">
            <div id=gauche>
                <?php include("diapo.php"); ?>
                <?php include("sondage.php"); ?>
            </div>
            <div id="milieu">
                <?php $event = $mysqli->query("SELECT * FROM events WHERE date < NOW() ORDER BY date DESC")->fetch_assoc(); ?>
                <h1>Dernier évent : <?=htmlspecialchars($event["nom"])?></h1>
                <h2>Photos</h2>
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
                <h2><?=htmlspecialchars($event["nom"])?></h2>
                <b>Le <?=strftime("%A %d %B %G", strtotime($event["date"]))?></b> à <b><?=htmlspecialchars($event["lieu"])?></b>
                <img src="assets/events/<?=$event["id"]?>.png" alt="<?=$event["nom"]?>" class="main-image" />
                <p><?=str_replace(["\n"], ["<br />"], htmlspecialchars($event["description"]))?></p>
                <a href="event.php?id=<?=$event["id"]?>" class="button" style="clear:both;">Plus d'informations</a>
            </div>
            <div id="droite">
                <?php include("prochaine.php"); ?>
            </div>
        </div>
        <?php include("footer.php"); ?>
    </body>
</html>