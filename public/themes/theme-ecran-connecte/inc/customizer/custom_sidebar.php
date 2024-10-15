<?php

/**
 * Custom sidebar
 *
 * @param $wp_customize
 */
function my_customize_sidebar_register($wp_customize)
{
    $wp_customize->add_section( 'ecran_connecte_sidebar' , array(
        'title'      => 'Barre de côté / Sidebar',
        'priority'   => 3,
        'panel' => 'ecran_connecte_control'
    ) );

    $wp_customize->add_setting( 'sidebar_right_display' , array(
    'default'     => true,
    'transport'   => 'refresh',
) );

    $wp_customize->add_control( 'sidebar_right_display', array(
        'label' => 'Barre droite',
        'section' => 'ecran_connecte_sidebar',
        'settings' => 'sidebar_right_display',
        'type' => 'radio',
        'choices' => array(
            'show' => 'Afficher la barre droite',
            'hide' => 'Ne pas afficher la barre droite',
        ),
    ) );

    $wp_customize->add_setting( 'sidebar_left_display' , array(
        'default'     => true,
        'transport'   => 'refresh',
    ) );

    $wp_customize->add_control( 'sidebar_left_display', array(
        'label' => 'Barre gauche',
        'section' => 'ecran_connecte_sidebar',
        'settings' => 'sidebar_left_display',
        'type' => 'radio',
        'choices' => array(
            'show' => 'Afficher la barre gauche',
            'hide' => 'Ne pas afficher la barre gauche',
        ),
    ) );
}

add_action('customize_register', 'my_customize_sidebar_register');
