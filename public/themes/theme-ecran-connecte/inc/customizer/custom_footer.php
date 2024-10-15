<?php

/**
 * Custom footer
 *
 * @param $wp_customize
 */
function my_customize_footer_register($wp_customize)
{
    $wp_customize->add_section( 'ecran_connecte_footer' , array(
        'title'      => 'Pied de page',
        'priority'   => 5,
        'panel' => 'ecran_connecte_control'
    ) );

    $wp_customize->add_setting( 'ecran_connecte_footer_alert' , array(
        'default'     => true,
        'transport'   => 'refresh',
    ) );

    $wp_customize->add_control( 'ecran_connecte_footer_alert', array(
        'label' => 'Afficher les alertes',
        'section' => 'ecran_connecte_footer',
        'settings' => 'ecran_connecte_footer_alert',
        'type' => 'radio',
        'choices' => array(
            'show' => 'Afficher les alertes',
            'hide' => 'Ne pas afficher les alertes',
        ),
    ) );

    $wp_customize->add_setting( 'ecran_connecte_footer_weather' , array(
        'default'     => true,
        'transport'   => 'refresh',
    ) );

    $wp_customize->add_control( 'ecran_connecte_footer_weather', array(
        'label' => 'Afficher la météo',
        'section' => 'ecran_connecte_footer',
        'settings' => 'ecran_connecte_footer_weather',
        'type' => 'radio',
        'choices' => array(
            'left' => 'Affiche la météo à gauche',
            'right' => 'Affiche la météo à droite',
            'hide' => 'Ne pas afficher la météo',
        ),
    ) );
}

add_action('customize_register', 'my_customize_footer_register');
