<?php

use controllers\rest\AlertRestController;
use controllers\rest\CodeAdeRestController;
use controllers\rest\InformationRestController;
use controllers\rest\ProfileRestController;

require_once 'vendor/R34ICS/R34ICS.php';
require 'widgets/WidgetAlert.php';
require 'widgets/WidgetWeather.php';
require 'widgets/WidgetInformation.php';

// Login for viewer
define('DB_USER_VIEWER', 'viewer');
define('DB_PASSWORD_VIEWER', 'viewer');
define('DB_HOST_VIEWER', 'localhost');
define('DB_NAME_VIEWER', 'adminwordpress');
define('URL_WEBSITE_VIEWER', 'http://adminwordpress/');

/**
 * Create all directory
 * (For ICS file and media)
 */
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH)) {
    mkdir($_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH);
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH)) {
    mkdir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH, 0777);
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0')) {
    mkdir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0', 0777);
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1')) {
    mkdir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1', 0777);
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2')) {
    mkdir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2', 0777);
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3')) {
    mkdir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3', 0777);
}

/**
 * Include all scripts
 * (CSS, JS)
 *
 * @return void
 */
function loadScriptsEcran() : void
{
    //jQuery
    wp_enqueue_script(
        'jquery_cdn', 'https://code.jquery.com/jquery-3.4.1.slim.min.js'
    );

    //Bootstrap
    wp_enqueue_style(
        'bootstrap_css',
        'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css'
    );
    wp_enqueue_script(
        'bootstrap_js',
        'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js',
        array('jquery_cdn'), '', true
    );

    // LIBRARY
    wp_enqueue_script(
        'pdf-js',
        'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.2.228/build/pdf.min.js',
        array(), '', false
    );
    wp_enqueue_script(
        'plugin-jquerymin',
        TV_PLUG_PATH . 'public/js/vendor/jquery.min.js', array('jquery'), '', true
    );
    wp_enqueue_script(
        'plugin-JqueryEzMin',
        TV_PLUG_PATH
        . 'public/js/vendor/jquery.easing.min.js', array('jquery'), '', true
    );
    wp_enqueue_script(
        'plugin-jqueryEzTic',
        TV_PLUG_PATH
        . 'public/js/vendor/jquery.easy-ticker.js', array('jquery'), '', true
    );
    wp_enqueue_script(
        'plugin-jqueryEzMinTic',
        TV_PLUG_PATH
        . 'public/js/vendor/jquery.easy-ticker.min.js', array('jquery'), '', true
    );
    wp_enqueue_script(
        'plugin-marquee',
        TV_PLUG_PATH
        . 'public/js/vendor/jquery.marquee.js', array('jquery'), '', true
    );
    wp_enqueue_script(
        'plugin-ticker',
        TV_PLUG_PATH
        . 'public/js/vendor/jquery.tickerNews.js', array('jquery'), '', true
    );

    //CSS
    wp_enqueue_style(
        'alert_ecran',
        TV_PLUG_PATH . 'public/css/alert.css', array(), '1.0'
    );
    wp_enqueue_style(
        'info_ecran',
        TV_PLUG_PATH . 'public/css/information.css', array(), '1.0'
    );
    wp_enqueue_style(
        'schedule_ecran',
        TV_PLUG_PATH . 'public/css/schedule.css', array(), '1.0'
    );
    wp_enqueue_style(
        'style_ecran',
        TV_PLUG_PATH . 'public/css/style.css', array(), '1.0'
    );
    wp_enqueue_style(
        'weather_ecran',
        TV_PLUG_PATH . 'public/css/weather.css', array(), '1.0'
    );

    wp_enqueue_style(
        'tablet_ecran',
        TV_PLUG_PATH . 'public/css/tablet.css', array(), '1.0'
    );

    // SCRIPT
    wp_enqueue_script(
        'addCheckBox_script_ecran',
        TV_PLUG_PATH . 'public/js/addAllCheckBox.js', array('jquery'), '1.0', true
    );
    wp_enqueue_script(
        'addCodeAlert_script_ecran',
        TV_PLUG_PATH
        . 'public/js/addOrDeleteAlertCode.js', array('jquery'), '1.0', true
    );
    wp_enqueue_script(
        'addDepartment_script',
        TV_PLUG_PATH
        . 'public/js/addOrDeleteDepartment.js', array('jquery'), '1.0', true
    );
    wp_enqueue_script(
        'addTag_script_ecran',
        TV_PLUG_PATH
        . 'public/js/addOrDeleteTag.js', array('jquery'), '1.0', true
    );
    wp_enqueue_script(
        'addCodeTv_script_ecran',
        TV_PLUG_PATH
        . 'public/js/addOrDeleteTvCode.js', array('jquery'), '1.0', true
    );
    wp_enqueue_script(
        'alertTicker_script_ecran',
        TV_PLUG_PATH . 'public/js/alertTicker.js', array('jquery'), '', true
    );
    wp_enqueue_script(
        'confPass_script_ecran',
        TV_PLUG_PATH . 'public/js/confirmPass.js', array('jquery'), '1.0', true
    );
    wp_enqueue_script(
        'scroll_script_ecran',
        TV_PLUG_PATH
        . 'public/js/scroll.js',
        array(
            'plugin-jquerymin',
            'plugin-jqueryEzTic',
        'plugin-jqueryEzMinTic', 'plugin-JqueryEzMin'), '', true
    );
    wp_enqueue_script(
        'search_script_ecran',
        TV_PLUG_PATH . 'public/js/search.js', array('jquery'), '1.0', true
    );
    wp_enqueue_script(
        'slideshow_script_ecran',
        TV_PLUG_PATH . 'public/js/slideshow.js', array('jquery'), '2.0', true
    );
    wp_enqueue_script(
        'sortTable_script_ecran',
        TV_PLUG_PATH . 'public/js/sortTable.js', array('jquery'), '1.0', true
    );
    wp_enqueue_script(
        'weatherTime_script_ecran',
        TV_PLUG_PATH
        . 'public/js/weather_and_time.js', array('jquery'), '1.0', true
    );
    wp_enqueue_script(
        'weather_script_ecran',
        TV_PLUG_PATH . 'public/js/weather.js', array( 'jquery' ), '1.0', true
    );
    wp_enqueue_script(
        'loadCssFormValue',
        TV_PLUG_PATH
        . 'public/js/loadCssFormValue.js', array('jquery'), '1.0', true
    );

}

