function updateGraph(budget=[], q=5) {
    var graph = document.getElementById("graph");
    graph.width = 200*q;
    graph.height = 100*q;
    var ctx = graph.getContext("2d");
    // tracé des axes
    ctx.strokeStyle = "#000000";
    ctx.lineWidth = q;
    ctx.beginPath();
    ctx.moveTo(190*q, 90*q);
    ctx.lineTo(20*q, 90*q);
    ctx.lineTo(20*q, 10*q);
    ctx.stroke();
    if (budget.length == 0) return;
    // calcul des points du graphique
    budget = budget.sort(function(a,b) {
        return new Date(a.date) - new Date(b.date);
    });
    var points = {};
    var sum = 0;
    for (let bud of budget) {
        sum += parseFloat(bud.valeur);
        if (points[new Date(bud.date).getTime()])
            points[new Date(bud.date).getTime()] += parseFloat(bud.valeur);
        else
            points[new Date(bud.date).getTime()] = sum;
    }
    // affichage du budget actuel
    document.getElementById("budget").innerText = "Budget : "+sum+"€";
    // calcul de l'échelle
    var startTime = new Date(budget[0].date).getTime();
    var tMax = 190*q;
    var endTime = new Date(budget[budget.length-1].date).getTime();
    var tMin = 20*q;
    var minBudget = 0;
    var bMax = 10*q;
    var maxBudget = Object.values(points).reduce(function(a, b) {return a>b ? a: b;});
    var bMin = 90*q;
    // écriture de valeurs de référence sur les axes
    ctx.font = 5*q+"px Helvetica,sans-serif";
    ctx.textAlign = "start";
    ctx.fillText(new Date(budget[0].date).toLocaleDateString(), 10*q, 98*q);
    ctx.textAlign = "end";
    ctx.fillText(new Date(budget[budget.length-1].date).toLocaleDateString(), 200*q, 98*q);
    ctx.textAlign = "center";
    ctx.fillText(new Date((startTime+endTime)/2).toLocaleDateString(), 105*q, 98*q);
    ctx.textAlign = "end";
    ctx.fillText(minBudget+"€", 17*q, 90*q);
    ctx.fillText(maxBudget+"€", 17*q, 10*q);
    ctx.fillText((minBudget+maxBudget)/2+"€", 17*q, 50*q);
    // tracé de la courbe en fonction de l'échelle
    ctx.strokeStyle = "#dd4444";
    ctx.beginPath();
    for (let t in points) {
        ctx.lineTo((t-startTime)/(endTime-startTime)*(tMax-tMin)+tMin,
            (points[t]-minBudget)/(maxBudget-minBudget)*(bMax-bMin)+bMin);
    }
    ctx.stroke();
}

function updateRecettesDepenses(budget=[]) {
    budget = budget.sort(function(a,b) {
        return new Date(a.date) - new Date(b.date);
    });
    var recettesDiv = document.getElementById("recettes")
    var depensesDiv = document.getElementById("depenses")
    var dateSpan = document.querySelector(".recettes-depenses > .date");
    for (let bud of budget) {
        if (parseFloat(bud.valeur) > 0) {
            recettesDiv.appendChild(createElement("div", {id:bud.id}, [
                createElement("span", {className:"nom"}, bud.nom),
                createElement("span", {className:"prix"}, "+"+bud.valeur+"€"),
                createElement("span", {className:"categorie"}, bud.categorie+" - "+new Date(bud.date).toLocaleDateString())
            ], {click: function() {
                popupBud(bud);
            }}));
        } else {
            depensesDiv.appendChild(createElement("div", {id:bud.id}, [
                createElement("span", {className:"nom"}, bud.nom),
                createElement("span", {className:"prix"}, bud.valeur+"€"),
                createElement("span", {className:"categorie"}, bud.categorie+" - "+new Date(bud.date).toLocaleDateString())
            ], {click: function() {
                popupBud(bud);
            }}));
        }
    }
}

function popupBud(bud) {
    popup([
        createElement("span", {className:"titre"}, bud.nom),
        createElement("span", {className:"prix"}, bud.valeur+"€"),
        createElement("div", {className:"bud-infos"}, [
            createElement("span", {}, bud.categorie),
            createElement("span", {}, "le "+new Date(bud.date).toLocaleDateString()),
            createElement("span", {}, bud.idUtilisateur?"ajouté par "+bud.pseudoUtilisateur:"ajouté automatiquement")
        ])
    ]);
}

