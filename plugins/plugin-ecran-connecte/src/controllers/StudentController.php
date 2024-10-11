<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\StudentView;

/**
 * Class StudentController
 *
 * Gère les étudiants : création, mise à jour, suppression et affichage.
 *
 * @package Controllers
 */
class StudentController extends UserController implements Schedule
{
    /**
     * @var User $model Modèle de l'utilisateur, utilisé pour interagir avec les données des utilisateurs.
     */
    private $model;

    /**
     * @var StudentView $view Vue dédiée à l'affichage des informations des étudiants.
     */
    public $view;

    /**
     * Constructeur de la classe StudentController.
     * Initialise le modèle et la vue des étudiants.
     */
    public function __construct() {
        parent::__construct(); // Appelle le constructeur de la classe parente UserController.
        $this->model = new User(); // Instanciation du modèle User.
        $this->view = new StudentView(); // Instanciation de la vue StudentView.
    }

    /**
     * Insère tous les utilisateurs présents dans le fichier Excel.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception Si le fichier Excel ne peut pas être lu.
     * @throws \PhpOffice\PhpSpreadsheet\Exception Si une erreur se produit lors du chargement du fichier.
     *
     * @return string HTML pour afficher le formulaire d'importation d'étudiants.
     */
    public function insert() {
        $actionStudent = filter_input(INPUT_POST, 'importEtu'); // Récupère l'action d'importation.

        if ($actionStudent) {
            $allowed_extension = ["Xls", "Xlsx", "Csv"]; // Extensions autorisées pour les fichiers Excel.
            $extension = ucfirst(strtolower(end(explode(".", $_FILES["excelEtu"]["name"])))); // Récupère l'extension du fichier.

            // Vérifie si l'extension est correcte.
            if (in_array($extension, $allowed_extension)) {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension); // Crée un lecteur pour le fichier Excel.
                $reader->setReadDataOnly(TRUE); // Définit le lecteur pour ne lire que les données.
                $spreadsheet = $reader->load($_FILES["excelEtu"]["tmp_name"]); // Charge le fichier Excel.

                $worksheet = $spreadsheet->getActiveSheet(); // Récupère la feuille active.
                $highestRow = $worksheet->getHighestRow(); // Récupère le nombre de la dernière ligne.

                // Récupère les valeurs de la première ligne.
                $row = $worksheet->getRowIterator(1, 1);
                $cells = [];

                foreach ($row as $value) {
                    $cellIterator = $value->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(FALSE); // Itère sur toutes les cellules, même celles vides.
                    foreach ($cellIterator as $cell) {
                        $cells[] = $cell->getValue(); // Ajoute la valeur de chaque cellule dans le tableau $cells.
                    }
                }

                // Vérifie si le fichier contient les bonnes colonnes.
                if ($cells[0] == "Identifiant Ent" && $cells[1] == "Adresse mail") {
                    $doubles = []; // Tableau pour stocker les doublons trouvés.
                    for ($i = 2; $i < $highestRow + 1; ++$i) {
                        $cells = [];
                        foreach ($worksheet->getRowIterator($i, $i + 1) as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(FALSE);
                            foreach ($cellIterator as $cell) {
                                $cells[] = $cell->getValue(); // Récupère les valeurs des cellules de la ligne.
                            }
                        }

                        $pwd = wp_generate_password(); // Génère un mot de passe aléatoire.
                        $login = $cells[0]; // Récupère le login depuis le fichier.
                        $email = $cells[1]; // Récupère l'email depuis le fichier.

                        // Vérifie que le login et l'email sont valides.
                        if (isset($login) && isset($email)) {
                            $this->model->setLogin($login); // Définit le login de l'utilisateur.
                            $this->model->setPassword($pwd); // Définit le mot de passe de l'utilisateur.
                            $this->model->setEmail($email); // Définit l'email de l'utilisateur.
                            $this->model->setRole('etudiant'); // Définit le rôle de l'utilisateur comme 'étudiant'.

                            // Vérifie les doublons avant d'insérer l'utilisateur dans la base de données.
                            if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                                // Envoie un email d'inscription à l'étudiant.
                                $to = $email; // Adresse de destination.
                                $subject = "Inscription à la télé-connecté"; // Sujet de l'email.
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

                                $headers = 'Content-Type: text/html; charset=UTF-8'; // Définit le type de contenu de l'email.

                                // Envoie l'email.
                                mail($to, $subject, $message, $headers);
                            } else {
                                // Si un doublon est trouvé, ajoute le login à la liste des doublons.
                                array_push($doubles, $login);
                            }
                        }
                    }
                    // Affiche les erreurs s'il y a des doublons.
                    if (sizeof($doubles) > 0) {
                        $this->view->displayErrorDouble($doubles);
                    } else {
                        $this->view->displayInsertValidate(); // Affiche une validation de l'insertion.
                    }
                } else {
                    $this->view->displayWrongFile(); // Affiche un message d'erreur pour un fichier incorrect.
                }
            } else {
                $this->view->displayWrongExtension(); // Affiche un message d'erreur pour une extension de fichier non valide.
            }
        }
        return $this->view->displayInsertImportFileStudent(); // Affiche le formulaire d'importation d'étudiants.
    }

    /**
     * Modifie les informations d'un étudiant.
     *
     * @param User $user Utilisateur à modifier.
     *
     * @return string HTML pour afficher le formulaire de modification de l'étudiant.
     */
    public function modify($user) {
        $page = get_page_by_title('Gestion des utilisateurs'); // Récupère la page de gestion des utilisateurs.
        $linkManageUser = get_permalink($page->ID); // Obtient le lien de la page.

        $action = filter_input(INPUT_POST, 'modifvalider'); // Récupère l'action de validation de modification.

        $codeAde = new CodeAde(); // Instancie CodeAde pour accéder aux codes.

        if ($action == 'Valider') { // Si l'utilisateur valide la modification.
            $year = filter_input(INPUT_POST, 'modifYear'); // Récupère l'année.
            $group = filter_input(INPUT_POST, 'modifGroup'); // Récupère le groupe.
            $halfGroup = filter_input(INPUT_POST, 'modifHalfgroup'); // Récupère le demi-groupe.

            // Vérifie que les valeurs sont numériques.
            if (is_numeric($year) && is_numeric($group) && is_numeric($halfGroup)) {
                $codes = [$year, $group, $halfGroup];
                $codesAde = []; // Tableau pour stocker les codes vérifiés.

                foreach ($codes as $code) {
                    if ($code !== 0) {
                        $code = $codeAde->getByCode($code); // Récupère le code correspondant.
                    }
                    $codesAde[] = $code; // Ajoute le code au tableau.
                }

                // Vérifie les types de codes et les met à zéro si le type ne correspond pas.
                if ($codesAde[0]->getType() !== 'year') {
                    $codesAde[0] = 0;
                }

                if ($codesAde[1]->getType() !== 'group') {
                    $codesAde[1] = 0;
                }

                if ($codesAde[2]->getType() !== 'halfGroup') {
                    $codesAde[2] = 0;
                }

                $user->setCodes($codesAde); // Définit les codes de l'utilisateur.
                if ($user->update()) { // Met à jour l'utilisateur et vérifie si cela a réussi.
                    $this->view->displayModificationValidate($linkManageUser); // Affiche la validation de la modification.
                }
            }
        }

        // Récupère tous les codes des différentes catégories.
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayModifyStudent($user, $years, $groups, $halfGroups); // Affiche le formulaire de modification de l'étudiant.
    }

    /**
     * Affiche l'emploi du temps de l'étudiant.
     *
     * @return bool|string Retourne l'emploi du temps de l'étudiant ou false si non trouvé.
     */
    public function displayMySchedule() {
        $current_user = wp_get_current_user(); // Récupère l'utilisateur courant.
        $user = $this->model->get($current_user->ID); // Récupère les informations de l'utilisateur.

        if (sizeof($user->getCodes()) > 0) { // Vérifie si l'utilisateur a des codes associés.
            $codes = array_reverse($user->getCodes()); // Inverse l'ordre des codes pour l'affichage.
            foreach ($codes as $code) {
                if ($code instanceof CodeAde) { // Vérifie que l'objet est de type CodeAde.
                    if (file_exists($this->getFilePath($code->getCode()))) { // Vérifie si le fichier de l'emploi du temps existe.
                        return $this->displaySchedule($code->getCode()); // Affiche l'emploi du temps.
                    }
                }
            }
        }
        $this->manageStudent($user); // Gère les cas où l'étudiant n'a pas de groupe.
    }

    /**
     * Vérifie si l'étudiant a un groupe.
     * Si ce n'est pas le cas, demande de sélectionner des groupes.
     *
     * @param User $user Utilisateur à gérer.
     *
     * @return string HTML pour afficher les options de groupe.
     */
    public function manageStudent($user) {
        $codeAde = new CodeAde(); // Instancie CodeAde pour accéder aux codes.

        // Récupère tous les codes des différentes catégories.
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        $action = filter_input(INPUT_POST, 'addSchedules'); // Récupère l'action d'ajout d'emploi du temps.

        if (isset($action)) { // Si l'utilisateur demande d'ajouter des emplois du temps.
            $year = filter_input(INPUT_POST, 'selectYears'); // Récupère l'année sélectionnée.
            $group = filter_input(INPUT_POST, 'selectGroups'); // Récupère le groupe sélectionné.
            $halfGroup = filter_input(INPUT_POST, 'selectHalfgroups'); // Récupère le demi-groupe sélectionné.

            // Vérifie que les valeurs sont numériques ou zéro.
            if ((is_numeric($year) || $year == 0) && (is_numeric($group) || $group == 0) && (is_numeric($halfGroup) || $halfGroup == 0)) {
                $codes = [$year, $group, $halfGroup];
                $codesAde = []; // Tableau pour stocker les codes vérifiés.

                foreach ($codes as $code) {
                    if ($code !== 0) {
                        $code = $codeAde->getByCode($code); // Récupère le code correspondant.
                    }
                    $codesAde[] = $code; // Ajoute le code au tableau.
                }

                // Vérifie les types de codes et les met à zéro si le type ne correspond pas.
                if ($codesAde[0]->getType() !== 'year') {
                    $codesAde[0] = 0;
                }

                if ($codesAde[1]->getType() !== 'group') {
                    $codesAde[1] = 0;
                }

                if ($codesAde[2]->getType() !== 'halfGroup') {
                    $codesAde[2] = 0;
                }

                $user->setCodes($codesAde); // Définit les codes de l'utilisateur.
                $user->update(); // Met à jour l'utilisateur.
                $this->view->refreshPage(); // Rafraîchit la page.
            }
        }
        return $this->view->selectSchedules($years, $groups, $halfGroups); // Affiche les options de sélection des groupes.
    }

    /**
     * Affiche tous les étudiants dans un tableau.
     *
     * @return string HTML pour afficher la liste des étudiants.
     */
    function displayAllStudents() {
        $users = $this->model->getUsersByRole('etudiant'); // Récupère tous les utilisateurs ayant le rôle 'étudiant'.
        return $this->view->displayAllStudent($users); // Affiche la liste des étudiants.
    }
}
