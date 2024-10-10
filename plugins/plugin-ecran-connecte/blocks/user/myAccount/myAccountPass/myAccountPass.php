<?php

use Controllers\UserController;
use Views\UserView;

/**
 * Function of the block
 *
 * @return string
 */
function password_modify_render_callback()
{
    if(is_page()) {
	    $myAccount = new UserController();
	    $view = new UserView();
        $myAccount->modifyPwd();
        return $view->displayModifyPassword();
    }
}

/**
 * Build a block
 */
function block_password_modify()
{
    wp_register_script(
        'pass_modify-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/modify-pass', array(
        'editor_script' => 'pass_modify-script',
        'render_callback' => 'password_modify_render_callback'
    ));
}
add_action( 'init', 'block_password_modify' );
