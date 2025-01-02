<?php

use models\Localisation;

/**
 * Injecter les valeurs de localisation et de requête AJAX au
 * script de la météo. Si l'utilisateur connecté a une localisation
 * dans la bd, on l'utilise, sinon on remplace par la localisation par défaut
 * (Ici, Aix-en-Provence)
 *
 * @return void
 */
function injectLocVariables() {
	$model = new Localisation();

	if($userLoc = $model->getLocFromUserId(get_current_user_id())) {
		$latitude = $userLoc->getLatitude();
		$longitude = $userLoc->getLongitude();
	} else {
		$longitude = 5.4510;
		$latitude = 43.5156;
	}

	wp_localize_script('weather_script_ecran', 'weatherVars', array(
		'longitude' => $longitude,
		'latitude' => $latitude,
		'apiKey' => "ae546c64c1c36e47123b3d512efa723e"
	));
}
add_action('wp_enqueue_scripts', 'injectLocVariables');

/**
 * Récupère les données reçues par requête AJAX et les traite
 * pour les insérer dans la base de données.
 *
 * @return void
 */
function handleMeteoAjaxData() {
	check_ajax_referer('locNonce', 'nonce'); // vérification de l'authenticité de la demande
	if (isset($_POST['longitude']) && isset($_POST['latitude'])) {
		$longitude = sanitize_text_field($_POST['longitude']);
		$latitude = sanitize_text_field($_POST['latitude']);
		$userId = sanitize_text_field($_POST['currentUserId']);

		$locModel = new Localisation();

		$locModel->setLongitude($longitude);
		$locModel->setLatitude($latitude);
		$locModel->setUserId($userId);

		$locModel->insert();

		wp_send_json_success(array(
			'message' => 'Données reçues avec succès',
			'currentUserId' => $userId,
			'longitude' => $longitude,
			'latitude' => $latitude
		));
	} else {
		// si les données ne sont pas présentes, envoyer une erreur
		wp_send_json_error('Données manquantes ; Latitude: ' . $_POST['latitude'] . ' ; Longitude: ' . $_POST['longitude']);
	}
}

add_action('wp_ajax_handleMeteoAjaxData', 'handleMeteoAjaxData');
add_action('wp_ajax_nopriv_handleMeteoAjaxData', 'handleMeteoAjaxData');


/**
 * Charge le script de demande de localisation si l'utilisateur n'en a pas sur la page d'accueil
 *
 * @return void
 */
function loadLocAjaxIfUserHasNoLoc(){
	$model = new Localisation();

	if(is_user_logged_in() && is_front_page() && !$model->getLocFromUserId(get_current_user_id()) ){
		wp_enqueue_script( 'retrieve_loc_script_ecran', TV_PLUG_PATH . 'public/js/retrieveLoc.js', array( 'jquery' ), '1.0', true );

		add_action('wp_enqueue_scripts', 'loadLocalisationScript');

		// injection de variables dans le script.
		wp_localize_script( 'retrieve_loc_script_ecran', 'retrieveLocVars', array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'ajaxNonce' => wp_create_nonce('locNonce'),
			'currentUserId' => get_current_user_id()
		));
	}

}

add_action('wp_enqueue_scripts', 'loadLocAjaxIfUserHasNoLoc');




