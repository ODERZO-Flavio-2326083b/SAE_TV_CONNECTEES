<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\TelevisionView;

/**
 * Class TelevisionController
 *
 * Gère les télévisions (Création, mise à jour, suppression, affichage, affichage des emplois du temps)
 *
 * @package Controllers
 */
class TelevisionController extends UserController implements Schedule
{

    /**
     * Modèle de TelevisionController.
     *
     * @var User
     */
    private $model;

    /**
     * Vue de TelevisionController.
     *
     * @var TelevisionView
     */
    private $view;

    /**
     * Constructeur de TelevisionController.
     * Initialise le modèle et la vue.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TelevisionView();
    }

    /**
     * Insère une télévision dans la base de données.
     *
     * @return string Retourne le HTML du formulaire d'insertion de télévision.
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createTv');
        $codeAde = new CodeAde();

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginTv');
            $password = filter_input(INPUT_POST, 'pwdTv');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTv');
            $codes = $_POST['selectTv'];

            // Validation des données d'entrée
            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm) {

                $codesAde = [];
                foreach ($codes as $code) {
                    if (is_numeric($code) && $code > 0) {
                        if (is_null($codeAde->getByCode($code)->getId())) {
                            return 'error'; // Code invalide
                        } else {
                            $codesAde[] = $codeAde->getByCode($code);
                        }
                    }
                }

                // Configuration du modèle de télévision
                $this->model->setLogin($login);
                $this->model->setEmail($login . '@' . $login . '.fr');
                $this->model->setPassword($password);
                $this->model->setRole('television');
                $this->model->setCodes($codesAde);

                // Insertion du modèle dans la base de données
                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorLogin();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }

        // Récupération des années, groupes et demi-groupes
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayFormTelevision($years, $groups, $halfGroups);
    }

    /**
     * Modifie une télévision.
     *
     * @param User $user L'utilisateur à modifier.
     *
     * @return string Retourne le HTML du formulaire de modification.
     */
    public function modify($user) {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $codeAde = new CodeAde();
        $action = filter_input(INPUT_POST, 'modifValidate');

        if (isset($action)) {
            $codes = $_POST['selectTv'];

            $codesAde = [];
            foreach ($codes as $code) {
                if (is_null($codeAde->getByCode($code)->getId())) {
                    return 'error'; // Code invalide
                } else {
                    $codesAde[] = $codeAde->getByCode($code);
                }
            }

            // Mise à jour des codes de l'utilisateur
            $user->setCodes($codesAde);

            if ($user->update()) {
                $this->view->displayModificationValidate($linkManageUser);
            }
        }

        // Récupération des années, groupes et demi-groupes
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->modifyForm($user, $years, $groups, $halfGroups);
    }

    /**
     * Affiche toutes les télévisions dans un tableau.
     *
     * @return string Retourne le HTML affichant toutes les télévisions.
     */
    public function displayAllTv() {
        $users = $this->model->getUsersByRole('television');
        return $this->view->displayAllTv($users);
    }

    /**
     * Affiche une liste d'emplois du temps.
     *
     * @return mixed|string Retourne les emplois du temps sous forme de chaîne de caractères.
     */
    public function displayMySchedule() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        $user = $this->model->getMyCodes([$user])[0];

        $string = "";
        if (sizeof($user->getCodes()) > 1) {
            if (get_theme_mod('ecran_connecte_schedule_scroll', 'vert') == 'vert') {
                $string .= '<div class="ticker1">
                        <div class="innerWrap tv-schedule">';
                foreach ($user->getCodes() as $code) {
                    $path = $this->getFilePath($code->getCode());
                    if (file_exists($path)) {
                        if ($this->displaySchedule($code->getCode())) {
                            $string .= '<div class="list">';
                            $string .= $this->displaySchedule($code->getCode());
                            $string .= '</div>';
                        }
                    }
                }
                $string .= '</div></div>';
            } else {
                $string .= $this->view->displayStartSlide();
                foreach ($user->getCodes() as $code) {
                    $path = $this->getFilePath($code->getCode());
                    if (file_exists($path)) {
                        if ($this->displaySchedule($code->getCode())) {
                            $string .= $this->view->displayMidSlide();
                            $string .= $this->displaySchedule($code->getCode());
                            $string .= $this->view->displayEndDiv();
                        }
                    }
                }
                $string .= $this->view->displayEndDiv();
            }
        } else {
            if (!empty($user->getCodes()[0])) {
                $string .= $this->displaySchedule($user->getCodes()[0]->getCode());
            } else {
                $string .= '<p>Vous n\'avez pas cours</p>';
            }
        }
        return $string;
    }
}
