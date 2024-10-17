<?php

namespace Controllers;

use Controllers\Controller;
use Models\Department;
use Views\DepartmentView;

class DepartmentController extends Controller {
	/**
	 * Modèle département
	 * @var Department
	 */
	private $model;

	/**
	 * Vue département
	 */
	private $view;

	/**
	 * Constructeur basique
	 */
	public function __construct() {
		$this->model = new Department();
		$this->view = new DepartmentView();
	}

	/**
	 * Utilise le POST du formulaire de création de département et
	 * filtre les inputs pour insérer les informations dans la base de données.
	 *
	 * @return string
	 */
	public function insert(): string {
		$action = filter_input(INPUT_POST, 'submit');

		if (isset($action)) {
			$name = filter_input(INPUT_POST, 'dept_name');

			if (is_string($name)) {
				$this->model->setName($name);

				if (!$this->checkDuplicate($this->model)) {
					$this->model->insert();
					$this->view->successCreation();
				} else {
					$this->view->errorDuplicate();
				}
			} else {
				$this->view->errorCreation();
			}

		}
		return $this->view->renderAddForm();
	}

	public function modify(): string {
		$id = $_GET['id'];

		$this->model->get($id);
		echo json_encode($this->model->jsonSerialize());

		if (!is_numeric($id) || !$this->model->get($id)) {
			return $this->view->errorNothing();
		}

		$submit = filter_input(INPUT_POST, 'submit');

		if (isset($submit)) {
			// TODO
		}

		return $this->view->renderModifForm($id);
	}

	/**
	 * Vérifie si le nom du département existe déjà dans la base de données
	 *
	 * @param Department $department
	 *
	 * @return bool
	 */
	public function checkDuplicate(Department $department): bool {
		$departments = $this->model->getDepartmentByName($department->getName());

		foreach ($departments as $d) {
			if ($department->getName() === $d->getName()) {
				return true;
			}

		}
		return false;
	}


}