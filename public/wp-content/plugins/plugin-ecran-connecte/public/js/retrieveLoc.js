/**
 * Permet d'envoyer par ajax les données de localisation
 * de l'utilisateur
 * @param longitude
 * @param latitude
 */
function sendMeteoData(longitude, latitude) {
    // Construire un objet FormData avec les données à envoyer
    var formData = new FormData();
    formData.append('action', 'handle_meteo_data'); // Spécifiez l'action AJAX
    formData.append('longitude', longitude);
    formData.append('latitude', latitude);

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

var options = {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0,
};

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
