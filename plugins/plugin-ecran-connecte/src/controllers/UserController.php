<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use Models\Information;
use Models\User;
use R34ICS;
use Views\UserView;

/**
 * Class UserController
 *
 * Gère tous les utilisateurs (Création, mise à jour, suppression)
 *
 * @package Controllers
 */
class UserController extends Controller
{

    /**
     * Modèle d'utilisateur.
     *
     * @var User
     */
    private $model;

    /**
     * Vue d'utilisateur.
     *
     * @var UserView
     */
    private $view;

    /**
     * Constructeur de UserController.
     * Initialise le modèle et la vue.
     */
    public function __construct() {
        $this->model = new User();
        $this->view = new UserView();
    }

    /**
     * Supprime un utilisateur.
     *
     * @param int $id L'ID de l'utilisateur à supprimer.
     */
    public function delete($id) {
        $user = $this->model->get($id);
        $userData = get_userdata($id);
        $user->delete();
        if (in_array("enseignant", $userData->roles) || in_array("secretaire", $userData->roles) ||
            in_array("administrator", $userData->roles) || in_array("directeuretude", $userData->roles)) {
            $modelAlert = new Alert();
            $alerts = $modelAlert->getAuthorListAlert($user->getLogin());
            foreach ($alerts as $alert) {
                $alert->delete();
            }
        }

        if (in_array("secretaire", $userData->roles) || in_array("administrator", $userData->roles) ||
            in_array("directeuretude", $userData->roles)) {
            $modelInfo = new Information();
            $infos = $modelInfo->getAuthorListInformation($user->getId());
            foreach ($infos as $info) {
                $goodType = ['img', 'pdf', 'tab', 'event'];
                if (in_array($info->getType(), $goodType)) {
                    $infoController = new InformationController();
                    $infoController->deleteFile($info->getId());
                }
                $modelInfo->delete();
            }
        }
    }

