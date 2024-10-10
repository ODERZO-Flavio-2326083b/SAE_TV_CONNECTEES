<?php

/**
 * Custom color of the website
 *
 * @param $wp_customize
 */
function my_customize_color_register($wp_customize)
{

    // PANEL //

    $wp_customize->add_panel('ecran_connecte_control', array(
        'priority' => 1,
        'title' => 'Ecran connecte',
        'description' => 'Permet de modifier le site'
    ));

    // SECTION //

    $wp_customize->add_section('ecran_connecte_colors_header', array(
        'title' => 'Couleurs en-tête',
        'panel' => 'ecran_connecte_control',
        'priority' => 10,
    ));

    $wp_customize->add_section('ecran_connecte_colors_content', array(
        'title' => 'Couleurs du contenu',
        'panel' => 'ecran_connecte_control',
        'priority' => 15,
    ));

    $wp_customize->add_section('ecran_connecte_colors_footer', array(
        'title' => 'Couleurs du pied de page',
        'panel' => 'ecran_connecte_control',
        'priority' => 20,
    ));

    // HEADER //

    //Background color header
    $wp_customize->add_setting('background_color_header', array(
        'default' => '#00558b',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color_header', array(
        'label' => 'Couleur de fond de l\'en-tête',
        'section' => 'ecran_connecte_colors_header',
        'settings' => 'background_color_header',
    )));

    // Color icon menu
    $wp_customize->add_setting('color_header', array(
        'default' => '#ffffff',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_header', array(
        'label' => 'Couleur de l\'icône du menu',
        'section' => 'ecran_connecte_colors_header',
        'settings' => 'color_header',
    )));

    // Color text menu
    $wp_customize->add_setting('color_menu', array(
        'default' => '#ffffff',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_menu', array(
        'label' => 'Couleur du texte du menu',
        'section' => 'ecran_connecte_colors_header',
        'settings' => 'color_menu',
    )));

    // CONTENT //

    // Background color body
    $wp_customize->add_setting('background_color_body', array(
        'default' => '#F4F4F4',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color_body', array(
        'label' => 'Couleur du fond d\'écran',
        'section' => 'ecran_connecte_colors_content',
        'settings' => 'background_color_body',
    )));

    // Background color main
    $wp_customize->add_setting('background_color_main', array(
        'default' => '#ffffff',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color_main', array(
        'label' => 'Couleur de fond des zones de texte',
        'section' => 'ecran_connecte_colors_content',
        'settings' => 'background_color_main',
    )));

    // Color title
    $wp_customize->add_setting('color_title', array(
        'default' => '#00558a',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_title', array(
        'label' => 'Couleur des titres',
        'section' => 'ecran_connecte_colors_content',
        'settings' => 'color_title',
    )));

    // Color text
    $wp_customize->add_setting('color_text', array(
        'default' => '#0a0a0a',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_text', array(
        'label' => 'Couleur du texte',
        'section' => 'ecran_connecte_colors_content',
        'settings' => 'color_text',
    )));

    //Background En-tête emploi du temps & tableau
    $wp_customize->add_setting('background_color_tab_header', array(
        'default' => '#00558a',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color_tab_header', array(
        'label' => 'Couleur de l\'en-tête emploi du temps & tableau',
        'section' => 'ecran_connecte_colors_content',
        'settings' => 'background_color_tab_header',
    )));

    // En-tête emploi du temps & tableau
    $wp_customize->add_setting('color_tab_header', array(
        'default' => '#ffffff',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_tab_header', array(
        'label' => 'Couleur du texte de l\'en-tête emploi du temps & tableau',
        'section' => 'ecran_connecte_colors_content',
        'settings' => 'color_tab_header',
    )));

    // Contenue emploi du temps & tableau
    $wp_customize->add_setting('background_color_tab_content', array(
        'default' => '#ffffff',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color_tab_content', array(
        'label' => 'Couleur de fond du contenue des emplois du temps & tableaux',
        'section' => 'ecran_connecte_colors_content',
        'settings' => 'background_color_tab_content',
    )));

    // Texte du contenue emploi du temps & tableau
    $wp_customize->add_setting('color_tab_content', array(
        'default' => '#000000',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_tab_content', array(
        'label' => 'Couleur de fond du contenue des emplois du temps & tableaux',
        'section' => 'ecran_connecte_colors_content',
        'settings' => 'color_tab_content',
    )));

    // Lien
    $wp_customize->add_setting('color_link', array(
        'default' => '#00558a',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_link', array(
        'label' => 'Couleur de fond du contenue des emplois du temps & tableaux',
        'section' => 'ecran_connecte_colors_content',
        'settings' => 'color_link',
    )));

    // Background boutton
    $wp_customize->add_setting('background_color_button', array(
        'default' => '#00558a',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color_button', array(
        'label' => 'Couleur de fond des bouttons',
        'section' => 'ecran_connecte_colors_content',
        'settings' => 'background_color_button',
    )));

    // Couleur boutton
    $wp_customize->add_setting('color_button', array(
        'default' => '#ffffff',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_button', array(
        'label' => 'Couleur du texte des bouttons',
        'section' => 'ecran_connecte_colors_content',
        'settings' => 'color_button',
    )));


    // FOOTER //

    // Background alert
    $wp_customize->add_setting('background_color_alert', array(
        'default' => '#FF0000',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color_alert', array(
        'label' => 'Couleur du fond des alertes',
        'section' => 'ecran_connecte_colors_footer',
        'settings' => 'background_color_alert',
    )));

    // Color text alert
    $wp_customize->add_setting('color_alert', array(
        'default' => '#ffffff',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_alert', array(
        'label' => 'Couleur du fond des alertes',
        'section' => 'ecran_connecte_colors_footer',
        'settings' => 'color_alert',
    )));

    //Background weather
    $wp_customize->add_setting('background_color_weather', array(
        'default' => '#00558b',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_color_weather', array(
        'label' => 'Couleur du fond de la météo',
        'section' => 'ecran_connecte_colors_footer',
        'settings' => 'background_color_weather',
    )));

    //Color weather
    $wp_customize->add_setting('color_weather', array(
        'default' => '#ffffff',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_weather', array(
        'label' => 'Couleur du texte de la météo',
        'section' => 'ecran_connecte_colors_footer',
        'settings' => 'color_weather',
    )));

    $wp_customize->get_setting('background_color_header')->transport = 'postMessage';
    $wp_customize->get_setting('color_header')->transport = 'postMessage';
    $wp_customize->get_setting('color_menu')->transport = 'postMessage';
    $wp_customize->get_setting('background_color_body')->transport = 'postMessage';
    $wp_customize->get_setting('background_color_main')->transport = 'postMessage';
    $wp_customize->get_setting('color_title')->transport = 'postMessage';
    $wp_customize->get_setting('color_text')->transport = 'postMessage';
    $wp_customize->get_setting('background_color_tab_header')->transport = 'postMessage';
    $wp_customize->get_setting('color_tab_header')->transport = 'postMessage';
    $wp_customize->get_setting('background_color_tab_content')->transport = 'postMessage';
    $wp_customize->get_setting('color_tab_content')->transport = 'postMessage';
    $wp_customize->get_setting('color_link')->transport = 'postMessage';
    $wp_customize->get_setting('background_color_button')->transport = 'postMessage';
    $wp_customize->get_setting('color_button')->transport = 'postMessage';
    $wp_customize->get_setting('background_color_alert')->transport = 'postMessage';
    $wp_customize->get_setting('color_alert')->transport = 'postMessage';
    $wp_customize->get_setting('background_color_weather')->transport = 'postMessage';
    $wp_customize->get_setting('color_weather')->transport = 'postMessage';
}

