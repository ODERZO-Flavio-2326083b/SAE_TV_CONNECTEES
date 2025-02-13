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

    public function createTVs() : string {
        $television = new TelevisionController();

        return
            $this->_view->displayStartMultiSelect() .
            $this->_view->displayTitleSelect('television', 'Télévisions', true) .
            $this->_view->displayEndOfTitle() .
            $this->_view->displayContentSelect('television', $television->insert(), true) .
            '</div>';
    }

    public function displayTVs() : string {
        $television = new TelevisionController();

        return
            $this->_view->displayStartMultiSelect() .
            $this->_view->displayTitleSelect('television', 'Télévisions', true) .
            $this->_view->displayEndOfTitle() .
            $this->_view->displayContentSelect(
                'television', $television->displayAllTv(), true
            ) .
            '</div>';
    }

    /**
     * Supprime les utilisateurs sélectionnés à partir des cases à cocher dans le
     * formulaire.
     *
     * Cette méthode vérifie si une action de suppression a été demandée. Si c'est le
     * cas, elle parcourt les rôles spécifiés (étudiant, enseignant, directeur,
     * technicien, secrétaire, télévision) et vérifie si des utilisateurs
     * correspondant à ces rôles ont été sélectionnés. Pour chaque utilisateur
     * sélectionné, la méthode appelle 'deleteUser' pour effectuer la suppression de
     * l'utilisateur correspondant.
     *
     * @return void Cette méthode n'a pas de valeur de retour, elle effectue
     * directement la suppression des utilisateurs.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function deleteUsers(): void
    {
        $actionDelete = filter_input(INPUT_POST, 'delete');
        $roles = ['Tech', 'Secre', 'Tele', 'Comm', 'Subadmin'];

        if (isset($actionDelete)) {
            foreach ($roles as $role) {
                if (isset($_REQUEST['checkboxStatus' . $role])) {
                    $checked_values = $_REQUEST['checkboxStatus' . $role];
                    foreach ($checked_values as $id) {
                        $this->_deleteUser($id);
                    }
                }
            }
        }
    }

    /**
     * Supprime un utilisateur spécifié par son identifiant.
     *
     * Cette méthode récupère l'utilisateur à partir de la base de données à l'aide
     * de l'identifiant fourni, puis appelle la méthode 'delete' sur l'instance de
     * l'utilisateur pour le supprimer de la base de données.
     *
     * @param int $id L'identifiant de l'utilisateur à supprimer.
     *
     * @return void Cette méthode n'a pas de valeur de retour, elle effectue
     *              directement la suppression de l'utilisateur.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    private function _deleteUser(int $id): void
    {
        $user = $this->_model->get($id);
        $user->delete();
    }
}