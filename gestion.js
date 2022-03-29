function switchPanel(panelName) {
    for (let p of ["events", "utilisateurs", "articles", "sondages"]) {
        document.getElementById(p).style.display = p==panelName ? "" : "none"; 
    }
}


/* Gestion des évents */
function refreshEvents() {
    let eventsDiv = document.getElementById("events");
    let eventsListe = eventsDiv.getElementsByClassName("liste")[0];
    eventsListe.innerHTML = "";
    for (let event of events) {
        let finalEvent = event;
        eventsListe.appendChild(createElement("span", {}, event.nom+" ("+event.id+")", {click:function() {
            editerEvent(finalEvent);
        }}));
    }
    eventsListe.appendChild(createElement("span", {className:"bouton-ajouter"}, "Créer un évent", {click: function() {
        ajouterEvent();
    }}));
}
function editerEvent(event) {
    document.querySelector("#events .indication").style.display = "none";
    var editeur = document.querySelector("#events .editeur");
    editeur.style.display = "";
    editeur.elements.nom.value = event.nom;
    editeur.elements.prix.value = event.prix;
    editeur.elements.date.value = event.date;
    editeur.elements.description.value = event.description;
    editeur.elements.lieu.value = event.lieu;
    editeur.elements.lat.value = event.lat;
    editeur.elements.lon.value = event.lon;
    document.getElementById("event-image").src = "assets/events/"+event.id+".png";
    editingEventId = event.id;
    document.getElementById("event-changer-image").style.display = "";
	document.getElementById("event-enregistrer").style.display = "";
	document.getElementById("event-supprimer").style.display = "";
	document.getElementById("event-publier").style.display = "none";
}
function ajouterEvent() {
    document.querySelector("#events .indication").style.display = "none";
    var editeur = document.querySelector("#events .editeur");
    editeur.style.display = "";
    editeur.elements.nom.value = "";
    editeur.elements.prix.value = "";
    editeur.elements.date.value = "";
    editeur.elements.description.value = "";
    editeur.elements.lieu.value = "";
    editeur.elements.lat.value = "";
    editeur.elements.lon.value = "";
    document.getElementById("event-image").src = "";
    editingEventId = -1;
    document.getElementById("event-changer-image").style.display = "";
	document.getElementById("event-enregistrer").style.display = "none";
	document.getElementById("event-supprimer").style.display = "none";
	document.getElementById("event-publier").style.display = "";
}
function changerImageEvent(input) {
    var image = document.getElementById("event-image");
    var files = input.files;
    if (files.length === 0) return;
    // TODO : tester le type de fichier ?
    var reader = new FileReader();
    reader.onload = function(e) {
        image.src = e.target.result;
    };
    reader.readAsDataURL(files[0]);
}
function modifierEvent() {
    var editeur = document.querySelector("#events .editeur");
    var data = "id="+editingEventId;
    let image = document.getElementById("event-image");
    let editImage = false;
    if (!image.src.endsWith(editingEventId+".png")) {
        data += "&image="+encodeURIComponent(image.src);
        editImage = true;
    }
    var event = events[events.findIndex(e=>e.id==editingEventId)];
    for (let para of ["nom", "prix", "description", "date", "lieu", "lat", "lon"])
        if (event[para]!=editeur.elements[para].value)
            data += "&"+para+"="+encodeURIComponent(editeur.elements[para].value);
    sendRequest("POST", "modifier-event.php", data).then(function(reponse) {
        if (reponse=="succes") {
            alert("L'évent a bien été enregistré");
            let event = events.find(e=>e.id==editingEventId);
            for (let para of ["nom", "prix", "description", "date", "lieu", "lat", "lon"])
                event[para] = editeur.elements[para].value;
            refreshEvents();
            if (editImage) {
                sendRequest("GET", "assets/events/"+editingEventId+".png", "", {"Cache-Control": "no-cache, must-revalidate"}).then(function() {
                    image.src = "assets/events/"+editingEventId+".png";
                });
            }
        } else
            alert(reponse);
    }).catch(function(error) {
        alert("Une erreur interne est survenue ("+error+")")
    });
}
function publierEvent() {
    var editeur = document.querySelector("#events .editeur");
    var data = "nom="+encodeURIComponent(editeur.elements["nom"].value);
    let image = document.getElementById("event-image");
    let editImage = false;
    if (image.src=="")
        return alert("Il manque une image");
    data += "&image="+encodeURIComponent(image.src);
    editImage = true;
    for (let para of ["prix", "description", "date", "lieu", "lat", "lon"])
        data += "&"+para+"="+encodeURIComponent(editeur.elements[para].value);
    sendRequest("POST", "ajouter-event.php", data).then(function(reponse) {
        if (reponse.startsWith("succes")) {
            alert("L'évent a bien été ajouté");
            let event = {id:parseInt(reponse.replace("succes ", ""))};
            for (let para of ["nom", "prix", "description", "date", "lieu", "lat", "lon"])
                event[para] = editeur.elements[para].value;
            events.push(event);
            editingEventId = event.id;
            refreshEvents();
            if (editImage) {
                sendRequest("GET", "assets/events/"+editingEventId+".png", "", {"Cache-Control": "no-cache, must-revalidate"}).then(function() {
                    image.src = "assets/events/"+editingEventId+".png";
                });
            }
        } else
            alert(reponse);
    }).catch(function(error) {
        alert("Une erreur interne est survenue ("+error+")")
    });
}
function supprimerEvent() {
    if (!confirm("Voulez-vous vraiment supprimer cet event ? Cela est définitif.")) return;
    if (!confirm("Cela est vraiment définitif ! Vous devrez rembourser les reservations deja prises.")) return;
    if (!confirm("Vraiment ?")) return;
    sendRequest("POST", "supprimer-event.php", "id="+editingEventId).then(function(reponse) {
        if (reponse == "succes") {
            events.splice(events.findIndex(e=>e.id==editingEventId), 1);
            refreshEvents();
        } else alert(reponse);
    })
}


