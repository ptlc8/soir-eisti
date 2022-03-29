<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Réinitialisation de mot de passe - Soir'EISTI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="reinitialisation.css" />
        <script src="script.js"></script>
        <script>
            window.addEventListener("load", function() {
                var codeMdp = window.location.search.slice(1);
                document.getElementById("reinitialiser").addEventListener("click", function(event) {
                    var mdp = document.getElementById("mdp").value;
                    var mdpConfirmation = document.getElementById("mdp-confirmation").value;
                    var formError = event.target.parentElement.getElementsByClassName("form-error")[0];
                    if (mdp=="" || mdpConfirmation=="")
                        return formError.innerText = "Veuillez compléter tous les champs";
                    if (mdp != mdpConfirmation)
                        return formError.innerText = "Les mots de passe ne correspondent pas";
                    sendRequest("POST", "reinitialisation-mdp.php", "code="+codeMdp+"&mdp="+mdp).then(function(reponse) {
                        if (reponse=="invalid")
                            formError.innerText = "Votre lien de réinitialisation de mot de passe est invalide, expiré ou a déjà été utilisé"
                        else if (reponse=="success") {
                            alert("Votre mot de passe a bien été réinitialisé");
                            window.location.href = ".";
                        } else
                            formError.innerText = "Une erreur interne est survenue 😳";
                    }).catch(function(error) {
                        formError.innerText = "Une erreur ("+error+") est survenue 😳";
                    })
                });
                document.getElementById("mdp").addEventListener("keyup", function(event) {
                    let mdpPower = event.target.parentElement.getElementsByClassName("mdp-power")[0];
                    let robustesse = Math.min(calculRobustesseMdp(event.target.value), 100);
                    //mdpPower.style.width = "calc("+robustesse+"% - "+1.6*robustesse/100+"em)";
                    mdpPower.style.width = robustesse+"%";
                    mdpPower.style.backgroundColor = "rgb("+Math.min(255,2*255-robustesse*255/50)+","+Math.min(255,robustesse*255/50)+",0)";
                })
            });
        </script>
    </head>
    <body>
        <div class="popup">
            <span class="titre">Changement de mot de passe</span>
            <input id="mdp" type="password" placeholder="Mot de passe" />
            <div class="mdp-power-conteneur">
                <div class="mdp-power"></div>
            </div>
            <input type="password" id="mdp-confirmation" placeholder="Confirmation du mot de passe" />
            <span class="form-error"></span>
            <button id="reinitialiser">Changer de mot de passe</button>
        </div>
    </body>
</html>