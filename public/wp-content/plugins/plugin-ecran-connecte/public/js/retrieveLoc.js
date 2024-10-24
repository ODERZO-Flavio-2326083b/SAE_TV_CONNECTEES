
// étape 1 : récupérer les informations de localisation de l'utilisateur

var options = {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0,
};

/**
 * Récupère les informations de localisation de l'utilisteur et les envoie à la fonction
 * de traitement AJAX
 * @param pos
 */
function success(pos) {
    var crd = pos.coords;

    console.log("Votre position actuelle est :");
    console.log(`Latitude : ${crd.latitude}`);
    console.log(`Longitude : ${crd.longitude}`);
    console.log(`La précision est de ${crd.accuracy} mètres.`);

    sendMeteoData(crd.longitude, crd.latitude);
}

function error(err) {
    console.warn(`ERREUR (${err.code}): ${err.message}`);
}

navigator.geolocation.getCurrentPosition(success, error, options);

/**
 * Permet d'envoyer par AJAX les données de localisation
 * de l'utilisateur.
 * Des variables sont injectées depuis le backend et récupérées
 * avec retrieveLocVars.
 * @param longitude
 * @param latitude
 */
function sendMeteoData(longitude, latitude) {
    // Construire un objet FormData avec les données à envoyer
    var formData = new FormData();
    formData.append('action', 'handleMeteoAjaxData'); // Spécifiez l'action AJAX
    formData.append('longitude', longitude);
    formData.append('latitude', latitude);
    formData.append('nonce', retrieveLocVars.ajaxNonce);
    formData.append('currentUserId', retrieveLocVars.currentUserId);

    console.log(retrieveLocVars.ajaxUrl);
    // Envoyer les données au serveur via l'URL AJAX
    fetch(retrieveLocVars.ajaxUrl, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Succès:', data);
            } else {
                console.log('Erreur:', data.data);
            }
        })
        .catch(error => console.error('Erreur de requête:', error));
}
