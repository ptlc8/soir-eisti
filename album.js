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