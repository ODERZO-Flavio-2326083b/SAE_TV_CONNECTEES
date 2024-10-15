<?php

/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since MyTheme 1.0
 */
class MyTheme_Customize
{
    /**
     * This hooks into 'customize_register' (available as of WP 3.4) and allows
     * you to add new sections and controls to the Theme Customize screen.
     *
     * Note: To enable instant preview, we have to actually write a bit of custom
     * javascript. See live_preview() for more.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @see add_action('customize_register',$func)
     * @link http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes/
     * @since MyTheme 1.0
     */
    public static function register($wp_customize)
    {
        $wp_customize->add_section( 'custom_ecran_connecte', array(
            'title' => __( 'Ecran Connecte' ),
            'description' => __( 'Modifier les couleurs des ecrans' ),
            'panel' => '', // Not typically needed.
                'priority' => 30,
            'capability' => 'edit_theme_options',
            'theme_supports' => '', // Rarely needed.
        ) );

        //1. Define a new section (if desired) to the Theme Customizer
        $wp_customize->add_section('ecranconnecte_options',
            array(
                'title' => __('MyTheme Options', 'Ecran connecte'), //Visible title of section
                'priority' => 1, //Determines what order this appears in
                'capability' => 'edit_theme_options', //Capability needed to tweak
                'description' => __('Allows you to customize some example settings for MyTheme.', 'mytheme'), //Descriptive tooltip
            )
        );

        //2. Register new settings to the WP database...
        $wp_customize->add_setting('link_textcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
            array(
                'default' => '#ffffff', //Default setting/value to save
                'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
                'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
                'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
            )
        );

        //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
        $wp_customize->add_control(new WP_Customize_Color_Control( //Instantiate the color control class
            $wp_customize, //Pass the $wp_customize object (required)
            'mytheme_link_textcolor', //Set a unique ID for the control
            array(
                'label' => __('Couleur des liens', 'mytheme'), //Admin-visible name of the control
                'settings' => 'link_textcolor', //Which setting to load and manipulate (serialized is okay)
                'priority' => 10, //Determines the order this control appears in for the specified section
                'section' => 'Ecran connecte', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            )
        ));

        $wp_customize->add_setting('header_background_color', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
            array(
                'default' => '#00558a', //Default setting/value to save
                'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
                'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
                'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
            )
        );

        //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
        $wp_customize->add_control(new WP_Customize_Color_Control( //Instantiate the color control class
            $wp_customize, //Pass the $wp_customize object (required)
            'mytheme_header_background_color', //Set a unique ID for the control
            array(
                'label' => __('Couleur du fond de l\'en-tête', 'mytheme'), //Admin-visible name of the control
                'settings' => 'header_background_color', //Which setting to load and manipulate (serialized is okay)
                'priority' => 11, //Determines the order this control appears in for the specified section
                'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            )
        ));

        //2. Register new settings to the WP database...
        $wp_customize->add_setting('dropdown_background_color', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
            array(
                'default' => '#ffffff', //Default setting/value to save
                'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
                'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
                'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
            )
        );

        //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
        $wp_customize->add_control(new WP_Customize_Color_Control( //Instantiate the color control class
            $wp_customize, //Pass the $wp_customize object (required)
            'mytheme_dropdown_background_color', //Set a unique ID for the control
            array(
                'label' => __('Dropdown background color', 'mytheme'), //Admin-visible name of the control
                'settings' => 'dropdown_background_color', //Which setting to load and manipulate (serialized is okay)
                'priority' => 11, //Determines the order this control appears in for the specified section
                'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            )
        ));

        //2. Register new settings to the WP database...
        $wp_customize->add_setting('dropdown_text_color', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
            array(
                'default' => '#000000', //Default setting/value to save
                'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
                'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
                'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
            )
        );

        //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
        $wp_customize->add_control(new WP_Customize_Color_Control( //Instantiate the color control class
            $wp_customize, //Pass the $wp_customize object (required)
            'mytheme_dropdown_text_color', //Set a unique ID for the control
            array(
                'label' => __('Dropdown text color', 'mytheme'), //Admin-visible name of the control
                'settings' => 'dropdown_text_color', //Which setting to load and manipulate (serialized is okay)
                'priority' => 3, //Determines the order this control appears in for the specified section
                'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            )
        ));

        //2. Register new settings to the WP database...
        $wp_customize->add_setting('alert_background_color', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
            array(
                'default' => '#FF0000', //Default setting/value to save
                'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
                'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
                'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
            )
        );

        //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
        $wp_customize->add_control(new WP_Customize_Color_Control( //Instantiate the color control class
            $wp_customize, //Pass the $wp_customize object (required)
            'mytheme_alert_background_color', //Set a unique ID for the control
            array(
                'label' => __('Couleur de fond des alertes', 'mytheme'), //Admin-visible name of the control
                'settings' => 'alert_background_color', //Which setting to load and manipulate (serialized is okay)
                'priority' => 3, //Determines the order this control appears in for the specified section
                'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            )
        ));

        //2. Register new settings to the WP database...
        $wp_customize->add_setting('alert_text_color', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
            array(
                'default' => '#ffffff', //Default setting/value to save
                'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
                'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
                'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
            )
        );

        //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
        $wp_customize->add_control(new WP_Customize_Color_Control( //Instantiate the color control class
            $wp_customize, //Pass the $wp_customize object (required)
            'mytheme_alert_text_color', //Set a unique ID for the control
            array(
                'label' => __('Couleur du texte des alertes', 'mytheme'), //Admin-visible name of the control
                'settings' => 'alert_text_color', //Which setting to load and manipulate (serialized is okay)
                'priority' => 3, //Determines the order this control appears in for the specified section
                'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            )
        ));

        //4. We can also change built-in settings by modifying properties. For instance, let's make some stuff use live preview JS...
        $wp_customize->get_setting('background_color')->transport = 'postMessage';
        $wp_customize->get_setting('header_background_color')->transport = 'postMessage';
        $wp_customize->get_setting('dropdown_background_color')->transport = 'postMessage';
        $wp_customize->get_setting('dropdown_text_color')->transport = 'postMessage';
        $wp_customize->get_setting('alert_background_color')->transport = 'postMessage';
        $wp_customize->get_setting('alert_text_color')->transport = 'postMessage';
    }

    /**
     * This will output the custom WordPress settings to the live theme's WP head.
     *
     * Used by hook: 'wp_head'
     *
     * @see add_action('wp_head',$func)
     * @since MyTheme 1.0
     */
    public static function header_output()
    {
        ?>
        <!--Customizer CSS-->
        <style type="text/css">
            <?php self::generate_css('header', 'background-color', 'header_background_color'); ?>
            <?php self::generate_css('.menu-item', 'color', 'header_textcolor'); ?>
            <?php self::generate_css('.menu-item a', 'color', 'header_textcolor'); ?>
            <?php self::generate_css('.dropbtn', 'color', 'header_textcolor'); ?>
            <?php self::generate_css('.menu-item_dropdown .menu-item', 'background-color', 'dropdown_background_color'); ?>
            <?php self::generate_css('.menu-item_dropdown .menu-item a', 'color', 'dropdown_text_color'); ?>
            <?php self::generate_css('body', 'background-color', 'background_color', '#'); ?>
            <?php self::generate_css('.alerts', 'background-color', 'alert_background_color'); ?>
            <?php self::generate_css('.alerts', 'color', 'alert_text_color'); ?>
        </style>
        <!--/Customizer CSS-->
        <?php
    }

    /**
     * This outputs the javascript needed to automate the live settings preview.
     * Also keep in mind that this function isn't necessary unless your settings
     * are using 'transport'=>'postMessage' instead of the default 'transport'
     * => 'refresh'
     *
     * Used by hook: 'customize_preview_init'
     *
     * @see add_action('customize_preview_init',$func)
     * @since MyTheme 1.0
     */
    public static function live_preview()
    {
        wp_enqueue_script(
            'mytheme-themecustomizer', // Give the script a unique ID
            get_template_directory_uri() . '/assets/js/theme-customizer.js', // Define the path to the JS file
            array('jquery', 'customize-preview'), // Define dependencies
            '', // Define a version (optional)
            true // Specify whether to put in footer (leave this true)
        );
    }

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
    public static function generate_css($selector, $style, $mod_name, $prefix = '', $postfix = ' !important', $echo = true)
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
}

// Setup the Theme Customizer settings and controls...
add_action('customize_register', array('MyTheme_Customize', 'register'));

// Output custom CSS to live site
add_action('wp_head', array('MyTheme_Customize', 'header_output'));

// Enqueue live preview javascript in Theme Customizer admin screen
add_action('customize_preview_init', array('MyTheme_Customize', 'live_preview'));

$args = array(
    'width' => 345,
    'height' => 100,
    'default-image' => get_template_directory_uri() . 'assets/media/header.png',
    'uploads' => true,
);
add_theme_support('custom-header', $args);

$args = array(
    'default-color' => '#ffffff',
    'default-image' => '%1$s/media/background.jpg',
);
add_theme_support('custom-background', $args);

$args = array(
    'default-color' => '#ffffff',
    'default-image' => '%1$s/media/background.jpg',
);
add_theme_support('custom-header-background', $args);

// Sidebars
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