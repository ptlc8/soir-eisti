function changeMdp() {
    let ancienMdp = createElement("input", {type:"password", placeholder:"Ancien Mot de passe", name:"ancienMdp"});
    let nouveauMdp = createElement("input", {type:"password", placeholder:"Nouveau Mot de passe", name:"nouveauMdp"});
    let confirmMdp = createElement("input", {type:"password", placeholder:"Confirmer Nouveau Mot de passe", name:"confirmMdp"});
    let formError = createElement("span", {className:"form-error"});
    popup([
        ancienMdp, nouveauMdp, confirmMdp, formError,
        createElement("button", {}, "Changer", {click: function(e) {
            if (ancienMdp.value=="" || nouveauMdp.value=="" || confirmMdp.value=="")
                return formError.innerText = "Veuillez compléter tous les champs";
            sendRequest("POST", "changeMdp.php", "ancienMdp="+encodeURIComponent(ancienMdp.value)+"&nouveauMdp="+encodeURIComponent(nouveauMdp.value)+"&confirmMdp="+encodeURIComponent(confirmMdp.value)).then(function(reponse) {
                if (reponse=="invalid")
                    formError.innerText = "un ou plusieurs mot de passe invalide(s)";
                else if (reponse=="success")
                    window.location.reload();
                else
                    formError.innerText = "Une erreur interne est survenue 😳";
            }).catch(function(error) {
                formError.innerText = "Une erreur ("+error+") est survenue 😳";
            })
        }})
    ]);
  
    ancienMdp.focus();
}

function changeAdresse() {
    let adresse = createElement("input", {type:"text", placeholder:"Adresse de livraison", name:"adresse"});
    let ville = createElement("input", {type:"text", placeholder:"Ville", name:"ville"});
    let codePostal = createElement("input", {type:"text", placeholder:"Code Postal", name:"codePostal"});
    let pays = createElement("input", {type:"text", placeholder:"Pays", name:"pays"});
    let formError = createElement("span", {className:"form-error"});
    popup([
        adresse, ville, codePostal, pays, formError,
        createElement("button", {}, "Changer", {click: function(e) {
            if (adresse.value=="" || ville.value=="" || codePostal.value=="" || pays.value=="")
                return formError.innerText = "Veuillez compléter tous les champs";
            sendRequest("POST", "changeAdresse.php", "adresse="+encodeURIComponent(adresse.value)+"&ville="+encodeURIComponent(ville.value)+"&codePostal="+encodeURIComponent(codePostal.value)+"&pays="+encodeURIComponent(pays.value)).then(function(reponse) {
                if (reponse=="invalid")
                    formError.innerText = "Une erreur interne est survenue 😳";
                else if (reponse=="success")
                    window.location.reload();
                else
                    formError.innerText = "Une erreur interne est survenue 😳";
            }).catch(function(error) {
                formError.innerText = "Une erreur ("+error+") est survenue 😳";
            })
        }})
    ]);
  
    adresse.focus();
}

function changeInfo(){
    let pseudo = createElement("input", {type:"text", placeholder:"Pseudo", name:"pseudo"});
    let nom = createElement("input", {type:"text", placeholder:"Nom", name:"nom"});
    let prenom = createElement("input", {type:"text", placeholder:"Prénom", name:"prenom"});
    let email = createElement("input", {type:"text", placeholder:"Email", name:"email"});
    let formError = createElement("span", {className:"form-error"});
    popup([
        pseudo, nom, prenom, email, formError,
        createElement("button", {}, "Changer", {click: function(e){
            if (pseudo.value=="" || nom.value=="" || prenom.value=="" || email.value=="")
                return formError.innerText = "Veuillez compléter tous les champs";
            sendRequest("POST", "changeInfo.php", "pseudo="+encodeURIComponent(pseudo.value)+"&nom="+encodeURIComponent(nom.value)+"&prenom="+encodeURIComponent(prenom.value)+"&email="+encodeURIComponent(email.value)).then(function(reponse) {
                if (reponse=="invalid")
                    formError.innerText = "Une erreur interne est survenue 😳";
                else if (reponse=="success")
                    window.location.reload();
                else if (reponse=="pseudoincorrect")
                    formError.innerText = "Pseudo déjà utilisé";
                else if (reponse=="emailincorrect")
                    formError.innerText = "Email déjà utilisé";
                else
                    formError.innerText = "Une erreur interne est survenue 😳";
            }).catch(function(error) {
                formError.innerText = "Une erreur ("+error+") est survenue 😳";
            })
        }})
    ]);
    pseudo.focus();
}

function selectPdp() {
    document.getElementById("pdp-input").click();
}
function changePdp(input) {
    if(input.files.length == 0) return;
    if (!input.files[0].type.startsWith("image"))
        return alert("Le fichier choisi doit être une image");
    var reader = new FileReader();
    reader.onload = function(e) {
        input.parentElement.classList.add("chargement");
        var pdp = e.target.result;
        sendRequest("POST", "changePdp.php", "pdp="+encodeURIComponent(pdp)).then(function(reponse){
            input.parentElement.classList.remove("chargement");
            if (reponse == "success") {
                sendRequest("GET", document.getElementById("pdp").src, "", {"Cache-Control": "no-cache, must-revalidate"}).then(function(){
                    document.getElementById("pdp").src += "";
                });
            } else if (reponse == "invalid image")
                alert("L'image que tu as envoyé n'est pas valide, seule une image en JPEG, PNG, GIF, BMP, WBMP, GD2 et WEBP sera acceptée.");
            else
                alert(reponse);
        })
    }
    reader.readAsDataURL(input.files[0]);
}