/* Gestion des utilisateurs */
function refreshUtilisateurs() {
    let utilisateursDiv = document.getElementById("utilisateurs");
    let utilisateursListe = utilisateursDiv.getElementsByClassName("liste")[0];
    utilisateursListe.innerHTML = "";
    for (let utilisateur of utilisateurs) {
        let finalUtilisateur = utilisateur;
        utilisateursListe.appendChild(createElement("span", {}, utilisateur.pseudo+" ("+utilisateur.id+")", {click:function() {
            editerUtilisateur(finalUtilisateur);
        }}));
    }
}
function editerUtilisateur(utilisateur) {
    document.querySelector("#utilisateurs .indication").style.display = "none";
    var editeur = document.querySelector("#utilisateurs .editeur");
    editeur.style.display = "";
    editeur.elements.pseudo.value = utilisateur.pseudo;
    editeur.elements.nom.value = utilisateur.nom;
    editeur.elements.prenom.value = utilisateur.prenom;
    editeur.elements.email.value = utilisateur.email;
    editeur.elements.admin.value = utilisateur.admin;
    editeur.elements.adresse.value = utilisateur.adresse;
    editeur.elements.codePostal.value = utilisateur.codePostal;
    editeur.elements.ville.value = utilisateur.ville;
    editeur.elements.pays.value = utilisateur.pays;
    document.getElementById("utilisateur-image").src = "assets/utilisateurs/"+utilisateur.id+".png";
    editingUtilisateurId = utilisateur.id;
    document.getElementById("utilisateur-changer-image").style.display = "";
	document.getElementById("utilisateur-enregistrer").style.display = "";
	document.getElementById("utilisateur-supprimer").style.display = "";
}
function changerImageUtilisateur(input) {
    var image = document.getElementById("utilisateur-image");
    var files = input.files;
    if (files.length === 0) return;
    // TODO : tester le type de fichier ?
    var reader = new FileReader();
    reader.onload = function(e) {
        image.src = e.target.result;
    };
    reader.readAsDataURL(files[0]);
}
function modifierUtilisateur() {
    var editeur = document.querySelector("#utilisateurs .editeur");
    var data = "id="+editingUtilisateurId;
    let image = document.getElementById("utilisateur-image");
    let editImage = false;
    if (!image.src.endsWith(editingUtilisateurId+".png")) {
        data += "&image="+encodeURIComponent(image.src);
        editImage = true;
    }
    var utilisateur = utilisateurs[utilisateurs.findIndex(e=>e.id==editingUtilisateurId)];
    for (let para of ["pseudo", "nom", "prenom", "email", "admin", "adresse", "codePostal", "ville", "pays"])
        if (utilisateur[para]!=editeur.elements[para].value)
            data += "&"+para+"="+encodeURIComponent(editeur.elements[para].value);
    sendRequest("POST", "modifier-utilisateur.php", data).then(function(reponse) {
        if (reponse=="succes") {
            alert("L'utilisateur a bien été modifié");
            let utilisateur = utilisateurs.find(u=>u.id==editingUtilisateurId);
            for (let para of ["pseudo", "nom", "prenom", "email", "admin", "adresse", "codePostal", "ville", "pays"])
                utilisateur[para] = editeur.elements[para].value;
            refreshUtilisateurs();
            if (editImage) {
                sendRequest("GET", "assets/utilisateurs/"+editingUtilisateurId+".png", "", {"Cache-Control": "no-cache, must-revalidate"}).then(function() {
                    image.src = "assets/utilisateurs/"+editingUtilisateurId+".png";
                });
            }
        } else
            alert(reponse);
    }).catch(function(error) {
        alert("Une erreur interne est survenue ("+error+")")
    });
}
function supprimerUtilisateur() {
    if (!confirm("Voulez-vous vraiment supprimer cet utilisateur ? Cela est définitif.")) return;
    if (!confirm("Cela est vraiment définitif ! Il ne pourra plus se connecter et avoir accès à ses réservations.")) return;
    sendRequest("POST", "supprimer-utilisateur.php", "id="+editingUtilisateurId).then(function(reponse) {
        if (reponse == "succes") {
            utilisateurs.splice(utilisateurs.findIndex(u=>u.id==editingUtilisateurId), 1);
            refreshUtilisateurs();
        } else alert(reponse);
    })
}


