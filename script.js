function connexion() {
    let pseudo = createElement("input", {type:"text", placeholder:"Pseudo", name:"pseudo"});
    let mdp = createElement("input", {type:"password", placeholder:"Mot de passe", name:"mdp"});
    let formError = createElement("span", {className:"form-error"});
    var connecter = function(e) {
        if (pseudo.value=="" || mdp.value=="")
            return formError.innerText = "Veuillez compléter tous les champs";
        sendRequest("POST", "connexion.php", "pseudo="+encodeURIComponent(pseudo.value)+"&mdp="+encodeURIComponent(mdp.value)).then(function(reponse) {
            if (reponse=="invalid")
                formError.innerText = "Pseudo ou mot de passe invalide(s)";
            else if (reponse=="success")
                window.location.reload();
            else
                formError.innerText = "Une erreur interne est survenue 😳";
        }).catch(function(error) {
            formError.innerText = "Une erreur ("+error+") est survenue 😳";
        });
    };
    popup([
        createElement("a", {}, "Inscription", {click: function(e) {
            e.target.parentElement.parentElement.parentElement.removeChild(e.target.parentElement.parentElement);
            inscription();
        }}),
        pseudo, mdp, formError,
        createElement("button", {}, "Connexion", {click: connecter}),
        createElement("a", {}, "Mot de passe oublié", {click: reinitialisation})
    ]);
    pseudo.focus();
}

function inscription() {
    let pseudo = createElement("input", {type:"text", placeholder:"Pseudo"});
    let prenom = createElement("input", {type:"text", placeholder:"Prénom"});
    let nom = createElement("input", {type:"text", placeholder:"Nom"});
    let email = createElement("input", {type:"email", placeholder:"E-mail"});
    let mdpPower = createElement("div", {className:"mdp-power"});
    let mdp = createElement("input", {type:"password", placeholder:"Mot de passe"}, [], {keyup: function(e) {
        let robustesse = Math.min(calculRobustesseMdp(e.target.value), 100);
        mdpPower.style.width = "calc("+robustesse+"% - "/*+1.6*robustesse/100*/+"0em)";
        mdpPower.style.backgroundColor = "rgb("+Math.min(255,2*255-robustesse*255/50)+","+Math.min(255,robustesse*255/50)+",0)";
    }});
    let mdpConfirmation = createElement("input", {type:"password", placeholder:"Confirmation de mot de passe"});
    let formError = createElement("span", {className:"form-error"});
    
    var inscrire = function(e) {
        if (pseudo.value=="" || prenom.value=="" || nom.value=="" || email.value=="" || mdp.value=="" || mdpConfirmation.value=="")
            return formError.innerText = "Veuillez compléter tous les champs";
        else if(email.value.match(/.+@.+/)==null)
            return formError.innerText = "E-mail invalide";
        else if(mdp.value!==mdpConfirmation.value)
            return formError.innerText = "Mot de passe différent de Confirmation de mot de passe";
        sendRequest("POST", "inscription.php", "pseudo="+encodeURIComponent(pseudo.value)+"&prenom="+encodeURIComponent(prenom.value)+"&nom="+encodeURIComponent(nom.value)+"&email="+encodeURIComponent(email.value)+"&mdp="+encodeURIComponent(mdp.value)).then(function(reponse) {
            if (reponse=="invalid")
                formError.innerText = "Pseudo ou e-mail déjà existant(s)";
            else if (reponse=="success")
                window.location.reload();
            else
                formError.innerText = "Une erreur interne est survenue 😳";
        }).catch(function(error) {
            formError.innerText = "Une erreur ("+error+") est survenue 😳";
        });
    }
    popup([
        createElement("a", {}, "Connexion", {click: function(e) {
            e.target.parentElement.parentElement.parentElement.removeChild(e.target.parentElement.parentElement);
            connexion();
        }}),
        pseudo, prenom, nom, email, mdp, mdpPower, mdpConfirmation, formError,
        createElement("button", {}, "Inscription", {click : inscrire})
    ]);
    pseudo.focus();
}

