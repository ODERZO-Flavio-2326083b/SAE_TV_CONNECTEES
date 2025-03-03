<?php
/**
 * Fichier TabletController.php
 *
 * Ce fichier contient la classe 'TabletController', qui gère les opérations
 * liées aux utilisateurs de type "tablette" dans l'application, telles que la
 * création d'un utilisateur, l'affichage de la liste des utilisateurs,
 * ainsi que la gestion de l'affichage de leurs emplois du temps.
 *
 * PHP version 8.3
 *
 * @category API
 * @package  Controllers
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/TabletController
 * Documentation de la classe
 * @since    2024-03-03
 */

namespace controllers;

use models\CodeAde;
use models\Department;
use models\User;
use utils\InputValidator;
use R34ICS;
use views\TabletICSView;
use views\TabletView;


/**
 * Fichier TabletController.php
 *
 * Ce fichier contient la classe 'TabletController', qui gère les opérations
 * liées aux utilisateurs de type "tablette" dans l'application, telles que la
 * création d'un utilisateur, l'affichage de la liste des utilisateurs,
 * ainsi que la gestion de l'affichage de leurs emplois du temps.
 *
 * PHP version 8.3
 *
 * @category Controllers
 * @package  Controllers
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0
 * @link     https://www.example.com/docs/TabletController
 * @since    2024-03-03
 */
class TabletController extends UserController implements Schedule
{

    private User $_model;

    private TabletView $_view;


    /**
     * Constructeur de la classe.
     *
     * Initialise le modèle utilisateur et la vue tablette.
     * Cette méthode appelle d'abord le constructeur de la classe parente
     * afin d'assurer l'initialisation correcte de l'héritage.
     * Ensuite, elle instancie un objet User pour gérer les données utilisateurs
     * et un objet TabletView pour la représentation de l'interface utilisateur.
     *
     * @version 1.0
     * @date    2024-03-03
     */
    public function __construct()
    {
        parent::__construct();
        $this->_model = new User();
        $this->_view = new TabletView();
    }


