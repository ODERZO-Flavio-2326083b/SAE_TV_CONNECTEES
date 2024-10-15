<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\StudentView;

/**
 * Class StudentController
 *
 * Gère les étudiants (Création, mise à jour, suppression, affichage).
 *
 * @package Controllers
 */
class StudentController extends UserController implements Schedule
{

    /**
     * @var User Modèle représentant un utilisateur.
     */
    private $model;

    /**
     * @var StudentView Vue associée au contrôleur des étudiants.
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
     * Insère des étudiants à partir d'un fichier Excel ou CSV.
     *
     * Cette méthode vérifie si un fichier a été téléchargé, valide l'extension du fichier,
     * lit les données du fichier à l'aide de PhpSpreadsheet, et insère chaque étudiant dans la
     * base de données. Elle envoie également un e-mail de confirmation contenant les informations
     * de connexion à chaque étudiant inscrit.
     *
     * @return string Renvoie la vue d'importation de fichier étudiant si aucune action n'a été
     *                effectuée, ou affiche des messages d'erreur ou de succès selon les résultats
     *                de l'importation.
     *
     * @example
     * // Insérer des étudiants à partir d'un fichier téléchargé :
     * $this->insert();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function insert() {
        $actionStudent = filter_input(INPUT_POST, 'importEtu');

        if ($actionStudent) {
            $allowed_extension = array("Xls", "Xlsx", "Csv");
            $extension = ucfirst(strtolower(end(explode(".", $_FILES["excelEtu"]["name"]))));

            // Vérifie si l'extension du fichier est valide.
            if (in_array($extension, $allowed_extension)) {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
                $reader->setReadDataOnly(TRUE);
                $spreadsheet = $reader->load($_FILES["excelEtu"]["tmp_name"]);

                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                $row = $worksheet->getRowIterator(1, 1);
                $cells = [];

                foreach ($row as $value) {
                    $cellIterator = $value->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(FALSE);
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue();
                    }
                }

                // Vérifie si le fichier a le bon format.
                if ($cells[0] == "Identifiant Ent" && $cells[1] == "Adresse mail") {
                    $doubles = array();
                    for ($i = 2; $i < $highestRow + 1; ++$i) {
                        $cells = array();
                        foreach ($worksheet->getRowIterator($i, $i + 1) as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(FALSE);
                            foreach ($cellIterator as $cell) {
                                $cells[] = $cell->getValue();
                            }
                        }

                        $pwd = wp_generate_password();
                        $login = $cells[0];
                        $email = $cells[1];

                        if (isset($login) && isset($email)) {

                            $this->model->setLogin($login);
                            $this->model->setPassword($pwd);
                            $this->model->setEmail($email);
                            $this->model->setRole('etudiant');

                            // Vérifie les utilisateurs en double et insère l'utilisateur.
                            if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {

                                // Génère un mail de confirmation.
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
                                        <p> Sur ce site, vous aurez accès à votre emploi du temps, à vos notes et aux informations concernant votre scolarité.</p>
                                        <p> Votre identifiant est ' . $login . ' et votre mot de passe est ' . $pwd . '.</p>
                                        <p> Veuillez changer votre mot de passe lors de votre première connexion pour plus de sécurité !</p>
                                        <p> Pour vous connecter, rendez-vous sur le site : <a href="' . home_url() . '"> ' . home_url() . ' </a>.</p>
                                        <p> Nous vous souhaitons une bonne expérience sur notre site.</p>
                                    </body>
                                 </html>';

                                $headers = 'Content-Type: text/html; charset=UTF-8';

                                mail($to, $subject, $message, $headers);
                            } else {
                                array_push($doubles, $login);
                            }
                        }
                    }
                    if (sizeof($doubles) > 0) {
                        $this->view->displayErrorDouble($doubles);
                    } else {
                        $this->view->displayInsertValidate();
                    }
                } else {
                    $this->view->displayWrongFile();
                }
            } else {
                $this->view->displayWrongExtension();
            }
        }
        return $this->view->displayInsertImportFileStudent();
    }

    /**
     * Modifie les informations d'un utilisateur spécifié.
     *
     * Cette méthode gère la modification des informations d'un utilisateur, notamment l'année,
     * le groupe et le demi-groupe. Elle vérifie si les valeurs saisies sont numériques, valide les
     * codes associés à l'utilisateur et met à jour les informations dans la base de données.
     * Si la modification est réussie, elle affiche un message de validation.
     *
     * @param User $user L'objet utilisateur dont les informations doivent être modifiées.
     *
     * @return string Renvoie la vue pour modifier l'étudiant, contenant des formulaires pour
     *                saisir les nouvelles informations et éventuellement des messages d'erreur.
     *
     * @example
     * // Modifier les informations d'un utilisateur :
     * $this->modify($user);
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function modify($user) {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $action = filter_input(INPUT_POST, 'modifvalider');

        $codeAde = new CodeAde();

        if ($action == 'Valider') {
            $year = filter_input(INPUT_POST, 'modifYear');
            $group = filter_input(INPUT_POST, 'modifGroup');
            $halfGroup = filter_input(INPUT_POST, 'modifHalfgroup');

            // Vérifie que les valeurs de l'année, du groupe et du demi-groupe sont numériques.
            if (is_numeric($year) && is_numeric($group) && is_numeric($halfGroup)) {

                $codes = [$year, $group, $halfGroup];
                $codesAde = array();
                foreach ($codes as $code) {
                    if ($code !== 0) {
                        $code = $codeAde->getByCode($code);
                    }
                    $codesAde[] = $code;
                }

                // Vérifie que les codes sont valides.
                if ($codesAde[0]->getType() !== 'year') {
                    $codesAde[0] = 0;
                }

                if ($codesAde[1]->getType() !== 'group') {
                    $codesAde[1] = 0;
                }

                if ($codesAde[2]->getType() !== 'halfGroup') {
                    $codesAde[2] = 0;
                }

                $user->setCodes($codesAde);
                if ($user->update()) {
                    $this->view->displayModificationValidate($linkManageUser);
                }
            }
        }

        // Récupère les années, groupes et demi-groupes.
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayModifyStudent($user, $years, $groups, $halfGroups);
    }

    /**
     * Affiche l'emploi du temps de l'utilisateur courant.
     *
     * Cette méthode récupère l'utilisateur actuellement connecté et vérifie s'il possède
     * des codes associés. Si des codes sont trouvés, elle tente de localiser un fichier d'emploi
     * du temps correspondant à chacun de ces codes, en commençant par le plus récent.
     * Si un fichier d'emploi du temps est trouvé, il est affiché à l'utilisateur.
     * Si aucun emploi du temps n'est trouvé pour les codes de l'utilisateur, la méthode appelle
     * la fonction pour gérer les informations de l'étudiant.
     *
     * @return string|null Retourne l'affichage de l'emploi du temps si trouvé, sinon gère
     *                    l'affichage des informations de l'étudiant.
     *
     * @example
     * // Afficher l'emploi du temps de l'utilisateur courant :
     * $this->displayMySchedule();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayMySchedule() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);

        if (sizeof($user->getCodes()) > 0) {
            $codes = array_reverse($user->getCodes());
            //$codes = [$user->getCodes()[2], $user->getCodes()[1], $user->getCodes()[0]];
            foreach ($codes as $code) {
                if ($code instanceof CodeAde) {
                    if (file_exists($this->getFilePath($code->getCode()))) {
                        return $this->displaySchedule($code->getCode());
                    }
                }
            }
        }
        $this->manageStudent($user);
    }

    /**
     * Gère les horaires de l'étudiant.
     *
     * Cette méthode récupère les codes associés à l'étudiant et permet à l'utilisateur
     * de sélectionner un année, un groupe, et un demi-groupe pour ajouter des horaires.
     * Elle vérifie que les valeurs sélectionnées sont valides et met à jour les codes de
     * l'utilisateur si les sélections sont correctes. Enfin, elle rafraîchit la page pour
     * refléter les changements.
     *
     * @param User $user L'utilisateur dont les horaires sont gérés.
     *
     * @return string Affiche le formulaire de sélection des horaires pour l'étudiant.
     *
     * @example
     * // Gérer les horaires d'un étudiant :
     * $this->manageStudent($user);
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function manageStudent($user) {
        $codeAde = new CodeAde();

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        $action = filter_input(INPUT_POST, 'addSchedules');

        if (isset($action)) {

            $year = filter_input(INPUT_POST, 'selectYears');
            $group = filter_input(INPUT_POST, 'selectGroups');
            $halfGroup = filter_input(INPUT_POST, 'selectHalfgroups');

            // Vérifie que les valeurs sélectionnées sont valides.
            if ((is_numeric($year) || $year == 0) && (is_numeric($group) || $group == 0) && (is_numeric($halfGroup) || $halfGroup == 0)) {

                $codes = [$year, $group, $halfGroup];
                $codesAde = [];
                foreach ($codes as $code) {
                    if ($code !== 0) {
                        $code = $codeAde->getByCode($code);
                    }
                    $codesAde[] = $code;
                }

                // Vérifie que les codes sont valides.
                if ($codesAde[0]->getType() !== 'year') {
                    $codesAde[0] = 0;
                }

                if ($codesAde[1]->getType() !== 'group') {
                    $codesAde[1] = 0;
                }

                if ($codesAde[2]->getType() !== 'halfGroup') {
                    $codesAde[2] = 0;
                }

                $user->setCodes($codesAde);
                $user->update();
                $this->view->refreshPage();
            }
        }
        return $this->view->selectSchedules($years, $groups, $halfGroups);
    }

    /**
     * Affiche tous les étudiants enregistrés.
     *
     * Cette méthode récupère tous les utilisateurs ayant le rôle d'étudiant dans le
     * système et les affiche à l'aide de la vue appropriée. Elle permet ainsi de
     * visualiser la liste des étudiants disponibles dans l'application.
     *
     * @return string Affiche la liste de tous les étudiants.
     *
     * @example
     * // Afficher tous les étudiants enregistrés :
     * $this->displayAllStudents();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    function displayAllStudents() {
        $users = $this->model->getUsersByRole('etudiant');
        return $this->view->displayAllStudent($users);
    }
}
