function afficherArticles(articles){
    var articlesDiv = document.getElementById("articles");
    articlesDiv.innerHTML='';
    for(var article of articles){
        let articleActuel = article;
        var a = createElement("div", {}, [
            createElement("div", {className:"imageArticle", style:{backgroundImage:"url(assets/articles/"+article.id+".png)"}}),
            createElement("span", {className:"titreArticle"}, article.nom),
            createElement("span", {className:"prixArticle"}, article.prix)
        ],{click: function(){
            popup([
                createElement("img", {className:"imageArticlePopup", src:"assets/articles/"+articleActuel.id+".png"}),
                createElement("span", {className:"titreArticle"}, articleActuel.nom),
                createElement("span", {className:"prixArticle"}, articleActuel.prix),
                createElement("div", {className:"restantArticle"}, "Nombre restants : "+articleActuel.restants),
                createElement("div", {className:"descriptionArticle"}, articleActuel.description),
                createElement("button", {className:"ajouterPanier"}, "Ajouter au panier", {click: function(event){
                    if(achats.find(achat => articleActuel.id == achat.id)){
                        alert("Cet article est déjà dans votre panier /!\\");
                    }else{
                        ajouterPanier(articleActuel.id, 1);
                    }
                    event.target.parentElement.parentElement.parentElement.removeChild(event.target.parentElement.parentElement);
                }})
            ])
        }});
        articlesDiv.appendChild(a);
    }
}

function ajouterPanier (id, quantite){
    sendRequest("POST", "panier.php", "idArticle="+id+"&quantite="+quantite).then(function(reponse) {
        if (reponse=="disconnected"){
            connexion();
        }
        else
            afficherPanier(achats=JSON.parse(reponse));
    });
}

window.addEventListener("load", function(){
    var barrePrix = document.getElementById("prix");
    barrePrix.addEventListener("change", function(event){
        afficherArticles(articles.filter(function(article){
            return parseInt(article.prix) <= barrePrix.value;
        }));
    })
})

function afficherPanier(achats){
    var panierDiv = document.getElementById("panier");
    panierDiv.innerHTML='';
    panierDiv.appendChild(createElement("h2", {}, "Panier 🛒 :"));
    var total = 0;
    for(var achat of achats){
        let achatActuel = achat;
        total += parseInt(achat.prix)*parseInt(achat.quantite);
        var a = createElement("div", {className:"achat"}, [
            createElement("div", {className:"titreAchat"}, achat.nom),
            createElement("img", {className: "imageAchat", src:"assets/articles/"+achat.id+".png"}),
            createElement("input", {type:"number", min:1, max:achat.restants, value:achat.quantite, className:"quantiteAchat"}, "", {change:function(event){
                ajouterPanier(achatActuel.id, event.target.value);
            }}),
            createElement("span", {className:"fermeture"}, "❌", {click: function(){
                ajouterPanier(achatActuel.id, 0);
            }}),
            createElement("span", {className:"prixAchat"}, achat.prix+"€"),
            createElement("span", {className:"restantsAchat"}, "Dispo : "+achat.restants)
        ]);
        panierDiv.appendChild(a);
    }
    if(achats.length!=0){
        var totalDiv = createElement("div", {className: "total"}, "Total : "+total+"€");
        panierDiv.appendChild(totalDiv);
        panierDiv.appendChild(createElement("button", {className:"validerPanier button"}, "Acheter", {click: function(){ popupPaiement(total);}}));
    }
    else panierDiv.appendChild(createElement("div", {className:"panierVide"}, "Votre panier est vide"));
}
    
function popupPaiement(total){
    var adresse, codePostal, ville, pays, enregistrer, erreur;
    popupAdresse = popup([createElement("div", {className: "titre"}, "Adresse de livraison :"), 
        adresse = createElement("input", {placeholder: "Adresse", value: adresseEnregistre}),
        codePostal = createElement("input", {placeholder: "Code postal", type: "number", value: codePostalEnregistre}),
        ville = createElement("input", {placeholder: "Ville", value: villeEnregistre}),
        pays = createElement("input", {placeholder: "Pays", value: paysEnregistre}),
        erreur = createElement("span", {className: "form-error"}),
        enregistrer = createElement("input", {id: "enregistrer", type: "checkbox", checked: true}), 
        createElement("label", {htmlFor: "enregistrer"}, "Définir comme adresse de livraison par défaut"),
        createElement("div", {className: "totalDiv"}, "Montant de votre commande : "+total+"€"),
        createElement("button", {className: "bouton"}, "Valider la commande !", {click: function(){
            if(!adresse.value || !codePostal.value || !ville.value || !pays.value)
                return(erreur.innerText="Tous les champs ne sont pas complétés !");
            sendRequest("POST", "paiement.php", "adresse="+encodeURIComponent(adresse.value)+"&codePostal="+codePostal.value+"&ville="+encodeURIComponent(ville.value)+"&pays="+encodeURIComponent(pays.value)+"&enregistrer="+enregistrer.checked+"&total="+total).then(function(reponse) {
                if(reponse == "[]"){
                    adresseEnregistre = adresse.value;
                    codePostalEnregistre = codePostal.value;
                    villeEnregistre = ville.value;
                    paysEnregistre = pays.value;
                    afficherPanier(achats=JSON.parse(reponse));
                }
            });
            removePopup(popupAdresse);
            popup([createElement("div", {className:"popupPaiement"}, [
                createElement("div", {ClassName:"textPaiement"}, "L'achat a bien été effectué")
            ])]);
        }})]);
}
    
    