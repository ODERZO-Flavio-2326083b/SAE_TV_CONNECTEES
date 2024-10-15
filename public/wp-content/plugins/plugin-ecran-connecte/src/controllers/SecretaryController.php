<?php

namespace Controllers;

use Models\User;
use Views\SecretaryView;

/**
 * Class SecretaryController
 *
 * Gère toutes les actions relatives aux secrétaires (Création, mise à jour, affichage).
 *
 * @package Controllers
 */
class SecretaryController extends UserController
{
    /**
     * @var User
     */
    private $model;

    /**
     * @var SecretaryView
     */
    private $view;

    /**
     * Constructor of SecretaryController.
     *
     * Initialise le modèle et la vue pour le contrôleur des secrétaires.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new SecretaryView();
    }

    /**
     * Affiche le bouton magique pour télécharger le calendrier.
     *
     * @return string
     */
    public function displayMySchedule() {
        return $this->view->displayWelcomeAdmin();
    }

    /**
     * Insère un nouveau compte de secrétaire dans la base de données.
     *
     * Cette méthode gère l'insertion d'un utilisateur avec le rôle "secrétaire" à partir
     * des données soumises via un formulaire. Elle vérifie d'abord la validité des
     * informations fournies, telles que le login, le mot de passe, et l'email, puis
     * s'assure qu'il n'existe pas déjà un utilisateur avec ces informations. En cas de
     * succès, l'utilisateur est inséré dans la base de données et un message de validation
     * est affiché. En cas d'échec, un message d'erreur est affiché.
     *
     * @return string Retourne le formulaire de création de secrétaire.
     *
     * @example
     * // Appel de la méthode lors de la soumission du formulaire :
     * $this->insert();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createSecre');

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginSecre');
            $password = filter_input(INPUT_POST, 'pwdSecre');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmSecre');
            $email = filter_input(INPUT_POST, 'emailSecre');

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('secretaire');

                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }
        return $this->view->displayFormSecretary();
    }

    /**
     * Affiche la liste de tous les secrétaires enregistrés.
     *
     * Cette méthode récupère tous les utilisateurs ayant le rôle de "secrétaire" à partir
     * du modèle, puis renvoie l'affichage de cette liste via la vue associée. Elle permet
     * ainsi de lister et gérer les comptes des secrétaires enregistrés dans le système.
     *
     * @return string Retourne l'affichage HTML de la liste des secrétaires.
     *
     * @example
     * // Affiche la liste des secrétaires :
     * echo $this->displayAllSecretary();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayAllSecretary() {
        $users = $this->model->getUsersByRole('secretaire');
        return $this->view->displayAllSecretary($users);
    }

    /**
     * Génère et affiche les formulaires de création d'utilisateurs pour différents rôles.
     *
     * Cette méthode instancie les contrôleurs des différents types d'utilisateurs (étudiants, enseignants,
     * directeurs d'études, secrétaires, techniciens, télévisions) et affiche un ensemble d'onglets de sélection
     * avec un formulaire d'insertion pour chaque rôle. L'utilisateur peut ainsi créer des comptes pour chaque
     * catégorie d'utilisateurs via une interface intuitive de multi-sélection.
     *
     * @return string Retourne l'affichage HTML des formulaires de création d'utilisateurs.
     *
     * @example
     * // Affiche l'interface de création d'utilisateurs :
     * echo $this->createUsers();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function createUsers() {
        $student = new StudentController();
        $teacher = new TeacherController();
        $studyDirector = new StudyDirectorController();
        $secretary = new SecretaryController();
        $technician = new TechnicianController();
        $television = new TelevisionController();

        return
            $this->view->displayStartMultiSelect() .
            $this->view->displayTitleSelect('student', 'Étudiants', true) .
            $this->view->displayTitleSelect('teacher', 'Enseignants') .
            $this->view->displayTitleSelect('studyDirector', 'Directeurs d\'études') .
            $this->view->displayTitleSelect('secretary', 'Secrétaires') .
            $this->view->displayTitleSelect('technician', 'Technicien') .
            $this->view->displayTitleSelect('television', 'Télévisions') .
            $this->view->displayEndOfTitle() .
            $this->view->displayContentSelect('student', $student->insert(), true) .
            $this->view->displayContentSelect('teacher', $teacher->insert()) .
            $this->view->displayContentSelect('studyDirector', $studyDirector->insert()) .
            $this->view->displayContentSelect('secretary', $secretary->insert()) .
            $this->view->displayContentSelect('technician', $technician->insert()) .
            $this->view->displayContentSelect('television', $television->insert()) .
            $this->view->displayEndDiv() .
            $this->view->contextCreateUser();
    }

    /**
     * Affiche les listes des utilisateurs par rôles.
     *
     * Cette méthode instancie les contrôleurs des différents types d'utilisateurs (étudiants, enseignants,
     * directeurs d'études, secrétaires, techniciens, télévisions) et affiche une interface avec des onglets de
     * multi-sélection permettant de consulter les utilisateurs enregistrés pour chaque catégorie. Chaque onglet
     * affiche la liste des utilisateurs correspondants, organisée par rôle.
     *
     * @return string Retourne l'affichage HTML des listes d'utilisateurs par rôles.
     *
     * @example
     * // Affiche l'interface de gestion des utilisateurs :
     * echo $this->displayUsers();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayUsers() {
        $student = new StudentController();
        $teacher = new TeacherController();
        $studyDirector = new StudyDirectorController();
        $secretary = new SecretaryController();
        $technician = new TechnicianController();
        $television = new TelevisionController();

        return
            $this->view->displayStartMultiSelect() .
            $this->view->displayTitleSelect('student', 'Étudiants', true) .
            $this->view->displayTitleSelect('teacher', 'Enseignants') .
            $this->view->displayTitleSelect('studyDirector', 'Directeurs d\'études') .
            $this->view->displayTitleSelect('secretary', 'Secrétaires') .
            $this->view->displayTitleSelect('technician', 'Technicien') .
            $this->view->displayTitleSelect('television', 'Télévisions') .
            $this->view->displayEndOfTitle() .
            $this->view->displayContentSelect('student', $student->displayAllStudents(), true) .
            $this->view->displayContentSelect('teacher', $teacher->displayAllTeachers()) .
            $this->view->displayContentSelect('studyDirector', $studyDirector->displayAllStudyDirector()) .
            $this->view->displayContentSelect('secretary', $secretary->displayAllSecretary()) .
            $this->view->displayContentSelect('technician', $technician->displayAllTechnician()) .
            $this->view->displayContentSelect('television', $television->displayAllTv()) .
            $this->view->displayEndDiv();
    }

    /**
     * Modifie les informations d'un utilisateur spécifié par son identifiant.
     *
     * Cette méthode vérifie si l'identifiant de l'utilisateur est valide et existe dans la base de données.
     * Si l'utilisateur est trouvé, elle détermine le rôle de l'utilisateur dans WordPress et appelle le
     * contrôleur approprié (étudiant, enseignant, directeur d'études ou télévision) pour procéder à la
     * modification des informations. Si l'utilisateur n'est pas trouvé ou si l'identifiant n'est pas valide,
     * un message indiquant qu'aucun utilisateur n'a été trouvé est affiché.
     *
     * @return string Retourne le contenu HTML pour modifier l'utilisateur, ou un message d'erreur si l'utilisateur n'est pas trouvé.
     *
     * @example
     * // Modifie les informations d'un utilisateur avec l'identifiant spécifié :
     * echo $this->modifyUser();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function modifyUser() {
        $id = $_GET['id'];
        if (is_numeric($id) && $this->model->get($id)) {
            $user = $this->model->get($id);
            $wordpressUser = get_user_by('id', $id);

            if (in_array("etudiant", $wordpressUser->roles)) {
                $controller = new StudentController();
                return $controller->modify($user);
            } elseif (in_array("enseignant", $wordpressUser->roles)) {
                $controller = new TeacherController();
                return $controller->modify($user);
            } elseif (in_array("directeuretude", $wordpressUser->roles)) {
                $controller = new StudyDirectorController();
                return $controller->modify($user);
            } elseif (in_array("television", $wordpressUser->roles)) {
                $controller = new TelevisionController();
                return $controller->modify($user);
            } else {
                return $this->view->displayNoUser();
            }
        } else {
            return $this->view->displayNoUser();
        }
    }

    /**
     * Supprime les utilisateurs sélectionnés à partir des cases à cocher dans le formulaire.
     *
     * Cette méthode vérifie si une action de suppression a été demandée. Si c'est le cas,
     * elle parcourt les rôles spécifiés (étudiant, enseignant, directeur, technicien, secrétaire, télévision)
     * et vérifie si des utilisateurs correspondant à ces rôles ont été sélectionnés. Pour chaque utilisateur
     * sélectionné, la méthode appelle `deleteUser` pour effectuer la suppression de l'utilisateur correspondant.
     *
     * @return void Cette méthode n'a pas de valeur de retour, elle effectue directement la suppression des utilisateurs.
     *
     * @example
     * // Supprime les utilisateurs sélectionnés :
     * $this->deleteUsers();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function deleteUsers() {
        $actionDelete = filter_input(INPUT_POST, 'delete');
        $roles = ['Etu', 'Teacher', 'Direc', 'Tech', 'Secre', 'Tele'];

        if (isset($actionDelete)) {
            foreach ($roles as $role) {
                if (isset($_REQUEST['checkboxStatus' . $role])) {
                    $checked_values = $_REQUEST['checkboxStatus' . $role];
                    foreach ($checked_values as $id) {
                        $this->deleteUser($id);
                    }
                }
            }
        }
    }

    /**
     * Supprime un utilisateur spécifié par son identifiant.
     *
     * Cette méthode récupère l'utilisateur à partir de la base de données à l'aide de l'identifiant
     * fourni, puis appelle la méthode `delete` sur l'instance de l'utilisateur pour le supprimer
     * de la base de données.
     *
     * @param int $id L'identifiant de l'utilisateur à supprimer.
     * @return void Cette méthode n'a pas de valeur de retour, elle effectue directement la suppression
     *               de l'utilisateur.
     *
     * @example
     * // Supprime l'utilisateur avec l'identifiant 5 :
     * $this->deleteUser(5);
     *
     * @version 1.0
     * @date 2024-10-15
     */
    private function deleteUser($id) {
        $user = $this->model->get($id);
        $user->delete();
    }
}
