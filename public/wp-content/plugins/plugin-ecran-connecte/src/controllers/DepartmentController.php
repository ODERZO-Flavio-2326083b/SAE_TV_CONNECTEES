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
    private $_view;

    /**
     * Constructeur basique
     */
    public function __construct() {
        $this->model = new Department();
        $this->_view = new DepartmentView();
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
                    $this->_view->successCreation();
                } else {
                    $this->_view->errorDuplicate();
                }
            } else {
                $this->_view->errorCreation();
            }

        }
        return $this->_view->renderAddForm();
    }

    /**
     * Affiche le menu de modification en fonction de l'id fourni en GET
     * et filtre les inputs pour modifier le nom
     * d'un département dans la base de données
     *
     * @return string
     */
    public function modify(): string {
        if (!isset($_GET['id'])) {
            return $this->_view->errorNothing();
        }

        $id = $_GET['id'];

        if (!is_numeric($id) || !$this->model->get($id)) {
            return $this->_view->errorNothing();
        }

        $submit = filter_input(INPUT_POST, 'submit');

        if (isset($submit)) {
            $nvNom = filter_input(INPUT_POST, 'dept_name');

            if (is_string($nvNom)) {
                $this->model->setIdDepartment($id);
                $this->model->setName($nvNom);

                if (!$this->checkDuplicate($this->model)) {
                    $this->model->update();
                    $this->_view->successUpdate();
                } else {
                    $this->_view->errorDuplicate();
                }
            } else {
                $this->_view->errorUpdate();
            }
        }

        $name = $this->model->get($id)->getName();
        return $this->_view->renderModifForm($name);
    }

    /**
     * Donne la liste de tous les départements à la vue
     * et affiche le tableau correspondant
     *
     * @return string
     */
    public function displayDeptTable(): string {
        $allDepts = $this->model->getAllDepts();

        return $this->_view->renderAllDeptsTable($allDepts);
    }

    /**
     * Si une requête POST est faite sur la page d'affichage des départements
     * on la traite et supprime les départements concernés
     *
     * @return void
     */
    public function deleteDepts(): void {
        $action = filter_input(INPUT_POST, 'delete');
        if (isset($action)) {
            if (isset($_REQUEST['checkboxStatusDept'])) {
                $checked_values = $_REQUEST['checkboxStatusDept'];
                foreach ($checked_values as $id) {
                    $this->model = $this->model->get($id);
                    $this->model->delete();
                    $this->_view->refreshPage();
                }
            }
        }
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