/* Gestion des articles / de la boutique */
function refreshArticles() {
    let articlesDiv = document.getElementById("articles");
    let articlesListe = articlesDiv.getElementsByClassName("liste")[0];
    articlesListe.innerHTML = "";
    for (let article of articles) {
        let finalArticle = article;
        articlesListe.appendChild(createElement("span", {}, article.nom+" ("+article.id+")", {click:function() {
            editerArticle(finalArticle);
        }}));
    }
    articlesListe.appendChild(createElement("span", {className:"bouton-ajouter"}, "Créer un article", {click: function() {
        ajouterArticle();
    }}));
}
function editerArticle(article) {
    document.getElementById("article-nom").value = article.nom;
    document.getElementById("article-prix").value = article.prix;
    document.getElementById("article-description").value = article.description;
    document.getElementById("article-restants").value = article.restants;
    document.getElementById("article-vendus").innerText = article.vendus+" vendu"+(article.vendus>1?"s":"");
    document.getElementById("article-image").src = "assets/articles/"+article.id+".png";
    editingArticleId = article.id;
    document.querySelector("#articles .indication").style.display = "none";
    document.querySelector("#articles .editeur").style.display = "";
    document.getElementById("article-changer-image").style.display = "";
	document.getElementById("article-enregistrer").style.display = "";
	document.getElementById("article-supprimer").style.display = "";
	document.getElementById("article-publier").style.display = "none";
}
function ajouterArticle() {
    document.getElementById("article-nom").value = "";
    document.getElementById("article-prix").value = "";
    document.getElementById("article-description").value = "";
    document.getElementById("article-restants").value = "";
    document.getElementById("article-vendus").innerText = 0+" vendu";
    document.getElementById("article-image").src = "";
    editingArticleId = -1;
    document.querySelector("#articles .indication").style.display = "none";
    document.querySelector("#articles .editeur").style.display = "";
    document.getElementById("article-changer-image").style.display = "";
	document.getElementById("article-enregistrer").style.display = "none";
	document.getElementById("article-supprimer").style.display = "none";
	document.getElementById("article-publier").style.display = "";
}
function changerImageArticle(input) {
    var image = document.getElementById("article-image");
    var files = input.files;
    if (files.length === 0) return;
    // TODO : tester le type de fichier ?
    var reader = new FileReader();
    reader.onload = function(e) {
        image.src = e.target.result;
    };
    reader.readAsDataURL(files[0]);
}
function modifierArticle() {
    var editeur = document.querySelector("#articles .editeur");
    var data = "id="+editingArticleId;
    let image = document.getElementById("article-image");
    let editImage = false;
    if (!image.src.endsWith(editingArticleId+".png")) {
        data += "&image="+encodeURIComponent(image.src);
        editImage = true;
    }
    var article = articles[articles.findIndex(e=>e.id==editingArticleId)];
    for (let para of ["nom", "prix", "description", "restants"])
        if (article[para]!=editeur.elements[para].value)
            data += "&"+para+"="+encodeURIComponent(editeur.elements[para].value);
    sendRequest("POST", "modifier-article.php", data).then(function(reponse) {
        if (reponse=="succes") {
            alert("L'article a bien été enregistré");
            let article = articles.find(a=>a.id==editingArticleId);
            for (let para of ["nom", "prix", "description", "restants"])
                article[para] = editeur.elements[para].value;
            refreshArticles();
            if (editImage) {
                sendRequest("GET", "assets/articles/"+editingArticleId+".png", "", {"Cache-Control": "no-cache, must-revalidate"}).then(function() {
                    image.src = "assets/articles/"+editingArticleId+".png";
                });
            }
        } else
            alert(reponse);
    }).catch(function(error) {
        alert("Une erreur interne est survenue ("+error+")")
    });
}
function publierArticle() {
    var editeur = document.querySelector("#articles .editeur");
    var data = "nom="+encodeURIComponent(editeur.elements["nom"].value);
    let image = document.getElementById("article-image");
    if (image.src=="")
        return alert("Il manque une image");
    data += "&image="+encodeURIComponent(image.src);
    for (let para of ["prix", "description", "restants"])
        data += "&"+para+"="+encodeURIComponent(editeur.elements[para].value);
    sendRequest("POST", "ajouter-article.php", data).then(function(reponse) {
        if (reponse.startsWith("succes")) {
            alert("L'article a bien été ajouté");
            let article = {id:parseInt(reponse.replace("succes ", ""))};
            for (let para of ["nom", "prix", "description", "restants"])
                article[para] = editeur.elements[para].value;
            articles.push(article);
            editingArticleId = article.id;
            refreshArticles();
            image.src = "assets/articles/"+editingArticleId+".png";
        } else
            alert(reponse);
    }).catch(function(error) {
        alert("Une erreur interne est survenue ("+error+")")
    });
}
function supprimerArticle() {
    if (!confirm("Voulez-vous vraiment supprimer cet article de la boutique ? Cela est définitif.")) return;
    sendRequest("POST", "supprimer-article.php", "id="+editingArticleId).then(function(reponse) {
        if (reponse == "succes") {
            articles.splice(articles.findIndex(a=>a.id==editingArticleId), 1);
            refreshArticles();
        } else alert(reponse);
    })
}

