function rechercher(){
    var recherche = document.getElementById('search').value;
    var dateDebut = document.getElementById('start').value;
    var dateFin = document.getElementById('end').value;
    var Lati = document.getElementById('Lati').value;
    var Long = document.getElementById('Long').value;
    var Dist = document.getElementById('Dist').value;
    sendRequest('get', 'rechercheEvents.php?recherche='+encodeURIComponent(recherche)+'&'+'dateDebut='+(dateDebut)+'&'+'dateFin='+(dateFin)+'&'+'Lati='+(Lati)+'&'+'Long='+(Long)+'&'+'Dist='+(Dist)).then(function(json){
        var events = JSON.parse(json);
        afficheResultat(events);
    });
}

function afficheResultat(resultats){
    var resultSection = document.getElementById('resultat');
    resultSection.innerHTML = '';
    
    for(var i=0; i<resultats.length; i++){
            var resultArticle = createElement('article', {className:"soiree"}, 
            [
                createElement('a', {href:'event.php?id='+resultats[i].id, title:resultats[i].nom},
                [
                    createElement('img', {src:'assets/events/'+resultats[i].id+'.png'}),
                    createElement('div', {className:'infos'}, 
                    [
                        createElement('h2', {}, resultats[i].nom),
                        createElement('time', {datetime:resultats[i].date}, new Date(resultats[i].date).toLocaleDateString ('fr-FR')),
                        createElement('span', {}, resultats[i].lieu),
                    ]),
                ])
            ])
            resultSection.appendChild(resultArticle);
    }
}

function rechercherLieu(input){
    sendRequest('get', 'https://api.mapbox.com/geocoding/v5/mapbox.places/'+encodeURIComponent(input.value)+'.json?access_token=pk.eyJ1IjoicHRsYyIsImEiOiJja2Qxb2tmN2Uwc2s1MndxbXk2dmdjMGNrIn0.bame3uGYhs6O4cIFUGAkhA').then
        (function(reponse){
        var lieux = JSON.parse(reponse);
        var long = lieux.features[0].center[0];
        var lat = lieux.features[0].center[1];
        document.getElementById('Long').value = long;
        document.getElementById('Lati').value = lat;
        rechercher();
    });
    
}


