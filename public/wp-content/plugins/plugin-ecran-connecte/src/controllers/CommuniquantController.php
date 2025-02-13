<?php

namespace controllers;

use models\Department;
use models\User;
use utils\InputValidator;
use views\CommuniquantView;

class CommuniquantController extends UserController
{
    private User $_model;

    private CommuniquantView $_view;

    public function __construct()
    {
        parent::__construct();
        $this->_model = new User();
        $this->_view = new CommuniquantView();
    }

    public function insert(): string
    {
        $action = filter_input(INPUT_POST, 'createComm');

        $currentUser = wp_get_current_user();
        $deptModel = new Department();

        $isAdmin = current_user_can('admin_perms');
        // si l'utilisateur actuel est admin, on envoie null car il n'a aucun
        // département, sinon on cherche le département
        $currDept
            = $isAdmin ? -1 : $deptModel->getUserDepartment(
            $currentUser->ID
        )->getIdDepartment();

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginComm');
            $password = filter_input(INPUT_POST, 'pwdComm');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmComm');
            $email = filter_input(INPUT_POST, 'emailComm');
            // les non-admins ne peuvent pas choisir le département, on empêche donc
            // ces utilisateurs de pouvoir le changer
            $deptId
                = $isAdmin ? filter_input(INPUT_POST, 'deptIdComm') : $currDept;

            // Validation des données d'entrée
            if (InputValidator::isValidLogin($login)
                && InputValidator::isValidPassword($password, $passwordConfirm)
                && InputValidator::isValidEmail($email)
            ) {
                $this->_model->setLogin($login);
                $this->_model->setPassword($password);
                $this->_model->setEmail($email);
                $this->_model->setRole('communicant');
                $this->_model->setIdDepartment($deptId);

                // Insertion dans la base de données
                if (!$this->checkDuplicateUser(
                        $this->_model
                    ) && $this->_model->insert()
                ) {
                    $this->_view->displayInsertValidate();
                } else {
                    $this->_view->displayErrorInsertion();
                }
            } else {
                $this->_view->displayErrorCreation();
            }
        }

        $allDepts = $deptModel->getAllDepts();

        return $this->_view->displayFormCommunicant($allDepts, $currDept, $isAdmin);
    }

    public function displayAllCommunicants(): string
    {
        $users = $this->_model->getUsersByRole('communicant');
        $deptModel = new Department();

        $userDeptList = array();
        foreach ($users as $user) {
            $userDeptList[] = $deptModel->getUserDepartment(
                $user->getId()
            )->getName();
        }

        return $this->_view->displayAllCommunicants($users, $userDeptList);
    }
}