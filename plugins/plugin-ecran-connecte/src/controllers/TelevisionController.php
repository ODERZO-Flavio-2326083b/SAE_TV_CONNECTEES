<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\TelevisionView;

/**
 * Class TelevisionController
 *
 * Gère les télévisions (création, mise à jour, suppression, affichage, affichage des emplois du temps).
 *
 * @package Controllers
 */
class TelevisionController extends UserController implements Schedule
{
    /**
     * Modèle de TelevisionController.
     * @var User
     */
    private $model;

    /**
     * Vue de TelevisionController.
     * @var TelevisionView
     */
    private $view;

    /**
     * Constructeur de la classe TelevisionController.
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
     * @return string Renvoie la vue du formulaire de télévision ou un message de validation.
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createTv');
        $codeAde = new CodeAde();

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginTv');
            $password = filter_input(INPUT_POST, 'pwdTv');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTv');
            $codes = $_POST['selectTv'];

            // Validation des données
            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm) {

                $codesAde = [];
                foreach ($codes as $code) {
                    if (is_numeric($code) && $code > 0) {
                        // Vérification si le code existe
                        if (is_null($codeAde->getByCode($code)->getId())) {
                            return 'error'; // Retourne une erreur si le code est invalide
                        } else {
                            $codesAde[] = $codeAde->getByCode($code);
                        }
                    }
                }

                // Configuration des données du modèle
                $this->model->setLogin($login);
                $this->model->setEmail($login . '@' . $login . '.fr'); // Création de l'email basé sur le login
                $this->model->setPassword($password);
                $this->model->setRole('television');
                $this->model->setCodes($codesAde);

                // Insertion du modèle
                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    return $this->view->displayInsertValidate(); // Affiche un message de validation
                } else {
                    return $this->view->displayErrorLogin(); // Affiche un message d'erreur de connexion
                }
            } else {
                return $this->view->displayErrorCreation(); // Affiche un message d'erreur de création
            }
        }

        // Récupération des années, groupes et demi-groupes pour le formulaire
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayFormTelevision($years, $groups, $halfGroups); // Affiche le formulaire
    }

    /**
     * Modifie une télévision.
     *
     * @param User $user L'utilisateur représentant la télévision à modifier.
     * @return string Renvoie le formulaire de modification ou un message de validation.
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
                // Vérification de l'existence du code
                if (is_null($codeAde->getByCode($code)->getId())) {
                    return 'error'; // Retourne une erreur si le code est invalide
                } else {
                    $codesAde[] = $codeAde->getByCode($code);
                }
            }

            $user->setCodes($codesAde); // Mise à jour des codes de l'utilisateur

            if ($user->update()) {
                return $this->view->displayModificationValidate($linkManageUser); // Affiche un message de validation
            }
        }

        // Récupération des données pour le formulaire de modification
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->modifyForm($user, $years, $groups, $halfGroups); // Affiche le formulaire de modification
    }

    /**
     * Affiche toutes les télévisions dans un tableau.
     *
     * @return string Renvoie la vue contenant tous les utilisateurs de type télévision.
     */
    public function displayAllTv() {
        $users = $this->model->getUsersByRole('television');
        return $this->view->displayAllTv($users); // Affiche la vue des télévisions
    }

    /**
     * Affiche une liste d'emplois du temps.
     *
     * @return mixed|string Renvoie une chaîne contenant les emplois du temps ou un message d'erreur.
     */
    public function displayMySchedule() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        $user = $this->model->getMycodes([$user])[0];

        $string = "";
        if (sizeof($user->getCodes()) > 1) {
            // Vérification du style d'affichage
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
                $string .= '<p>Vous n\'avez pas cours</p>'; // Message si aucun cours n'est disponible
            }
        }
        return $string; // Retourne l'ensemble des emplois du temps
    }
}
