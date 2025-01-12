<?php
// TODO : Ajouter la doc du fichier
namespace controllers;

use models\Department;
use models\User;
use views\SubadminView;

/**
 * TODO : Ajouter les tags @author, @category, @license et @link
 * Class TechnicianController
 *
 * Gère les techniciens (Création, mise à jour, suppression, affichage)
 *
 * @package controllers
 */
class SubadminController extends UserController
{

    /**
     * Model User de Subadmin Controller.
     *
     * @var User
     */
    private User $_model;

    /**
     * Vue de SubadminController.
     *
     * @var SubadminView
     */
    private SubadminView $_view;

    // TODO : Ajouter une description pour la fonction
    public function __construct()
    {
        parent::__construct();
        $this->_model = new User();
        $this->_view = new SubadminView();
    }

    // TODO : Ajouter une doc pour la fonction
    public function insert(): string
    {
        $action = filter_input(INPUT_POST, 'createSubadmin');

        $currentUser = wp_get_current_user();
        $deptModel = new Department();

        $isAdmin = current_user_can('admin_perms');
        // si l'utilisateur actuel est admin, on envoie null car il n'a aucun
        // département, sinon on cherche le département
        $currDept = $isAdmin ? -1 :
        $deptModel->getUserDepartment($currentUser->ID)->getIdDepartment();

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginSubadmin');
            $password = filter_input(INPUT_POST, 'pwdSubadmin');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmSubadmin');
            $email = filter_input(INPUT_POST, 'emailSubadmin');
            // les non-admins ne peuvent pas choisir le département, on empêche donc
            // ces utilisateurs de pouvoir le changer
            $deptId = $isAdmin ? filter_input(INPUT_POST, 'deptIdSubadmin'):
            $currDept;

            // Validation des données d'entrée
            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 
                && is_string($password)
                && strlen($password) >= 8 && strlen($password) <= 25
                && $password === $passwordConfirm && is_email($email)
            ) {
                $this->_model->setLogin($login);
                $this->_model->setPassword($password);
                $this->_model->setEmail($email);
                $this->_model->setRole('subadmin');
                $this->_model->setIdDepartment($deptId);

                // Insertion dans la base de données
                if (!$this->checkDuplicateUser($this->_model)
                    && $this->_model->insert()
                ) {
                    $this->_view->displayInsertValidate();
                } else {
                    $this->_view->displayErrorInsertion();
                }
            } else {
                $this->_view->displayErrorCreation();
            }
        }

        $allDepts = $deptModel->getAllDeptsWithoutSubadmin();

        return $this->_view->displayFormSubadmin($allDepts, $currDept, $isAdmin);
    }

    // TODO : Ajouter une description
    public function displayAllSubadmin(): string
    {
        $users = $this->_model->getUsersByRole('subadmin');

        $deptModel = new Department();

        $userDeptList = array();
        foreach ($users as $user) {
            $userDeptList[] = $deptModel->getUserDepartment(
                $user->getId()
            )->getName();
        }

        return $this->_view->displayAllTechnicians($users, $userDeptList);
    }

}
