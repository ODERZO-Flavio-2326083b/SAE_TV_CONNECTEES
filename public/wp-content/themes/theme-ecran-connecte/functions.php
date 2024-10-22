<?php

include_once 'inc/customizer/custom_colors.php';
include_once 'inc/customizer/custom_sidebar.php';
include_once 'inc/customizer/custom_schedule.php';
include_once 'inc/customizer/custom_footer.php';

add_filter('auto_update_plugin', '__return_true');
add_filter('auto_update_theme', '__return_true');


error_reporting(0);
/*
function wp_maintenance_mode()
{
	if(!current_user_can('edit_themes') || !is_user_logged_in()) {
		wp_die('<h1 style="color:red">Site en maintenance</h1>
<br />Le site est en cours de maintenance, merci de bien patienter
<script src="/wp-content/themes/theme-ecran-connecte/assets/js/refresh.js"></script>');
	}
}
add_action('get_header', 'wp_maintenance_mode');
*/

function add_scripts()
{
    //jQuery
    wp_enqueue_script('jquery_cdn', 'https://code.jquery.com/jquery-3.4.1.slim.min.js');

    //Bootstrap
    wp_enqueue_style('bootstrap_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap_js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js', array('jquery_cdn'), '', true);

    // CSS
    wp_enqueue_style('style_ecran', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('header_ecran_theme', get_template_directory_uri() . '/assets/css/header.css');
    wp_enqueue_style('content_ecran_theme', get_template_directory_uri() . '/assets/css/content.css');
    wp_enqueue_style('sidebar_ecran_theme', get_template_directory_uri() . '/assets/css/sidebar.css');
    wp_enqueue_style('footer_ecran_theme', get_template_directory_uri() . '/assets/css/footer.css');


    // Passer le département au script
    $departement = 'info'; // Récupère la valeur depuis la logique de ton application (par exemple, depuis la base de données)
    wp_localize_script('jquery_cdn', 'myTheme', array(
        'departement' => $departement));
}

add_action('wp_enqueue_scripts', 'add_scripts');

add_action('wp_footer', 'load_dynamic_css');
function load_dynamic_css() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Récupérer le département passé depuis PHP
            const cssDepartement = myTheme.departement;

            // Créer le nom du fichier CSS à charger
            const cssFile = `global-${cssDepartement}.css`; // Par exemple, 'global-info.css'

            // Créer une balise <link> pour ajouter le fichier CSS correspondant
            const linkElement = document.createElement('link');
            linkElement.rel = 'stylesheet';
            linkElement.href = '<?php echo get_template_directory_uri(); ?>/' + cssFile; // Charger le fichier CSS de valeurs

            // Ajouter la balise <link> au <head>
            document.head.appendChild(linkElement);
        });
    </script>
    <?php
}

/**
 * Load all scripts (CSS / JS)
 */
function add_theme_scripts()
{
    wp_enqueue_script( 'theme-jquery', get_template_directory_uri() . '/assets/js/vendor/jquery-3.3.1.min.js', array (), '', false);
    wp_enqueue_script( 'theme-jqueryUI', get_template_directory_uri() . '/assets/js/vendor/jquery-ui.min.js', array ( 'jquery' ), '', false);
}
//add_action('wp_enqueue_scripts', 'add_theme_scripts');

/**
 * CSS for login page
 */
function my_login_stylesheet()
{
    wp_enqueue_style('login_css', get_stylesheet_directory_uri() . '/assets/css/login.css');
}
add_action('login_enqueue_scripts', 'my_login_stylesheet');

/**
 * Remove the wordpress bar
 */
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar()
{
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

/**
 * Disable the url /wp-admin except for the admin
 */
function wpm_admin_redirection()
{
    if (is_admin() && !current_user_can('administrator')) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('init', 'wpm_admin_redirection');

/**
 * Change the url of the logo
 * @return mixed
 */
function my_login_logo_url()
{
    return $_SERVER['HTTP_HOST'];
}
add_filter('login_headerurl', 'my_login_logo_url');

/**
 * Change the title of the logo
 *
 * @return string
 */
function my_login_logo_url_title()
{
    return get_bloginfo('name');
}
add_filter('login_headertitle', 'my_login_logo_url_title');

// Register a new navigation menu
function add_Main_Nav()
{
    register_nav_menu('header-menu',__('Header Menu'));
}
add_action('init', 'add_Main_Nav');

$args = array(
    'flex-width'    => true,
    'width'         => 500,
    'flex-height'   => true,
    'height'        => 500,
    'default-image' => get_template_directory_uri() . '/assets/media/header.png',
);
add_theme_support('custom-header', $args);

//Met la bonne heure
global $wpdb;
date_default_timezone_set('Europe/Paris');
$wpdb->time_zone = 'Europe/Paris';

// All sidebars
if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name' => 'Header',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
        'name' => 'Footer',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
    register_sidebar(array(
        'name' => 'Footer gauche',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
    register_sidebar(array(
        'name' => 'Footer droite',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
    register_sidebar(array(
        'name' => 'Colonne Gauche',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
    register_sidebar(array(
        'name' => 'Colonne Droite',
        'before_widget' => '<li>',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
}