function deconnexion() {
    sendRequest("POST", "deconnexion.php").then(function(reponse) {
        if (reponse=="success")
            window.location.reload();
        else
            alert("Une erreur interne est survenue lors de la déconnexion");
    }).catch(function(errorCode) {
        alert("Une erreur est survenue lors de la déconnexion ("+errorCode+")");
    });
}

function reinitialisation(){
    let email = createElement("input", {placeholder: "E-mail"});
    let formError = createElement("span", {className:"form-error"});
    var reinitialiser = function(){
        if (email.value == "")
            return formError.innerText = "Veuillez insérer une e-mail";
        sendRequest("POST", "mdp-oublie.php", "email="+encodeURIComponent(email.value)).then(function(reponse){
            if(reponse=="invalid")
                formError.innerText = "E-mail inconnue";
            else if (reponse=="success"){
                email.parentElement.parentElement.parentElement.removeChild(email.parentElement.parentElement);
                alert("Un lien de changement de mot de passe vous a été envoyé à l'adresse : "+email.value);
            }
        })
    };
    
    popup([
        email, formError, createElement("button", {}, "Changer de mot de passe", {click: reinitialiser})
    ]);
    
    email.focus();
}

function calculRobustesseMdp(mdp) {
    var robu = 0;
	if (mdp.match(/[a-z]/))
		robu += 26;
	if (mdp.match(/[A-Z]/))
		robu += 26;
	if (mdp.match(/[0-9]/))
		robu += 10;
	if (mdp.match(/[@\[ \]\^_!"#\$%&'\(\)\*\+,\-\.\/:;{}<>=\|~\?\\'"]/))
		robu += 34;
	if (mdp.match(/[^a-zA-Z0-9@\[ \]\^_!"#\$%&'\(\)\*\+,\-\.\/:;{}<>=\|~\?\\'"]/))
		robu += 50;
	robu = Math.pow(robu, mdp.length);
	return Math.log2(robu);
}

// Les 2 (+1) fonctions toujours utiles d'Ambi ;)
function sendRequest(method, url, body=undefined, headers={"Content-Type":"application/x-www-form-urlencoded"}) {
    var promise = new (Promise||ES6Promise)(function(resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open(method, url);
        for (h of Object.keys(headers))
            xhr.setRequestHeader(h, headers[h]);
        xhr.onreadystatechange = function() {
            if (this.readyState == XMLHttpRequest.DONE)
            if (this.status == 200)
                resolve(this.response);
            else
                reject(this.status);
        }
        xhr.send(body);
    });
    return promise;
}
function createElement(tag, properties={}, inner=[], eventListeners={}) {
    let el = document.createElement(tag);
    for (let p of Object.keys(properties)) if (p != "style") el[p] = properties[p];
    if (properties.style) for (let p of Object.keys(properties.style)) el.style[p] = properties.style[p];
    if (typeof inner == "object") for (let i of inner) el.appendChild(typeof i == "string" ? document.createTextNode(i) : i);
    else el.innerText = inner;
    for (let l of Object.keys(eventListeners)) el.addEventListener(l, eventListeners[l]);
    return el
}
function popup(content=[]) {
    content = content.slice();
    content.unshift(createElement("button", {className:"close-button"}, "❌", {click: function(e) {
        e.target.parentElement.style.top = "150%";
        setTimeout(()=>document.body.removeChild(e.target.parentElement.parentElement), 500);
    }}));
    var popup;
    var popupContainer = createElement("div", {className:"popup-conteneur"}, [
        popup = createElement("div", {className:"popup"}, content, {mousedown: function(e) {
            e.stopPropagation();
        }})
    ], {mousedown: function(e) {
        e.target.getElementsByClassName("popup")[0].style.top = "150%";
        setTimeout(()=>document.body.removeChild(e.target),500);
    }});
    document.body.appendChild(popupContainer);
    popup.style.top = "150%";
    getComputedStyle(popup).top;
    popup.style.top = "";
    return popupContainer;
}
function removePopup(popupContainer) {
    popupContainer.getElementsByClassName("popup")[0].style.top = "150%";
    setTimeout(()=>document.body.removeChild(popupContainer), 500);
}


