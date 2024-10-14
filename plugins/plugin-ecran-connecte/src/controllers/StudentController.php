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
     * Insère tous les utilisateurs à partir d'un fichier Excel.
     *
     * @return string Retourne le HTML pour afficher le formulaire d'importation d'étudiants.
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
     * Modifie un étudiant.
     *
     * @param User $user L'utilisateur à modifier.
     *
     * @return string Retourne le HTML du formulaire de modification de l'étudiant.
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
     * Affiche l'emploi du temps de l'étudiant.
     *
     * @return bool|mixed|string Retourne l'emploi du temps ou redirige vers la gestion de l'étudiant.
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
     * Vérifie si l'étudiant a un groupe.
     * Si ce n'est pas le cas, demande de sélectionner des groupes.
     *
     * @param User $user L'utilisateur à gérer.
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
     * Affiche tous les étudiants dans un tableau.
     */
    function displayAllStudents() {
        $users = $this->model->getUsersByRole('etudiant');
        return $this->view->displayAllStudent($users);
    }
}
