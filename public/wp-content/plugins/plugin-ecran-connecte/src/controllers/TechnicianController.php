<?php

namespace Controllers;

use Models\CodeAde;
use Models\Department;
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
     * Constructeur de la classe TechnicianController.
     *
     * Ce constructeur initialise le modèle et la vue pour le contrôleur des
     * techniciens. Il appelle le constructeur parent pour s'assurer que toutes
     * les propriétés héritées sont correctement initialisées.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TechnicianView();
    }

    /**
     * Insère un nouvel utilisateur de type technicien dans la base de données.
     *
     * Cette méthode traite les données soumises via un formulaire pour créer un
     * nouvel utilisateur technicien. Elle effectue les validations nécessaires sur
     * les entrées, telles que la longueur des chaînes et la confirmation du mot de
     * passe, avant de les insérer dans la base de données. Si l'insertion réussit,
     * un message de validation est affiché, sinon un message d'erreur est présenté.
     *
     * @return string Retourne l'affichage du formulaire de création de technicien
     *                ou un message de validation ou d'erreur selon le résultat
     *                de l'insertion.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createTech');

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginTech');
            $password = filter_input(INPUT_POST, 'pwdTech');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTech');
            $email = filter_input(INPUT_POST, 'emailTech');
			$deptId = filter_input(INPUT_POST, 'deptIdTech');

            // Validation des données d'entrée
            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('technicien');
				$this->model->setIdDepartment($deptId);

                // Insertion dans la base de données
	            if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
		            $this->view->displayInsertValidate();
	            } else {
		            $this->view->displayErrorInsertion();
	            }
            } else {
                $this->view->displayErrorCreation();
            }
        }
        $deptModel = new Department();
        $allDepts = $deptModel->getAllDepts();

		$currDept = $deptModel->get(get_current_user_id());

        return $this->view->displayFormTechnician($allDepts, $currDept);
    }

    /**
     * Affiche la liste de tous les techniciens.
     *
     * Cette méthode récupère tous les utilisateurs ayant le rôle de technicien
     * depuis le modèle et les passe à la vue pour affichage. Elle permet à
     * l'administrateur ou à un responsable de visualiser la liste complète
     * des techniciens inscrits dans le système.
     *
     * @return string Retourne le rendu de l'affichage de tous les techniciens.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayAllTechnician() {
        $users = $this->model->getUsersByRole('technicien');

		$deptModel = new Department();

	    $userDeptList = array();
	    foreach ($users as $user) {
		    $userDeptList[] = $deptModel->getUserDepartment($user->getId())->getName();
	    }

        return $this->view->displayAllTechnicians($users, $userDeptList);
    }

    /**
     * Affiche l'emploi du temps de l'utilisateur pour chaque année.
     *
     * Cette méthode récupère tous les codes d'années à partir de l'objet CodeAde,
     * puis génère et concatène les affichages de l'emploi du temps pour chaque année
     * en utilisant ces codes. Elle retourne finalement une chaîne contenant
     * tous les emplois du temps pour l'utilisateur.
     *
     * @return string Retourne une chaîne contenant tous les emplois du temps pour
     *                chaque année associée à l'utilisateur.
     *
     *
     * @version 1.0
     * @date 2024-10-15
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
