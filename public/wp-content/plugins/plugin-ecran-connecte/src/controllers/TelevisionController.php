<?php

namespace Controllers;

use Models\CodeAde;
use Models\Department;
use models\Scrapper;
use Models\User;
use Views\TelevisionView;

/**
 * Class TelevisionController
 *
 * Gère les télévisions (Création, mise à jour, suppression, affichage, affichage des emplois du temps)
 *
 * @package Controllers
 */
class TelevisionController extends UserController implements Schedule
{

    /**
     * Modèle de TelevisionController.
     *
     * @var User
     */
    private $model;

    /**
     * Vue de TelevisionController.
     *
     * @var TelevisionView
     */
    private $view;

    /**
     * Initialise une nouvelle instance de la classe.
     *
     * Ce constructeur appelle le constructeur parent pour s'assurer que
     * toutes les initialisations nécessaires sont effectuées. Il crée
     * également une instance de la classe User pour la gestion des utilisateurs
     * et une instance de la classe TelevisionView pour l'affichage des vues
     * liées à la télévision.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TelevisionView();
    }

    /**
     * Insère un nouvel utilisateur de télévision dans la base de données.
     *
     * Cette méthode traite les données du formulaire d'inscription de
     * l'utilisateur de télévision. Elle valide les entrées, s'assure que
     * les codes associés sont valides, et crée un nouvel enregistrement
     * dans la base de données pour l'utilisateur. Si l'insertion est réussie,
     * un message de validation est affiché. En cas d'erreur, des messages
     * d'erreur appropriés sont retournés.
     *
     * @return string Retourne l'affichage du formulaire d'inscription à la télévision
     *                si les données sont invalides, sinon renvoie un message de validation
     *                ou d'erreur selon le résultat de l'insertion.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function insert(): string {
        $action = filter_input(INPUT_POST, 'createTv');
        $codeAde = new CodeAde();

	    $currentUser = wp_get_current_user();
	    $deptModel = new Department();

	    $isAdmin = in_array("administrator", $currentUser->roles);
	    // si l'utilisateur actuel est admin, on envoie null car il n'a aucun département, sinon on cherche le département
	    $currDept = $isAdmin ? null : $deptModel->getUserDepartment($currentUser->ID)->getIdDepartment();

        if (isset($action)) {
            $login = filter_input(INPUT_POST, 'loginTv');
            $password = filter_input(INPUT_POST, 'pwdTv');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTv');
            $codes = filter_input(INPUT_POST, 'selectTv', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
			// les non-admins ne peuvent pas choisir le département, on empêche donc ces utilisateurs
	        // de pouvoir le changer
			$deptId = $isAdmin ? filter_input(INPUT_POST, 'deptIdTv') : $currDept;

            // Validation des données d'entrée
            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm) {

                $codesAde = array();
                foreach ($codes as $code) {
                    if (is_numeric($code) && $code > 0) {
                        if (is_null($codeAde->getByCode($code)->getId())) {
                            return 'error'; // Code invalide
                        } else {
                            $codesAde[] = $codeAde->getByCode($code);
                        }
                    }
                }

                // Configuration du modèle de télévision
                $this->model->setLogin($login);
                $this->model->setEmail($login . '@' . $login . '.fr');
                $this->model->setPassword($password);
                $this->model->setRole('television');
                $this->model->setCodes($codesAde);
				$this->model->setIdDepartment($deptId);

                // Insertion du modèle dans la base de données
                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }

        // Récupération des années, groupes et demi-groupes
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

	    $allDepts = $deptModel->getAllDepts();

        return $this->view->displayFormTelevision($years, $groups, $halfGroups, $allDepts, $isAdmin, $currDept);
    }

    /**
     * Modifie les informations d'un utilisateur de télévision existant.
     *
     * Cette méthode traite la demande de modification des données d'un
     * utilisateur de télévision. Elle vérifie les codes sélectionnés,
     * s'assure qu'ils sont valides, et met à jour les codes associés
     * de l'utilisateur. Si la mise à jour est réussie, un message de
     * validation est affiché. Sinon, la méthode affiche le formulaire
     * de modification avec les données actuelles de l'utilisateur.
     *
     * @param User $user L'utilisateur de télévision dont les informations doivent être modifiées.
     *
     * @return string Retourne le formulaire de modification avec les données actuelles de l'utilisateur
     *                et les options disponibles pour les années, groupes et demi-groupes,
     *                ou un message d'erreur si la mise à jour échoue.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function modify(User $user): string {
        $page = get_page_by_title_custom('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $codeAde = new CodeAde();
        $action = filter_input(INPUT_POST, 'modifValidate');

        if (isset($action)) {
	        $codes = filter_input(INPUT_POST, 'selectTv', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            $codesAde = array();
            foreach ($codes as $code) {
                if (is_null($codeAde->getByCode($code)->getId())) {
                    return 'error'; // Code invalide
                } else {
                    $codesAde[] = $codeAde->getByCode($code);
                }
            }

            // Mise à jour des codes de l'utilisateur
            $user->setCodes($codesAde);

            if ($user->update()) {
                $this->view->displayModificationValidate($linkManageUser);
            }
        }

        // Récupération des années, groupes et demi-groupes
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->modifyForm($user, $years, $groups, $halfGroups);
    }

    /**
     * Affiche tous les utilisateurs ayant le rôle de télévision.
     *
     * Cette méthode récupère tous les utilisateurs qui ont été attribués
     * au rôle de télévision. Les utilisateurs récupérés sont ensuite
     * passés à la vue pour affichage. Cela permet à l'administrateur ou
     * à un utilisateur autorisé de visualiser la liste complète des
     * utilisateurs ayant ce rôle spécifique.
     *
     * @return string Retourne le contenu HTML généré pour afficher la liste
     *                de tous les utilisateurs de télévision.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayAllTv(): string {
        $users = $this->model->getUsersByRole('television');
		$deptModel = new Department();

		$userDeptList = array();
		foreach ($users as $user) {
			$userDeptList[] = $deptModel->getUserDepartment($user->getId())->getName();
		}

        return $this->view->displayAllTv($users, $userDeptList);
    }

    /**
     * Affiche l'emploi du temps de l'utilisateur courant.
     *
     * Cette méthode récupère l'utilisateur actuellement connecté et
     * ses codes associés pour afficher leur emploi du temps. Si l'utilisateur
     * a plusieurs codes, elle vérifie la configuration de l'affichage (défilement
     * ou diaporama) et génère le contenu HTML approprié pour l'affichage.
     *
     * - Si l'utilisateur a plusieurs codes, il affiche les emplois du temps
     *   dans un défilement ou un diaporama selon la configuration du thème.
     * - Si l'utilisateur a un seul code, il affiche simplement l'emploi
     *   du temps correspondant. Si aucun emploi du temps n'est trouvé,
     *   un message indiquant l'absence de cours est affiché.
     *
     * @return string Retourne le contenu HTML de l'emploi du temps de l'utilisateur,
     *                ou un message indiquant qu'il n'a pas de cours.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayMySchedule(): string {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        $user = $this->model->getMyCodes([$user])[0];


        $string = "";
        if (sizeof($user->getCodes()) > 1) {
            if (get_theme_mod('ecran_connecte_schedule_scroll', 'vert') == 'vert') {
                $string .= '<div class="ticker1">
                        <div class="innerWrap tv-schedule">';
                foreach ($user->getCodes() as $code) {
                    $path = $this->getFilePath($code->getCode());
                    if (file_exists($path)) {
                        if ($this->displaySchedule($code->getCode())) {
                            $string .= '<div class="list">';
                            $string .= $this->displaySchedule($code->getCode());
                            $string .= '</div>';
                        }
                    }
                }
                $string .= '</div></div>';
            } else {
                $string .= $this->view->displayStartSlide();
                foreach ($user->getCodes() as $code) {
                    $path = $this->getFilePath($code->getCode());
                    if (file_exists($path)) {
                        if ($this->displaySchedule($code->getCode())) {
                            $string .= $this->view->displayMidSlide();
                            $string .= $this->displaySchedule($code->getCode());
                            $string .= '</div>';
                        }
                    }
                }
                $string .= '</div>';
            }
        } else {
            if (!empty($user->getCodes()[0])) {
                $string .= $this->displaySchedule($user->getCodes()[0]->getCode());
            } else {
                $string .= '<p>Vous n\'avez pas cours</p>';
            }
        }
        return $string;
    }
}
