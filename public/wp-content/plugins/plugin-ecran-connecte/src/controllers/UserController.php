<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use Models\Department;
use Models\Information;
use Models\User;
use R34ICS;
use Views\StudyDirectorView;
use Views\UserView;

/**
 * Class UserController
 *
 * Gère tous les utilisateurs (Création, mise à jour, suppression)
 *
 * @package Controllers
 */
class UserController extends Controller
{

    /**
     * Modèle d'utilisateur.
     *
     * @var User
     */
    private $model;

    /**
     * Vue d'utilisateur.
     *
     * @var UserView
     */
    private $view;

    /**
     * Constructeur de la classe UserController.
     *
     * Ce constructeur initialise le modèle d'utilisateur et la vue
     * associée à l'interface utilisateur. Il est appelé lors de la
     * création d'une instance de cette classe. Le modèle est utilisé
     * pour interagir avec la base de données des utilisateurs, tandis
     * que la vue gère l'affichage des données à l'utilisateur.
     *
     * - Le modèle `User` est responsable de la logique métier liée aux utilisateurs.
     * - La vue `UserView` est utilisée pour rendre le contenu HTML associé
     *   à l'affichage des utilisateurs.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function __construct() {
        $this->model = new User();
        $this->view = new UserView();
    }

    /**
     * Supprime un utilisateur et toutes les données associées.
     *
     * Cette méthode supprime un utilisateur identifié par son ID. En plus de
     * supprimer l'utilisateur, elle gère la suppression des alertes et des
     * informations associées si l'utilisateur a certains rôles (enseignant,
     * secrétaire, administrateur, directeur d'études).
     *
     * - Récupère l'utilisateur à supprimer en utilisant l'ID fourni.
     * - Supprime les alertes associées à l'utilisateur si son rôle est
     *   "enseignant", "secretaire", "administrator" ou "directeuretude".
     * - Supprime les informations associées à l'utilisateur si son rôle est
     *   "secretaire", "administrator" ou "directeuretude".
     * - Pour chaque information, si son type est "img", "pdf", "tab" ou
     *   "event", le fichier associé est également supprimé.
     *
     * @param int $id L'ID de l'utilisateur à supprimer.
     * @throws Exception Si la suppression de l'utilisateur échoue.
     *
     * @return void
     */
    public function delete($id) {
        $user = $this->model->get($id);
        $userData = get_userdata($id);
        $user->delete();
        if (in_array("enseignant", $userData->roles) || in_array("secretaire", $userData->roles) ||
            in_array("administrator", $userData->roles) || in_array("directeuretude", $userData->roles)) {
            $modelAlert = new Alert();
            $alerts = $modelAlert->getAuthorListAlert($user->getLogin());
            foreach ($alerts as $alert) {
                $alert->delete();
            }
        }

        if (in_array("secretaire", $userData->roles) || in_array("administrator", $userData->roles) ||
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
     * Gère la suppression du compte utilisateur.
     *
     * Cette méthode permet à l'utilisateur de demander la suppression de son compte
     * en envoyant une demande de désinscription, puis de valider la suppression
     * en utilisant un code de désinscription. La méthode suit deux étapes :
     *
     * 1. **Demande de désinscription** : Lorsque l'utilisateur soumet son mot de
     *    passe pour vérifier son identité, un code de désinscription est généré et
     *    envoyé par e-mail à l'utilisateur.
     *
     * 2. **Validation de la désinscription** : L'utilisateur peut ensuite soumettre
     *    le code reçu pour confirmer la suppression de son compte. Si le code
     *    correspond, le compte est supprimé.
     *
     * @return string La vue pour supprimer le compte et entrer le code de désinscription.
     */
    public function deleteAccount() {
        $action = filter_input(INPUT_POST, 'deleteMyAccount');
        $actionDelete = filter_input(INPUT_POST, 'deleteAccount');
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        if (isset($action)) {
            $password = filter_input(INPUT_POST, 'verifPwd');
            if (wp_check_password($password, $current_user->user_pass)) {

                $code = wp_generate_password();
                if (!empty($user->getCodeDeleteAccount())) {
                    $user->updateCode($code);
                } else {
                    $user->createCode($code);
                }

                // Construction de l'e-mail
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
                               		<p> Pour vous désinscrire, rendez-vous sur le site : <a href="' . home_url() . '/mon-compte/"> Tv Connectée.</p>
                              	</body>
                             </html>';

                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail($to, $subject, $message, $headers);
                $this->view->displayMailSend();
            } else {
                $this->view->displayWrongPassword();
            }
        } elseif (isset($actionDelete)) {
            $code = filter_input(INPUT_POST, 'codeDelete');
            $userCode = $user->getCodeDeleteAccount();
            if ($code == $userCode) {
                $user->deleteCode();
                $user->delete();
                $this->view->displayModificationValidate();
            } else {
                echo 'Code ' . $code;
                echo 'User code ' . $userCode;
                $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayDeleteAccount() . $this->view->displayEnterCode();
    }

    /**
     * Affiche les options de modification pour le compte utilisateur.
     *
     * Cette méthode génère une interface utilisateur permettant aux étudiants et aux autres
     * types d'utilisateurs de choisir les options de modification disponibles pour leur
     * compte. Les utilisateurs peuvent modifier leur mot de passe, leurs codes
     * d'accès ou supprimer leur compte.
     *
     * - Pour les étudiants :
     *   - Options disponibles : Modifier mes codes, Modifier mon mot de passe, Supprimer mon compte.
     *
     * - Pour les autres utilisateurs :
     *   - Options disponibles : Modifier mon mot de passe, Supprimer mon compte.
     *
     * @return string La vue affichant les options de modification sélectionnées par l'utilisateur.
     */
    public function chooseModif() {
        $current_user = wp_get_current_user();
        $string = $this->view->displayStartMultiSelect();

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

        $string .= $this->view->displayContentSelect('delete', $this->deleteAccount()) . $this->view->displayEndDiv();

        return $string;
    }

    /**
     * Modifie le mot de passe de l'utilisateur actuel.
     *
     * Cette méthode gère la logique de modification du mot de passe d'un utilisateur connecté.
     * Elle vérifie d'abord si le mot de passe actuel fourni par l'utilisateur est correct,
     * puis, si c'est le cas, elle met à jour le mot de passe avec le nouveau mot de passe spécifié.
     *
     * - Si l'utilisateur fournit un mot de passe correct, le nouveau mot de passe est enregistré,
     *   et une confirmation de la modification est affichée.
     * - Si le mot de passe actuel est incorrect, un message d'erreur est affiché.
     *
     * @return string La vue affichant le formulaire pour modifier le mot de passe
     *                ou un message de confirmation de modification.
     */
    public function modifyPwd() {
        $action = filter_input(INPUT_POST, 'modifyMyPwd');
        $current_user = wp_get_current_user();
        if (isset($action)) {
            $pwd = filter_input(INPUT_POST, 'verifPwd');
            if (wp_check_password($pwd, $current_user->user_pass)) {
                $newPwd = filter_input(INPUT_POST, 'newPwd');
                wp_set_password($newPwd, $current_user->ID);
                $this->view->displayModificationPassValidate();
            } else {
                $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayModifyPassword();
    }

    /**
     * Affiche le calendrier des événements associés à un code donné.
     *
     * Cette méthode récupère et affiche les événements programmés pour un code spécifique,
     * en utilisant la classe R34ICS pour traiter et formater les données du calendrier.
     *
     * @param string $code Le code associé aux événements à afficher.
     *                     Ce code est utilisé pour localiser le fichier du calendrier.
     * @param bool $allDay Indique si les événements à afficher sont des événements
     *                     toute la journée. Par défaut, cette valeur est false.
     *
     * @return string Le contenu HTML généré pour afficher le calendrier des événements
     *                associés au code spécifié.
     *
     * @global R34ICS $R34ICS Instance de la classe R34ICS utilisée pour afficher le calendrier.
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
     * Affiche le calendrier des événements pour une année spécifique.
     *
     * Cette méthode récupère l'identifiant de l'URL de l'année, vérifie sa validité,
     * puis affiche le calendrier des événements associés à cette année.
     * Si l'identifiant n'est pas valide ou si aucun calendrier n'est trouvé,
     * elle renvoie un affichage pour sélectionner un calendrier.
     *
     * @return string Le contenu HTML généré pour afficher le calendrier
     *                de l'année spécifiée, ou un formulaire pour sélectionner
     *                un calendrier si l'identifiant est invalide ou inexistant.
     *
     * @throws Exception Si une erreur se produit lors de la récupération du
     *                   calendrier ou si l'identifiant n'est pas valide.
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
     * Vérifie si un utilisateur avec le même login ou email existe déjà dans la base de données.
     *
     * Cette méthode compare les informations du nouvel utilisateur avec les utilisateurs existants
     * pour déterminer s'il y a des doublons en fonction du login et de l'email.
     * Si un utilisateur correspondant est trouvé, la méthode renvoie true, sinon false.
     *
     * @param User $newUser L'objet utilisateur à vérifier, contenant les informations
     *                      de login et d'email à comparer.
     *
     * @return bool Retourne true si un utilisateur avec le même login ou email existe,
     *              sinon false.
     *
     * @throws Exception Si une erreur se produit lors de la vérification des utilisateurs.
     */
    public function checkDuplicateUser(User $newUser) {
        $codesAde = $this->model->checkUser($newUser->getLogin(), $newUser->getEmail());

        if (sizeof($codesAde) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Modifie les codes associés à l'utilisateur actuel.
     *
     * Cette méthode permet à l'utilisateur de modifier ses codes d'année, de groupe
     * et de demi-groupe. Elle récupère les informations soumises via un formulaire,
     * valide les données, et met à jour les codes de l'utilisateur dans la base de données.
     *
     * Les étapes suivantes sont suivies :
     * 1. Vérification des données soumises (année, groupe, demi-groupe).
     * 2. Récupération des objets CodeAde correspondants.
     * 3. Validation des types de code.
     * 4. Mise à jour des codes de l'utilisateur et notification du succès ou de l'échec de l'opération.
     *
     * @return string Retourne le rendu HTML du formulaire de modification des codes,
     *                y compris les messages de succès ou d'erreur si applicable.
     *
     * @throws Exception Si une erreur se produit lors de la récupération des codes
     *                   ou lors de la mise à jour de l'utilisateur.
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
                    $this->view->successMesageChangeCode();
                } else {
                    $this->view->errorMesageChangeCode();
                }
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayModifyMyCodes($this->model->getCodes(), $years, $groups, $halfGroups);
    }

    public function displayAllDepartement() {
        $deptModel = new Department();
        $dept = $deptModel->getAllDepts();
        return $this->view->displayAllDepartement($dept);
    }
}
