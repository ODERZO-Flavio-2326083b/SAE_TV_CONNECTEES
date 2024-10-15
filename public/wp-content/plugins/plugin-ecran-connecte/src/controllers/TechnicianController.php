<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\TechnicianView;

/**
 * Class TechnicianController
 *
 * Gère les techniciens (Création, mise à jour, suppression, affichage, affichage de l'emploi du temps)
 *
 * @package Controllers
 */
class TechnicianController extends UserController implements Schedule
{

    /**
     * Modèle de TechnicianController.
     *
     * @var User
     */
    private $model;

    /**
     * Vue de TechnicianController.
     *
     * @var TechnicianView
     */
    private $view;

    /**
     * Constructeur de TechnicianController.
     * Initialise le modèle et la vue.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TechnicianView();
    }

    /**
     * Insère un technicien dans la base de données.
     *
     * @return string Retourne le HTML du formulaire d'insertion de technicien.
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createTech');

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginTech');
            $password = filter_input(INPUT_POST, 'pwdTech');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTech');
            $email = filter_input(INPUT_POST, 'emailTech');

            // Validation des données d'entrée
            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('technicien');

                // Insertion dans la base de données
                if ($this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }
        return $this->view->displayFormTechnician();
    }

    /**
     * Affiche tous les techniciens dans un tableau.
     *
     * @return string Retourne le HTML affichant tous les techniciens.
     */
    public function displayAllTechnician() {
        $users = $this->model->getUsersByRole('technicien');
        return $this->view->displayAllTechnicians($users);
    }

    /**
     * Affiche l'emploi du temps de tous les étudiants.
     *
     * @return mixed|string Retourne les horaires des étudiants sous forme de chaîne de caractères.
     */
    public function displayMySchedule() {
        $codeAde = new CodeAde();

        $years = $codeAde->getAllFromType('year');
        $string = "";
        foreach ($years as $year) {
            $string .= $this->displaySchedule($year->getCode());
        }
        return $string;
    }
}
