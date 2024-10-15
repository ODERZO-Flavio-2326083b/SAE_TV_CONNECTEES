/**
 * Get the date in french format
 */
function dateFr() {
    // les noms de jours / mois
    let jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    let mois = ["janvier", "fevrier", "mars", "avril", "mai", "juin", "juillet", "aout", "septembre", "octobre", "novembre", "decembre"];
    // on recupere la date
    let date = new Date();
    // on construit le message
    let message = jours[date.getDay()] + " ";   // nom du jour
    message += date.getDate() + " ";   // numero du jour
    message += mois[date.getMonth()] + " ";   // mois
    message += date.getFullYear();
    if (document.getElementById('Date') !== null)
        document.getElementById("Date").innerHTML = message;
}

/**
 * Get the time
 */
function heure() {
    let date = new Date();
    let heure = date.getHours();
    let minutes = date.getMinutes();
    let seconds = date.getSeconds();
    if (minutes < 10)
        minutes = "0" + minutes;
    if (seconds < 10)
        seconds = "0" + seconds;
    if (document.getElementById('Time') !== null)
        document.getElementById("Time").innerHTML = heure + ":" + minutes + ":" + seconds;
}

setInterval(dateFr, 1000);
setInterval(heure, 1000);