<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8" />
		<title>Trésorerie - Soir'EISTI</title>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="style.css" />
		<link rel="stylesheet" href="tresorerie.css" />
		<script src="script.js"></script>
		<script src="tresorerie.js"></script>
	</head>
	<body>
		<?php
		include("header.php");
		// redirection si non connecté ou si non admin
		if ($utilisateur==null || intval($utilisateur["admin"])<1) {
			header("Location: .");
			exit("</body></html>");
		}
		// récupération des dépenses et recettes
		$result = $mysqli->query("SELECT budget.*, utilisateurs.pseudo AS pseudoUtilisateur FROM budget LEFT OUTER JOIN  utilisateurs ON utilisateurs.id = budget.idUtilisateur");
		$budget = [];
		while (($b = $result->fetch_assoc()) != null)
			$budget[] = $b;
		$categories = [];
		foreach ($budget as $bud)
		    if (!in_array($bud["categorie"], $categories))
		        array_push($categories, $bud["categorie"]);
		echo '<datalist id="categories">';
		foreach($categories as $categorie)
		    echo "<option value=\"$categorie\">";
        echo '</datalist>';
        ?>
		<script>
			var budget = <?=json_encode($budget)?>;
			var categories = [];
			for (let bud of budget)
			    if (!categories.includes(bud.categorie))
			        categories.push(bud.categorie);
		</script>
		<div id="main">
			<div id="milieu">
				<h1>Trésorerie</h1>
				<div>
    				<canvas id="graph">Impossible d'afficher l'évolution du budget</canvas>
    				<span id="budget">Budget : ?€</span>
    				<script>updateGraph(budget);</script>
    			</div>
    			<div class="depenses-recettes">
					<div>
					    <h2 class="title">Dépenses</h2>
					    <div id="depenses" onscroll="onDepensesScroll(this)" onmouseout="this.mouseIsOver = false;" onmouseover="this.mouseIsOver = true;"></div>
					    <button class="button" onclick="ajouterDepense()">Ajouter</button>
					</div>
					<span id="date"></span>
					<div>
					    <h2 class="title">Recettes</h2>
					    <div id="recettes" onscroll="onRecettesScroll(this);" onmouseout="this.mouseIsOver = false;" onmouseover="this.mouseIsOver = true;"></div>
					    <button class="button" onclick="ajouterRecette()">Ajouter</button>
					</div>
				</div>
				<script>updateRecettesDepenses(budget)</script>
			</div>
		</div>
		<?php include("footer.php"); ?>
	</body>
</html>