function switchPanel(panelName) {
    for (let p of ["events", "utilisateurs", "articles", "sondages"]) {
        document.getElementById(p).style.display = p==panelName ? "" : "none"; 
    }
}

function refreshSondages() {
    let sondagesDiv = document.getElementById("sondages");
    let sondagesListe = sondagesDiv.getElementsByClassName("liste")[0];
    sondagesListe.innerHTML = "";
    for (let sondage of sondages) {
        let finalSondage = sondage;
        sondagesListe.appendChild(createElement("span", {}, sondage.question+" ("+sondage.id+")", {click:function() {
            editerSondage(finalSondage);
        }}));
    }
    sondagesListe.appendChild(createElement("span", {className:"bouton-ajouter"}, "Créer un sondage", {click: function() {
        ajouterSondage();
    }}));
}
function editerSondage(sondage) {
    document.querySelector("#sondages .indication").style.display = "none";
    var editeur = document.querySelector("#sondages .editeur");
    editeur.style.display = "";
    editeur.elements.question.value = sondage.question;
    reponsesDiv = editeur.getElementsByClassName("reponses")[0];
    reponsesDiv.innerHTML = "";
    for(let reponse of sondage.reponses){
        ajouterReponse(reponsesDiv, reponse.nom, reponse.couleur, reponse.votes);
    }
    editingSondageId = sondage.id;
	document.getElementById("sondage-enregistrer").style.display = "";
	document.getElementById("sondage-supprimer").style.display = "";
	document.getElementById("sondage-publier").style.display = "none";
}
function ajouterSondage() {
    document.querySelector("#sondages .indication").style.display = "none";
    var editeur = document.querySelector("#sondages .editeur");
    editeur.style.display = "";
    editeur.elements.question.value = "";
    reponsesDiv = editeur.getElementsByClassName("reponses")[0];
    reponsesDiv.innerHTML = "";
    editingSondageId = -1;
	document.getElementById("sondage-enregistrer").style.display = "none";
	document.getElementById("sondage-supprimer").style.display = "none";
	document.getElementById("sondage-publier").style.display = "";
}
function enregistrerSondage() {
    var editeur = document.querySelector("#events .editeur");
    var data = "id="+editingSondageId;
    /*for (let para of ["nom", "date", "description", "lieu", "lat", "lon"])
        if (sondages[editingSondageId][para]!=editeur.elements[para].value)
            data += "&"+para+"="+encodeURIComponent(editeur.elements[para].value);*/
    sendRequest("POST", "modifier-sondage.php", data).then(function(reponse) {
        
    }).catch(function(error) {
        
    });
}
function modifierSondage() {
    var editeur = document.querySelector("#sondages .editeur");
    var data = "id="+editingSondageId;
    var sondage = sondages[sondages.findIndex(e=>e.id==editingSondageId)];
    for (let para of ["question"])
        if (sondage[para]!=editeur.elements[para].value)
            data += "&"+para+"="+encodeURIComponent(editeur.elements[para].value);
    var reponses = [];
    for (let i = 0; document.getElementById("sondage-reponse"+i); i++)
        reponses.push({reponse:document.getElementById("sondage-reponse"+i).value, couleur:document.getElementById("sondage-couleur"+i).value, votes:document.getElementById("sondage-votes"+i).value||0});
    data += "&"+reponses+"="+encodeURIComponent(JSON.stringify(reponses));
    sendRequest("POST", "modifier-sondage.php", data).then(function(reponse) {
        if (reponse=="succes") {
            alert("Le sondage a bien été enregistré");
            let sondage = sondages.find(s=>s.id==editingSondageId);
            for (let para of ["question"])
                sondage[para] = editeur.elements[para].value;
            sondage.reponses = reponses;
            refreshSondages();
        } else
            alert(reponse);
    }).catch(function(error) {
        alert("Une erreur interne est survenue ("+error+")")
    });
}
function publierSondage() {
    var editeur = document.querySelector("#sondages .editeur");
    var data = "question="+encodeURIComponent(editeur.elements["question"].value);
    var reponses = [];
    for (let i = 0; document.getElementById("sondage-reponse"+i); i++)
        reponses.push({reponse:document.getElementById("sondage-reponse"+i).value, couleur:document.getElementById("sondage-couleur"+i).value, votes:document.getElementById("sondage-votes"+i).value||0});
    data += "&reponses="+encodeURIComponent(JSON.stringify(reponses));
    sendRequest("POST", "ajouter-sondage.php", data).then(function(reponse) {
        if (reponse.startsWith("succes")) {
            alert("L'sondage a bien été ajouté");
            let sondage = {id:parseInt(reponse.replace("succes ", "")), reponses:reponses};
            sondages.push(sondage);
            editingSondageId = sondage.id;
            refreshSondages();
        } else
            alert(reponse);
    }).catch(function(error) {
        alert("Une erreur interne est survenue ("+error+")")
    });
}
function supprimerSondage() {
    if (!confirm("Voulez-vous vraiment supprimer ce sondage ? Cela est définitif.")) return;
    sendRequest("POST", "supprimer-sondage.php", "id="+editingSondageId).then(function(reponse) {
        if (reponse == "succes") {
            sondages.splice(sondages.findIndex(a=>a.id==editingSondageId), 1);
            refreshSondages();
        } else alert(reponse);
    })
}

function supprimerReponse(div){
    div.parentElement.parentElement.removeChild(div.parentElement);
}

function ajouterReponse(div, nom="reponse", couleur="#FFFFFF", votes=0){
    let index = div.children.length;
    a = createElement("div", {className: "reponse"}, [
        createElement("input", {className: "reponse", placeholder:"Réponse", name: "reponse", value:nom, id:"sondage-reponse"+index}),
        createElement("input", {className: "couleur", name: "couleur", type: "color", value:couleur, id:"sondage-couleur"+index}),
        createElement("input", {className: "votes", name: "votes", type: "number", disabled: "true", value:votes, id:"sondage-votes"+index}),
        createElement("button", {type: "button"}, "-", {click: function(event){supprimerReponse(event.target);}})]);
    div.appendChild(a);
}