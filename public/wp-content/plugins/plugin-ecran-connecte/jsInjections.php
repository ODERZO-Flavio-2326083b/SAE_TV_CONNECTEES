<?php

use controllers\InformationController;
use models\CodeAde;
use models\Department;
use models\Information;
use models\Localisation;
use views\AlertView;
use views\InformationView;
use views\TelevisionView;

/**
 * Injecter les valeurs de localisation et de requête AJAX au
 * script de la météo. Si l'utilisateur connecté a une localisation
 * dans la bd, on l'utilise, sinon on remplace par la localisation par défaut
 * (Ici, Aix-en-Provence)
 *
 * @return void
 */
function injectLocVariables(): void
{
    $model = new Localisation();

    if ($userLoc = $model->getLocFromUserId(get_current_user_id())) {
        $latitude = $userLoc->getLatitude();
        $longitude = $userLoc->getLongitude();
    } else {
        $longitude = 5.4510;
        $latitude = 43.5156;
    }

    wp_localize_script(
        'weather_script_ecran', 'weatherVars', array(
        'longitude' => $longitude,
        'latitude' => $latitude,
        'apiKey' => "ae546c64c1c36e47123b3d512efa723e"
        )
    );
}
add_action('wp_enqueue_scripts', 'injectLocVariables');

/**
 * Récupère les données reçues par requête AJAX et les traite
 * pour les insérer dans la base de données.
 *
 * @return void
 */
function handleMeteoAjaxData(): void
{
    // vérification de l'authenticité de la demande
    check_ajax_referer('locNonce', 'nonce');
    if (isset($_POST['longitude']) && isset($_POST['latitude'])) {
        $longitude = sanitize_text_field($_POST['longitude']);
        $latitude = sanitize_text_field($_POST['latitude']);
        $userId = sanitize_text_field($_POST['currentUserId']);

        $locModel = new Localisation();

        if (!$locModel->getLocFromUserId(get_current_user_id())) {
            $locModel->setLongitude($longitude);
            $locModel->setLatitude($latitude);
            $locModel->setUserId($userId);

            $locModel->insert();
        }
        wp_send_json_success(
            array(
            'message' => 'Données reçues avec succès',
            'currentUserId' => $userId,
            'longitude' => $longitude,
            'latitude' => $latitude
            )
        );
    } else {
        // si les données ne sont pas présentes, envoyer une erreur
        wp_send_json_error(
            'Données manquantes ; Latitude: '
                           . $_POST['latitude'] . ' ; Longitude: '
            . $_POST['longitude']
        );
    }
}

add_action('wp_ajax_handleMeteoAjaxData', 'handleMeteoAjaxData');
add_action('wp_ajax_nopriv_handleMeteoAjaxData', 'handleMeteoAjaxData');


/**
 * Charge le script de demande de localisation
 * si l'utilisateur n'en a pas sur la page d'accueil
 *
 * @return void
 */
function loadLocAjaxIfUserHasNoLoc(): void
{
    $model = new Localisation();

    if (is_user_logged_in()
        && is_front_page() && !$model->getLocFromUserId(get_current_user_id())
    ) {
        wp_enqueue_script(
            'retrieve_loc_script_ecran', TV_PLUG_PATH
                          . 'public/js/retrieveLoc.js', array( 'jquery' ),
            '1.0', true 
        );

        add_action('wp_enqueue_scripts', 'loadLocalisationScript');

        // injection de variables dans le script.
        wp_localize_script(
            'retrieve_loc_script_ecran',
            'retrieveLocVars', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'ajaxNonce' => wp_create_nonce('locNonce'),
                'currentUserId' => get_current_user_id()
            )
        );
    }

}

add_action('wp_enqueue_scripts', 'loadLocAjaxIfUserHasNoLoc');

/**
 * Récupère les durées de chaque information du département de l'utilisateur
 * connecté, et les trie dans deux listes : durées des vidéos, et durées
 * des informations non vidéos.
 *
 * @return void
 */
function loadInformationDurations(): void
{
    $informationModel = new Information();
    $deptModel = new Department();

    if (is_user_logged_in() && get_current_user_id() != 1) {
        $currentUserDeptId = $deptModel->getUserDepartment(get_current_user_id())
            ->getIdDepartment();

        $informations = $informationModel->getInformationsByDeptId(
            $currentUserDeptId, 0, 1000
        );

        $videoDurations = array();
        $otherDurations = array();

        foreach ($informations as $information) {
            if ($information->getType() === 'video') {
                $videoDurations[] = $information->getDuration();
            } else {
                $otherDurations[] = $information->getDuration();
            }
        }

        wp_localize_script(
            'slideshow_script_ecran',
            'DURATIONS', array(
            'videoDurations' => $videoDurations,
            'otherDurations' => $otherDurations
            )
        );
    }
}

add_action('wp_enqueue_scripts', 'loadInformationDurations');

/**
 * Envoie le code HTML du sélecteur de code ADE pour la modification de
 * compte télévision.
 *
 * @return void
 */
function injectAllCodesOnTvEdit(): void
{
    $codeAde = new CodeAde();
    $deptModel = new Department();

    $years = $codeAde->getAllFromType('year');
    $groups = $codeAde->getAllFromType('group');
    $halfGroups = $codeAde->getAllFromType('halfGroup');

    $allDepts = $deptModel->getAllDepts();

    wp_localize_script(
        'addCodeTv_script_ecran', 'codeHTML', array(
        'tv' => TelevisionView::buildSelectCode(
            $years, $groups, $halfGroups, $allDepts
        )
        )
    );
}

add_action('wp_enqueue_scripts', 'injectAllCodesOnTvEdit');

function injectCodesOnInfoEdit(): void
{
    $codeAde = new CodeAde();
    $deptModel = new Department();

    $allDepts = $deptModel->getAllDepts();

    if(!is_user_logged_in()) return;

    list($years, $groups, $halfGroups) =
        InformationController::getAllAvailableCodes();

    wp_localize_script(
        'addDepartment_script', 'codeHTML', array(
            'infoCode' => InformationView::buildSelectCode(
                $years, $groups, $halfGroups, $allDepts
            )));
}

add_action('wp_enqueue_scripts', 'injectCodesOnInfoEdit');

/**
 * Envoie le code HTML du sélecteur de code ADE pour
 * la modification d'alertes.
 *
 * @return void
 */
function injectAllCodesOnAlertEdit(): void
{
    $codeAde = new CodeAde();
    $deptModel = new Department();

    $years = $codeAde->getAllFromType('year');
    $groups = $codeAde->getAllFromType('group');
    $halfGroups = $codeAde->getAllFromType('halfGroup');

    $allDepts = $deptModel->getAllDepts();

    wp_localize_script(
        'addCodeAlert_script_ecran', 'codeHTML', array(
        'infoCode' => AlertView::buildSelectCode(
            $years, $groups, $halfGroups, $allDepts
        )
        )
    );
}

add_action('wp_enqueue_scripts', 'injectAllCodesOnAlertEdit');