//diapo.php

function defiler(diapo, imagesDiapo, defilement=1){
	var photo = diapo.getElementsByClassName("photo")[0];
	var photoAvant = diapo.getElementsByClassName("photo-avant")[0];
	var photoApres = diapo.getElementsByClassName("photo-apres")[0];
	diapo.numImage+=defilement;
	diapo.numImage = (diapo.numImage%imagesDiapo.length+imagesDiapo.length)%imagesDiapo.length;
	
	if (defilement>0) {
		photo.className = "photo-avant";
		photoApres.className = "photo";
		photoAvant.className = "photo-apres";
		photoAvant.src = imagesDiapo[diapo.numImage==imagesDiapo.length-1 ? 0 : diapo.numImage+1];
		photoAvant.style.visibility = "hidden";
		photoApres.style.visibility = "";
	}else if(defilement<0){
		photo.className = "photo-apres";
		photoApres.className = "photo-avant";
		photoAvant.className = "photo";
		photoApres.src = imagesDiapo[diapo.numImage==0 ? imagesDiapo.length-1 : diapo.numImage-1];
		photoApres.style.visibility = "hidden";
		photoAvant.style.visibility = "";
	}
}

function initialiserDiapo(diapo, imagesDiapo){
	diapo.numImage = 0;
	diapo.getElementsByClassName("photo")[0].src = imagesDiapo[0];
	diapo.getElementsByClassName("photo-avant")[0].src = imagesDiapo[imagesDiapo.length-1];
	diapo.getElementsByClassName("photo-apres")[0].src = imagesDiapo[1];
}



// Thèmes de couleur
function switchColorTheme() {
    if (document.documentElement.classList.contains("dark-mode")) {
        document.documentElement.classList.remove("dark-mode");
        if (window.map)
            window.map.setStyle("mapbox://styles/mapbox/outdoors-v11");
        localStorage.setItem("ayaya.theme", "light");
    } else {
        document.documentElement.classList.add("dark-mode");
        if (window.map)
            window.map.setStyle("mapbox://styles/mapbox/dark-v9");
        localStorage.setItem("ayaya.theme", "dark");
    }
}

if (localStorage.getItem("ayaya.theme") == "dark")
    document.documentElement.classList.add("dark-mode");


// Magnification des images
function magnifyPicture(url) {
    popup([createElement("img", {src:url, className:"magnified"})]);
}

