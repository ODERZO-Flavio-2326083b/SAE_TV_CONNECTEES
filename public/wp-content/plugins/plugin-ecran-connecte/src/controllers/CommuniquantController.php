<?php
/**
 * Fichier CommuniquantController.php
 *
 * Ce fichier contient la classe 'CommuniquantController', qui gère les opérations
 * liées aux utilisateurs de type "communicant" dans l'application.
 * Il permet l'insertion de nouveaux utilisateurs communicants et l'affichage
 * de la liste de ces utilisateurs.
 *
 * PHP version 8.3
 *
 * @category Controllers
 * @package  Controllers
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://www.example.com/docs/CommuniquantController
 * @since    2024-03-03
 */

namespace controllers;

use models\Department;
use models\User;
use utils\InputValidator;
use views\CommuniquantView;

/**
 * Classe CommuniquantController
 *
 * Cette classe est responsable de la gestion des utilisateurs communicants,
 * incluant leur création et l'affichage de la liste des communicants enregistrés.
 * Elle hérite de la classe 'UserController' pour bénéficier des fonctionnalités
 * de gestion des utilisateurs.
 *
 * @category Controllers
 * @package  Controllers
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://www.example.com/docs/CommuniquantController
 * @since    2024-03-03
 */
class CommuniquantController extends UserController
{
    /**
     * Instance du modèle utilisateur pour gérer les données des communicants.
     *
     * @var User $_model Modèle utilisateur.
     */
    private User $_model;

    /**
     * Instance de la vue pour afficher les interfaces
     * utilisateur liées aux communicants.
     *
     * @var CommuniquantView $_view Vue communicant.
     */
    private CommuniquantView $_view;

    /**
     * Constructeur de la classe CommuniquantController.
     *
     * Initialise le modèle utilisateur et la vue associée aux communicants.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_model = new User();
        $this->_view = new CommuniquantView();
    }

    /**
     * Insère un nouvel utilisateur de type communicant dans la base de données.
     *
     * Cette méthode récupère les données du formulaire, valide les entrées,
     * et insère un nouvel utilisateur communicant si toutes les conditions
     * sont remplies.
     * Elle gère aussi l'affichage des messages de succès ou d'erreur via la vue.
     *
     * @return string Le code HTML du formulaire d'insertion des communicants.
     */
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
            $passwordConfirm = filter_input(
                INPUT_POST,
                'pwdConfirmComm'
            );
            $email = filter_input(INPUT_POST, 'emailComm');
            // les non-admins ne peuvent pas choisir le département, on empêche donc
            // ces utilisateurs de pouvoir le changer
            $deptId
                = $isAdmin ? filter_input(
                    INPUT_POST,
                    'deptIdComm'
                ) : $currDept;

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

    /**
     * Affiche la liste de tous les utilisateurs communicants.
     *
     * Cette méthode récupère tous les utilisateurs ayant le rôle 'communicant'
     * et affiche la liste via la vue associée.
     *
     * @return string Le code HTML de la liste des communicants.
     */
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
