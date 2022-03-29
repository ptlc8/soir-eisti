<div class="mini-event">
    <?php
    $event = null;
    $result = $mysqli->query("SELECT * FROM events WHERE date > CURDATE() ORDER BY date");
    if ($result->num_rows !== 0) {
        $event = $result->fetch_assoc();
        ?>
        <span class="titre">Prochain évent :</span>
        <span class="nom"><?= htmlspecialchars($event["nom"]) ?></span>
        <span class="datelieu">Le <b><?= strftime("%A %d %B %G", strtotime($event["date"])) ?></b> à <b><?= htmlspecialchars($event["lieu"]) ?></b></span>
        <br />
        <img src="assets/events/<?= $event["id"] ?>.png" class="fit" alt="<?= $event["nom"] ?>" />
        <p class="description"><?= $event["description"] ?></p>
        <a href="event.php?id=<?= $event["id"] ?>" class="button more">Plus d'infos</a>
    <?php } else { ?>
        <span class="titre">Aucun évent prévu prochainement... 😔</span>
    <?php } ?>
</div>