    /**
     * Supprime le compte de l'utilisateur.
     */
    public function deleteAccount() {
        $action = filter_input(INPUT_POST, 'deleteMyAccount');
        $actionDelete = filter_input(INPUT_POST, 'deleteAccount');
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        if (isset($action)) {
            $password = filter_input(INPUT_POST, 'verifPwd');
            if (wp_check_password($password, $current_user->user_pass)) {

                $code = wp_generate_password();
                if (!empty($user->getCodeDeleteAccount())) {
                    $user->updateCode($code);
                } else {
                    $user->createCode($code);
                }

                // Construction de l'e-mail
                $to = $current_user->user_email;
                $subject = "Désinscription à la télé-connecté";
                $message = ' <!DOCTYPE html>
                             <html lang="fr">
                             	<head>
                               		<title>Désnscription à la télé-connecté</title>
                              	</head>
                              	<body>
                               		<p>Bonjour, vous avez décidé de vous désinscrire sur le site de la Télé Connecté</p>
                               		<p> Votre code de désinscription est : ' . $code . '.</p>
                               		<p> Pour vous désinscrire, rendez-vous sur le site : <a href="' . home_url() . '/mon-compte/"> Tv Connectée.</p>
                              	</body>
                             </html>';

                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail($to, $subject, $message, $headers);
                $this->view->displayMailSend();
            } else {
                $this->view->displayWrongPassword();
            }
        } elseif (isset($actionDelete)) {
            $code = filter_input(INPUT_POST, 'codeDelete');
            $userCode = $user->getCodeDeleteAccount();
            if ($code == $userCode) {
                $user->deleteCode();
                $user->delete();
                $this->view->displayModificationValidate();
            } else {
                echo 'Code ' . $code;
                echo 'User code ' . $userCode;
                $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayDeleteAccount() . $this->view->displayEnterCode();
    }

    /**
     * Permet à l'utilisateur de choisir une action de modification.
     *
     * @return string Retourne le HTML du formulaire de sélection de modification.
     */
    public function chooseModif() {
        $current_user = wp_get_current_user();
        $string = $this->view->displayStartMultiSelect();

        if (in_array('etudiant', $current_user->roles)) {
            $string .= $this->view->displayTitleSelect('code', 'Modifier mes codes', true) .
                $this->view->displayTitleSelect('pass', 'Modifier mon mot de passe');
        } else {
            $string .= $this->view->displayTitleSelect('pass', 'Modifier mon mot de passe', true);
        }

        $string .= $this->view->displayTitleSelect('delete', 'Supprimer mon compte') .
            $this->view->displayEndOfTitle();

        if (in_array('etudiant', $current_user->roles)) {
            $string .= $this->view->displayContentSelect('code', $this->modifyCodes(), true) .
                $this->view->displayContentSelect('pass', $this->modifyPwd());
        } else {
            $string .= $this->view->displayContentSelect('pass', $this->modifyPwd(), true);
        }

        $string .= $this->view->displayContentSelect('delete', $this->deleteAccount()) . $this->view->displayEndDiv();

        return $string;
    }

    /**
     * Modifie le mot de passe de l'utilisateur.
     *
     * @return string Retourne le HTML du formulaire de modification du mot de passe.
     */
    public function modifyPwd() {
        $action = filter_input(INPUT_POST, 'modifyMyPwd');
        $current_user = wp_get_current_user();
        if (isset($action)) {
            $pwd = filter_input(INPUT_POST, 'verifPwd');
            if (wp_check_password($pwd, $current_user->user_pass)) {
                $newPwd = filter_input(INPUT_POST, 'newPwd');
                wp_set_password($newPwd, $current_user->ID);
                $this->view->displayModificationPassValidate();
            } else {
                $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayModifyPassword();
    }

    /**
     * Affiche l'emploi du temps.
     *
     * @param int $code Le code ADE de l'emploi du temps.
     * @param bool $allDay Indique si l'emploi du temps est pour toute la journée.
     *
     * @return string|bool Retourne l'affichage de l'emploi du temps ou false en cas d'erreur.
     */
    public function displaySchedule($code, $allDay = false) {
        global $R34ICS;
        $R34ICS = new R34ICS();

        $url = $this->getFilePath($code);
        $args = array(
            'count' => 10,
            'description' => null,
            'eventdesc' => null,
            'format' => null,
            'hidetimes' => null,
            'showendtimes' => null,
            'title' => null,
            'view' => 'list',
        );
        return $R34ICS->display_calendar($url, $code, $allDay, $args);
    }

    /**
     * Affiche le lien vers l'emploi du temps correspondant au code dans l'URL.
     *
     * @return string Retourne le HTML de sélection de l'emploi du temps.
     */
    function displayYearSchedule() {
        $id = $this->getMyIdUrl();

        $codeAde = new CodeAde();

        if (is_numeric($id)) {
            $codeAde = $codeAde->get($id);
            if (!is_null($codeAde->getTitle()) && $codeAde->getType() === 'year') {
                return $this->displaySchedule($codeAde->getCode(), true);
            }
        }

        return $this->view->displaySelectSchedule();
    }

    /**
     * Vérifie si un utilisateur avec le même titre ou code existe déjà.
     *
     * @param User $newUser L'utilisateur à vérifier.
     *
     * @return bool Retourne true si l'utilisateur existe déjà, false sinon.
     */
    public function checkDuplicateUser(User $newUser) {
        $codesAde = $this->model->checkUser($newUser->getLogin(), $newUser->getEmail());

        if (sizeof($codesAde) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Modifie les codes ADE de l'étudiant.
     *
     * @return string Retourne le HTML du formulaire de modification des codes.
     */
    public function modifyCodes() {
        $current_user = wp_get_current_user();
        $codeAde = new CodeAde();
        $this->model = $this->model->get($current_user->ID);

        $action = filter_input(INPUT_POST, 'modifvalider');

        if (isset($action)) {
            $year = filter_input(INPUT_POST, 'modifYear');
            $group = filter_input(INPUT_POST, 'modifGroup');
            $halfGroup = filter_input(INPUT_POST, 'modifHalfgroup');

            if (is_numeric($year) && is_numeric($group) && is_numeric($halfGroup)) {

                $codes = [$year, $group, $halfGroup];
                $codesAde = [];
                foreach ($codes as $code) {
                    if ($code !== 0) {
                        $code = $codeAde->getByCode($code);
                    }
                    $codesAde[] = $code;
                }

                if ($codesAde[0]->getType() !== 'year') {
                    $codesAde[0] = 0;
                }

                if ($codesAde[1]->getType() !== 'group') {
                    $codesAde[1] = 0;
                }

                if ($codesAde[2]->getType() !== 'halfGroup') {
                    $codesAde[2] = 0;
                }

                $this->model->setCodes($codesAde);

                if ($this->model->update()) {
                    $this->view->successMesageChangeCode();
                } else {
                    $this->view->errorMesageChangeCode();
                }
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayModifyMyCodes($this->model->getCodes(), $years, $groups, $halfGroups);
    }
}
