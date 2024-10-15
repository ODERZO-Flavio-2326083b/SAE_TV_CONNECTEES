<?php

namespace Controllers;

use Models\User;
use Views\TeacherView;

/**
 * Class TeacherController
 *
 * Gère les enseignants (Création, mise à jour, suppression, affichage)
 *
 * @package Controllers
 */
class TeacherController extends UserController implements Schedule
{

    /**
     * Modèle de TeacherController.
     *
     * @var User
     */
    private $model;

    /**
     * Vue de TeacherController.
     *
     * @var TeacherView
     */
    private $view;

    /**
     * Constructeur de TeacherController.
     * Initialise le modèle et la vue.
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TeacherView();
    }

    /**
     * Affiche l'emploi du temps de l'enseignant.
     *
     * @return string Retourne l'emploi du temps ou un message indiquant qu'aucun emploi du temps n'est disponible.
     */
    public function displayMySchedule() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        $schedule = $this->displaySchedule($user->getCodes()[0]->getCode());

        if ($schedule) {
            return $schedule;
        } else {
            return $this->view->displayNoStudy();
        }
    }

    /**
     * Insère tous les enseignants à partir d'un fichier Excel.
     *
     * @return string Retourne le HTML de l'interface d'importation de fichier.
     */
    public function insert() {
        $actionTeacher = filter_input(INPUT_POST, 'importProf');
        if ($actionTeacher) {
            $allowed_extension = array("Xls", "Xlsx", "Csv");
            $extension = ucfirst(strtolower(end(explode(".", $_FILES["excelProf"]["name"]))));

            // Vérifie si l'extension est valide
            if (in_array($extension, $allowed_extension)) {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
                $reader->setReadDataOnly(TRUE);
                $spreadsheet = $reader->load($_FILES["excelProf"]["tmp_name"]);

                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                // Lit la première ligne
                $row = $worksheet->getRowIterator(1, 1);
                $cells = [];

                if (!empty($row)) {
                    foreach ($row as $value) {
                        $cellIterator = $value->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(FALSE);
                        foreach ($cellIterator as $cell) {
                            $cells[] = $cell->getValue();
                        }
                    }
                }

                // Vérifie si c'est un bon fichier
                if ($cells[0] == "Identifiant Ent" && $cells[1] == "Adresse mail" && $cells[2] == "Code") {
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

                        $password = wp_generate_password();

                        $login = $cells[0];
                        $email = $cells[1];
                        $code = $cells[2];
                        if (isset($login) && isset($email)) {

                            $this->model->setLogin($login);
                            $this->model->setPassword($password);
                            $this->model->setEmail($email);
                            $this->model->setRole('enseignant');
                            $this->model->setCodes($code);

                            if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                                $path = $this->getFilePath($code);
                                if (!file_exists($path)) {
                                    $this->addFile($code);
                                }

                                // Envoie un email au nouvel utilisateur
                                $to = $email;
                                $subject = "Inscription à la télé-connecté";
                                $message = '
	                            <html>
	                             	<head>
	                               		<title>Inscription à la télé-connecté</title>
	                              	</head>
	                              	<body>
	                               		<p>Bonjour, vous avez été inscrit sur le site de la Télé Connecté de votre département en tant qu\'enseignant.</p>
	                               		<p>Sur ce site, vous aurez accès à votre emploi du temps, aux informations concernant votre scolarité et vous pourrez poster des alertes.</p>
	                               		<p>Votre identifiant est ' . $login . ' et votre mot de passe est ' . $password . '.</p>
	                               		<p>Veuillez changer votre mot de passe lors de votre première connexion pour plus de sécurité !</p>
	                               		<p>Pour vous connecter, rendez-vous sur le site : <a href="' . home_url() . '"> ' . home_url() . ' </a>.</p>
	                               		<p>Nous vous souhaitons une bonne expérience sur notre site.</p>
	                              	</body>
	                            </html>';

                                $headers = array('Content-Type: text/html; charset=UTF-8');

                                mail($to, $subject, $message, $headers);
                            } else {
                                array_push($doubles, $cells[0]);
                            }
                        }
                    }
                    if (!is_null($doubles[0])) {
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
        return $this->view->displayInsertImportFileTeacher();
    }

    /**
     * Modifie un enseignant.
     *
     * @param User $user L'utilisateur à modifier.
     *
     * @return string Retourne le HTML du formulaire de modification de l'enseignant.
     */
    public function modify($user) {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $action = filter_input(INPUT_POST, 'modifValidate');

        if ($action === 'Valider') {
            $code = filter_input(INPUT_POST, 'modifCode');
            if (is_numeric($code)) {
                $user->setRole('enseignant');
                $user->getCodes()[0]->setCode($code);

                if ($user->update()) {
                    $this->view->displayModificationValidate($linkManageUser);
                }
            }
        }

        return $this->view->modifyForm($user);
    }

    /**
     * Affiche tous les enseignants dans un tableau.
     *
     * @return string Retourne le HTML affichant tous les enseignants.
     */
    public function displayAllTeachers() {
        $users = $this->model->getUsersByRole('enseignant');
        $users = $this->model->getMyCodes($users);
        return $this->view->displayAllTeachers($users);
    }
}
