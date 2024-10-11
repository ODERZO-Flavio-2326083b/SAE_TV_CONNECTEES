<?php

namespace Controllers;

use Models\Information;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Views\InformationView;

/**
 * Class InformationController
 *
 * Contrôleur pour gérer les informations (créer, modifier, supprimer, afficher).
 *
 * Ce contrôleur permet de manipuler les informations dans l'application, incluant la création
 * d'informations, leur modification, ainsi que leur suppression.
 *
 * @package Controllers
 */
class InformationController extends Controller
{
    /**
     * @var Information Modèle utilisé pour la gestion des informations.
     */
    private $model;

    /**
     * @var InformationView Vue utilisée pour afficher les informations.
     */
    private $view;

    /**
     * Constructeur de la classe InformationController.
     *
     * Initialise le modèle et la vue.
     */
    public function __construct() {
        $this->model = new Information();
        $this->view = new InformationView();
    }

    /**
     * Crée une nouvelle information et l'ajoute à la base de données.
     *
     * Cette méthode traite les entrées du formulaire, valide les données,
     * et insère l'information dans la base de données selon le type sélectionné.
     *
     * @return string HTML du formulaire ou message de succès.
     * @throws \PhpOffice\PhpSpreadsheet\Exception En cas d'erreur avec PhpSpreadsheet.
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception En cas d'erreur de lecture avec PhpSpreadsheet.
     */
    public function create() {
        $current_user = wp_get_current_user(); // Récupère l'utilisateur courant.

        // Récupération de toutes les entrées du formulaire.
        $actionText = filter_input(INPUT_POST, 'createText');
        $actionImg = filter_input(INPUT_POST, 'createImg');
        $actionTab = filter_input(INPUT_POST, 'createTab');
        $actionPDF = filter_input(INPUT_POST, 'createPDF');
        $actionEvent = filter_input(INPUT_POST, 'createEvent');

        // Variables pour le titre, le contenu et les dates.
        $title = filter_input(INPUT_POST, 'title');
        $content = filter_input(INPUT_POST, 'content');
        $endDate = filter_input(INPUT_POST, 'expirationDate');
        $creationDate = date('Y-m-d');

        // Définit un titre par défaut si celui-ci est vide.
        if ($title == '') {
            $title = 'Sans titre';
        }

        $information = $this->model;

        // Définit les propriétés de l'information à créer.
        $information->setTitle($title);
        $information->setAuthor($current_user->ID);
        $information->setCreationDate($creationDate);
        $information->setExpirationDate($endDate);
        $information->setAdminId(null);

        // Traitement des différents types d'informations.
        if (isset($actionText)) { // Si le type d'information est du texte.
            $information->setContent($content);
            $information->setType("text");

            // Essaye d'insérer l'information et affiche le résultat.
            if ($information->insert()) {
                $this->view->displayCreateValidate();
            } else {
                $this->view->displayErrorInsertionInfo();
            }
        }

        if (isset($actionImg)) { // Si le type d'information est une image.
            $type = "img";
            $information->setType($type);
            $filename = $_FILES['contentFile']['name'];
            $fileTmpName = $_FILES['contentFile']['tmp_name'];
            $explodeName = explode('.', $filename);
            $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];

            // Validation de l'extension du fichier image.
            if (in_array(end($explodeName), $goodExtension)) {
                $this->registerFile($filename, $fileTmpName, $information);
            } else {
                $this->view->buildModal('Image non valide', '<p>Ce fichier est une image non valide, veuillez choisir une autre image</p>');
            }
        }

        if (isset($actionTab)) { // Si le type d'information est un tableau.
            $type = "tab";
            $information->setType($type);
            $filename = $_FILES['contentFile']['name'];
            $fileTmpName = $_FILES['contentFile']['tmp_name'];
            $explodeName = explode('.', $filename);
            $goodExtension = ['xls', 'xlsx', 'ods'];

            // Validation de l'extension du fichier tableau.
            if (in_array(end($explodeName), $goodExtension)) {
                $this->registerFile($filename, $fileTmpName, $information);
            } else {
                $this->view->buildModal('Tableau non valide', '<p>Ce fichier est un tableau non valide, veuillez choisir un autre tableau</p>');
            }
        }

