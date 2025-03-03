<?php

namespace controllers;

use models\CodeAde;
use models\Department;
use models\User;
use utils\InputValidator;
use R34ICS;
use views\TabletICSView;
use views\TabletView;

class TabletController extends UserController implements Schedule
{

    private User $_model;

    private TabletView $_view;

    public function __construct() {
        parent::__construct();
        $this->_model = new User();
        $this->_view = new TabletView();
    }

    public function insert(): string
    {
        $action = filter_input(INPUT_POST, 'createTa');
        $codeAde = new CodeAde();

        $currentUser = wp_get_current_user();
        $deptModel = new Department();

        $isAdmin = current_user_can('admin_perms');
        // si l'utilisateur actuel est admin, on envoie null car il n'a aucun
        // département, sinon on cherche le département
        $currDept
            = $isAdmin ? null : $deptModel->getUserDepartment(
            $currentUser->ID
        )->getIdDepartment();

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginTa');
            $password = filter_input(INPUT_POST, 'pwdTa');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTa');
            $codes = filter_input(
                INPUT_POST, 'selectTa', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY
            );
            // les non-admins ne peuvent pas choisir le département, on empêche donc
            // ces utilisateurs de pouvoir le changer
            $deptId
                = $isAdmin ? filter_input(INPUT_POST, 'deptIdTa') : $currDept;

            // Validation des données d'entrée
            if (InputValidator::isValidLogin($login)
                && InputValidator::isValidPassword($password, $passwordConfirm)
            ) {
                $codesAde = array();
                $code = $codes[0];
                if (is_numeric($code) && $code > 0) {
                    if (is_null($codeAde->getByCode($code)->getId())) {
                        return 'error'; // Code invalide
                    } else {
                        $codesAde[] = $codeAde->getByCode($code);
                    }
                }

                // Configuration du modèle de télévision
                $this->_model->setLogin($login);
                $this->_model->setEmail($login . '@' . $login . '.fr');
                $this->_model->setPassword($password);
                $this->_model->setRole('tablette');
                $this->_model->setCodes($codesAde);
                $this->_model->setIdDepartment($deptId);

                // Insertion du modèle dans la base de données
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

        // Récupération des salles
        $classes = $codeAde->getAllFromType('class');

        $allDepts = $deptModel->getAllDepts();

        return $this->_view->displayFormTablet(
            $classes, $allDepts, $isAdmin, $currDept
        );
    }

    public function displayAllTablet(): string
    {
        $users = $this->_model->getUsersByRole('tablette');

        $deptModel = new Department();

        $userDeptList = array();
        foreach ($users as $user) {
            $userDeptList[] = $deptModel->getUserDepartment(
                $user->getId()
            )->getName();
        }

        return $this->_view->displayAllTablets($users, $userDeptList);
    }

    public function displayMySchedule(): string
    {
        $user = new User();
        $tabUserObj = $user->get(wp_get_current_user()->ID);

        $code = $tabUserObj->getCodes();

        return $this->displaySchedule($code[0]->getCode());
    }

    public function displaySchedule($code, $allDay = false) : string
    {
        $R34ICS = new R34ICS();

        $url = $this->getFilePath($code);
        $args = array(
            'count' => 10,
            'description' => null,
            'eventdesc' => null,
            'format' => null,
            'hidetimes' => null,
            'showendtimes' => null,
            'title' => null,
            'view' => 'list',
            'first_date' => date_i18n('Ymd', strtotime('monday this week')),
        );

        list($ics_data, $allDay) = $R34ICS->get_event_data($url, $code, $allDay, $args);

        $model = new CodeAde();
        $title = $model->getByCode($code)->getTitle();

        $icsView = new TabletICSView();
        return $icsView->displaySchedule($ics_data, $title, $allDay);
    }


}