<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\StudentView;

/**
 * Class StudentController
 *
 * Gère les étudiants (Création, mise à jour, suppression, affichage)
 *
 * @package Controllers
 */
class StudentController extends UserController implements Schedule
{

    /**
     * @var User
     */
    private $model;

    /**
     * @var StudentView
     */
    public $view;

    /**
     * Constructeur de StudentController.
     * Initialise le modèle et la vue.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new StudentView();
    }

    /**
     * Insère tous les utilisateurs à partir d'un fichier Excel.
     *
     * @return string
     */
    public function insert() {
        $actionStudent = filter_input(INPUT_POST, 'importEtu');

        if ($actionStudent) {
            $allowed_extension = array("Xls", "Xlsx", "Csv");
            $extension = ucfirst(strtolower(pathinfo($_FILES['fileSecre']['name'], PATHINFO_EXTENSION)));

            // Vérifie si l'extension est correcte
            if (in_array($extension, $allowed_extension)) {
                $data = $this->getDataExcel($_FILES['fileSecre']['tmp_name']);
                foreach ($data as $row) {
                    if (count($row) < 3) continue;

                    // Extrait les données
                    $login = $row[0];
                    $email = $row[1];
                    $pwd = substr(md5(uniqid(rand(), true)), 0, 8);

                    // Crée un nouvel utilisateur
                    $this->model->setLogin($login);
                    $this->model->setEmail($email);
                    $this->model->setPassword($pwd);
                    $this->model->setRole('etudiant');

                    // Vérifie les utilisateurs en double et insère l'utilisateur
                    if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                        $this->sendWelcomeEmail($login, $email, $pwd);
                    }
                }
                return $this->view->displayInsertValidate();
            } else {
                return $this->view->displayErrorFileType();
            }
        }
        return $this->view->displayImportForm();
    }

    /**
     * Affiche un utilisateur spécifique.
     *
     * @return string
     */
    public function displayOneStudent() {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $user = $this->model->get($id);
        if ($user) {
            return $this->view->displayStudent($user);
        }
        return $this->view->displayErrorUser();
    }

    /**
     * Affiche tous les étudiants.
     *
     * @return string
     */
    public function displayAllStudents() {
        $students = $this->model->getUsersByRole('etudiant');
        return $this->view->displayAllStudents($students);
    }

    /**
     * Affiche le programme d'un étudiant spécifique.
     *
     * @return string
     */
    public function displayMySchedule() {
        // Implémente l'affichage de l'emploi du temps de l'étudiant
        return $this->view->displayStudentSchedule($this->model->getUserSchedule());
    }

    /**
     * Gère l'emploi du temps de l'étudiant.
     *
     * @return string
     */
    public function manageStudent() {
        $group = filter_input(INPUT_POST, 'groupSecre');
        $year = filter_input(INPUT_POST, 'yearSecre');
        if ($group && $year) {
            $this->model->setGroup($group);
            $this->model->setYear($year);
            if ($this->model->update()) {
                return $this->view->displaySuccessUpdate();
            } else {
                return $this->view->displayErrorUpdate();
            }
        }
        return $this->view->displayManageForm();
    }

    /**
     * Modifie un étudiant spécifique.
     *
     * @return string
     */
    public function modify() {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($this->model->get($id)) {
            $user = $this->model->get($id);
            if ($user->update()) {
                return $this->view->displaySuccessModification();
            } else {
                return $this->view->displayErrorModification();
            }
        }
        return $this->view->displayErrorUser();
    }

    /**
     * Supprime un étudiant spécifique.
     *
     * @return string
     */
    public function delete() {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($this->model->get($id)) {
            $this->model->delete($id);
            return $this->view->displaySuccessDeletion();
        }
        return $this->view->displayErrorUser();
    }

    /**
     * Envoie un e-mail de bienvenue à l'étudiant.
     *
     * @param string $login
     * @param string $email
     * @param string $pwd
     */
    private function sendWelcomeEmail($login, $email, $pwd) {
        $to = $email;
        $subject = "Inscription à la télé-connecté";
        $message = '
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <title>Inscription à la télé-connecté</title>
            </head>
            <body>
                <p>Bonjour, vous avez été inscrit sur le site de la Télé Connecté de votre département en tant qu\'étudiant</p>
                <p>Votre identifiant est ' . htmlspecialchars($login) . ' et votre mot de passe est ' . htmlspecialchars($pwd) . '.</p>
                <p>Veuillez changer votre mot de passe lors de votre première connexion pour plus de sécurité !</p>
                <p>Pour vous connecter, rendez-vous sur le site : <a href="' . htmlspecialchars(home_url()) . '">' . htmlspecialchars(home_url()) . '</a>.</p>
                <p>Nous vous souhaitons une bonne expérience sur notre site.</p>
            </body>
        </html>';

        $headers = 'Content-Type: text/html; charset=UTF-8';
        mail($to, $subject, $message, $headers);
    }

}
