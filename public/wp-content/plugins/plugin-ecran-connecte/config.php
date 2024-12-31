<?php

use Controllers\AlertRestController;
use Controllers\CodeAdeRestController;
use Controllers\InformationRestController;
use Controllers\ProfileRestController;
use Models\Localisation;

include_once __DIR__ . '/config-notifs.php';
include_once 'vendor/R34ICS/R34ICS.php';
include 'widgets/WidgetAlert.php';
include 'widgets/WidgetWeather.php';
include 'widgets/WidgetInformation.php';

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
 */
function loadScriptsEcran()
{
    //jQuery
    wp_enqueue_script('jquery_cdn', 'https://code.jquery.com/jquery-3.4.1.slim.min.js');

    //Bootstrap
    wp_enqueue_style('bootstrap_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap_js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js', array('jquery_cdn'), '', true);

    // LIBRARY
    wp_enqueue_script('pdf-js', 'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.2.228/build/pdf.min.js', array(), '', false);
    wp_enqueue_script('onesignal-js', 'https://cdn.onesignal.com/sdks/OneSignalSDK.js', array(), '', false);
    wp_enqueue_script('plugin-jquerymin', TV_PLUG_PATH . 'public/js/vendor/jquery.min.js', array('jquery'), '', true);
    wp_enqueue_script('plugin-JqueryEzMin', TV_PLUG_PATH . 'public/js/vendor/jquery.easing.min.js', array('jquery'), '', true);
    wp_enqueue_script('plugin-jqueryEzTic', TV_PLUG_PATH . 'public/js/vendor/jquery.easy-ticker.js', array('jquery'), '', true);
    wp_enqueue_script('plugin-jqueryEzMinTic', TV_PLUG_PATH . 'public/js/vendor/jquery.easy-ticker.min.js', array('jquery'), '', true);
    wp_enqueue_script('plugin-marquee', TV_PLUG_PATH . 'public/js/vendor/jquery.marquee.js', array('jquery'), '', true);
    wp_enqueue_script('plugin-ticker', TV_PLUG_PATH . 'public/js/vendor/jquery.tickerNews.js', array('jquery'), '', true);

    //CSS
    wp_enqueue_style('alert_ecran', TV_PLUG_PATH . 'public/css/alert.css', array(), '1.0');
    wp_enqueue_style('info_ecran', TV_PLUG_PATH . 'public/css/information.css', array(), '1.0');
    wp_enqueue_style('schedule_ecran', TV_PLUG_PATH . 'public/css/schedule.css', array(), '1.0');
    wp_enqueue_style('style_ecran', TV_PLUG_PATH . 'public/css/style.css', array(), '1.0');
    wp_enqueue_style('weather_ecran', TV_PLUG_PATH . 'public/css/weather.css', array(), '1.0');

    // SCRIPT
    wp_enqueue_script('addCheckBox_script_ecran', TV_PLUG_PATH . 'public/js/addAllCheckBox.js', array('jquery'), '1.0', true);
    wp_enqueue_script('addCodeAlert_script_ecran', TV_PLUG_PATH . 'public/js/addOrDeleteAlertCode.js', array('jquery'), '1.0', true);
    wp_enqueue_script('addCodeTv_script_ecran', TV_PLUG_PATH . 'public/js/addOrDeleteTvCode.js', array('jquery'), '1.0', true);
    wp_enqueue_script('alertTicker_script_ecran', TV_PLUG_PATH . 'public/js/alertTicker.js', array('jquery'), '', true);
    wp_enqueue_script('confPass_script_ecran', TV_PLUG_PATH . 'public/js/confirmPass.js', array('jquery'), '1.0', true);
    wp_enqueue_script('oneSignal_script_ecran', TV_PLUG_PATH . 'public/js/oneSignalPush.js', array('jquery'), '', true);
    wp_add_inline_script('oneSignal_script_ecran', 'const ONESIGNAL_APP_ID = \'' . ONESIGNAL_APP_ID . '\';', 'before');
    wp_enqueue_script('scroll_script_ecran', TV_PLUG_PATH . 'public/js/scroll.js', array('plugin-jquerymin', 'plugin-jqueryEzTic', 'plugin-jqueryEzMinTic', 'plugin-JqueryEzMin'), '', true);
    wp_enqueue_script('search_script_ecran', TV_PLUG_PATH . 'public/js/search.js', array('jquery'), '1.0', true);
    wp_enqueue_script('slideshow_script_ecran', TV_PLUG_PATH . 'public/js/slideshow.js', array('jquery'), '2.0', true);
    wp_enqueue_script('sortTable_script_ecran', TV_PLUG_PATH . 'public/js/sortTable.js', array('jquery'), '1.0', true);
    wp_enqueue_script('weatherTime_script_ecran', TV_PLUG_PATH . 'public/js/weather_and_time.js', array('jquery'), '1.0', true);
	wp_enqueue_script('weather_script_ecran', TV_PLUG_PATH . 'public/js/weather.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_script('loadCssFormValue', TV_PLUG_PATH . 'public/js/loadCssFormValue.js', array('jquery'), '1.0', true);

}

add_action('wp_enqueue_scripts', 'loadScriptsEcran');

/**
 * Create tables in the database (Alert & Information)
 */
function installDatabaseEcran()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name = 'ecran_information';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			title VARCHAR (40),
			content VARCHAR(280) NOT NULL,
			creation_date datetime DEFAULT NOW() NOT NULL,
			expiration_date datetime NOT NULL,
			author BIGINT(20) UNSIGNED NOT NULL,
			type VARCHAR (10) DEFAULT 'text' NOT NULL,
			administration_id INT(10) DEFAULT NULL,
			department_id INT(10),
            duration INT(10), DEFAULT '10' NOT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (author) REFERENCES wp_users(ID) ON DELETE CASCADE,
			FOREIGN KEY (department_id) REFERENCES ecran_departement(dept_id) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    $table_name = 'ecran_alert';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			content VARCHAR(280) NOT NULL,
			creation_date datetime DEFAULT NOW() NOT NULL,
			expiration_date datetime NOT NULL,
			author BIGINT(20) UNSIGNED NOT NULL,
			for_everyone INT(1) DEFAULT '1' NOT NULL,
			administration_id INT(10) DEFAULT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (author) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    $table_name = 'ecran_code_ade';

    $query = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			type VARCHAR(15) NOT NULL,
			title VARCHAR (60) NOT NULL,
			code VARCHAR (20) NOT NULL,
			PRIMARY KEY (id)
			) $charset_collate;";

    dbDelta($query);

    // With wordpress id = 1 can't be access if we do : /page/1
    $sql = "ALTER TABLE $table_name AUTO_INCREMENT = 2;";
    dbDelta($sql);

    $table_name = 'ecran_code_alert';

    $query = "CREATE TABLE IF NOT EXISTS $table_name (
			alert_id INT(10) NOT NULL ,
			code_ade_id INT(10) NOT NULL ,
			PRIMARY KEY (alert_id, code_ade_id),
			FOREIGN KEY (alert_id) REFERENCES ecran_alert(id) ON DELETE CASCADE,
			FOREIGN KEY (code_ade_id) REFERENCES ecran_code_ade(id) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($query);

    $table_name = 'ecran_code_user';

    $query = "CREATE TABLE IF NOT EXISTS $table_name (
			user_id BIGINT(20) UNSIGNED NOT NULL,
			code_ade_id INT(10) NOT NULL ,
			PRIMARY KEY (user_id, code_ade_id),
			FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE,
			FOREIGN KEY (code_ade_id) REFERENCES ecran_code_ade(id) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($query);

    $table_name = 'ecran_code_delete_account';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			code VARCHAR(40) NOT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    $table_name = 'ecran_departement';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            dept_id INT(10) NOT NULL AUTO_INCREMENT,
            dept_nom VARCHAR (60) NOT NULL,
            PRIMARY KEY (dept_id)) $charset_collate;";

    dbDelta($sql);

    $table_name = 'ecran_user_departement';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT(10) NOT NULL AUTO_INCREMENT,
			dept_id INT(10) NOT NULL ,
			user_id BIGINT(20) UNSIGNED NOT NULL ,
			PRIMARY KEY (id, dept_id, user_id),
			FOREIGN KEY (dept_id) REFERENCES ecran_departement(dept_id) ON DELETE CASCADE,
			FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($sql);

    $table_name = 'ecran_localisation';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            localisation_id INT(10) NOT NULL AUTO_INCREMENT,
            latitude DECIMAL(10,6) NOT NULL,
            longitude DECIMAL(10,6) NOT NULL,
            adresse VARCHAR (60) NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            PRIMARY KEY (localisation_id),
            FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
            ) $charset_collate;";

    dbDelta($sql);
}

add_action('plugins_loaded', 'installDatabaseEcran');


/*
 * CREATE ROLES
 */

$result = add_role(
    'secretaire',
    __('Secretaire'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'television',
    __('Television'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'technicien',
    __('Technicien'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'informationposter',
    __('informationPoster'),
    array(
        'read' => true,  // true allows this capability
    )
);

/*
 * CREATE REST API ENDPOINTS
 */

add_action('rest_api_init', function () {
    $controller = new InformationRestController();
    $controller->register_routes();

    $controller = new CodeAdeRestController();
    $controller->register_routes();

    $controller = new AlertRestController();
    $controller->register_routes();

    $controller = new ProfileRestController();
    $controller->register_routes();
});