function onDepensesScroll(depensesDiv) {
    var depenses = budget.filter((b)=>b.valeur<=0).sort((a,b)=>new Date(a.date)-new Date(b.date));
    var date = new Date(depenses[Math.floor((depensesDiv.scrollTop+depensesDiv.offsetHeight/2)/depensesDiv.scrollHeight*depenses.length)].date);
    if (depensesDiv.mouseIsOver)
        recettesScrollTo(date);
    document.getElementById("date").innerText = date.toLocaleDateString();
}

function onRecettesScroll(recettesDiv) {
    var recettes = budget.filter((b)=>b.valeur>0).sort((a,b)=>new Date(a.date)-new Date(b.date));
    var date = new Date(recettes[Math.floor((recettesDiv.scrollTop+recettesDiv.offsetHeight/2)/recettesDiv.scrollHeight*recettes.length)].date);
    if (recettesDiv.mouseIsOver)
        depensesScrollTo(date);
    document.getElementById("date").innerText = date.toLocaleDateString();
}

function depensesScrollTo(date) {
    var depensesDiv = document.getElementById("depenses");
    if (depensesDiv.mouseIsOver===true) return;
    var depenses = budget.filter((b)=>b.valeur<=0).sort((a,b)=>new Date(a.date)-new Date(b.date));
    for (let i = 0; i < depenses.length; i++) {
        if (new Date(depenses[i].date) >= date) {
            depensesDiv.scrollTo({top:i*(depensesDiv.scrollHeight+depensesDiv.offsetHeight)/depenses.length-depensesDiv.offsetHeight/2, behavior: "smooth"});
            break;
        }
    }
}

function recettesScrollTo(date) {
    var recettesDiv = document.getElementById("recettes");
    if (recettesDiv.mouseIsOver===true) return;
    var recettes = budget.filter((b)=>b.valeur>0).sort((a,b)=>new Date(a.date)-new Date(b.date));
    for (let i = 0; i < recettes.length; i++) {
        if (new Date(recettes[i].date) >= date) {
            recettesDiv.scrollTo({top:i*(recettesDiv.scrollHeight+recettesDiv.offsetHeight)/recettes.length-recettesDiv.offsetHeight/2, behavior:"smooth"});
            break;
        }
    }
}

function ajouterRecette(){
    ajoutRecette = popup([createElement("div", {className:"ajout"}, [
        createElement("div", {className: "titreAjout"}, "Ajouter une recette :"), 
        nom = createElement("input", {className:"nomAjout", placeholder: "Nom recette"}),
        prix = createElement("input", {className:"prixAjout", placeholder: "Prix"}),
        categorie = createElement("input", {className: "categorie", placeholder: "Catégorie"}),
        erreur = createElement("div", {className:"erreurAjout"}),
        createElement("button", {className: "bouton"}, "Ajouter", {click: function(){
            if(!nom.value || !prix.value || !categorie.value)
                return(erreur.innerText="Tous les champs ne sont pas complétés !");
            sendRequest("POST", "ajoutRecette.php", "nom="+encodeURIComponent(nom.value)+"&prix="+prix.value+"&categorie="+encodeURIComponent(categorie.value)).then(function(reponse) {
                if(reponse == "succes"){
                    removePopup(ajoutRecette);
                    popup([createElement("div", {className:"popupValider"}, [
                        createElement("div", {className:"textValider"}, "L'ajout a bien été effectué")
                    ])]);
                }
            });
        }})
    ])]);
    categorie.setAttribute("list", "categories");
}

function ajouterDepense(){
    ajoutDepense = popup([createElement("div", {className:"ajout"}, [
        createElement("div", {className: "titreAjout"}, "Ajouter une dépense :"), 
        nom = createElement("input", {className:"nomAjout", placeholder: "Nom Dépense"}),
        prix = createElement("input", {className:"prixAjout", placeholder: "Prix"}),
        categorie = createElement("input", {className: "categorie", placeholder: "Catégorie"}),
        erreur = createElement("div", {className:"erreurAjout"}),
        createElement("button", {className: "bouton"}, "Ajouter", {click: function(){
            if(!nom.value || !prix.value || !categorie.value)
                return(erreur.innerText="Tous les champs ne sont pas complétés !");
            sendRequest("POST", "ajoutDepense.php", "nom="+encodeURIComponent(nom.value)+"&prix="+prix.value+"&categorie="+encodeURIComponent(categorie.value)).then(function(reponse) {
                if(reponse == "succes"){
                    removePopup(ajoutDepense);
                    popup([createElement("div", {className:"popupValider"}, [
                        createElement("div", {className:"textValider"}, "L'ajout a bien été effectué")
                    ])]);
                }
            });
        }})
    ])]);
    categorie.setAttribute("list", "categories");
}