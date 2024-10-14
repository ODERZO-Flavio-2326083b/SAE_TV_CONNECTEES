<?php

namespace Controllers;

use Models\User;
use Views\TeacherView;

/**
 * Class TeacherController
 *
 * Contrôleur pour gérer les enseignants (création, mise à jour, suppression, affichage).
 *
 * @package Controllers
 */
class TeacherController extends UserController implements Schedule
{
    /**
     * Modèle de TeacherController
     * @var User
     */
    private $model;

    /**
     * Vue de TeacherController
     * @var TeacherView
     */
    private $view;

    /**
     * Constructeur de la classe TeacherController
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
     * @return string|mixed Renvoie l'emploi du temps ou un message d'absence de données.
     */
    public function displayMySchedule() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        $schedule = $this->displaySchedule($user->getCodes()[0]->getCode());

        // Vérifie si l'emploi du temps est disponible
        if ($schedule) {
            return $schedule; // Renvoie l'emploi du temps
        } else {
            return $this->view->displayNoStudy(); // Affiche un message si aucun emploi du temps n'est disponible
        }
    }

    /**
     * Insère tous les enseignants à partir d'un fichier Excel.
     *
     * @return string Renvoie la vue pour importer les enseignants.
     *
     * @throws Exception Lève une exception si le fichier est invalide ou si l'insertion échoue.
     */
    public function insert() {
        $actionTeacher = filter_input(INPUT_POST, 'importProf');

        if ($actionTeacher) {
            $allowed_extension = array("Xls", "Xlsx", "Csv");
            $extension = ucfirst(strtolower(end(explode(".", $_FILES["excelProf"]["name"]))));

            // Vérification de l'extension du fichier
            if (in_array($extension, $allowed_extension)) {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
                $reader->setReadDataOnly(TRUE);
                $spreadsheet = $reader->load($_FILES["excelProf"]["tmp_name"]);

                $worksheet = $spreadsheet->getActiveSheet();
                $highestRow = $worksheet->getHighestRow();

                $row = $worksheet->getRowIterator(1, 1);
                $cells = [];

                // Lecture de la première ligne pour obtenir les en-têtes
                if (!empty($row)) {
                    foreach ($row as $value) {
                        $cellIterator = $value->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(FALSE);
                        foreach ($cellIterator as $cell) {
                            $cells[] = $cell->getValue();
                        }
                    }
                }

                // Vérification des en-têtes du fichier
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

                        $password = wp_generate_password(); // Génération d'un mot de passe aléatoire

                        $login = $cells[0];
                        $email = $cells[1];
                        $code = $cells[2];

                        if (isset($login) && isset($email)) {
                            $this->model->setLogin($login);
                            $this->model->setPassword($password);
                            $this->model->setEmail($email);
                            $this->model->setRole('enseignant');
                            $this->model->setCodes($code);

                            // Vérification des doublons avant insertion
                            if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                                $path = $this->getFilePath($code);
                                if (!file_exists($path)) {
                                    $this->addFile($code); // Ajoute un fichier si nécessaire
                                }

                                // Envoi d'un email au nouvel utilisateur
                                $to = $email;
                                $subject = "Inscription à la télé-connecté";
                                $message = '
	                            <html>
	                             	<head>
	                               		<title>Inscription à la télé-connecté</title>
	                              	</head>
	                              	<body>
	                               		<p>Bonjour, vous avez été inscrit sur le site de la Télé Connecté de votre département en tant qu\'enseignant</p>
	                               		<p> Sur ce site, vous aurez accès à votre emploi du temps, aux informations concernant votre scolarité et vous pourrez poster des alertes.</p>
	                               		<p> Votre identifiant est ' . $login . ' et votre mot de passe est ' . $password . '.</p>
	                               		<p> Veuillez changer votre mot de passe lors de votre première connexion pour plus de sécurité !</p>
	                               		<p> Pour vous connecter, rendez-vous sur le site : <a href="' . home_url() . '"> ' . home_url() . ' </a>.</p>
	                               		<p> Nous vous souhaitons une bonne expérience sur notre site.</p>
	                              	</body>
	                            </html>';

                                $headers = array('Content-Type: text/html; charset=UTF-8');

                                mail($to, $subject, $message, $headers); // Envoi de l'email
                            } else {
                                array_push($doubles, $cells[0]); // Ajout à la liste des doublons
                            }
                        }
                    }
                    // Vérification et affichage des messages appropriés
                    if (!is_null($doubles[0])) {
                        $this->view->displayErrorDouble($doubles);
                    } else {
                        $this->view->displayInsertValidate();
                    }
                } else {
                    $this->view->displayWrongFile(); // Affiche un message d'erreur pour fichier incorrect
                }
            } else {
                $this->view->displayWrongExtension(); // Affiche un message d'erreur pour extension invalide
            }
        }
        return $this->view->displayInsertImportFileTeacher(); // Affiche le formulaire d'importation
    }

    /**
     * Modifie les informations d'un enseignant.
     *
     * @param User $user L'utilisateur à modifier.
     * @return string Renvoie la vue pour modifier l'enseignant.
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
                $user->setRole('enseignant'); // Mise à jour du rôle
                $user->getCodes()[0]->setCode($code); // Mise à jour du code

                if ($user->update()) {
                    $this->view->displayModificationValidate($linkManageUser); // Affiche un message de succès
                }
            }
        }

        return $this->view->modifyForm($user); // Affiche le formulaire de modification
    }

    /**
     * Affiche tous les enseignants dans un tableau.
     *
     * @return string Renvoie la vue contenant tous les enseignants.
     */
    public function displayAllTeachers() {
        // Récupère tous les utilisateurs avec le rôle d'enseignant
        $users = $this->model->getUsersByRole('enseignant');
        $users = $this->model->getMyCodes($users); // Récupération des codes associés
        return $this->view->displayAllTeachers($users); // Affichage de la vue
    }
}
