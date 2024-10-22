var meteoRequest = new XMLHttpRequest();
var longitude = weatherVars.longitude;
var latitude = weatherVars.latitude;
var appId = "ae546c64c1c36e47123b3d512efa723e";
var url = "https://api.openweathermap.org/data/2.5/weather?lat=" + latitude + "&lon=" + longitude + "&lang=fr&APPID=" + appId;

/**
 * Display the weather
 */
function refreshWeather() {
    meteoRequest.open('GET', url, true);
    meteoRequest.setRequestHeader('Accept', 'application/json');
    meteoRequest.send();
}

meteoRequest.onload = function () {
    var json = JSON.parse(this.responseText);
    var temp = Math.round(getTemp(json));
    var vent = getWind(json).toFixed(0);
    if (document.getElementById('Weather') !== null) {
        var div = document.getElementById('Weather');
        div.innerHTML = "";
        var weather = document.createElement("DIV");
        weather.innerHTML = temp + "<span class=\"degree\">°C</span>";
        weather.id = "weather";
        var imgTemp = document.createElement("IMG");
        imgTemp.id = "icon";
        imgTemp.src = "/wp-content/plugins/plugin-ecran-connecte/public/img/" + getIcon(json) + ".png";
        imgTemp.alt = getAlt(json);
        weather.appendChild(imgTemp);
        var wind = document.createElement("DIV");
        wind.innerHTML = vent + "<span class=\"kmh\">km/h</span>";
        wind.id = "wind";
        var imgVent = document.createElement("IMG");
        imgVent.src = "/wp-content/plugins/plugin-ecran-connecte/public/img/wind.png";
        imgVent.alt = "Img du vent";
        wind.appendChild(imgVent);
        div.appendChild(weather);
        div.appendChild(wind);
        setTimeout(refreshWeather, 900000);
    }
};

/*
var cityRequest = new XMLHttpRequest();
var cityUrl = "https://api.openweathermap.org/geo/1.0/reverse?lat=" + latitude + "&lon=" + longitude +"&appid="+appId

function refreshCity() {
    cityRequest.open('GET', cityUrl, true);
    cityRequest.setRequestHeader('Accept', 'application/json');
    cityRequest.send();
}

cityRequest.onload = function() {
    console.log("coucou");
    console.log(JSON.parse(this.responseText)["local_names"]["fr"])
}
*/
/** Getter **/
function getAlt(json) {
    return json["weather"][0]["description"];
}

function getIcon(json) {
    return cutIcon(json["weather"][0]["icon"]);
}

function cutIcon(str) {
    return str.substr(0, str.length - 1);
}

function getTemp(json) {
    return kelvinToC(json["main"]["temp"]);
}

function kelvinToC(kelvin) {
    return kelvin - 273.15;
}

function getWind(json) {
    return msToKmh(json["wind"]["speed"]);
}

function msToKmh(speed) {
    return speed * 3.6;
}

refreshWeather();