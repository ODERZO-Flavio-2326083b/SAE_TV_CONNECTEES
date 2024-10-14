<?php

namespace Controllers;

use Models\User;
use Views\StudyDirectorView;

/**
 * Class StudyDirectorController
 *
 * Gère les directeurs d'études (Création, mise à jour, suppression, affichage)
 *
 * @package Controllers
 */
class StudyDirectorController extends UserController implements Schedule
{

    /**
     * @var User Modèle représentant un utilisateur.
     */
    private $model;

    /**
     * @var StudyDirectorView Vue associée au contrôleur des directeurs d'études.
     */
    private $view;

    /**
     * Constructeur de StudyDirectorController.
     * Initialise le modèle et la vue.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new StudyDirectorView();
    }

    /**
     * Affiche l'emploi du temps du directeur d'études.
     *
     * @return bool|mixed|string Retourne l'emploi du temps ou un message d'erreur si aucun code n'est enregistré.
     */
    public function displayMySchedule() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        if (sizeof($user->getCodes()) > 0) {
            return $this->displaySchedule($user->getCodes()[0]->getCode());
        } else {
            return $this->view->errorMessageNoCodeRegister();
        }
    }

    /**
     * Insère un directeur d'études dans la base de données.
     *
     * @return string Retourne le HTML du formulaire de création du directeur d'études.
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createDirec');

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginDirec');
            $password = filter_input(INPUT_POST, 'pwdDirec');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmDirec');
            $email = filter_input(INPUT_POST, 'emailDirec');
            $code = filter_input(INPUT_POST, 'codeDirec');

            // Vérifie les conditions de validation des entrées.
            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('directeuretude');
                $this->model->setCodes($code);

                // Insère l'utilisateur et gère les fichiers associés.
                if ($this->model->insert()) {
                    $path = $this->getFilePath($code);
                    if (!file_exists($path)) {
                        $this->addFile($code);
                    }
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }
        return $this->view->displayCreateDirector();
    }

    /**
     * Modifie le directeur d'études.
     *
     * @param User $user L'utilisateur à modifier.
     *
     * @return string Retourne le HTML du formulaire de modification du directeur d'études.
     */
    public function modify($user) {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $action = filter_input(INPUT_POST, 'modifValidate');

        if ($action === 'Valider') {
            $code = filter_input(INPUT_POST, 'modifCode');
            if (is_numeric($code)) {
                $user->setRole('directeuretude');
                $user->getCodes()[0]->setCode($code);

                if ($user->update()) {
                    $this->view->displayModificationValidate($linkManageUser);
                }
            }
        }
        return $this->view->displayModifyStudyDirector($user);
    }

    /**
     * Affiche tous les directeurs d'études.
     *
     * @return string Retourne le HTML affichant tous les directeurs d'études.
     */
    public function displayAllStudyDirector() {
        $users = $this->model->getUsersByRole('directeuretude');
        $users = $this->model->getMyCodes($users);
        return $this->view->displayAllStudyDirector($users);
    }
}
