<?php

use Controllers\UserController;

/**
 * Function of the block
 *
 * @return string
 */
function choose_account_render_callback()
{
    if(is_page()) {
	    $myAccount = new UserController();
        return $myAccount->chooseModif();
    }
}

/**
 * Build a block
 */
function block_choose_account() {
    wp_register_script(
        'choose_account-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/choose-account', array(
        'editor_script' => 'choose_account-script',
        'render_callback' => 'choose_account_render_callback'
    ));
}
add_action( 'init', 'block_choose_account' );