    /**
     * Insère un nouvel utilisateur de type "tablette".
     *
     * Cette méthode récupère et valide les données envoyées via un formulaire
     * POST pour créer un nouvel utilisateur. Elle vérifie notamment :
     * - Si l'utilisateur actuel est administrateur ou non.
     * - La validité des identifiants et du mot de passe.
     * - L'existence des codes ADE sélectionnés.
     * - Les éventuels doublons d'utilisateur.
     *
     * Après validation, les informations sont enregistrées dans le modèle,
     * puis insérées en base de données. En cas de succès, un message de
     * confirmation est affiché, sinon un message d'erreur est retourné.
     *
     * @return string Le message de validation ou d'erreur.
     *
     * @version 1.0
     * @date    2024-03-03
     */
    public function insert(): string
    {
        $action = filter_input(INPUT_POST, 'createTa');
        $codeAde = new CodeAde();

        $currentUser = wp_get_current_user();
        $deptModel = new Department();

        $isAdmin = current_user_can('admin_perms');
        // si l'utilisateur actuel est admin, on envoie null car il n'a aucun
        // département, sinon on cherche le département
        $currDept
            = $isAdmin ? null : $deptModel->getUserDepartment(
                $currentUser->ID
            )->getIdDepartment();

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginTa');
            $password = filter_input(INPUT_POST, 'pwdTa');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTa');
            $codes = filter_input(
                INPUT_POST, 'selectTa', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY
            );
            // les non-admins ne peuvent pas choisir le département, on empêche donc
            // ces utilisateurs de pouvoir le changer
            $deptId
                = $isAdmin ? filter_input(INPUT_POST, 'deptIdTa') : $currDept;

            // Validation des données d'entrée
            if (InputValidator::isValidLogin($login)
                && InputValidator::isValidPassword($password, $passwordConfirm)
            ) {
                $codesAde = array();
                $code = $codes[0];
                if (is_numeric($code) && $code > 0) {
                    if (is_null($codeAde->getByCode($code)->getId())) {
                        return 'error'; // Code invalide
                    } else {
                        $codesAde[] = $codeAde->getByCode($code);
                    }
                }

                // Configuration du modèle de télévision
                $this->_model->setLogin($login);
                $this->_model->setEmail($login . '@' . $login . '.fr');
                $this->_model->setPassword($password);
                $this->_model->setRole('tablette');
                $this->_model->setCodes($codesAde);
                $this->_model->setIdDepartment($deptId);

                // Insertion du modèle dans la base de données
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

        // Récupération des salles
        $classes = $codeAde->getAllFromType('class');

        $allDepts = $deptModel->getAllDepts();

        return $this->_view->displayFormTablet(
            $classes, $allDepts, $isAdmin, $currDept
        );
    }

    /**
     * Affiche la liste de tous les utilisateurs de type "tablette".
     *
     * Cette méthode récupère tous les utilisateurs ayant le rôle "tablette"
     * depuis le modèle, puis associe chaque utilisateur à son département.
     * Les informations sont ensuite préparées pour l'affichage.
     *
     * @return string Le rendu de la liste des utilisateurs "tablette".
     *
     * @version 1.0
     * @date    2024-03-03
     */
    public function displayAllTablet(): string
    {
        $users = $this->_model->getUsersByRole('tablette');

        $deptModel = new Department();

        $userDeptList = array();
        foreach ($users as $user) {
            $userDeptList[] = $deptModel->getUserDepartment(
                $user->getId()
            )->getName();
        }

        return $this->_view->displayAllTablets($users, $userDeptList);
    }

    /**
     * Affiche l'emploi du temps de l'utilisateur connecté.
     *
     * Cette méthode récupère l'utilisateur actuellement connecté,
     * extrait son premier code ADE associé, puis génère et retourne
     * l'affichage de son emploi du temps.
     *
     * @return string Le rendu de l'emploi du temps de l'utilisateur.
     *
     * @version 1.0
     * @date    2024-03-03
     */
    public function displayMySchedule(): string
    {
        $user = new User();
        $tabUserObj = $user->get(wp_get_current_user()->ID);

        $code = $tabUserObj->getCodes();

        return $this->displaySchedule($code[0]->getCode());
    }

    /**
     * Génère et affiche l'emploi du temps pour un code ADE donné.
     *
     * Cette méthode utilise un code ADE pour récupérer un fichier ICS contenant
     * les événements de l'emploi du temps. Elle configure les paramètres nécessaires
     * pour l'affichage, récupère les données des événements, et génère l'affichage
     * via la vue appropriée. Le paramètre `$allDay` permet de spécifier si tous
     * les événements de la journée doivent être affichés.
     *
     * @param string $code   Le code ADE permettant de récupérer l'emploi du
     *                       temps.
     * @param bool   $allDay Si true, affiche tous les événements de la journée
     *                       (par défaut :
     *                       false).
     *
     * @return string Le rendu HTML de l'emploi du temps.
     *
     * @version 1.0
     * @date    2024-03-03
     */
    public function displaySchedule($code, $allDay = false) : string
    {
        $R34ICS = new R34ICS();

        $url = $this->getFilePath($code);
        $args = array(
            'count' => 10,
            'description' => null,
            'eventdesc' => null,
            'format' => null,
            'hidetimes' => null,
            'showendtimes' => null,
            'title' => null,
            'view' => 'list',
            'first_date' => date_i18n('Ymd', strtotime('monday this week')),
        );

        list($ics_data, $allDay) = $R34ICS->get_event_data(
            $url, $code, $allDay, $args
        );

        $model = new CodeAde();
        $title = $model->getByCode($code)->getTitle();

        $icsView = new TabletICSView();
        return $icsView->displaySchedule($ics_data, $title, $allDay);
    }


}
