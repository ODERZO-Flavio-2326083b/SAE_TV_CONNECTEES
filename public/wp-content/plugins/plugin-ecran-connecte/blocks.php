<?php

use controllers\AlertController;
use controllers\CodeAdeController;
use controllers\CSSCustomizerController;
use controllers\DepartmentController;
use controllers\InformationController;
use controllers\SecretaryController;
use controllers\TechnicianController;
use controllers\TelevisionController;
use controllers\UserController;
use views\UserView;


/*
 * Gestion des alertes : création, affichage et modification.
 */

// Fonction de rendu du bloc de création d'alertes
function alert_render_callback()
{
	if (current_user_can('add_alert')) {
		$alert = new AlertController();
		return $alert->insert();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc de création d'alertes
function block_alert()
{
	wp_register_script(
		'alert-script',
		plugins_url('/blocks/alert/create.js', __FILE__),
		array('wp-blocks', 'wp-element', 'wp-data')
	);

	register_block_type('tvconnecteeamu/add-alert', array(
		'editor_script' => 'alert-script',
		'render_callback' => 'alert_render_callback'
	));
}
add_action('init', 'block_alert');

// Fonction de rendu du bloc d'affichage des alertes
function alert_management_render_callback()
{
	if (current_user_can('view_alerts')) {
		$alert = new AlertController();
		return $alert->displayAll();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc d'affichage des alertes
function block_alert_management()
{
	wp_register_script(
		'alert_manage-script',
		plugins_url('/blocks/alert/displayAll.js', __FILE__),
		array('wp-blocks', 'wp-element', 'wp-data')
	);

	register_block_type('tvconnecteeamu/manage-alert', array(
		'editor_script' => 'alert_manage-script',
		'render_callback' => 'alert_management_render_callback'
	));
}
add_action('init', 'block_alert_management');

// Fonction de rendu du bloc de modification des alertes
function alert_modify_render_callback()
{
	if (current_user_can('edit_alert')) {
		$alert = new AlertController();
		return $alert->modify();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc de modification des alertes
function block_alert_modify()
{
	wp_register_script(
		'alert_modify-script',
		plugins_url('/blocks/alert/modify.js', __FILE__),
		array('wp-blocks', 'wp-element', 'wp-data')
	);

	register_block_type('tvconnecteeamu/modify-alert', array(
		'editor_script' => 'alert_modify-script',
		'render_callback' => 'alert_modify_render_callback'
	));
}
add_action('init', 'block_alert_modify');

/*
 * Gestion des codes ADE : création, affichage et modification.
 */

// Fonction de rendu du bloc d'ajout de Code ADE
function code_ade_render_callback()
{
	if (current_user_can('add_ade_code')) {
		$codeAde = new CodeAdeController();
		return $codeAde->insert();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc de création de Code ADE
function block_code_ade()
{
	wp_register_script(
		'code_ade-script',
		plugins_url('/blocks/codeAde/create.js', __FILE__),
		array('wp-blocks', 'wp-element', 'wp-data')
	);

	register_block_type('tvconnecteeamu/add-code', array(
		'editor_script' => 'code_ade-script',
		'render_callback' => 'code_ade_render_callback'
	));
}
add_action('init', 'block_code_ade');

// Fonction de rendu du bloc d'affichage des codes ADE
function code_management_render_callback()
{
	if (current_user_can('view_ade_codes')) {
		$code = new CodeAdeController();
		$code->deleteCodes();
		return $code->displayAllCodes();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc d'affichage des codes ADE
function block_code_management()
{
	wp_register_script(
		'code_manage-script',
		plugins_url('/blocks/codeAde/displayAll.js', __FILE__),
		array('wp-blocks', 'wp-element', 'wp-data')
	);

	register_block_type('tvconnecteeamu/manage-code', array(
		'editor_script' => 'code_manage-script',
		'render_callback' => 'code_management_render_callback'
	));
}
add_action('init', 'block_code_management');

// Fonction de rendu du bloc de modification des codes ADE
function code_modify_render_callback()
{
	if (current_user_can('edit_ade_code')) {
		$code = new CodeAdeController();
		return $code->modify();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc de modification des codes ADE
function block_code_modify()
{
	wp_register_script(
		'code_modify-script',
		plugins_url('/blocks/codeAde/modify.js', __FILE__),
		array('wp-blocks', 'wp-element', 'wp-data')
	);

	register_block_type('tvconnecteeamu/modify-code', array(
		'editor_script' => 'code_modify-script',
		'render_callback' => 'code_modify_render_callback'
	));
}
add_action('init', 'block_code_modify');

/*
 * Gestion des informations : création, affichage et modification.
 */

// Fonction de rendu du bloc de création d'informations
function information_render_callback()
{
	if (current_user_can('add_information')) {
		$information = new InformationController();
		return $information->create();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc de création d'informations
function block_information()
{
	wp_register_script(
		'information-script',
		plugins_url('/blocks/information/create.js', __FILE__),
		array('wp-blocks', 'wp-element', 'wp-data')
	);

	register_block_type('tvconnecteeamu/add-information', array(
		'editor_script' => 'information-script',
		'render_callback' => 'information_render_callback'
	));
}
add_action('init', 'block_information');

// Fonction de rendu du bloc d'affichage des informations
function information_management_render_callback()
{
	if (current_user_can('view_informations')) {
		$information = new InformationController();
		return $information->displayAll();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc d'affichage des informations
function block_information_management()
{
	wp_register_script(
		'information_manage-script',
		plugins_url('/blocks/information/displayAll.js', __FILE__),
		array('wp-blocks', 'wp-element', 'wp-data')
	);

	register_block_type('tvconnecteeamu/manage-information', array(
		'editor_script' => 'information_manage-script',
		'render_callback' => 'information_management_render_callback'
	));
}
add_action('init', 'block_information_management');

// Fonction de rendu du bloc de modification des informations
function information_modify_render_callback()
{
	if (current_user_can('edit_information')) {
		$information = new InformationController();
		return $information->modify();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc de modification des informations
function block_information_modify()
{
	wp_register_script(
		'information_modify-script',
		plugins_url('/blocks/information/modify.js', __FILE__),
		array('wp-blocks', 'wp-element', 'wp-data')
	);

	register_block_type('tvconnecteeamu/modify-information', array(
		'editor_script' => 'information_modify-script',
		'render_callback' => 'information_modify_render_callback'
	));
}
add_action('init', 'block_information_modify');

/*
 * Gestion des emplois du temps : création, affichage et modification.
 */

// Rendu de l'emploi du temps correspondant au role de l'utilisateur connecté
function schedule_render_callback()
{
        $current_user = wp_get_current_user();
        if (in_array("television", $current_user->roles)) {
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

// Ajout du bloc d'affichage de l'emploi du temps
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

// Bloc de rendu de l'emploi du temps de l'année
function schedules_render_callback()
{
    if(does_user_has_role(array('administrator', 'secretaire'))) {
        $schedule = new UserController();
        return $schedule->displayYearSchedule();
    } else {
	    echo "Désolé, vous n'avez pas la permission de voir ce contenu";
	    exit;
    }
}

// Ajout du bloc d'affichage de l'emploi du temps de l'année
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
 * Gestion des utilisateurs : création, affichage et modification.
 */

// Bloc de création d'utilisateur
function creation_user_render_callback()
{
    if(current_user_can('add_user')) {
        $manageUser = new SecretaryController();
        return $manageUser->createUsers();
    } else {
	    echo "Désolé, vous n'avez pas la permission de voir ce contenu";
	    exit;
    }
}

// Ajout du bloc de création d'utilisateurs
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

// Bloc de suppressions d'utilisateurs
function management_user_render_callback()
{
    if(current_user_can('view_users')) {
        $manageUser = new SecretaryController();
        $manageUser->deleteUsers();
        return $manageUser->displayUsers();
    } else {
	    echo "Désolé, vous n'avez pas la permission de voir ce contenu";
	    exit;
    }
}

// Ajout du bloc de suppressions d'utilisateurs
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

// Bloc de modification d'utilisateurs
function user_modify_render_callback()
{
    if(current_user_can('edit_user')) {
        $user = new SecretaryController();
        return $user->modifyUser();
    } else {
	    echo "Désolé, vous n'avez pas la permission de voir ce contenu";
	    exit;
    }
}

// Ajout du bloc de modification des utilisateurs
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

// Bloc de modification de son propre compte
function choose_account_render_callback()
{
    if(is_user_logged_in()) {
        $user = new UserController();
        return $user->chooseModif();
    } else {
		echo 'Merci de vous connecter avant d\'accéder à cette page.';
		exit;
    }
}

// Ajout du bloc de modification de son propre compte
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


// Bloc de suppression de son propre compte
function delete_account_render_callback()
{
    if(is_user_logged_in()) {
        $myAccount = new UserController();
        $_view = new UserView();
        $myAccount->deleteAccount();
        return $_view->displayDeleteAccount().$_view->displayEnterCode();
    }
}

// Ajout du bloc de suppression de son compte
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

// Bloc de modification de son mot de passe
function password_modify_render_callback()
{
    if(is_user_logged_in()) {
        $myAccount = new UserController();
        $_view = new UserView();
        $myAccount->modifyPwd();
        return $_view->displayModifyPassword();
    } else {
		echo 'Merci de vous connecter avec d\'accéder à cette page.';
		exit;
    }
}

// Ajout du bloc de modification de son mot de passe
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

/*
 * Gestion du CSS
 */

// Bloc de customisation du CSS
function css_customizer_render_callback()
{
    if(current_user_can('edit_css')) {
        $controller = new CSSCustomizerController();
        $controller->useCssCustomizer();
    } else {
	    echo "Désolé, vous n'avez pas la permission de voir ce contenu";
	    exit;
    }
}

// Ajout du bloc de customisation du CSS
function block_modif_css()
{
    wp_register_script(
        'css_customizer_script',
        plugins_url( 'blocks/cssCustomizer/modify.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/modify-css', array(
        'editor_script' => 'css_customizer_script',
        'render_callback' => 'css_customizer_render_callback'
    ));
}
add_action( 'init', 'block_modif_css' );

/*
 * Gestion des départements : création, affichage et modification.
 */

// Bloc de création de départements
function department_add_render_callback() {
	if(current_user_can('add_department')) {
		$dpt = new DepartmentController();
		return $dpt->insert();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc de création de départements
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

// Bloc de modification de département
function department_modify_render_callback() {
	if(current_user_can('edit_department')) {
		$dpt = new DepartmentController();
		return $dpt->modify();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc de modification de département
function block_department_modify()
{
	wp_register_script(
		'department_modify-script',
		plugins_url( '/blocks/department/modify.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-data' )
	);

	register_block_type('tvconnecteeamu/modify-department', array(
		'editor_script' => 'department_modify-script',
		'render_callback' => 'department_modify_render_callback'
	));
}
add_action('init', 'block_department_modify');

// Bloc d'affichage de départements
function department_displayDeptTable_render_callback() {
	if(current_user_can('view_departments')) {
		$dpt = new DepartmentController();
		$dpt->deleteDepts();
		return $dpt->displayDeptTable();
	} else {
		echo "Désolé, vous n'avez pas la permission de voir ce contenu";
		exit;
	}
}

// Ajout du bloc d'affichage des départements
function block_department_displayDeptTable()
{
	wp_register_script(
		'department_showall-script',
		plugins_url( '/blocks/department/showAll.js', __FILE__),
		array( 'wp-blocks', 'wp-element', 'wp-data' )
	);

	register_block_type('tvconnecteeamu/showall-department', array(
		'editor_script' => 'department_showall-script',
		'render_callback' => 'department_displayDeptTable_render_callback'
	));
}
add_action('init', 'block_department_displayDeptTable');