        if (isset($actionPDF)) { // Si le type d'information est un PDF.
            $type = "pdf";
            $information->setType($type);
            $filename = $_FILES['contentFile']['name'];
            $explodeName = explode('.', $filename);

            // Validation de l'extension du fichier PDF.
            if (end($explodeName) == 'pdf') {
                $fileTmpName = $_FILES['contentFile']['tmp_name'];
                $this->registerFile($filename, $fileTmpName, $information);
            } else {
                $this->view->buildModal('PDF non valide', '<p>Ce fichier est un tableau non PDF, veuillez choisir un autre PDF</p>');
            }
        }

        if (isset($actionEvent)) { // Si le type d'information est un événement.
            $type = 'event';
            $information->setType($type);
            $countFiles = count($_FILES['contentFile']['name']);
            for ($i = 0; $i < $countFiles; $i++) {
                $this->model->setId(null);
                $filename = $_FILES['contentFile']['name'][$i];
                $fileTmpName = $_FILES['contentFile']['tmp_name'][$i];
                $explodeName = explode('.', $filename);
                $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg', 'pdf'];

                // Validation de l'extension des fichiers d'événements.
                if (in_array(end($explodeName), $goodExtension)) {
                    $this->registerFile($filename, $fileTmpName, $information);
                }
            }
        }