// Messagerie
window.addEventListener("load", function() {
    if (!window.connecte) return;
    var messagerieDiv;
    var inputMessage;
    document.body.appendChild(messagerieDiv = createElement("div", {className:"messagerie"}, [
        createElement("div", {className:"titre"}, "Messagerie", {click: function(e){
            if (messagerieDiv.classList.contains("ouverte"))
                messagerieDiv.classList.remove("ouverte");
            else
                messagerieDiv.classList.add("ouverte");
            e.target.classList.remove("ping");
        }}),
        createElement("div", {className:"corps"}, [
            createElement("div", {className:"conversations"}, [
                createElement("span", {className:"creer"}, "Nouvelle conversation", {click: function(){
                    messagerieDiv.getElementsByClassName("nouvelle")[0].style.display = "";
                }})
            ]),
            createElement("div", {className:"conversation"}, [
                createElement("div", {className:"nouveau-message"}, [
                    createElement("input", {}, [], {keydown:(e)=>e.keyCode==13?envoyerMessage(e.target):""}),
                    createElement("button", {}, ">", {click:(e)=>envoyerMessage(e.target.previousElementSibling)})
                ])
            ]),
            createElement("div", {className:"nouvelle", style:{display:"none"}}, [
                createElement("input", {placeholder:"Pseudo du destinataire"}, [], {change:function(e){
                    var nouvelle = messagerieDiv.getElementsByClassName("nouvelle")[0];
                    sendRequest("GET", "rechercher-utilisateurs.php?pseudo="+encodeURIComponent(e.target.value)).then(function(reponse) {
                        reponse = JSON.parse(reponse);
                        while(nouvelle.children.length > 1)
                            nouvelle.removeChild(nouvelle.lastChild);
                        if (reponse.length == 0)
                            nouvelle.appendChild(createElement("span", {}, "Aucun pseudo ne correspond..."))
                        else for (let utilisateur of reponse) {
                            let idFix = utilisateur.id;
                            nouvelle.appendChild(createElement("span", {}, utilisateur.pseudo, {click:function(e) {
                                nouvelle.style.display = "none";
                                conversationSelectionnee = idFix;
                                rafraichirConversation(messagerieDiv.getElementsByClassName("conversation")[0]);
                            }}));
                        }
                    })
                }})
            ])
        ])
    ]));
    actualiserMessagerie(messagerieDiv);
    setInterval(()=>actualiserMessagerie(messagerieDiv), 10000);
});
var messagerie = undefined;
var conversationSelectionnee = undefined;
var pingAudio = new Audio('assets/plop.mp3');
function actualiserMessagerie(messagerieDiv) {
    sendRequest("GET", "messagerie.php").then(function(reponse) {
        var exMessagerie = messagerie;
        messagerie = JSON.parse(reponse);
        rafraichirMessagerie(messagerieDiv);
        if (exMessagerie) {
            for (let id in messagerie) {
                if (!exMessagerie[id] || messagerie[id].messages.length > exMessagerie[id].messages.length) {
                    messagerieDiv.getElementsByClassName("conv"+id)[0].classList.add("ping");
                    messagerieDiv.getElementsByClassName("titre")[0].classList.add("ping");
                    if (parseInt(id) > 0)
                        pingAudio.play();
                }
            }
        }
    });
}
function rafraichirMessagerie(messagerieDiv) {
    var conversationsDiv = messagerieDiv.getElementsByClassName("conversations")[0];
    var conversationDiv = messagerieDiv.getElementsByClassName("conversation")[0];
    while (conversationsDiv.children.length > 1)
        conversationsDiv.removeChild(conversationsDiv.lastChild);
    for (let id in messagerie) {
        let conversation = messagerie[id];
        let idFix = id;
        conversationsDiv.appendChild(createElement("span", {className:"conv"+id}, conversation.pseudo, {click: function(e) {
            e.target.classList.remove("ping");
            conversationSelectionnee = idFix;
            rafraichirConversation(conversationDiv);
            messagerieDiv.getElementsByClassName("nouvelle")[0].style.display = "none";
        }}));
    }
    rafraichirConversation(conversationDiv)
}
function rafraichirConversation(conversationDiv) {
    while (conversationDiv.children.length > 1)
        conversationDiv.removeChild(conversationDiv.lastChild);
    if (conversationSelectionnee === undefined || !messagerie[conversationSelectionnee]) return;
    for (let message of messagerie[conversationSelectionnee].messages)
        conversationDiv.appendChild(createElement("div", {className:(message.moi?"mon ":"son ")+"message"}, [
            createElement("span", {className:"pseudo"}, conversationSelectionnee==0?message.pseudo:message.moi?"Moi":messagerie[conversationSelectionnee].pseudo),
            createElement("span", {className:"time"}, new Date(message.date).toLocaleDateString("fr-FR", {weekday:'long', year:'numeric', month:'long', day:'numeric', hour:"2-digit", minute:"2-digit"})),
            createElement("span", {className:"texte"}, message.texte)
        ]));
}
function envoyerMessage(input) {
    if (conversationSelectionnee == undefined) return;
    if (!input.value) return;
    sendRequest("POST", "envoyer-message.php", "texte="+encodeURIComponent(input.value)+"&a="+encodeURIComponent(conversationSelectionnee)).then(function(reponse) {
        if (reponse == "succes") {
            if (!messagerie[conversationSelectionnee])
                messagerie[conversationSelectionnee] = {pseudo:"", messages:[]};
            messagerie[conversationSelectionnee].messages.unshift({texte:input.value,date:new Date().toISOString().slice(0, 19).replace('T', ' '),moi:true,pseudo:"Moi"});
            input.value = "";
            rafraichirMessagerie(input.parentElement.parentElement.parentElement.parentElement);
        }
    });
}