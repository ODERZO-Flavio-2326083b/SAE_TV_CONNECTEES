<?php

use Controllers\UserController;
use Views\UserView;

/**
 * Function of the block
 *
 * @return string
 */
function delete_account_render_callback()
{
    if(is_page()) {
	    $myAccount = new UserController();
	    $view = new UserView();
        $myAccount->deleteAccount();
        return $view->displayDeleteAccount().$view->displayEnterCode();
    }
}

/**
 * Build a block
 */
function block_delete_account()
{
    wp_register_script(
        'delete_account-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/delete-account', array(
        'editor_script' => 'delete_account-script',
        'render_callback' => 'delete_account_render_callback'
    ));
}
add_action( 'init', 'block_delete_account' );