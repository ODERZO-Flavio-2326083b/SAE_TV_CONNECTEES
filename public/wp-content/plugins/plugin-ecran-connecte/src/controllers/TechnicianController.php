<?php
/**
 * Fichier TechnicianController.php
 *
 * Ce fichier contient la classe 'TechnicianController', qui gère les opérations
 * relatives aux techniciens, telles que la création, la mise à jour, la suppression,
 * l'affichage des techniciens, ainsi que
 * l'affichage de l'emploi du temps des techniciens.
 *
 * PHP version 8.3
 *
 * @category API
 * @package  Controllers
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/TechnicianController
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace controllers;

use models\CodeAde;
use models\Department;
use models\User;
use utils\InputValidator;
use views\TechnicianView;

/**
 * Class TechnicianController
 *
 * Gère les techniciens (Création, mise à jour, suppression, affichage, affichage de
 * l'emploi du temps)
 *
 * @category API
 * @package  Controllers
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/TechnicianController Documentation de
 * la classe
 * @since    2025-01-07
 */
class TechnicianController extends UserController implements Schedule
{

    /**
     * Modèle de TechnicianController.
     *
     * @var User
     */
    private User $_model;

    /**
     * Vue de TechnicianController.
     *
     * @var TechnicianView
     */
    private TechnicianView $_view;

    /**
     * Constructeur de la classe TechnicianController.
     *
     * Ce constructeur initialise le modèle et la vue pour le contrôleur des
     * techniciens. Il appelle le constructeur parent pour s'assurer que toutes
     * les propriétés héritées sont correctement initialisées.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function __construct()
    {
        parent::__construct();
        $this->_model = new User();
        $this->_view = new TechnicianView();
    }

    /**
     * Insère un nouvel utilisateur de type technicien dans la base de données.
     *
     * Cette méthode traite les données soumises via un formulaire pour créer un
     * nouvel utilisateur technicien. Elle effectue les validations nécessaires sur
     * les entrées, telles que la longueur des chaînes et la confirmation du mot de
     * passe, avant de les insérer dans la base de données. Si l'insertion réussit,
     * un message de validation est affiché, sinon un message d'erreur est présenté.
     *
     * @return string Retourne l'affichage du formulaire de création de technicien
     *                ou un message de validation ou d'erreur selon le résultat
     *                de l'insertion.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function insert(): string
    {
        $action = filter_input(INPUT_POST, 'createTech');

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
            $login = filter_input(INPUT_POST, 'loginTech');
            $password = filter_input(INPUT_POST, 'pwdTech');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTech');
            $email = filter_input(INPUT_POST, 'emailTech');
            // les non-admins ne peuvent pas choisir le département, on empêche donc
            // ces utilisateurs de pouvoir le changer
            $deptId
                = $isAdmin ? filter_input(INPUT_POST, 'deptIdTech') : $currDept;

            // Validation des données d'entrée
            if (InputValidator::isValidLogin($login) 
                && InputValidator::isValidPassword($password, $passwordConfirm) 
                && InputValidator::isValidEmail($email)
            ) {
                $this->_model->setLogin($login);
                $this->_model->setPassword($password);
                $this->_model->setEmail($email);
                $this->_model->setRole('technicien');
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

        return $this->_view->displayFormTechnician($allDepts, $currDept, $isAdmin);
    }

    /**
     * Affiche la liste de tous les techniciens.
     *
     * Cette méthode récupère tous les utilisateurs ayant le rôle de technicien
     * depuis le modèle et les passe à la vue pour affichage. Elle permet à
     * l'administrateur ou à un responsable de visualiser la liste complète
     * des techniciens inscrits dans le système.
     *
     * @return string Retourne le rendu de l'affichage de tous les techniciens.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayAllTechnician(): string
    {
        $users = $this->_model->getUsersByRole('technicien');

        $deptModel = new Department();

        $userDeptList = array();
        foreach ($users as $user) {
            $userDeptList[] = $deptModel->getUserDepartment(
                $user->getId()
            )->getName();
        }

        return $this->_view->displayAllTechnicians($users, $userDeptList);
    }

    /**
     * Affiche l'emploi du temps de l'utilisateur pour chaque année.
     *
     * Cette méthode récupère tous les codes d'années à partir de l'objet CodeAde,
     * puis génère et concatène les affichages de l'emploi du temps pour chaque année
     * en utilisant ces codes. Elle retourne finalement une chaîne contenant
     * tous les emplois du temps pour l'utilisateur.
     *
     * @return string Retourne une chaîne contenant tous les emplois du temps pour
     *                chaque année associée à l'utilisateur.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayMySchedule(): string
    {
        $codeAde = new CodeAde();
        $user = new User();
        $techUserObj = $user->get(wp_get_current_user()->ID);

        $years = $codeAde->getAllFromType('year');
        $string = "";
        foreach ($years as $year) {
            if ($year->getDeptId() == $techUserObj->getIdDepartment()) {
                $string .= $this->displaySchedule($year->getCode());
            }
        }
        return $string;
    }
}
