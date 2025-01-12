<?php

/**
 * Plugin Name:       Ecran connecté AMU
 * Plugin URI:        https://github.com/Nicolas-Rohrbach/plugin-ecran-connecte
 * Description:       Plugin écrans connectés de l'AMU, ce plugin permet de générer des fichiers ICS. Ces fichiers sont ensuite lus pour pouvoir afficher l'emploi du temps de la personne connectée. Ce plugin permet aussi d'afficher la météo, des informations, des alertes. Tant en ayant une gestion des utilisateurs et des informations.
 * Version:           1.2.9
 * Author:            Léa Arnaud & Nicolas Rohrbach
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ecran-connecte
 * GitHub Plugin URI: https://github.com/Nicolas-Rohrbach/plugin-ecran-connecte
 */

use controllers\AlertController;
use controllers\CodeAdeController;
use controllers\InformationController;
use models\CodeAde;
use models\User;

if (! defined('ABSPATH')) {
	die;
}

define('TV_PLUG_PATH', '/wp-content/plugins/plugin-ecran-connecte/');
define('TV_UPLOAD_PATH', '/wp-content/uploads/media/');
define('TV_ICSFILE_PATH', '/wp-content/uploads/fileICS/');

require_once __DIR__ . '/vendor/autoload.php';

include 'config.php';
include 'jsInjections.php';
include 'blocks.php';

// Upload schedules
$submit = filter_input(INPUT_POST, 'updatePluginEcranConnecte');
if(isset($submit)) {
    include_once(ABSPATH . 'wp-includes/pluggable.php');
    $current_user = wp_get_current_user();
    if(in_array('administrator', $current_user->roles) || in_array('secretaire', $current_user->roles)) {
	    downloadFileICS_func();
    }
}

/**
 * Function for WPCron
 * Upload schedules
 */
function downloadFileICS_func()
{
    move_fileICS_schedule();

	$controllerAde = new CodeAdeController();
    $model = new CodeAde();

    $codesAde = $model->getList();
    foreach ($codesAde as $codeAde) {
        $controllerAde->addFile($codeAde->getCode());
    }

	/*
    $information = new InformationController();
    $information->registerNewInformation();

    $alert = new AlertController();
    $alert->registerNewAlert();
	*/
}

add_action('downloadFileICS', 'downloadFileICS_func');

/**
 * Upload the schedule of users
 *
 * @param $users    User[]
 */
function downloadSchedule($users)
{
    $controllerAde = new CodeAdeController();
    foreach ($users as $user) {
        foreach ($user->getCodes() as $code) {
            $controllerAde->addFile($code->getCode());
        }
    }
}

/**
 * Change place of file
 */
function move_fileICS_schedule()
{
    if ($myFiles = scandir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3')) {
        foreach ($myFiles as $myFile) {
            if (is_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3/' . $myFile)) {
                wp_delete_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3/' . $myFile);
            }
        }
    }
    if ($myFiles = scandir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2')) {
        foreach ($myFiles as $myFile) {
            if (is_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2/' . $myFile)) {
                copy($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2/' . $myFile, $_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3/' . $myFile);
                wp_delete_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2/' . $myFile);
            }
        }
    }

    if ($myFiles = scandir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1')) {
        foreach ($myFiles as $myFile) {
            if (is_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1/' . $myFile)) {
                copy($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1/' . $myFile, $_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2/' . $myFile);
                wp_delete_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1/' . $myFile);
            }
        }
    }

    if ($myFiles = scandir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0')) {
        foreach ($myFiles as $myFile) {
            if (is_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0/' . $myFile)) {
                copy($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0/' . $myFile, $_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1/' . $myFile);
                wp_delete_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0/' . $myFile);
            }
        }
    }
}

/**
 * Function for "Nuit de l'info"
 * Give numbers of participant
 *
 * @return string
 */
function displayParticipant()
{
    $url = "https://www.nuitdelinfo.com/inscription/sites/55";
    $result = file_get_contents($url);
    $result = explode('<li class="list-group-item list-group-item-info">', $result);
    $result1 = substr($result[1], 0, -60);
    $result2 = substr($result[2], 0, 70);
    return '<p class="info-text"> - '.$result1.'</p><p class="info-text"> - '.$result2.'</p>';
}