add_action('customize_register', 'my_customize_color_register');


/**
 * This will generate a line of CSS for use in header output. If the setting
 * ($mod_name) has no defined value, the CSS will not be output.
 *
 * @param string $selector CSS selector
 * @param string $style The name of the CSS *property* to modify
 * @param string $mod_name The name of the 'theme_mod' option to fetch
 * @param string $prefix Optional. Anything that needs to be output before the CSS property
 * @param string $postfix Optional. Anything that needs to be output after the CSS property
 * @param bool $echo Optional. Whether to print directly to the page (default: true).
 * @return string Returns a single line of CSS with selectors and a property.
 * @uses get_theme_mod()
 * @since MyTheme 1.0
 */
function generate_css($selector, $style, $mod_name, $prefix = '', $postfix = ' !important', $echo = true)
{
    $return = '';
    $mod = get_theme_mod($mod_name);
    if (!empty($mod)) {
        $return = sprintf('%s { %s:%s; }',
            $selector,
            $style,
            $prefix . $mod . $postfix
        );
        if ($echo) {
            echo $return;
        }
    }
    return $return;
}


add_action('wp_head', 'ecran_connecte_customizer_css');
/**
 * Create the custom CSS
 */
function ecran_connecte_customizer_css()
{
    ?>
    <style type="text/css">
        <?php generate_css('header', 'background-color', 'background_color_header', ''); ?>
        <?php generate_css('nav.menu ul, .menu-item_dropdown-content', 'background-color', 'background_color_header', ''); ?>
        <?php generate_css('.icon, .icon:hover', 'color', 'color_header', ''); ?>
        <?php generate_css('.menu-item, .menu-item a, .dropbtn, .menu-item a:hover, nav.menu a:hover', 'color', 'color_menu', ''); ?>
        <?php generate_css('body', 'background-color', 'background_color_body', ''); ?>
        <?php generate_css('section', 'background-color', 'background_color_main', ''); ?>
        <?php generate_css('h1, h2, h3, h4, h5, h6', 'color', 'color_title', ''); ?>
        <?php generate_css('h1, h2, h3, h4, h5, h6', 'color', 'color_title', ''); ?>
        <?php generate_css('body', 'color', 'color_text', ''); ?>
        <?php generate_css('thead tr th', 'background-color', 'background_color_tab_header', ''); ?>
        <?php generate_css('thead tr th', 'color', 'color_tab_header', ''); ?>
        <?php //generate_css('td, tr th', 'background-color', 'background_color_tab_content', ''); ?>
        <?php generate_css('td, tr th', 'color', 'color_tab_content', ''); ?>
        <?php generate_css('a, a:hover', 'color', 'color_link', ''); ?>
        <?php generate_css('input[type="button"], input[type="submit"], input[type="reset"], button', 'background-color', 'background_color_button', ''); ?>
        <?php generate_css('input[type="button"], input[type="submit"], input[type="reset"], button', 'color', 'color_button', ''); ?>
        <?php generate_css('.alerts', 'background-color', 'background_color_alert', ''); ?>
        <?php generate_css('.alerts', 'color', 'color_alert', ''); ?>
        <?php generate_css('.Infos', 'background-color', 'background_color_weather', ''); ?>
        <?php generate_css('.Infos', 'color', 'color_weather', ''); ?>
        <?php generate_css('.Infos .Weather, .Infos .Weather #weather', 'border-color', 'color_weather', ''); ?>
    </style>
    <?php
}

add_action('customize_preview_init', 'cd_customizer');
/**
 * Js to see the change immediately
 */
function cd_customizer()
{
    wp_enqueue_script(
        'ecran_connecte_control',
        get_template_directory_uri() . '/assets/js/customizer.js',
        array('jquery', 'customize-preview'),
        '',
        true
    );
}