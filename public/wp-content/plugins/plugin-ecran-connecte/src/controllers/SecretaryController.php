<?php
/**
 * Fichier SecretaryController.php
 *
 * Ce fichier contient la classe 'SecretaryController', qui gère toutes les actions
 * relatives aux secrétaires. Elle permet la création, la mise à jour, ainsi
 * que l'affichage
 * des informations liées aux secrétaires.
 *
 * PHP version 8.3
 *
 * @category API
 * @package  Controllers
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/SecretaryController
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace controllers;

use models\Department;
use models\User;
use utils\InputValidator;
use views\SecretaryView;
use views\UserView;

/**
 * Class SecretaryController
 *
 * Gère toutes les actions relatives aux secrétaires (Création, mise à jour,
 * affichage).
 *
 * @category API
 * @package  Controllers
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/SecretaryController Documentation de
 * la classe
 * @since    2025-01-07
 */
class SecretaryController extends UserController
{
    /**
     * Permet d'utiliser le model de User
     *
     * @var User
     */
    private User $_model;

    /**
     *  Permet d'utiliser la vue de User
     *
     * @var SecretaryView
     */
    private SecretaryView $_view;

    /**
     * Constructor of SecretaryController.
     *
     * Initialise le modèle et la vue pour le contrôleur des secrétaires.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_model = new User();
        $this->_view = new SecretaryView();
    }

    /**
     * Affiche le bouton magique pour télécharger le calendrier.
     *
     * @return string
     */
    public function displayMySchedule(): string
    {
        return $this->_view->displayWelcomeAdmin();
    }

