<?php

use Controllers\AlertController;
use Controllers\CodeAdeController;
use Controllers\DepartmentController;
use Controllers\InformationController;
use Controllers\SecretaryController;
use Controllers\StudentController;
use Controllers\StudyDirectorController;
use Controllers\TeacherController;
use Controllers\TechnicianController;
use Controllers\TelevisionController;
use Controllers\UserController;
use Views\HelpMapView;
use Views\UserView;

/*
 * ALERT BLOCKS
 */

/**
 * Function of the block
 *
 * @return string
 */
function alert_render_callback()
{
    if(is_page()) {
        $alert = new AlertController();
        return $alert->insert();
    }
}

/**
 * Build a block
 */
function block_alert()
{
    wp_register_script(
        'alert-script',
        plugins_url( '/blocks/alert/create.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/add-alert', array(
        'editor_script' => 'alert-script',
        'render_callback' => 'alert_render_callback'
    ));
}
add_action('init', 'block_alert');


/**
 * Function of the block
 *
 * @return string
 */
function alert_management_render_callback()
{
    if(is_page()) {
        $alert = new AlertController();
        return $alert->displayAll();
    }
}

/**
 * Build a block
 */
function block_alert_management()
{
    wp_register_script(
        'alert_manage-script',
        plugins_url( '/blocks/alert/displayAll.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/manage-alert', array(
        'editor_script' => 'alert_manage-script',
        'render_callback' => 'alert_management_render_callback'
    ));
}
add_action( 'init', 'block_alert_management' );

/**
 * Function of the block
 *
 * @return string
 */
function alert_modify_render_callback()
{
    if(is_page()) {
        $alert = new AlertController();
        return $alert->modify();
    }
}

/**
 * Build a block
 */
function block_alert_modify()
{
    wp_register_script(
        'alert_modify-script',
        plugins_url( '/blocks/alert/modify.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/modify-alert', array(
        'editor_script' => 'alert_modify-script',
        'render_callback' => 'alert_modify_render_callback'
    ));
}
add_action( 'init', 'block_alert_modify' );

/*
 * CODE ADE BLOCKS
 */

/**
 * Function of the block
 *
 * @return string
 */
function code_ade_render_callback()
{
    if(is_page()) {
        $codeAde = new CodeAdeController();
        return $codeAde->insert();
    }
}

/**
 * Build a block
 */
function block_code_ade()
{
    wp_register_script(
        'code_ade-script',
        plugins_url( '/blocks/codeAde/create.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/add-code', array(
        'editor_script' => 'code_ade-script',
        'render_callback' => 'code_ade_render_callback'
    ));
}
add_action( 'init', 'block_code_ade' );

/**
 * Function of the block
 *
 * @return string
 */
function code_management_render_callback()
{
    if(is_page()) {
        $code = new CodeAdeController();
        $code->deleteCodes();
        return $code->displayAllCodes();
    }
}

/**
 * Build a block
 */
function block_code_management()
{
    wp_register_script(
        'code_manage-script',
        plugins_url( '/blocks/codeAde/displayAll.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/manage-code', array(
        'editor_script' => 'code_manage-script',
        'render_callback' => 'code_management_render_callback'
    ));
}
add_action( 'init', 'block_code_management' );


/**
 * Function of the block
 *
 * @return string
 */
function code_modify_render_callback()
{
    if(is_page()) {
        $code = new CodeAdeController();
        return $code->modify();
    }
}

/**
 * Build a block
 */
function block_code_modify()
{
    wp_register_script(
        'code_modify-script',
        plugins_url( '/blocks/codeAde/modify.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/modify-code', array(
        'editor_script' => 'code_modify-script',
        'render_callback' => 'code_modify_render_callback'
    ));
}
add_action( 'init', 'block_code_modify' );

/*
 * INFORMATION BLOCKS
 */

/**
 * Function of the block
 *
 * @return string
 */
function information_render_callback()
{
    if(is_page()) {
        $information = new InformationController();
        return $information->create();
    }
}

/**
 * Build a block
 */
function block_information()
{
    wp_register_script(
        'information-script',
        plugins_url( '/blocks/information/create.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/add-information', array(
        'editor_script' => 'information-script',
        'render_callback' => 'information_render_callback'
    ));
}
add_action( 'init', 'block_information' );

/**
 * Function of the block
 *
 * @return string
 */
function information_management_render_callback()
{
    if(is_page()) {
        $information = new InformationController();
        return $information->displayAll();
    }
}

/**
 * Build a block
 */
function block_information_management()
{
    wp_register_script(
        'information_manage-script',
        plugins_url( '/blocks/information/displayAll.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/manage-information', array(
        'editor_script' => 'information_manage-script',
        'render_callback' => 'information_management_render_callback'
    ));
}
add_action( 'init', 'block_information_management' );


/**
 * Function of the block
 *
 * @return string
 */
function information_modify_render_callback()
{
    if(is_page()) {
        $information = new InformationController();
        return $information->modify();
    }
}

/**
 * Build a block
 */
function block_information_modify()
{
    wp_register_script(
        'information_modify-script',
        plugins_url( '/blocks/information/modify.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/modify-information', array(
        'editor_script' => 'information_modify-script',
        'render_callback' => 'information_modify_render_callback'
    ));
}
add_action( 'init', 'block_information_modify' );

/*
 * SCHEDULE BLOCKS
 */

/**
 * Function of the block
 *
 * @return string
 */
function schedule_render_callback()
{
        $current_user = wp_get_current_user();
        if(in_array('directeuretude', $current_user->roles)) {
            $controller = new StudyDirectorController();
            return $controller->displayMySchedule();
        } else if (in_array("enseignant", $current_user->roles)) {
            $controller = new TeacherController();
            return $controller->displayMySchedule();
        } else if (in_array("etudiant", $current_user->roles)) {
            $controller = new StudentController();
            return $controller->displayMySchedule();
        } else if (in_array("television", $current_user->roles)) {
            $controller = new TelevisionController();
            return $controller->displayMySchedule();
        } else if (in_array("technicien", $current_user->roles)) {
            $controller = new TechnicianController();
            return $controller->displayMySchedule();
        } else if (in_array("administrator", $current_user->roles) || in_array("secretaire", $current_user->roles)) {
            $controller = new SecretaryController();
            return $controller->displayMySchedule();
        } else {
            $user = new UserView();
            return $user->displayHome();
        }
}

/**
 * Build a block
 */
function block_schedule()
{
    wp_register_script(
        'schedule-script',
        plugins_url('/blocks/schedule/userSchedule.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-data')
    );

    register_block_type('tvconnecteeamu/schedule', array(
        'editor_script' => 'schedule-script',
        'render_callback' => 'schedule_render_callback'
    ));
}
add_action('init', 'block_schedule');

/**
 * Function of the block
 *
 * @return string
 */
function schedules_render_callback()
{
    if(is_page()) {
        $schedule = new UserController();
        return $schedule->displayYearSchedule();
    }
}

/**
 * Build a block
 */
function block_schedules()
{
    wp_register_script(
        'schedules-script',
        plugins_url( '/blocks/schedule/globalSchedule.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/schedules', array(
        'editor_script' => 'schedules-script',
        'render_callback' => 'schedules_render_callback'
    ));
}
add_action( 'init', 'block_schedules' );

/*
 *
 */

/**
 * Function of the block
 *
 * @return string
 */
function subscription_render_callback()
{
    if(is_page()) {
        $view = new UserView();
        return $view->displayButtonSubscription();
    }
}

/**
 * Build a block
 */
function block_subscription()
{
    wp_register_script(
        'subscription-script',
        plugins_url( '/blocks/subscriptionPush/subscriptionPush.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/subscription', array(
        'editor_script' => 'subscription-script',
        'render_callback' => 'subscription_render_callback'
    ));
}
add_action('init', 'block_subscription');

/*
 * USER BLOCKS
 */

/**
 * Function of the block
 *
 * @return string
 */
function creation_user_render_callback()
{
    if(is_page()) {
        $manageUser = new SecretaryController();
        return $manageUser->createUsers();
    }
}

/**
 * Build a block
 */
function block_creation_user()
{
    wp_register_script(
        'creation_user-script',
        plugins_url( '/blocks/user/create.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/creation-user', array(
        'editor_script' => 'creation_user-script',
        'render_callback' => 'creation_user_render_callback'
    ));
}
add_action( 'init', 'block_creation_user' );

/**
 * Function of the block
 *
 * @return string
 */
function management_user_render_callback()
{
    if(is_page()) {
        $manageUser = new SecretaryController();
        $manageUser->deleteUsers();
        return $manageUser->displayUsers();
    }
}

/**
 * Build a block
 */
function block_management_user()
{
    wp_register_script(
        'management_user-script',
        plugins_url( '/blocks/user/displayAll.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/management-user', array(
        'editor_script' => 'management_user-script',
        'render_callback' => 'management_user_render_callback'
    ));
}
add_action( 'init', 'block_management_user' );

/**
 * Function of the block
 *
 * @return string
 */
function user_modify_render_callback()
{
    if(is_page()) {
        $user = new SecretaryController();
        return $user->modifyUser();
    }
}

/**
 * Build a block
 */
function block_user_modify()
{
    wp_register_script(
        'user_modify-script',
        plugins_url( '/blocks/user/modify.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/modify-user', array(
        'editor_script' => 'user_modify-script',
        'render_callback' => 'user_modify_render_callback'
    ));
}
add_action( 'init', 'block_user_modify' );

/**
 * Function of the block
 *
 * @return string
 */
function choose_account_render_callback()
{
    if(is_page()) {
        $user = new UserController();
        return $user->chooseModif();
    }
}

/**
 * Build a block
 */
function block_choose_account() {
    wp_register_script(
        'choose_account-script',
        plugins_url( '/blocks/user/account.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/choose-account', array(
        'editor_script' => 'choose_account-script',
        'render_callback' => 'choose_account_render_callback'
    ));
}
add_action( 'init', 'block_choose_account' );

/**
 * Function of the block
 *
 * @return string
 */
function code_account_render_callback()
{
    $current_user = wp_get_current_user();
    if(is_page() && in_array('etudiant', $current_user->roles)) {
        $myAccount = new StudentController();
        $myAccount->modifyCodes();
    }
}

/**
 * Build a block
 */
function block_code_account()
{
    wp_register_script(
        'code_account-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/code-account', array(
        'editor_script' => 'code_account-script',
        'render_callback' => 'code_account_render_callback'
    ));
}
add_action( 'init', 'block_code_account' );

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

/**
 * Function of the block
 *
 * @return string
 */
function help_map_render_callback()
{
    if(is_page()) {
        $view = new HelpMapView();
        return $view->displayHelpMap();
    }
}

/**
 * Build a block
 */
function block_help_map()
{
    wp_register_script(
        'help_map-script',
        plugins_url( '/blocks/helpMap/display.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/help-map', array(
        'editor_script' => 'help_map-script',
        'render_callback' => 'help_map_render_callback'
    ));
}
add_action( 'init', 'block_help_map' );

/**
 *
 */
function department_add_render_callback() {
	if(is_page()) {
		$dpt = new DepartmentController();
		return $dpt->insert();
	}
}

function block_department_add()
{
	wp_register_script(
		'department_add-script',
		plugins_url( '/blocks/department/create.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-data' )
	);

	register_block_type('tvconnecteeamu/add-department', array(
		'editor_script' => 'department_add-script',
		'render_callback' => 'department_add_render_callback'
	));
}
add_action( 'init', 'block_department_add' );
