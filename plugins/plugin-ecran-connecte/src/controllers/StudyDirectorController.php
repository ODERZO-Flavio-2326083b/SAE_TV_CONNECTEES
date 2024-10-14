<?php

namespace Controllers;

use Models\User;
use Views\StudyDirectorView;

/**
 * Class StudyDirectorController
 *
 * Contrôleur pour gérer les directeurs d'études (création, mise à jour, suppression, affichage).
 *
 * @package Controllers
 */
class StudyDirectorController extends UserController implements Schedule
{
    /**
     * @var User Modèle utilisateur pour interagir avec la base de données
     */
    private $model;

    /**
     * @var StudyDirectorView Vue associée au directeur d'études
     */
    private $view;

    /**
     * Constructeur de la classe StudyDirectorController
     * Initialise le modèle et la vue.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new StudyDirectorView();
    }

    /**
     * Affiche l'emploi du temps du directeur d'études.
     *
     * @return bool|mixed|string Renvoie le contenu de l'emploi du temps ou un message d'erreur.
     */
    public function displayMySchedule() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);

        // Vérifie si l'utilisateur a des codes d'accès
        if (sizeof($user->getCodes()) > 0) {
            return $this->displaySchedule($user->getCodes()[0]->getCode());
        } else {
            return $this->view->errorMessageNoCodeRegister();
        }
    }

    /**
     * Insère un directeur d'études dans la base de données.
     *
     * @return string Renvoie la vue pour créer un directeur ou un message de validation.
     *
     * @throws Exception Lève une exception si l'insertion échoue ou si les données sont invalides.
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createDirec');

        if (isset($action)) {
            // Récupération des données du formulaire
            $login = filter_input(INPUT_POST, 'loginDirec');
            $password = filter_input(INPUT_POST, 'pwdDirec');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmDirec');
            $email = filter_input(INPUT_POST, 'emailDirec');
            $code = filter_input(INPUT_POST, 'codeDirec');

            // Validation des données saisies
            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                // Initialisation du modèle avec les données validées
                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('directeuretude');
                $this->model->setCodes($code);

                // Insertion dans la base de données
                if ($this->model->insert()) {
                    $path = $this->getFilePath($code);
                    if (!file_exists($path)) {
                        $this->addFile($code); // Ajoute un fichier si celui-ci n'existe pas
                    }
                    $this->view->displayInsertValidate(); // Affiche un message de succès
                } else {
                    $this->view->displayErrorInsertion(); // Affiche un message d'erreur
                }
            } else {
                $this->view->displayErrorCreation(); // Affiche un message d'erreur de création
            }
        }
        return $this->view->displayCreateDirector(); // Affiche le formulaire de création
    }

    /**
     * Modifie les informations d'un directeur d'études.
     *
     * @param User $user L'utilisateur à modifier.
     * @return string Renvoie la vue de modification du directeur d'études.
     */
    public function modify($user) {
        // Lien vers la gestion des utilisateurs
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $action = filter_input(INPUT_POST, 'modifValidate');

        if ($action === 'Valider') {
            $code = filter_input(INPUT_POST, 'modifCode');
            // Vérification que le code est numérique
            if (is_numeric($code)) {
                $user->setRole('directeuretude'); // Mise à jour du rôle
                $user->getCodes()[0]->setCode($code); // Mise à jour du code

                if ($user->update()) {
                    $this->view->displayModificationValidate($linkManageUser); // Affiche un message de succès
                }
            }
        }
        return $this->view->displayModifyStudyDirector($user); // Affiche le formulaire de modification
    }

    /**
     * Affiche tous les directeurs d'études.
     *
     * @return string Renvoie la vue contenant tous les directeurs d'études.
     */
    public function displayAllStudyDirector() {
        // Récupère tous les utilisateurs avec le rôle de directeur d'études
        $users = $this->model->getUsersByRole('directeuretude');
        $users = $this->model->getMyCodes($users); // Récupération des codes associés
        return $this->view->displayAllStudyDirector($users); // Affichage de la vue
    }
}