    /**
     * Insère un nouveau compte de secrétaire dans la base de données.
     *
     * Cette méthode gère l'insertion d'un utilisateur avec le rôle "secrétaire" à
     * partir des données soumises via un formulaire. Elle vérifie d'abord la
     * validité des informations fournies, telles que le login, le mot de passe, et
     * l'email, puis s'assure qu'il n'existe pas déjà un utilisateur avec ces
     * informations. En cas de succès, l'utilisateur est inséré dans la base de
     * données et un message de validation est affiché. En cas d'échec, un message
     * d'erreur est affiché.
     *
     * @return string Retourne le formulaire de création de secrétaire.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function insert(): string
    {
        $action = filter_input(INPUT_POST, 'createSecre');

        $currentUser = wp_get_current_user();
        $deptModel = new Department();

        $isAdmin = current_user_can('admin_perms');
        // si l'utilisateur actuel est admin, on envoie null car il n'a aucun
        // département, sinon on cherche le département
        $currDept = $isAdmin ? -1 : $deptModel->getUserDepartment(
            $currentUser->ID
        )->getIdDepartment();

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginSecre');
            $password = filter_input(INPUT_POST, 'pwdSecre');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmSecre');
            $email = filter_input(INPUT_POST, 'emailSecre');
            // les non-admins ne peuvent pas choisir le département, on empêche donc
            // ces utilisateurs de pouvoir le changer
            $deptId = $isAdmin ? filter_input(INPUT_POST, 'deptIdSecre') : $currDept;

            if (InputValidator::isValidLogin($login) 
                && InputValidator::isValidPassword($password, $passwordConfirm) 
                && InputValidator::isValidEmail($email)
            ) {
                $this->_model->setLogin($login);
                $this->_model->setPassword($password);
                $this->_model->setEmail($email);
                $this->_model->setRole('secretaire');
                $this->_model->setIdDepartment($deptId);

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


        return $this->_view->displayFormSecretary($allDepts, $isAdmin, $currDept);
    }

    /**
     * Affiche la liste de tous les secrétaires enregistrés.
     *
     * Cette méthode récupère tous les utilisateurs ayant le rôle de "secrétaire" à
     * partir du modèle, puis renvoie l'affichage de cette liste via la vue associée.
     * Elle permet ainsi de lister et gérer les comptes des secrétaires enregistrés
     * dans le système.
     *
     * @return string Retourne l'affichage HTML de la liste des secrétaires.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayAllSecretary(): string
    {
        $users = $this->_model->getUsersByRole('secretaire');
        $deptModel = new Department();

        $userDeptList = array();
        foreach ($users as $user) {
            $userDeptList[] = $deptModel->getUserDepartment(
                $user->getId()
            )->getName();
        }

        return $this->_view->displayAllSecretary($users, $userDeptList);
    }

    /**
     * Génère et affiche les formulaires de création d'utilisateurs pour différents
     * rôles.
     *
     * Cette méthode instancie les contrôleurs des différents types d'utilisateurs
     * (étudiants, enseignants, directeurs d'études, secrétaires, techniciens,
     * télévisions) et affiche un ensemble d'onglets de sélection avec un formulaire
     * d'insertion pour chaque rôle. L'utilisateur peut ainsi créer des comptes pour
     * chaque catégorie d'utilisateurs via une interface intuitive de
     * multi-sélection.
     *
     * @return string Retourne l'affichage HTML des formulaires de création
     * d'utilisateurs.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function createUsers(): string
    {
        $secretary = new SecretaryController();
        $technician = new TechnicianController();
        $television = new TelevisionController();
        $subadmin = new SubadminController();
        $communicant = new CommuniquantController();

        $subadminTitle = $subadminContent = '';
        if (current_user_can('subadmin_access')) {
            $subadminTitle = $this->_view->displayTitleSelect(
                'subadmin', 'Sous-administrateurs'
            );
            $subadminContent = $this->_view->displayContentSelect(
                'subadmin',
                $subadmin->insert()
            );
        }

        return
            $this->_view->displayStartMultiSelect() .
            $this->_view->displayTitleSelect('secretary', 'Secrétaires', true) .
            $this->_view->displayTitleSelect('technician', 'Agent d\'entretien') .
            $this->_view->displayTitleSelect('communicant', 'Communiquant') .
            $this->_view->displayTitleSelect('television', 'Télévisions') .
            $subadminTitle .
            $this->_view->displayEndOfTitle() .
            $this->_view->displayContentSelect(
                'secretary', $secretary->insert(), true
            ) .
            $this->_view->displayContentSelect('technician', $technician->insert()) .
            $this->_view->displayContentSelect('communicant', $communicant->insert()) .
            $this->_view->displayContentSelect('television', $television->insert()) .
            $subadminContent .
            '</div>' .
            $this->_view->contextCreateUser();
    }

    /**
     * Affiche les listes des utilisateurs par rôles.
     *
     * Cette méthode instancie les contrôleurs des différents types d'utilisateurs
     * (étudiants, enseignants, directeurs d'études, secrétaires, techniciens,
     * télévisions) et affiche une  interface avec des onglets de
     * multi-sélection permettant de consulter les utilisateurs enregistrés pour
     * chaque catégorie. Chaque onglet affiche la liste des utilisateurs
     * correspondants, organisée par rôle.
     *
     * @return string Retourne l'affichage HTML des listes d'utilisateurs par rôles.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayUsers(): string
    {
        $secretary = new SecretaryController();
        $technician = new TechnicianController();
        $television = new TelevisionController();
        $subadmin = new SubadminController();
        $communicant = new CommuniquantController();

        $subadminTitle = $subadminContent = '';
        if (current_user_can('subadmin_access')) {
            $subadminTitle = $this->_view->displayTitleSelect(
                'subadmin', 'Sous-administrateurs'
            );
            $subadminContent = $this->_view->displayContentSelect(
                'subadmin',
                $subadmin->displayAllSubadmin()
            );
        }

        return
            $this->_view->displayStartMultiSelect() .
            $this->_view->displayTitleSelect('secretary', 'Secrétaires', true) .
            $this->_view->displayTitleSelect('technician', 'Agents d\'entretiens') .
            $this->_view->displayTitleSelect('communicant', 'Communiquant') .
            $this->_view->displayTitleSelect('television', 'Télévisions') .
            $subadminTitle .
            $this->_view->displayEndOfTitle() .
            $this->_view->displayContentSelect(
                'secretary', $secretary->displayAllSecretary(), true
            ) .
            $this->_view->displayContentSelect(
                'technician', $technician->displayAllTechnician()
            ) .
            $this->_view->displayContentSelect(
                'communicant', $communicant->displayAllCommunicants()
            ) .
            $this->_view->displayContentSelect(
                'television', $television->displayAllTv()
            ) .
            $subadminContent .
            '</div>';
    }

    /**
     * Modifie les informations d'un utilisateur spécifié par son identifiant.
     *
     * Cette méthode vérifie si l'identifiant de l'utilisateur est valide et existe
     * dans la base de données.
     * Si l'utilisateur est trouvé, elle détermine le rôle de l'utilisateur dans
     * WordPress et appelle le contrôleur approprié (étudiant, enseignant, directeur
     * d'études ou télévision) pour procéder à la modification des informations.
     * Si l'utilisateur n'est pas trouvé ou si l'identifiant n'est pas valide,
     * un message indiquant qu'aucun utilisateur n'a été trouvé est affiché.
     *
     * @return string Retourne le contenu HTML pour modifier l'utilisateur, ou un
     * message d'erreur si l'utilisateur n'est pas trouvé.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function modifyUser(): string
    {
        $id = $_GET['id'];
        if (is_numeric($id) && $this->_model->get($id)) {
            $user = $this->_model->get($id);
            $wordpressUser = get_user_by('id', $id);

            if (in_array("television", $wordpressUser->roles)) {
                $controller = new TelevisionController();
                return $controller->modify($user);
            }
        }
        return $this->_view->displayNoUser();

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
