<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use Models\Information;
use Models\User;
use R34ICS;
use Views\UserView;

/**
 * Class UserController
 *
 * Gère toutes les opérations liées aux utilisateurs (création, mise à jour, suppression).
 *
 * @package Controllers
 */
class UserController extends Controller
{
    /**
     * @var User Modèle représentant un utilisateur.
     */
    private $model;

    /**
     * @var UserView Vue associée à l'affichage des utilisateurs.
     */
    private $view;

    /**
     * Constructeur de UserController.
     * Initialise le modèle et la vue pour l'utilisateur.
     */
    public function __construct() {
        $this->model = new User();
        $this->view = new UserView();
    }

    /**
     * Supprime un utilisateur.
     *
     * @param int $id L'identifiant de l'utilisateur à supprimer.
     *
     * @throws Exception Si l'utilisateur n'existe pas.
     *
     * Cette méthode supprime l'utilisateur et, si cet utilisateur a des alertes ou des informations
     * associées, elle les supprime également. Les utilisateurs avec des rôles spécifiques
     * (enseignant, secrétaire, administrateur, directeur d'étude) déclenchent la suppression de leurs alertes
     * et informations.
     */
    public function delete($id) {
        // Récupérer les données de l'utilisateur à supprimer
        $user = $this->model->get($id);
        if (!$user) {
            throw new Exception("Utilisateur non trouvé.");
        }
        $userData = get_userdata($id);
        $user->delete(); // Supprimer l'utilisateur

        // Supprimer les alertes si l'utilisateur a un rôle spécifique
        if (in_array("enseignant", $userData->roles) ||
            in_array("secretaire", $userData->roles) ||
            in_array("administrator", $userData->roles) ||
            in_array("directeuretude", $userData->roles)) {
            $modelAlert = new Alert();
            $alerts = $modelAlert->getAuthorListAlert($user->getLogin());
            foreach ($alerts as $alert) {
                $alert->delete();
            }
        }

        // Supprimer les informations si l'utilisateur a un rôle spécifique
        if (in_array("secretaire", $userData->roles) ||
            in_array("administrator", $userData->roles) ||
            in_array("directeuretude", $userData->roles)) {
            $modelInfo = new Information();
            $infos = $modelInfo->getAuthorListInformation($user->getId());
            foreach ($infos as $info) {
                $goodType = ['img', 'pdf', 'tab', 'event'];
                if (in_array($info->getType(), $goodType)) {
                    $infoController = new InformationController();
                    $infoController->deleteFile($info->getId());
                }
                $modelInfo->delete();
            }
        }
    }