        // Retourne le sélecteur avec tous les formulaires.
        return
            $this->view->displayStartMultiSelect() .
            $this->view->displayTitleSelect('text', 'Texte', true) .
            $this->view->displayTitleSelect('image', 'Image') .
            $this->view->displayTitleSelect('table', 'Tableau') .
            $this->view->displayTitleSelect('pdf', 'PDF') .
            $this->view->displayTitleSelect('event', 'Événement') .
            $this->view->displayEndOfTitle() .
            $this->view->displayContentSelect('text', $this->view->displayFormText(), true) .
            $this->view->displayContentSelect('image', $this->view->displayFormImg()) .
            $this->view->displayContentSelect('table', $this->view->displayFormTab()) .
            $this->view->displayContentSelect('pdf', $this->view->displayFormPDF()) .
            $this->view->displayContentSelect('event', $this->view->displayFormEvent()) .
            $this->view->displayEndDiv() .
            $this->view->contextCreateInformation();
    }

    /**
     * Modifie une information existante.
     *
     * Cette méthode vérifie si l'utilisateur a les permissions nécessaires,
     * récupère l'information, puis permet de la modifier ou de la supprimer.
     *
     * @return string HTML du formulaire de modification ou message d'erreur.
     * @throws \PhpOffice\PhpSpreadsheet\Exception En cas d'erreur avec PhpSpreadsheet.
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception En cas d'erreur de lecture avec PhpSpreadsheet.
     */
    public function modify() {
        $id = $_GET['id']; // Récupère l'identifiant de l'information à modifier.

        // Vérifie que l'identifiant est valide et que l'information existe.
        if (empty($id) || is_numeric($id) && !$this->model->get($id)) {
            return $this->view->noInformation();
        }

        $current_user = wp_get_current_user();
        $information = $this->model->get($id);

        // Vérifie si l'utilisateur a les permissions pour modifier l'information.
        if (!(in_array('administrator', $current_user->roles) || in_array('secretaire', $current_user->roles) || $information->getAuthor()->getId() == $current_user->ID)) {
            return $this->view->noInformation();
        }

        // Vérifie si l'information a été validée par un administrateur.
        if (!is_null($information->getAdminId())) {
            return $this->view->informationNotAllowed();
        }

        // Traitement du formulaire de modification.
        $submit = filter_input(INPUT_POST, 'submit');
        if (isset($submit)) {
            $title = filter_input(INPUT_POST, 'title');
            $content = filter_input(INPUT_POST, 'content');
            $endDate = filter_input(INPUT_POST, 'expirationDate');

            // Met à jour le titre et la date d'expiration.
            $information->setTitle($title);
            $information->setExpirationDate($endDate);

            if ($information->getType() == 'text') {
                // Met à jour le contenu si le type est du texte.
                $information->setContent($content);
            } else {
                // Vérifie si un fichier a été téléchargé pour les autres types.
                if ($_FILES["contentFile"]['size'] != 0) {
                    $filename = $_FILES["contentFile"]['name'];
                    if ($information->getType() == 'img') {
                        $explodeName = explode('.', $filename);
                        $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];

                        // Validation de l'extension du fichier image.
                        if (in_array(end($explodeName), $goodExtension)) {
                            $this->deleteFile($information->getId()); // Supprime le fichier existant.
                            $this->registerFile($filename, $_FILES["contentFile"]['tmp_name'], $information);
                        } else {
                            $this->view->buildModal('Image non valide', '<p>Ce fichier est une image non valide, veuillez choisir une autre image</p>');
                        }
                    } else if ($information->getType() == 'pdf') {
                        $explodeName = explode('.', $filename);

                        // Validation de l'extension du fichier PDF.
                        if (end($explodeName) == 'pdf') {
                            $this->deleteFile($information->getId());
                            $this->registerFile($filename, $_FILES["contentFile"]['tmp_name'], $information);
                        } else {
                            $this->view->buildModal('PDF non valide', '<p>Ce fichier est un PDF non valide, veuillez choisir un autre PDF</p>');
                        }
                    } else if ($information->getType() == 'tab') {
                        $explodeName = explode('.', $filename);
                        $goodExtension = ['xls', 'xlsx', 'ods'];

                        // Validation de l'extension du fichier tableau.
                        if (in_array(end($explodeName), $goodExtension)) {
                            $this->deleteFile($information->getId());
                            $this->registerFile($filename, $_FILES["contentFile"]['tmp_name'], $information);
                        } else {
                            $this->view->buildModal('Tableau non valide', '<p>Ce fichier est un tableau non valide, veuillez choisir un autre tableau</p>');
                        }
                    }
                }
            }

            // Met à jour l'information dans la base de données.
            if ($information->update()) {
                $this->view->displayModifyValidate();
            } else {
                $this->view->errorMessageCantAdd();
            }
        }

        // Traitement de la suppression de l'information.
        $delete = filter_input(INPUT_POST, 'delete');
        if (isset($delete)) {
            $information->delete(); // Supprime l'information de la base de données.
            $this->view->displayModifyValidate();
        }

        // Retourne le formulaire de modification pré-rempli avec les données de l'information.
        return $this->view->displayModifyInformationForm($information->getTitle(), $information->getContent(), $information->getExpirationDate(), $information->getType());
    }

    /**
     * Télécharge un fichier dans un répertoire et dans la base de données.
     *
     * Cette méthode gère le téléchargement d'un fichier, l'enregistre dans le système
     * de fichiers et met à jour l'enregistrement de l'information dans la base de données.
     *
     * @param string $filename Le nom du fichier à enregistrer.
     * @param string $tmpName Le nom temporaire du fichier téléchargé.
     * @param Information $entity L'entité information à mettre à jour.
     */
    public function registerFile($filename, $tmpName, $entity) {
        $id = 'temporary'; // Utilisé pour un enregistrement temporaire.
        $extension_upload = strtolower(substr(strrchr($filename, '.'), 1)); // Récupère l'extension du fichier.
        $name = $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $id . '.' . $extension_upload;

        // Télécharge le fichier.
        if ($result = move_uploaded_file($tmpName, $name)) {
            $entity->setContent('temporary content'); // Contenu temporaire pour l'enregistrement.
            if ($entity->getId() == null) {
                $id = $entity->insert(); // Insère une nouvelle entité.
            } else {
                $entity->update(); // Met à jour l'entité existante.
                $id = $entity->getId();
            }
        } else {
            $this->view->errorMessageCantAdd();
        }

        // Vérifie que le téléchargement et l'enregistrement de l'information ont réussi.
        if ($id != 0) {
            $entity->setId($id); // Met à jour l'identifiant de l'entité.

            // Renomme le fichier avec un hash MD5 pour l'unicité.
            $md5Name = $id . md5_file($name);
            rename($name, $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $md5Name . '.' . $extension_upload);

            $content = $md5Name . '.' . $extension_upload;

            $entity->setContent($content); // Définit le contenu de l'entité.
            if ($entity->update()) {
                $this->view->displayCreateValidate(); // Affiche le message de validation.
            } else {
                $this->view->errorMessageCantAdd();
            }
        }
    }

    /**
     * Supprime le fichier lié à l'identifiant fourni.
     *
     * Cette méthode supprime le fichier du système de fichiers correspondant
     * à l'information donnée par l'identifiant.
     *
     * @param int $id L'identifiant de l'information à supprimer.
     */
    public function deleteFile($id) {
        $this->model = $this->model->get($id); // Récupère le modèle d'information.
        $source = $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $this->model->getContent(); // Chemin du fichier à supprimer.
        wp_delete_file($source); // Supprime le fichier du système.
    }

    /**
     * Display all information entities in a paginated format.
     *
     * This method handles the display of all information records,
     * including pagination, content generation, and user permissions.
     * It also manages deletion of records based on user input.
     *
     * @return string The HTML string for displaying the information list.
     */
    public function displayAll() {
        // Count all entities in the model.
        $numberAllEntity = $this->model->countAll();
        $url = $this->getPartOfUrl(); // Get URL parts for pagination.
        $number = filter_input(INPUT_GET, 'number'); // Get number of items per page from GET parameters.
        $pageNumber = 1; // Default to the first page.

        // Check if a valid page number is provided in the URL.
        if (sizeof($url) >= 2 && is_numeric($url[1])) {
            $pageNumber = $url[1];
        }

        // Set the number of records to display per page; default is 25.
        if (isset($number) && !is_numeric($number) || empty($number)) {
            $number = 25;
        }

        // Calculate the starting index for records.
        $begin = ($pageNumber - 1) * $number;

        // Calculate the maximum number of pages.
        $maxPage = ceil($numberAllEntity / $number);

        // Adjust the current page number if it exceeds the maximum.
        if ($maxPage <= $pageNumber && $maxPage >= 1) {
            $pageNumber = $maxPage;
        }

        // Get the current user.
        $current_user = wp_get_current_user();

        // Retrieve the appropriate list of information based on user roles.
        if (in_array('administrator', $current_user->roles) || in_array('secretaire', $current_user->roles)) {
            $informationList = $this->model->getList($begin, $number);
        } else {
            $informationList = $this->model->getAuthorListInformation($current_user->ID, $begin, $number);
        }

        // Prepare header and data for the table display.
        $name = 'Info';
        $header = ['Titre', 'Contenu', 'Date de création', 'Date d\'expiration', 'Auteur', 'Type', 'Modifier'];
        $dataList = [];
        $row = $begin;
        $imgExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];

        // Process each information record for display.
        foreach ($informationList as $information) {
            ++$row;

            // Split the content to determine file type.
            $contentExplode = explode('.', $information->getContent());
            $content = TV_UPLOAD_PATH;

            // Check if the information has an associated admin ID.
            if (!is_null($information->getAdminId())) {
                $content = URL_WEBSITE_VIEWER . TV_UPLOAD_PATH;
            }

            // Prepare the content display based on the type of information.
            if (in_array($information->getType(), ['img', 'pdf', 'event', 'tab'])) {
                if (in_array($contentExplode[1], $imgExtension)) {
                    $content = '<img class="img-thumbnail img_table_ecran" src="' . $content . $information->getContent() . '" alt="' . $information->getTitle() . '">';
                } else if ($contentExplode[1] === 'pdf') {
                    $content = '[pdf-embedder url="' . TV_UPLOAD_PATH . $information->getContent() . '"]';
                } else if ($information->getType() === 'tab') {
                    $content = 'Tableau Excel';
                }
            } else {
                $content = $information->getContent(); // Default to the content text.
            }

            // Determine the type of information for display.
            $type = $information->getType();
            switch ($type) {
                case 'img':
                    $type = 'Image';
                    break;
                case 'pdf':
                    $type = 'PDF';
                    break;
                case 'event':
                    $type = 'Événement';
                    break;
                case 'text':
                    $type = 'Texte';
                    break;
                case 'tab':
                    $type = 'Table Excel';
                    break;
            }

            // Build the data list for display.
            $dataList[] = [
                $row,
                $this->view->buildCheckbox($name, $information->getId()),
                $information->getTitle(),
                $content,
                $information->getCreationDate(),
                $information->getExpirationDate(),
                $information->getAuthor()->getLogin(),
                $type,
                $this->view->buildLinkForModify(esc_url(get_permalink(get_page_by_title('Modifier une information'))) . '?id=' . $information->getId())
            ];
        }

        // Handle the deletion of selected information records.
        $submit = filter_input(INPUT_POST, 'delete');
        if (isset($submit)) {
            if (isset($_REQUEST['checkboxStatusInfo'])) {
                $checked_values = $_REQUEST['checkboxStatusInfo'];
                foreach ($checked_values as $id) {
                    $entity = $this->model->get($id); // Retrieve the entity to be deleted.
                    // Check user permissions for deletion.
                    if (in_array('administrator', $current_user->roles) || in_array('secretaire', $current_user->roles) || $entity->getAuthor()->getId() == $current_user->ID) {
                        $type = $entity->getType();
                        $types = ["img", "pdf", "tab", "event"];
                        if (in_array($type, $types)) {
                            $this->deleteFile($id); // Delete associated file if applicable.
                        }
                        $entity->delete(); // Delete the information record.
                    }
                }
                $this->view->refreshPage(); // Refresh the page after deletion.
            }
        }

        // Build the return string for the display.
        $returnString = "";
        if ($pageNumber == 1) {
            $returnString = $this->view->contextDisplayAll();
        }

        // Return the full display of information.
        return $returnString . $this->view->displayAll($name, 'Informations', $header, $dataList) . $this->view->pageNumber($maxPage, $pageNumber, esc_url(get_permalink(get_page_by_title('Gestion des informations'))), $number);
    }

    /**
     * Check if the end date is today or earlier.
     * If so, delete the associated information and its file.
     *
     * @param int $id The ID of the information entity.
     * @param string $endDate The expiration date of the information.
     * @return void
     */
    public function endDateCheckInfo($id, $endDate) {
        if ($endDate <= date("Y-m-d")) {
            $information = $this->model->get($id); // Retrieve the information record.
            $this->deleteFile($id); // Delete the associated file.
            $information->delete(); // Delete the information record.
        }
    }

    /**
     * Display a slideshow of all information entities.
     *
     * This method handles the generation and display of a slideshow
     * containing all current information, skipping any that have expired.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function informationMain() {
        $informations = $this->model->getList(); // Get all information records.
        $this->view->displayStartSlideshow(); // Start the slideshow display.

        // Process each information record for the slideshow.
        foreach ($informations as $information) {
            $endDate = date('Y-m-d', strtotime($information->getExpirationDate()));
            if (!$this->endDateCheckInfo($information->getId(), $endDate)) {
                // Handle spreadsheet content if type is 'tab'.
                if ($information->getType() == 'tab') {
                    $list = $this->readSpreadSheet(TV_UPLOAD_PATH . $information->getContent());
                    $content = "";
                    foreach ($list as $table) {
                        $content .= $table; // Concatenate all tables for display.
                    }
                    $information->setContent($content);
                }

                // Check if the information has an associated admin ID.
                $adminSite = true;
                if (is_null($information->getAdminId())) {
                    $adminSite = false;
                }

                // Display the slide for the information.
                $this->view->displaySlide($information->getTitle(), $information->getContent(), $information->getType(), $adminSite);
            }
        }

        $this->view->displayEndDiv(); // End the slideshow display.
    }

    /**
     * Register new information by synchronizing with the admin website.
     *
     * This method checks for existing information and updates or deletes
     * them as necessary, while also adding new entries from the admin site.
     *
     * @return void
     */
    public function registerNewInformation() {
        $informationList = $this->model->getFromAdminWebsite(); // Get information from the admin website.
        $myInformationList = $this->model->getAdminWebsiteInformation(); // Get existing information.

        // Update existing information with data from the admin site.
        foreach ($myInformationList as $information) {
            if ($adminInfo = $this->model->getInformationFromAdminSite($information->getId())) {
                if ($information->getTitle() != $adminInfo->getTitle()) {
                    $information->setTitle($adminInfo->getTitle());
                }
                if ($information->getContent() != $adminInfo->getContent()) {
                    $information->setContent($adminInfo->getContent());
                }
                if ($information->getExpirationDate() != $adminInfo->getExpirationDate()) {
                    $information->setExpirationDate($adminInfo->getExpirationDate());
                }
                $information->update(); // Update the information record.
            } else {
                $information->delete(); // Delete if no longer present on the admin site.
            }
        }

        // Add new information entries that are not yet registered.
        foreach ($informationList as $information) {
            $exist = 0;
            foreach ($myInformationList as $myInformation) {
                if ($information->getId() == $myInformation->getAdminId()) {
                    ++$exist; // Check if already exists.
                }
            }
            if ($exist == 0) {
                $information->setAdminId($information->getId());
                $information->insert(); // Insert new information.
            }
        }
    }

    /**
     * Display a full-screen slideshow of event information.
     *
     * This method retrieves all events and displays them in a
     * fullscreen slideshow format.
     *
     * @return void
     */
    public function displayEvent() {
        $events = $this->model->getListInformationEvent(); // Retrieve event information.
        $this->view->displayStartSlideEvent(); // Start the slideshow for events.

        // Process each event for display.
        foreach ($events as $event) {
            $this->view->displaySlideBegin(); // Begin a new slide for the event.
            $extension = explode('.', $event->getContent());
            $extension = end($extension); // Get the file extension.

            // Check file type and display accordingly.
            if ($extension == "pdf") {
                echo '<div class="canvas_pdf" id="' . $event->getContent() . '"></div>';
                // Uncomment the following line to embed PDF.
                // echo do_shortcode('[pdf-embedder url="'.$event->getContent().'"]');
            } else {
                echo '<img src="' . TV_UPLOAD_PATH . $event->getContent() . '" alt="' . $event->getTitle() . '">';
            }

            echo $this->view->displayEndDiv(); // End the slide.
        }

        $this->view->displayEndDiv(); // End the slideshow display.
    }

    /**
     * Read an Excel file and return its contents as an array of HTML tables.
     *
     * @param string $content The path to the Excel file.
     * @return array An array of HTML strings representing tables from the spreadsheet.
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function readSpreadSheet($content) {
        $file = $_SERVER['DOCUMENT_ROOT'] . $content; // Get the full path to the file.

        $extension = ucfirst(strtolower(end(explode(".", $file)))); // Determine the file extension.
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension); // Create a reader based on the extension.
        $reader->setReadDataOnly(true); // Set reader to read data only.
        $spreadsheet = $reader->load($file); // Load the spreadsheet.

        $worksheet = $spreadsheet->getActiveSheet(); // Get the active worksheet.
        $highestRow = $worksheet->getHighestRow(); // Get the highest row number.

        $contentList = []; // Initialize content array.
        $content = ""; // Initialize content string.
        $mod = 0; // Initialize modulo counter.

        // Loop through the rows of the worksheet.
        for ($i = 0; $i < $highestRow; ++$i) {
            $mod = $i % 10; // Check modulo for formatting.
            if ($mod == 0) {
                $content .= '<table class ="table table-bordered tablesize">'; // Start a new table every 10 rows.
            }
            foreach ($worksheet->getRowIterator($i + 1, 1) as $row) {
                $content .= '<tr scope="row">'; // Start a new row.
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Iterate through all cells.
                foreach ($cellIterator as $cell) {
                    $content .= '<td class="text-center">' . $cell->getValue() . '</td>'; // Add cell data.
                }
                $content .= '</tr>'; // End the row.
            }
            if ($mod == 9) {
                $content .= '</table>'; // End the table every 10 rows.
                array_push($contentList, $content); // Store the table.
                $content = ""; // Reset content for the next table.
            }
        }

        // Finalize the last table if it contains any rows.
        if ($mod != 9 && $i > 0) {
            $content .= '</table>';
            array_push($contentList, $content); // Store the last table.
        }

        return $contentList; // Return the list of HTML tables.
    }
}