add_action('wp_enqueue_scripts', 'loadScriptsEcran');

/**
 * Create tables in the database (Alert & Information)
 *
 * @return void
 */
function installDatabaseEcran() : void
{
    global $wpdb;
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';

    if (get_option('init_database') == 1) {
        return;
    }

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS ecran_departement (
            dept_id INT(10) NOT NULL AUTO_INCREMENT,
            dept_nom VARCHAR (60) NOT NULL,
            PRIMARY KEY (dept_id)) $charset_collate;";

    dbDelta($sql);


    $sql = "CREATE TABLE IF NOT EXISTS ecran_information (
			id INT(10) NOT NULL AUTO_INCREMENT,
			title VARCHAR (40),
			content VARCHAR(280) NOT NULL,
			creation_date datetime DEFAULT NOW() NOT NULL,
			expiration_date datetime NOT NULL,
			author BIGINT(20) UNSIGNED NOT NULL,
			type VARCHAR (10) DEFAULT 'text' NOT NULL,
			administration_id INT(10) DEFAULT NULL,
			duration INT(10) DEFAULT 5000 NOT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (author) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    $sql = "CREATE TABLE IF NOT EXISTS ecran_alert (
			id INT(10) NOT NULL AUTO_INCREMENT,
			content VARCHAR(280) NOT NULL,
			creation_date datetime DEFAULT NOW() NOT NULL,
			expiration_date datetime NOT NULL,
			author BIGINT(20) UNSIGNED NOT NULL,
			administration_id INT(10) DEFAULT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (author) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    $query = "CREATE TABLE IF NOT EXISTS ecran_code_ade (
			id INT(10) NOT NULL AUTO_INCREMENT,
			type VARCHAR(15) NOT NULL,
			title VARCHAR (60) NOT NULL,
			code VARCHAR (20) NOT NULL,
			dept_id INT(10),
			PRIMARY KEY (id),
			FOREIGN KEY (dept_id) 
            REFERENCES ecran_departement(dept_id) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($query);

    // With wordpress id = 1 can't be access if we do : /page/1
    $sql = "ALTER TABLE ecran_code_ade AUTO_INCREMENT = 2;";
    dbDelta($sql);


    $query = "CREATE TABLE IF NOT EXISTS ecran_code_alert (
			alert_id INT(10) NOT NULL ,
			code_ade_id INT(10) NOT NULL ,
			PRIMARY KEY (alert_id, code_ade_id),
			FOREIGN KEY (alert_id) REFERENCES ecran_alert(id) ON DELETE CASCADE,
			FOREIGN KEY (code_ade_id) REFERENCES ecran_code_ade(id) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($query);


    $query = "CREATE TABLE IF NOT EXISTS ecran_code_user (
			user_id BIGINT(20) UNSIGNED NOT NULL,
			code_ade_id INT(10) NOT NULL ,
			PRIMARY KEY (user_id, code_ade_id),
			FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE,
			FOREIGN KEY (code_ade_id) REFERENCES ecran_code_ade(id) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($query);


    $sql = "CREATE TABLE IF NOT EXISTS ecran_code_delete_account (
			id INT(10) NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			code VARCHAR(40) NOT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);




    $sql = "CREATE TABLE IF NOT EXISTS ecran_user_departement (
            id INT(10) NOT NULL AUTO_INCREMENT,
			dept_id INT(10) NOT NULL ,
			user_id BIGINT(20) UNSIGNED NOT NULL ,
			PRIMARY KEY (id, dept_id, user_id),
			FOREIGN KEY (dept_id) 
            REFERENCES ecran_departement(dept_id) ON DELETE CASCADE,
			FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($sql);


    $sql = "CREATE TABLE IF NOT EXISTS ecran_localisation (
            localisation_id INT(10) NOT NULL AUTO_INCREMENT,
            latitude DECIMAL(10,6) NOT NULL,
            longitude DECIMAL(10,6) NOT NULL,
            adresse VARCHAR (60) NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            PRIMARY KEY (localisation_id),
            FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
            ) $charset_collate;";

    dbDelta($sql);


    $sql = "CREATE TABLE IF NOT EXISTS ecran_scraping_tags (
            id INT(10) NOT NULL AUTO_INCREMENT,
            id_info INT(10) NOT NULL,
            content VARCHAR (280) NOT NULL,
            tag VARCHAR (25) NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (id_info) REFERENCES ecran_information(id) ON DELETE CASCADE
            ) $charset_collate;";

    dbDelta($sql);

    $sql = "CREATE TABLE IF NOT EXISTS ecran_info_code_ade (
            id INT(10) NOT NULL AUTO_INCREMENT,
            info_id INT(10) NOT NULL,
            code_ade_id INT(10) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE (info_id, code_ade_id),
            FOREIGN KEY (code_ade_id) 
                REFERENCES ecran_code_ade(id) ON DELETE CASCADE,
            FOREIGN KEY (info_id) REFERENCES ecran_information(id) ON DELETE CASCADE
            ) $charset_collate;";

    dbDelta($sql);

    update_option('init_database', 1);
}

