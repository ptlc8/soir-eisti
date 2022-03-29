<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Évents - Soir'EISTI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="events.css" />
        <script src="script.js"></script>
        <script src="events.js"></script>
    </head>
    <body>
        <?php include("header.php"); ?>
        
        <div id="main">
            
            <div id=gauche>
                <h2>Filtres</h2>
                <label for="start">Date minimum</label>
                <input type="Date" id="start" onchange="rechercher()">
                <br>
                <label for="end">Date maximum</label>
                <input type="Date" id="end" onchange="rechercher()">
                
            
                <label for="Lati">Distance (en km)</label>
                <input type="number" id="Dist" onchange="rechercher()">
                <label for="Lieu">Lieu</label>
                <input type="text" id="Lieu" onchange="rechercherLieu(this)">
                
                <label for="Lati">Latitude</label>
                <input type="number" id="Lati" value="49.035065" onchange="rechercher()">
                <label for="Longi">Longitude</label>
                <input type="number" id="Long" value="2.069632" onchange="rechercher()">
                
            </div>
            
            <div id="milieu">
            <h1>Bienvenue dans Events !<br> Voici la liste des évènements à venir :</h1>
            <div class="BarreDeRecherche">
                 <input type="recherche" id="search" placeholder="Rechercher un mot clé" onKeyPress="if(event.keyCode == 13) rechercher();">
                 <button id="Rechercher" onclick="rechercher()">Entrer</button>
            </div>
            
            <section id="resultat">
                
                <script>
                   rechercher();
                </script>
                
                
                <!-- <article class="soiree">
                    <a href="event.php?id=1" title="Soirée légumes">
                    <img src="assets/events/1.png" width="170" height="120" id="Image1">
                    <div class="infos"><h2>Soirée legumes</h2>
                    <span><time datetime="2021-10-06">10 avril 2021</time></span>
                    <span><br>Lieu: Cergy</span></div></a> 
                </article> -->
                
            </section>
            
            </div>
            <div id="droite">
                <?php include("prochaine.php"); ?>
            </div>

        </div>
        <?php include("footer.php"); ?>
    </body>
</html>