<?php
/**
 * Fichier TechnicianController.php
 *
 * Ce fichier contient la classe `TechnicianController`, qui gère les opérations
 * relatives aux techniciens, telles que la création, la mise à jour, la suppression
 * et l'affichage des techniciens.
 *
 * PHP version 7.4 or later
 *
 * @category API
 * @package  Controllers
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/TechnicianController
 * Documentation de la classe
 * @since    2025-01-07
 */
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
 * @category API
 * @package  Controllers
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/TechnicianController Documentation de
 * la classe
 * @since    2025-01-07
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

    /**
     * Initialise un nouvel objet et configure le modèle et la vue associés.
     *
     * Ce constructeur appelle le constructeur parent et initialise les propriétés
     * `_model` et `_view`. Le modèle est une instance de la classe `User`, et la vue
     * est une instance de la classe `SubadminView`.
     * Cela permet de lier le modèle et la vue
     * pour une gestion et un affichage des données
     * dans les opérations suivantes de l'objet.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function __construct()
    {
        parent::__construct();
        $this->_model = new User();
        $this->_view = new SubadminView();
    }

    /**
     * Insère un nouvel administrateur ou sous-administrateur dans
     * la base de données.
     *
     * Cette fonction récupère les données envoyées via un formulaire
     * de création de sous-administrateur
     * (login, mot de passe, email, département) et
     * effectue une série de validations avant de procéder à
     * l'insertion dans la base de données. Si les
     * données sont valides et qu'il n'y a pas d'utilisateur en double,
     * un nouvel utilisateur avec le rôle de sous-administrateur
     * est créé. En cas d'erreur, des messages appropriés
     * sont affichés pour informer l'utilisateur.
     *
     * @return string Le code HTML généré pour afficher le
     * formulaire de création ou un message de validation/erreur.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
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
            if (InputValidator::isValidLogin($login) 
                && InputValidator::isValidPassword($password, $passwordConfirm) 
                && InputValidator::isValidEmail($email)
            ) {
                $this->_model->setLogin($login);
                $this->_model->setPassword($password);
                $this->_model->setEmail($email);
                $this->_model->setRole('subadmin');
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

        $allDepts = $deptModel->getAllDeptsWithoutSubadmin();

        return $this->_view->displayFormSubadmin($allDepts, $currDept, $isAdmin);
    }

    /**
     * Affiche la liste de tous les sous-administrateurs avec leur département.
     *
     * Cette fonction récupère tous les utilisateurs ayant le rôle de
     * sous-administrateur, puis
     * pour chaque utilisateur, elle obtient le nom de son département.
     * Elle renvoie ensuite un
     * code HTML qui affiche la liste des sous-administrateurs et les
     * départements auxquels ils
     * appartiennent via une vue spécifique.
     *
     * @return string Le code HTML généré pour afficher la liste des
     * sous-administrateurs et leurs départements.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function displayAllSubadmin(): string
    {
        $users = $this->_model->getUsersByRole('subadmin');

        $deptModel = new Department();

        $userDeptList = array();
        foreach ($users as $user) {
            $userDeptList[]
                = $deptModel->getUserDepartment($user->getId())->getName();
        }

        return $this->_view->displayAllTechnicians($users, $userDeptList);
    }

}
