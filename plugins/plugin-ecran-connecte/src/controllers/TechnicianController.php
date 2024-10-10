<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\TechnicianView;

/**
 * Class TechnicianController
 *
 * Manage Technician (Create, update, delete, display, display schedule)
 *
 * @package Controllers
 */
class TechnicianController extends UserController implements Schedule
{

    /**
     * @var User
     */
    private $model;

    /**
     * @var TechnicianView
     */
    private $view;

    /**
     * Constructor of SecretaryController.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TechnicianView();
    }

    /**
     * Insert a technician in the database
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createTech');

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginTech');
            $password = filter_input(INPUT_POST, 'pwdTech');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTech');
            $email = filter_input(INPUT_POST, 'emailTech');

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm
                && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('technicien');

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
     * Display all technicians in a table
     *
     * @return string
     */
    public function displayAllTechnician() {
        $users = $this->model->getUsersByRole('technicien');
        return $this->view->displayAllTechnicians($users);
    }

    /**
     * Display the schedule of all students
     *
     * @return mixed|string
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
