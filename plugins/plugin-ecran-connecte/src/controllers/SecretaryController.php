<?php

namespace Controllers;

use Models\User;
use Views\SecretaryView;

/**
 * Class SecretaryController
 *
 * Contrôle toutes les actions pour le secrétaire (Créer, mettre à jour, afficher).
 *
 * @package Controllers
 */
class SecretaryController extends UserController
{
    /**
     * @var User Modèle utilisateur pour la gestion des secrétaires.
     */
    private $model;

    /**
     * @var SecretaryView Vue pour l'affichage des informations du secrétaire.
     */
    private $view;

    /**
     * Constructeur de SecretaryController.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new SecretaryView();
    }

    /**
     * Affiche le bouton magique pour télécharger l'horaire.
     *
     * @return string Contenu HTML affichant le message de bienvenue pour l'administrateur.
     */
    public function displayMySchedule() {
        return $this->view->displayWelcomeAdmin();
    }

    /**
     * Insère un secrétaire dans la base de données.
     *
     * @return string Contenu HTML pour le formulaire de création de secrétaire.
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createSecre');

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginSecre');
            $password = filter_input(INPUT_POST, 'pwdSecre');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmSecre');
            $email = filter_input(INPUT_POST, 'emailSecre');

            // Validation des données
            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                // Affectation des valeurs au modèle
                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('secretaire');

                // Vérification de l'unicité et insertion dans la base de données
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
     * Affiche tous les secrétaires.
     *
     * @return string Contenu HTML affichant tous les secrétaires.
     */
    public function displayAllSecretary() {
        $users = $this->model->getUsersByRole('secretaire');
        return $this->view->displayAllSecretary($users);
    }

    /*** GESTION DES UTILISATEURS ***/

    /**
     * Crée un utilisateur.
     *
     * @return string Contenu HTML pour le formulaire de création d'utilisateurs.
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
     * Affiche les utilisateurs par rôles.
     *
     * @return string Contenu HTML affichant les utilisateurs triés par rôle.
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
     * Modifie un utilisateur.
     *
     * @return string Contenu HTML pour le formulaire de modification d'un utilisateur.
     */
    public function modifyUser() {
        $id = $_GET['id'];
        if (is_numeric($id) && $this->model->get($id)) {
            $user = $this->model->get($id);
            $wordpressUser = get_user_by('id', $id);

            // Détermination du rôle et appel du contrôleur approprié
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
     * Supprime des utilisateurs.
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
     * Supprime un utilisateur.
     *
     * @param int $id L'ID de l'utilisateur à supprimer.
     */
    private function deleteUser($id) {
        $user = $this->model->get($id);
        $user->delete();
    }
}
