<?php

/**
 * Custom schedule
 *
 * @param $wp_customize
 */
function my_customize_schedule_register($wp_customize)
{
    $wp_customize->add_section( 'ecran_connecte_schedule' , array(
        'title'      => 'Emploi du temps',
        'priority'   => 1,
        'panel' => 'ecran_connecte_control'
    ) );

    $wp_customize->add_setting( 'ecran_connecte_schedule_msg' , array(
        'default'     => true,
        'transport'   => 'refresh',
    ) );

    $wp_customize->add_control( 'ecran_connecte_schedule_msg', array(
        'label' => 'Cacher "Vous n\'avez pas cours !" lors de l\'affichage d\'un utilisateur "télévision"',
        'section' => 'ecran_connecte_schedule',
        'settings' => 'ecran_connecte_schedule_msg',
        'type' => 'radio',
        'choices' => array(
            'show' => 'Afficher le message',
            'hide' => 'Ne pas afficher le message',
        ),
    ) );

	$wp_customize->add_setting( 'ecran_connecte_schedule_scroll' , array(
		'default'     => true,
		'transport'   => 'refresh',
	) );

	$wp_customize->add_control( 'ecran_connecte_schedule_scroll', array(
		'label' => 'Lors de l\'affichage de plusieurs emplois du temps',
		'section' => 'ecran_connecte_schedule',
		'settings' => 'ecran_connecte_schedule_scroll',
		'type' => 'radio',
		'choices' => array(
			'vert' => 'Faire un défilement vertical',
			'one' => 'Afficher les emplois du temps un par un',
		),
	) );
}

add_action('customize_register', 'my_customize_schedule_register');
