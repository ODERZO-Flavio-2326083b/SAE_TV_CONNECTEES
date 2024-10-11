<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\TechnicianView;

/**
 * Class TechnicianController
 *
 * Contrôleur pour gérer les techniciens (création, mise à jour, suppression, affichage, affichage des emplois du temps).
 *
 * @package Controllers
 */
class TechnicianController extends UserController implements Schedule
{
    /**
     * Modèle de TechnicianController.
     * @var User
     */
    private $model;

    /**
     * Vue de TechnicianController.
     * @var TechnicianView
     */
    private $view;

    /**
     * Constructeur de la classe TechnicianController.
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
     * @return string Renvoie le formulaire de création de technicien ou un message de validation.
     *
     * @throws Exception Lève une exception si les données fournies sont invalides.
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createTech');

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginTech');
            $password = filter_input(INPUT_POST, 'pwdTech');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTech');
            $email = filter_input(INPUT_POST, 'emailTech');

            // Vérification des conditions de création
            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm &&
                is_email($email)) {

                // Configuration des données du modèle
                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('technicien');

                // Insertion du technicien dans la base de données
                if ($this->model->insert()) {
                    return $this->view->displayInsertValidate(); // Affiche un message de validation
                } else {
                    return $this->view->displayErrorInsertion(); // Affiche un message d'erreur d'insertion
                }
            } else {
                return $this->view->displayErrorCreation(); // Affiche un message d'erreur de création
            }
        }

        return $this->view->displayFormTechnician(); // Affiche le formulaire de création de technicien
    }

    /**
     * Affiche tous les techniciens dans un tableau.
     *
     * @return string Renvoie la vue contenant tous les techniciens.
     */
    public function displayAllTechnician() {
        // Récupération de tous les utilisateurs avec le rôle de technicien
        $users = $this->model->getUsersByRole('technicien');
        return $this->view->displayAllTechnicians($users); // Affichage de la vue des techniciens
    }

    /**
     * Affiche l'emploi du temps de tous les étudiants.
     *
     * @return mixed|string Renvoie une chaîne contenant les emplois du temps ou un message d'erreur.
     */
    public function displayMySchedule() {
        $codeAde = new CodeAde();

        // Récupération de toutes les années
        $years = $codeAde->getAllFromType('year');
        $string = "";

        // Affichage de l'emploi du temps pour chaque année
        foreach ($years as $year) {
            $string .= $this->displaySchedule($year->getCode());
        }
        return $string; // Renvoie l'ensemble des emplois du temps
    }
}
