<?php

namespace Controllers;

use Models\User;
use Views\StudyDirectorView;

/**
 * Class StudyDirectorController
 *
 * Gère les directeurs d'études (Création, mise à jour, suppression, affichage)
 *
 * @package Controllers
 */
class StudyDirectorController extends UserController implements Schedule
{

    /**
     * @var User Modèle représentant un utilisateur.
     */
    private $model;

    /**
     * @var StudyDirectorView Vue associée au contrôleur des directeurs d'études.
     */
    private $view;

    /**
     * Initialise une instance de la classe.
     *
     * Cette méthode est le constructeur de la classe, elle appelle le constructeur
     * de la classe parente et initialise les propriétés de modèle et de vue.
     * Le modèle est instancié en tant qu'objet `User` et la vue est instanciée
     * en tant qu'objet `StudyDirectorView`. Cela permet de gérer les opérations
     * liées aux utilisateurs et d'afficher les vues correspondantes.
     *
     * @return void Ce constructeur n'a pas de valeur de retour.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new StudyDirectorView();
    }

    /**
     * Affiche l'emploi du temps de l'utilisateur courant.
     *
     * Cette méthode récupère l'utilisateur actuellement connecté et vérifie
     * s'il possède des codes associés. Si des codes sont trouvés, elle affiche
     * l'emploi du temps correspondant au premier code de l'utilisateur.
     * Si aucun code n'est enregistré, elle renvoie un message d'erreur
     * indiquant que l'utilisateur n'a pas de code associé.
     *
     * @return string Retourne l'affichage de l'emploi du temps si un code
     *                est disponible, sinon un message d'erreur.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayMySchedule() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        if (sizeof($user->getCodes()) > 0) {
            return $this->displaySchedule($user->getCodes()[0]->getCode());
        } else {
            return $this->view->errorMessageNoCodeRegister();
        }
    }

    /**
     * Insère un nouvel utilisateur de type directeur d'études dans la base de données.
     *
     * Cette méthode traite les informations fournies par le formulaire d'inscription
     * d'un directeur d'études, effectue des validations sur les données saisies
     * (login, mot de passe, e-mail, et code) et, si tout est valide, insère
     * le nouvel utilisateur dans la base de données. Elle gère également
     * la création de fichiers associés pour le code fourni.
     *
     * @return string Retourne l'affichage du formulaire de création de directeur
     *                d'études, ou un message d'erreur si les validations échouent.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createDirec');

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginDirec');
            $password = filter_input(INPUT_POST, 'pwdDirec');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmDirec');
            $email = filter_input(INPUT_POST, 'emailDirec');
            $code = filter_input(INPUT_POST, 'codeDirec');

            // Vérifie les conditions de validation des entrées.
            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('directeuretude');
                $this->model->setCodes($code);

                // Insère l'utilisateur et gère les fichiers associés.
                if ($this->model->insert()) {
                    $path = $this->getFilePath($code);
                    if (!file_exists($path)) {
                        $this->addFile($code);
                    }
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }
        return $this->view->displayCreateDirector();
    }

    /**
     * Modifie les informations d'un directeur d'études existant.
     *
     * Cette méthode gère la mise à jour des données d'un directeur d'études, y compris
     * le code associé à son compte. Elle récupère les informations du formulaire de
     * modification, vérifie que le code est numérique et met à jour les informations
     * de l'utilisateur dans la base de données. Si la mise à jour réussit, un message
     * de validation est affiché.
     *
     * @param User $user L'objet utilisateur à modifier, représentant le directeur
     *                   d'études dont les informations doivent être mises à jour.
     *
     * @return string Retourne l'affichage du formulaire de modification de directeur
     *                d'études, ou un message de validation si la modification est réussie.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function modify($user) {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $action = filter_input(INPUT_POST, 'modifValidate');

        if ($action === 'Valider') {
            $code = filter_input(INPUT_POST, 'modifCode');
            if (is_numeric($code)) {
                $user->setRole('directeuretude');
                $user->getCodes()[0]->setCode($code);

                if ($user->update()) {
                    $this->view->displayModificationValidate($linkManageUser);
                }
            }
        }
        return $this->view->displayModifyStudyDirector($user);
    }

    /**
     * Affiche tous les directeurs d'études avec leurs codes associés.
     *
     * Cette méthode récupère tous les utilisateurs ayant le rôle de directeur d'études
     * et récupère leurs codes associés. Ensuite, elle affiche la liste des directeurs
     * d'études avec les informations pertinentes, y compris les codes.
     *
     * @return string Retourne l'affichage de tous les directeurs d'études, incluant
     *                leurs codes associés.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayAllStudyDirector() {
        $users = $this->model->getUsersByRole('directeuretude');
        $users = $this->model->getMyCodes($users);
        return $this->view->displayAllStudyDirector($users);
    }
}
