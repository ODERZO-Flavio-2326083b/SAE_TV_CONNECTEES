<?php

namespace controllers;

use models\Department;
use models\User;
use utils\InputValidator;
use views\SubadminView;

/**
 * Class TechnicianController
 *
 * Gère les techniciens (Création, mise à jour, suppression, affichage)
 *
 * @package controllers
 */
class SubadminController extends UserController {

	/**
	 * Model User de Subadmin Controller.
	 *
	 * @var User
	 */
	private User $model;

	/**
	 * Vue de SubadminController.
	 *
	 * @var SubadminView
	 */
	private SubadminView $view;

	public function __construct() {
		parent::__construct();
		$this->model = new User();
		$this->view = new SubadminView();
	}

	public function insert(): string {
		$action = filter_input(INPUT_POST, 'createSubadmin');

		$currentUser = wp_get_current_user();
		$deptModel = new Department();

		$isAdmin = current_user_can('admin_perms');
		// si l'utilisateur actuel est admin, on envoie null car il n'a aucun département,
		// sinon on cherche le département
		$currDept = $isAdmin ? -1 :
			$deptModel->getUserDepartment($currentUser->ID)->getIdDepartment();

		if (isset($action)) {
			$login = filter_input(INPUT_POST, 'loginSubadmin');
			$password = filter_input(INPUT_POST, 'pwdSubadmin');
			$passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmSubadmin');
			$email = filter_input(INPUT_POST, 'emailSubadmin');
			// les non-admins ne peuvent pas choisir le département, on empêche donc ces utilisateurs
			// de pouvoir le changer
			$deptId =
				$isAdmin ? filter_input(INPUT_POST, 'deptIdSubadmin'):
				$currDept;

			// Validation des données d'entrée
			if (InputValidator::isValidLogin($login) &&
                InputValidator::isValidPassword($password, $passwordConfirm) &&
                InputValidator::isValidEmail($email)) {
				$this->model->setLogin($login);
				$this->model->setPassword($password);
				$this->model->setEmail($email);
				$this->model->setRole('subadmin');
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

		$allDepts = $deptModel->getAllDeptsWithoutSubadmin();

		return $this->view->displayFormSubadmin($allDepts, $currDept, $isAdmin);
	}

	public function displayAllSubadmin(): string {
		$users = $this->model->getUsersByRole('subadmin');

		$deptModel = new Department();

		$userDeptList = array();
		foreach ($users as $user) {
			$userDeptList[] =
				$deptModel->getUserDepartment($user->getId())->getName();
		}

		return $this->view->displayAllTechnicians($users, $userDeptList);
	}

}