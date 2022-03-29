<h2></h2>
<div id="sondage-conteneur">
    <?php
        $sondages = $mysqli->query("SELECT * FROM sondages ORDER BY id DESC");
        $sondage = $sondages->fetch_assoc();
        
    ?>
    <canvas id="sondage" width = 300 height = 300>
        
    </canvas>
    
    <script>
        var sondage = document.getElementById('sondage');
        var questionSondage = "<?=str_replace(["\""], ["\\\""], $sondage["question"])?>";
        var question = createElement("p", {}, questionSondage);
        sondage.parentElement.appendChild(question);
        var ctx = sondage.getContext('2d');
        var reponses = <?=$sondage["reponses"]??"[]"?>;
        var nombre_possibilites = reponses.length;
        var nombre_votes = 0;
        for(var reponse of reponses){
            nombre_votes += reponse.votes;
        }
        var angle_debut = 0;
        var angle_fin = 0;
        for(var reponse of reponses){
            let reponseFix = reponse;
            angle_debut = angle_fin;
            angle_fin = (2 * Math.PI / nombre_votes) * reponse.votes + angle_debut;
            ctx.beginPath();
            ctx.fillStyle = (reponse.couleur);
            ctx.arc(150, 150, 140, angle_debut, angle_fin);
            ctx.lineTo(150, 150);
            ctx.closePath();
            ctx.fill();
            var bouton = createElement("button", {style: {backgroundColor: reponse.couleur}, className:"button"}, reponse.nom+" ("+reponse.votes+")");
            sondage.parentElement.appendChild(bouton);
            bouton.addEventListener("click", function(){
                if(connecte){
                    sendRequest("POST", "voterSondage.php", "vote="+reponseFix.nom+"&voteId="+<?=$sondage["id"]??"null"?>).then(function(reponse){
                        if(reponse=="déjà répondu")
                            alert("Vous avez déjà répondu à ce sondage !!!");
                        if(reponse=="succes")
                            window.location.reload();
                    });
                }
                else{
                    connexion();
                }
            })
        }
    </script>
</div>