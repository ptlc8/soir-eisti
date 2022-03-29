function afficherPostulation(){
    var demande = createElement("textarea", {placeholder:"Pourquoi nous rejoindre ?"});
    var bouton = createElement("button", {className:"button"}, "Envoyer", {click:function(event){
        sendRequest("POST", "postuler.php", "demande="+encodeURIComponent(demande.value)).then(function(reponse) {
            if(reponse=="succes"){
                
            }
            window.location.reload();
        });
    }});
    var milieu = document.getElementById("milieu");
    milieu.appendChild(demande);
    milieu.appendChild(bouton);
}

function afficherCandidatures(candidatures){
    let i=0;
    for(var candidature of candidatures){
        i++;
        let candidatureActuelle = candidature;
        var candidaturesConteneur = document.getElementById("candidatures")
        let ligneCandidature = createElement("div", {className: "ligneCandidature"});
        var pseudo = createElement("span", {}, candidatureActuelle["pseudo"]);
        var demande = createElement("span", {}, candidatureActuelle["demande"]);
        var bouton = createElement("span", {className:"validation"}, "❌✔", {click: function(event){
            sendRequest("POST", "candidatures.php", "id="+candidatureActuelle["id"]).then(function(reponse){
                
            })
            candidaturesConteneur.removeChild(ligneCandidature);
        }});
        ligneCandidature.appendChild(pseudo);
        ligneCandidature.appendChild(demande);
        ligneCandidature.appendChild(bouton);
        candidaturesConteneur.appendChild(ligneCandidature);
    }
}