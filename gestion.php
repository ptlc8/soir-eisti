<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8" />
		<title>Gestion - Soir'EISTI</title>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="style.css" />
		<link rel="stylesheet" href="gestion.css" />
		<script src="script.js"></script>
		<script src="gestion.js"></script>
	</head>
	<body>
		<?php
		include("header.php");
		// redirection si non connecté ou si non admin
		if ($utilisateur==null || intval($utilisateur["admin"])<1) {
			header("Location: .");
			exit("</body></html>");
		}
		// récupération des évents
		$result = $mysqli->query("SELECT * FROM events");
		$events = [];
		while (($event = $result->fetch_assoc()) != null)
			$events[] = $event;
		// récupération des utilisateurs
		$result = $mysqli->query("SELECT * FROM utilisateurs");
		$utilisateurs = [];
		while (($utilisateur = $result->fetch_assoc()) != null)
			$utilisateurs[] = $utilisateur;
		// récupération de la boutique
		$result = $mysqli->query("SELECT * FROM articles");
		$articles = [];
		while (($article = $result->fetch_assoc()) != null)
			$articles[] = $article;
		// récupération des sondages
		$result = $mysqli->query("SELECT * FROM sondages");
		$sondages = [];
		while (($sondage = $result->fetch_assoc()) != null) {
		    $sondage["reponses"] = json_decode($sondage["reponses"]);
			$sondages[] = $sondage;
		}
		?>
		<script>
			var events = <?=json_encode($events)?>;
			var utilisateurs = <?=json_encode($utilisateurs)?>;
			var articles = <?=json_encode($articles)?>;
			var sondages = <?=json_encode($sondages)?>
		</script>
		<div id="main">
			<div id="milieu">
				<h1>Gestion</h1>
				<div class="gestion-menu">
					<a href="javascript:switchPanel('events')" class="gestion-menu-events">Évents</a>
					<a href="javascript:switchPanel('utilisateurs')" class="gestion-menu-utilisateurs">Utilisateurs</a>
					<a href="javascript:switchPanel('articles')" class="gestion-menu-articles">Boutique</a>
					<a href="javascript:switchPanel('sondages')" class="gestion-menu-sondages">Sondages</a>
				</div>
				<div id="events" class="editeur-conteneur">
					<div class="liste"></div>
					<div class="indication">⇦ Sélectionnez un évent</div>
					<form class="editeur" style="display:none;">
					    <input placeholder="Nom" id="event-nom" name="nom" />
					    <input placeholder="Prix" id="event-prix" name="prix" type="number" step="0.01" min="0" />
					    <input type="date" id="event-date" name="date" />
					    <textarea placeholder="Description" rows="10" id="event-description" name="description"></textarea>
					    <input placeholder="lieu" id="event-lieu" name="lieu" />
					    <input type="number" step="0.000001" placeholder="Latitude" id="event-lat" name="lat" />
					    <input type="number" step="0.000001" placeholder="Longitude" id="event-lon" name="lon" />
					    <input type="file" id="event-image-input" accept="image/*" onchange="changerImageEvent(this)" name="image" />
					    <img src="" id="event-image" alt="Aucune image" />
					    <label id="event-changer-image" for="event-image-input" class="button">Changer l'image</label>
					    <button type="button" id="event-enregistrer" onclick="modifierEvent()">Enregistrer</button>
					    <button type="button" id="event-supprimer" onclick="supprimerEvent()">Supprimer</button>
					    <button type="button" id="event-publier" onclick="publierEvent()">Publier</button>
					</form>
    				<script>refreshEvents();</script>
				</div>
				<div id="utilisateurs" class="editeur-conteneur">
					<div class="liste"></div>
					<div class="indication">⇦ Sélectionnez un utilisateur</div>
					<form class="editeur" style="display:none;">
					    <img src="" id="utilisateur-image" alt="Aucune image" />
					    <input placeholder="Pseudo" id="utilisateur-pseudo" name="pseudo" />
					    <input placeholder="Nom" id="utilisateur-nom" name="nom" />
					    <input placeholder="Prénom" id="utilisateur-prenom" name="prenom" />
					    <input placeholder="E-mail" id="utilisateur-email" name="email" type="email" />
					    <select id="utilisateur-admin" name="admin">
					        <option value="0">Membre</option>
					        <option value="1">Admin</option>
					        <option value="2">Super-admin</option>
					    </select>
					    <input placeholder="Adresse" id="utilisateur-adresse" name="adresse" />
					    <input placeholder="Code postal" id="utilisateur-code-postal" name="codePostal" type="number" min="0" step="1" />
					    <input placeholder="Ville" id="utilisateur-ville" name="ville" />
					    <input placeholder="Pays" id="utilisateur-pays" name="pays" />
					    <input type="file" id="utilisateur-image-input" accept="image/*" onchange="changerImageUtilisateur(this)" name="image" />
					    <label id="utilisateur-changer-image" for="utilisateur-image-input" class="button">Changer l'image</label>
					    <button type="button" id="utilisateur-enregistrer" onclick="modifierUtilisateur()">Enregistrer</button>
					    <button type="button" id="utilisateur-supprimer" onclick="supprimerUtilisateur()">Supprimer</button>
					</form>
					<script>refreshUtilisateurs();</script>
				</div>
				<div id="articles" class="editeur-conteneur">
					<div class="liste"></div>
					<div class="indication">⇦ Sélectionnez un article</div>
					<form class="editeur" style="display:none;">
					    <input placeholder="Nom" id="article-nom" name="nom" />
					    <input type="number" step="0.01" min="0" placeholder="Prix" id="article-prix" name="prix" />
					    <textarea placeholder="Description" rows="8" id="article-description" name="description"></textarea>
					    <input type="number" min="0" placeholder="Restants" id="article-restants" name="restants" />
					    <span id="article-vendus"></span>
					    <input type="file" id="article-image-input" accept="image/*" onchange="changerImageArticle(this)" name="image" />
					    <img src="" id="article-image" alt="Aucune image" />
					    <label for="article-image-input" class="button" id="article-changer-image">Changer l'image</label>
					    <button type="button" id="article-enregistrer" onclick="modifierArticle()">Enregistrer</button>
					    <button type="button" id="article-supprimer" onclick="supprimerArticle()">Supprimer</button>
					    <button type="button" id="article-publier" onclick="publierArticle()">Ajouter</button>
					</form>
    				<script>refreshArticles();</script>
				</div>
				<div id="sondages" class="editeur-conteneur">
					<div class="liste"></div>
					<div class="indication">⇦ Sélectionnez un sondage</div>
					<form class="editeur" style="display:none;">
					    <input placeholder="Question" id="sondage-question" name="question" />
					    <div class="reponses">
					    </div>
				        <button type="button" onclick="ajouterReponse(this.parentElement.getElementsByClassName('reponses')[0]);">Ajouter une réponse</button>
					    <button type="button" id="sondage-enregistrer" onclick="enregistrerSondage()">Enregistrer</button>
					    <button type="button" id="sondage-supprimer" onclick="supprimerSondage()">Supprimer</button>
					    <button type="button" id="sondage-publier" onclick="publierSondage()">Publier</button>
					</form>
				</div>
				<script>refreshSondages();</script>
				<script>switchPanel("events");</script>
			</div>
		</div>
		<?php include("footer.php"); ?>
	</body>
</html>