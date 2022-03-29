function commenter(eventId) {
    if (!connecte) return connexion();
    var commentaire = createElement("input", {type:"text", placeholder:"Commentaire"});
    var formError = createElement("span", {className:"form-error"});
    popup([
        commentaire, formError,
        createElement("button", {}, "Commenter", {click: function(e) {
            if (commentaire.value == "")
                return formError.innerText = "Le commentaire ne peut être vide";
            sendRequest("POST", "commenter.php", "event="+encodeURIComponent(eventId)+"&commentaire="+encodeURIComponent(commentaire.value)).then(function(reponse) {
                if (reponse == "success")
                    window.location.reload();
                else if (reponse == "notlogged")
                    connexion();
                else
                    formError.innerText = "Une erreur interne est survenue";
            }).catch(function(error) {
                formError.innerText = "Une erreur ("+error+") est survenue";
            })
        }})
    ]);
    commentaire.focus();
}

function inscriptionEvent(eventId, prixAffiche="?") {
    if (!connecte) return connexion();
    var p = popup([
        createElement("span", {className:"titre"}, "Payement fictif"),
        createElement("button", {}, "Payer "+prixAffiche+"€", {click: function() {
            sendRequest("POST", "inscrire-event.php", "event="+eventId).then(function(reponse) {
                removePopup(p);
                popup([
                    createElement("span", {}, reponse=="succes" ? "Votre réservation est faite ! Vous pouvez la retrouvez dans la page \"Mes réservations\" en étant connecté(e)." : reponse=="not logged" ? "Vous n'êtes plus connecté, actualisez la page 😥" :"Une erreur est survenue lors de la reservation")
                ]);
            });
        }})
    ]);
}

var map = null;
function initMap(id, lon, lat) {
    mapboxgl.accessToken = "pk.eyJ1IjoicHRsYyIsImEiOiJja2Qxb2tmN2Uwc2s1MndxbXk2dmdjMGNrIn0.bame3uGYhs6O4cIFUGAkhA";
    map = new mapboxgl.Map({
        container: id,
        style: "mapbox://styles/mapbox/"+(document.documentElement.classList.contains("dark-mode")?"dark-v9":"outdoors-v11"),
        center: [lon, lat],
        zoom: 15
    });
    map.addControl(new mapboxgl.NavigationControl());
    new mapboxgl.Marker().setLngLat([lon, lat]).addTo(map);
    map.resize();
}

function supprComment(idCommentaire){
    
}