    /**
     * Supprime le compte de l'utilisateur.
     *
     * @return string L'affichage des options de suppression de compte.
     *
     * Cette méthode gère la suppression de compte en vérifiant d'abord le mot de passe de l'utilisateur
     * puis en envoyant un code de désinscription par e-mail. Si le code est validé, le compte est supprimé.
     */
    public function deleteAccount() {
        $action = filter_input(INPUT_POST, 'deleteMyAccount');
        $actionDelete = filter_input(INPUT_POST, 'deleteAccount');
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        if (isset($action)) {
            $password = filter_input(INPUT_POST, 'verifPwd');
            if (wp_check_password($password, $current_user->user_pass)) {
                // Génération d'un code de désinscription
                $code = wp_generate_password();
                if (!empty($user->getCodeDeleteAccount())) {
                    $user->updateCode($code);
                } else {
                    $user->createCode($code);
                }

                // Construction de l'e-mail de désinscription
                $to = $current_user->user_email;
                $subject = "Désinscription à la télé-connecté";
                $message = ' <!DOCTYPE html>
                             <html lang="fr">
                             	<head>
                               		<title>Désnscription à la télé-connecté</title>
                              	</head>
                              	<body>
                               		<p>Bonjour, vous avez décidé de vous désinscrire sur le site de la Télé Connecté</p>
                               		<p> Votre code de désinscription est : ' . $code . '.</p>
                               		<p> Pour vous désinscrire, rendez-vous sur le site : <a href="' . home_url() . '/mon-compte/">Tv Connectée.</p>
                              	</body>
                             </html>';

                $headers = array('Content-Type: text/html; charset=UTF-8');

                // Envoi de l'e-mail
                wp_mail($to, $subject, $message, $headers);
                return $this->view->displayMailSend();
            } else {
                return $this->view->displayWrongPassword();
            }
        } elseif (isset($actionDelete)) {
            $code = filter_input(INPUT_POST, 'codeDelete');
            $userCode = $user->getCodeDeleteAccount();
            if ($code == $userCode) {
                $user->deleteCode();
                $user->delete();
                return $this->view->displayModificationValidate();
            } else {
                echo 'Code ' . $code;
                echo 'User code ' . $userCode;
                return $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayDeleteAccount() . $this->view->displayEnterCode();
    }

    /**
     * Permet à l'utilisateur de choisir de modifier son mot de passe,
     * de supprimer son compte ou de modifier ses groupes.
     *
     * @return string Le contenu des options de modification.
     */
    public function chooseModif() {
        $current_user = wp_get_current_user();
        $string = $this->view->displayStartMultiSelect();

        // Vérifier les rôles de l'utilisateur pour afficher les options appropriées
        if (in_array('etudiant', $current_user->roles)) {
            $string .= $this->view->displayTitleSelect('code', 'Modifier mes codes', true) .
                $this->view->displayTitleSelect('pass', 'Modifier mon mot de passe');
        } else {
            $string .= $this->view->displayTitleSelect('pass', 'Modifier mon mot de passe', true);
        }

        $string .= $this->view->displayTitleSelect('delete', 'Supprimer mon compte') .
            $this->view->displayEndOfTitle();

        if (in_array('etudiant', $current_user->roles)) {
            $string .= $this->view->displayContentSelect('code', $this->modifyCodes(), true) .
                $this->view->displayContentSelect('pass', $this->modifyPwd());
        } else {
            $string .= $this->view->displayContentSelect('pass', $this->modifyPwd(), true);
        }

        $string .= $this->view->displayContentSelect('delete', $this->deleteAccount()) .
            $this->view->displayEndDiv();

        return $string;
    }

    /**
     * Modifie le mot de passe de l'utilisateur.
     *
     * @return string Le contenu de la vue pour modifier le mot de passe.
     *
     * Cette méthode gère le changement de mot de passe après validation du mot de passe actuel
     * de l'utilisateur.
     */
    public function modifyPwd() {
        $action = filter_input(INPUT_POST, 'modifyMyPwd');
        $current_user = wp_get_current_user();
        if (isset($action)) {
            $pwd = filter_input(INPUT_POST, 'verifPwd');
            if (wp_check_password($pwd, $current_user->user_pass)) {
                $newPwd = filter_input(INPUT_POST, 'newPwd');
                wp_set_password($newPwd, $current_user->ID);
                return $this->view->displayModificationPassValidate();
            } else {
                return $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayModifyPassword();
    }

    /**
     * Affiche l'emploi du temps.
     *
     * @param int $code Code ADE de l'emploi du temps.
     * @param bool $allDay Indique si l'emploi du temps est sur toute la journée.
     *
     * @return string|bool Le calendrier affiché ou false en cas d'erreur.
     *
     * Cette méthode utilise R34ICS pour afficher le calendrier à partir d'un URL
     * généré à partir du code ADE.
     */
    public function displaySchedule($code, $allDay = false) {
        global $R34ICS;
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
        );
        return $R34ICS->display_calendar($url, $code, $allDay, $args);
    }

    /**
     * Affiche le lien vers l'emploi du temps de l'année à partir du code dans l'URL.
     *
     * @return string Le contenu de l'emploi du temps ou une option de sélection.
     *
     * Cette méthode récupère l'identifiant à partir de l'URL et affiche l'emploi du temps
     * correspondant à l'année si le code est valide.
     */
    function displayYearSchedule() {
        $id = $this->getMyIdUrl();

        $codeAde = new CodeAde();

        if (is_numeric($id)) {
            $codeAde = $codeAde->get($id);
            if (!is_null($codeAde->getTitle()) && $codeAde->getType() === 'year') {
                return $this->displaySchedule($codeAde->getCode(), true);
            }
        }

        return $this->view->displaySelectSchedule();
    }

    /**
     * Vérifie si un utilisateur avec le même titre ou code existe déjà.
     *
     * @param User $newUser L'utilisateur à vérifier.
     *
     * @return bool true si un utilisateur dupliqué existe, false sinon.
     */
    public function checkDuplicateUser(User $newUser) {
        $codesAde = $this->model->checkUser($newUser->getLogin(), $newUser->getEmail());

        return sizeof($codesAde) > 0;
    }

    /**
     * Modifie les codes ADE pour l'étudiant.
     *
     * @return string Le contenu des modifications de code.
     *
     * Cette méthode gère la mise à jour des codes ADE de l'étudiant après validation
     * des entrées.
     */
    public function modifyCodes() {
        $current_user = wp_get_current_user();
        $codeAde = new CodeAde();
        $this->model = $this->model->get($current_user->ID);

        $action = filter_input(INPUT_POST, 'modifvalider');

        if (isset($action)) {
            $year = filter_input(INPUT_POST, 'modifYear');
            $group = filter_input(INPUT_POST, 'modifGroup');
            $halfGroup = filter_input(INPUT_POST, 'modifHalfgroup');

            if (is_numeric($year) && is_numeric($group) && is_numeric($halfGroup)) {
                $codes = [$year, $group, $halfGroup];
                $codesAde = [];
                foreach ($codes as $code) {
                    if ($code !== 0) {
                        $code = $codeAde->getByCode($code);
                    }
                    $codesAde[] = $code;
                }

                // Validation des types de codes
                if ($codesAde[0]->getType() !== 'year') {
                    $codesAde[0] = 0;
                }

                if ($codesAde[1]->getType() !== 'group') {
                    $codesAde[1] = 0;
                }

                if ($codesAde[2]->getType() !== 'halfGroup') {
                    $codesAde[2] = 0;
                }

                $this->model->setCodes($codesAde);

                if ($this->model->update()) {
                    return $this->view->successMesageChangeCode();
                } else {
                    return $this->view->errorMesageChangeCode();
                }
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayModifyMyCodes($this->model->getCodes(), $years, $groups, $halfGroups);
    }
}
