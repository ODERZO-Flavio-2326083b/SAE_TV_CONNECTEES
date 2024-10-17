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
			$lat = filter_input(INPUT_POST, 'dept_lat');
			$long = filter_input(INPUT_POST, 'dept_long');


			if (is_string($name)) {
				$this->model->setName($name);
				$this->model->setLatitude($lat);
				$this->model->setLongitude($long);

				if (!$this->checkDuplicate($this->model)) {

					$this->view->successCreation();
					$this->model->insert();
					$this->view->refreshPage();
				} else {
					$this->view->errorDuplicate();
				}
			} else {
				$this->view->errorCreation();
			}

		}
		return $this->view->renderAddForm();
	}

	public function update() {

	}

	public function checkDuplicate(Department $department) {
		$departments = $this->model->checkExists($department->getName());

		$count = 0;

		foreach ($departments as $d) {
			if ($department->getName() === $d->getName()) {
				unset($departments[$count]);
			}

			$count++;

		}

		return sizeof($departments) > 0;
	}
}