add_action('plugins_loaded', 'installDatabaseEcran');


/**
 * Retirer les roles par défaut de WordPress
 *
 * @return void
 */
function removeBuiltInRoles(): void
{
    if (get_option('built_in_roles_removed') == 1) {
        return;
    }

    global $wp_roles;
    $roles_to_remove = array('subscriber', 'contributor', 'author', 'editor');
    foreach ($roles_to_remove as $role) {
        if (isset($wp_roles->roles[$role])) {
            $wp_roles->remove_role($role);
        }
    }
    update_option('built_in_roles_removed', 1);
}

add_action('init', 'removeBuiltInRoles');

/**
 * Ajoute les nouveaux roles et permissions à WordPress
 *
 * @return void
 */
function addNewRoles()
{

    $allCaps = [
    // Permissions liées aux alertes
    'alert_header_menu_access',  // Accès au menu des alertes dans l'interface
    'add_alert',                 // Permission d'ajouter de nouvelles alertes
    'view_alerts',               // Permission de voir toutes les alertes
    'edit_alert',                // Permission de modifier les alertes existantes

    // Permissions liées aux utilisateurs
    'user_header_menu_access',   // Accès au menu des utilisateurs dans
                                     // l'interface
    'subadmin_access',           // Accès aux administrateurs de département
    'add_user',                  // Permission d'ajouter de nouveaux utilisateurs
    'view_users',                // Permission de voir la liste complète des
                                     // utilisateurs
    'edit_user',                 // Permission de modifier les informations des
                                     // utilisateurs

    // Permissions liées aux informations
    'information_header_menu_access',  // Accès au menu des informations dans
                                           // l'interface
    'add_information',                 // Permission d'ajouter de nouvelles
                                           // informations
    'information_to_any_code',         // Permission d'ajouter une information
                                           // à n'importe quel code ade
    'view_informations',               // Permission de consulter toutes les
                                           // informations
    'edit_information',                // Permission de modifier les
                                           // informations existantes

    // Permissions liées aux départements
    'department_header_menu_access',   // Accès au menu des départements dans
                                           // l'interface
    'add_department',                  // Permission d'ajouter de nouveaux
                                           // départements
    'view_departments',                // Permission de consulter la
                                           // liste des départements
    'edit_department',                 // Permission de modifier les départements
                                           // existants

    // Permissions liées aux codes ADE
    'ade_code_header_menu_access',     // Accès au menu des codes ADE dans
                                           // l'interface
    'add_ade_code',                    // Permission d'ajouter de nouveaux
                                           // codes ADE
    'view_ade_codes',                  // Permission de consulter la liste des
                                           // codes ADE
    'edit_ade_code',                   // Permission de modifier les codes ADE
                                           // existants

    // Permissions diverses
    'admin_perms',                     // Permission d'accès complet pour
                                           // les administrateurs
    'edit_css',                        // Permission de modifier le CSS du site
    'schedule_access',                 // Permission d'accès à l'emploi du temps
    ];

    $admin = get_role('administrator');
    foreach ( $allCaps as $cap ) {
        $admin->add_cap($cap);
    }

    add_role(
        'secretaire',
        __('Secretaire'),
        array()
    );

    add_role(
        'television',
        __('Television'),
        array()
    );

    add_role(
        'technicien',
        __('Agent d\'entretien'),
        array()
    );

    add_role(
        'subadmin',
        __('Sous-administrateur'),
        // Un admin de département peut faire tout ce qu'un administrateur peut faire sur le
        // site, sauf accéder aux autres admins de département
        array()
    );

    add_role(
        'communicant',
        __('Communicant'),
        array()
    );

    add_role(
        'tablette',
        __('Tablette'),
        array()
    );

    $secretaire = get_role('secretaire');
    $secretaireCaps = [
        'information_header_menu_access',
        'add_information',
        'view_informations',
        'edit_information',
        'alert_header_menu_access',
        'add_alert',
        'view_alerts',
        'edit_alert',
        'user_header_menu_access',
        'add_user',
        'view_users',
        'edit_user',
    ];

    foreach ( $secretaireCaps as $cap ) {
        $secretaire->add_cap($cap);
    }

    $subadmin = get_role('subadmin');
    $subadminCaps = array_diff($allCaps, array('subadmin_access'));

    foreach ( $subadminCaps as $cap ) {
        $subadmin->add_cap($cap);
    }

    $technicien = get_role('technicien');
    $technicienCaps = [
        'schedule_access'
    ];

    foreach ( $technicienCaps as $cap ) {
        $technicien->add_cap($cap);
    }

    $television = get_role('television');
    $televisionCaps = [
        'schedule_access'
    ];

    foreach ( $televisionCaps as $cap ) {
        $television->add_cap($cap);
    }

    $tablette = get_role('tablette');
    $tabletteCaps = [
        'schedule_access'
    ];

    foreach ( $tabletteCaps as $cap ) {
        $tablette->add_cap($cap);
    }

    $communicant = get_role('communicant');
    $communicantCaps = [
        'information_header_menu_access',
        'add_information',
        'view_informations',
        'edit_information',
        'alert_header_menu_access',
        'add_alert',
        'view_alerts',
        'edit_alert',
        'view_departments',
        'information_to_any_code'
    ];

    foreach ( $communicantCaps as $cap ) {
        $communicant->add_cap($cap);
    }
}

add_action('init', 'addNewRoles');

/*
 * CREATE REST API ENDPOINTS
 */

add_action(
    'rest_api_init', function () {
        $controller = new InformationRestController();
        $controller->registerRoutes();

        $controller = new CodeAdeRestController();
        $controller->registerRoutes();

        $controller = new AlertRestController();
        $controller->registerRoutes();

        $controller = new ProfileRestController();
        $controller->registerRoutes();
    }
);
