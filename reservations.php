<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Réservations - Soir'EISTI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="reservations.css" />
        <script src="script.js"></script>
        <script src="qrcode.min.js"></script><!--https://davidshimjs.github.io/qrcodejs/-->
        <script src="reservations.js"></script>
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
        <div id="main">
            <div id="milieu" class="carte-lieu">
                <h1>Vos réservations :</h1>
                <span class="avertissement">⚠ Attention ! Cette page doit rester confidentielle pour éviter de vous faire voler vos réservations</span>
                <div class="reservations">
                    <?php
                    $reservations = $mysqli->query("SELECT reservations.*, events.nom, events.date AS dateEvent FROM reservations JOIN events ON events.id = idEvent WHERE idUtilisateur = '".$utilisateur["id"]."'")->fetch_all(MYSQLI_ASSOC);
                    if (count($reservations)==0) {
                        ?>Vos réservations aux évents apparaîtront ici, vous n'en avez pas encore. <a href="events.php">Rechercher des évents🎫</a><?php 
                    }
                    foreach($reservations as $reservation) {
                        ?>
                        <div onclick="popupQR('<?=$reservation["code"]?>', '<?=$utilisateur["pseudo"]?>')">
                            <div class="textes">
                                <span class="titre"><?=$reservation["nom"]?></span>
                                <time class="date" datetime="<?=$reservation["dateEvent"]?>">Date et heure : <?=strftime("%A %e %B %Y, %kh%Mm%S", strtotime($reservation["dateEvent"]))?></time>
                                <time class="date-reservation" datetime="<?=$reservation["date"]?>">Reservée le <?=strftime("%A %e %B %Y, %kh%Mm%S", strtotime($reservation["date"]))?></time>
                                <?=$reservation["utilisee"]?'<span class="avertissement">utilisée</span> ':""?>
                            </div>
                            <div class="qr"></div>
                            <script defer>
                                new QRCode(document.currentScript.previousElementSibling, "<?=$reservation["code"]?>");
                            </script>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <!--<div id="droite">
                <?php include("prochaine.php"); ?>
            </div>-->
        </div>
        <?php include("footer.php"); ?>
    </body>
</html>