<?php

namespace controllers;

use models\Department;
use models\Information;
use models\Scrapper;
use models\User;
use views\InformationView;


/**
 * Class InformationController
 *
 * Manage information (create, update, delete, display)
 *
 * @package controllers
 */
class InformationController extends Controller
{

    /**
     * @var Information
     */
    private $model;

    /**
     * @var InformationView
     */
    private $view;

    /**
     * Constructeur de la classe.
     *
     * Ce constructeur initialise les instances des modèles et des vues
     * nécessaires pour gérer les informations. Il crée une nouvelle
     * instance de la classe 'Information' pour le modèle et une
     * instance de 'InformationView' pour la vue.
     *
     * @version 1.0
     * @date 2024-10-16
     */
    public function __construct() {
        $this->model = new Information();
        $this->view = new InformationView();
    }

    /**
     * Crée de nouvelles informations à partir des entrées utilisateur.
     *
     * Cette méthode gère la création de différents types d'informations
     * (texte, image, PDF, événement, vidéo, short) en fonction des actions
     * des formulaires soumis. Elle effectue les opérations suivantes :
     *
     * - Récupère l'utilisateur courant.
     * - Récupère les données des formulaires pour chaque type d'information.
     * - Définit les propriétés de l'objet 'Information'.
     * - Insère l'information dans le modèle approprié et gère les fichiers
     *   téléchargés selon leur type.
     *
     * Si le titre est vide, un titre par défaut "Sans titre" est utilisé.
     * La méthode valide les types de fichiers et affiche des messages
     * d'erreur si des fichiers non valides sont soumis. Elle construit
     * également et retourne un formulaire pour permettre à l'utilisateur
     * de créer plus d'informations.
     *
     * @return string HTML du formulaire de création d'informations avec des
     *                options de sélection.
     */
    public function create(): string {

	    $currentUser = wp_get_current_user();
	    $deptModel = new Department();
        $userModel = new User();

	    $isAdmin = in_array("administrator", $currentUser->roles);
	    // Si l'utilisateur actuel est admin, on envoie null car il n'a aucun département, sinon on cherche le département
	    $currDept = $isAdmin ? null : $deptModel->getUserDepartment($currentUser->ID)->getIdDepartment();

	    $allDepts = $deptModel->getAllDepts();

        // Pour toutes les actions
        $actionText = filter_input(INPUT_POST, 'createText');
        $actionImg = filter_input(INPUT_POST, 'createImg');
        $actionPDF = filter_input(INPUT_POST, 'createPDF');
        $actionEvent = filter_input(INPUT_POST, 'createEvent');
	    $actionVideo = filter_input(INPUT_POST, 'createVideo');
	    $actionShort = filter_input(INPUT_POST, 'createShort');

        // Variables
        $title = filter_input(INPUT_POST, 'title');
        $content = filter_input(INPUT_POST, 'content');
        $endDate = filter_input(INPUT_POST, 'expirationDate');
        $creationDate = date('Y-m-d');
		// Si l'utilisateur est un admin, il peut choisir un département, sinon on prend le dpt de l'utilisateur
	    $deptId = $isAdmin ? filter_input(INPUT_POST, 'informationDept') : $currDept;

	    // Si le titre est vide
        if ($title == '') {
            $title = 'Sans titre';
        }

        $information = $this->model;
	    $information->setContent($content);
	    $information->setTitle($title);
	    $information->setAuthor($userModel->get($currentUser->ID));
	    $information->setCreationDate($creationDate);
	    $information->setExpirationDate($endDate);
	    $information->setAdminId(null);
	    $information->setIdDepartment($deptId ?: 0);

	    if (isset($actionText)) {   // Si l'information est un texte

		    $information->setType("text");
            if ($information->insert()) {
                $this->view->displayCreateValidate();
            } else {
                $this->view->displayErrorInsertionInfo();
            }
        }
        if (isset($actionImg)) {  // Si l'information est une image
            $type = "img";
            $information->setType($type);
            $filename = $_FILES['contentFile']['name'];
            $fileTmpName = $_FILES['contentFile']['tmp_name'];
            $explodeName = explode('.', $filename);
            $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg']; // On définit les extensions valides pour nos images
            if (in_array(end($explodeName), $goodExtension)) {
                $this->registerFile($filename, $fileTmpName, $information);
            } else {
                $this->view->buildModal('Image non valide', '<p>Ce fichier est une image non valide, veuillez choisir une autre image</p>');
            }
        }
        if (isset($actionPDF)) { // Si l'information est un PDF
            $type = "pdf";
            $information->setType($type);
            $filename = $_FILES['contentFile']['name'];
            $explodeName = explode('.', $filename);
            if (end($explodeName) == 'pdf') {
                $fileTmpName = $_FILES['contentFile']['tmp_name'];
                $this->registerFile($filename, $fileTmpName, $information);
            } else {
                $this->view->buildModal('PDF non valide', '<p>Ce fichier est un tableau non PDF, veuillez choisir un autre PDF.</p>');
            }
        }
        if (isset($actionEvent)) { // Si l'information est un événement
            $type = 'event';
            $information->setType($type);
            $countFiles = count($_FILES['contentFile']['name']);
            for ($i = 0; $i < $countFiles; $i++) {
                $this->model->setId(null);
                $filename = $_FILES['contentFile']['name'][$i];
                $fileTmpName = $_FILES['contentFile']['tmp_name'][$i];
                $explodeName = explode('.', $filename);
                $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg', 'pdf']; // On définit les extensions valides pour nos événements
                if (in_array(end($explodeName), $goodExtension)) {
                    $this->registerFile($filename, $fileTmpName, $information);
                } else {
					$this->view->buildModal('Fichiers non valide', '<p>Ce fichier n\'est pas valide, merci de choisir d\'autres fichiers.</p>');
                }
            }
        }
	    if (isset($actionShort) || isset($actionVideo)){ // Si l'information est un short ou une vidéo
		    isset($actionShort) ? $type = "short" : $type = "video";
		    $information->setType($type);
		    $filename = $_FILES['contentFile']['name'];
		    $fileTmpName = $_FILES['contentFile']['tmp_name'];
		    $explodeName = explode('.', $filename);
		    $goodExtension = ['mp4', 'mov', 'avi']; // On définit les extensions valides pour nos vidéos/shorts
		    if (in_array(end($explodeName), $goodExtension)) {
			    $this->registerFile($filename, $fileTmpName, $information);
		    } else {
			    $this->view->buildModal('Vidéo non valide', '<p>Ce fichier est une vidéo non valide, veuillez choisir une autre vidéo</p>');
		    }
	    }

        return
            $this->view->displayStartMultiSelect() .
            $this->view->displayTitleSelect('text', 'Texte', true) .
            $this->view->displayTitleSelect('image', 'Image') .
            $this->view->displayTitleSelect('pdf', 'PDF') .
            $this->view->displayTitleSelect('event', 'Événement') .
            $this->view->displayTitleSelect('video', "Vidéos") .
            $this->view->displayTitleSelect('short', "Shorts") .
            $this->view->displayEndOfTitle() .
            $this->view->displayContentSelect('text', $this->view->displayFormText($allDepts, $isAdmin, $currDept), true) .
            $this->view->displayContentSelect('image', $this->view->displayFormImg($allDepts, $isAdmin, $currDept)) .
            $this->view->displayContentSelect('pdf', $this->view->displayFormPDF($allDepts, $isAdmin, $currDept)) .
            $this->view->displayContentSelect('event', $this->view->displayFormEvent($allDepts, $isAdmin, $currDept)) .
            $this->view->displayContentSelect('video', $this->view->displayFormVideo($allDepts, $isAdmin, $currDept)) .
            $this->view->displayContentSelect('short', $this->view->displayFormShort($allDepts, $isAdmin, $currDept)) .
            '</div>' .
            $this->view->contextCreateInformation();
    }

    /**
     * Modifie une information existante en fonction de l'identifiant fourni.
     *
     * Cette méthode gère la modification des informations en vérifiant
     * d'abord si l'utilisateur a les droits nécessaires. Elle permet de
     * mettre à jour le titre, le contenu, la date d'expiration et de
     * remplacer un fichier (image, PDF, vidéo, short) si un nouveau fichier
     * est téléchargé. Elle inclut également une option pour supprimer
     * l'information.
     *
     * Le processus suit les étapes suivantes :
     *
     * - Vérifie si l'identifiant est fourni et valide.
     * - Vérifie les droits d'accès de l'utilisateur actuel.
     * - Charge l'information à modifier.
     * - Gère la mise à jour des champs de l'information.
     * - Gère le téléchargement d'un nouveau fichier, avec des validations
     *   de type de fichier appropriées.
     * - Affiche un message de validation ou d'erreur selon le résultat
     *   de la mise à jour ou de la suppression.
     *
     * Si l'utilisateur n'a pas les droits requis ou si l'information
     * n'existe pas, la méthode retourne un message approprié.
     *
     * @return string HTML du formulaire de modification de l'information
     *                avec les valeurs actuelles.
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function modify(): string
    {
        $id = $_GET['id'];

        if (empty($id) || is_numeric($id) && !$this->model->get($id)) {
            return $this->view->noInformation();
        }

        $deptModel = new Department();
        $currentUser = wp_get_current_user();

        $isAdmin = in_array("administrator", $currentUser->roles);
        // Si l'utilisateur actuel est admin, on envoie null car il n'a aucun département, sinon on cherche le département.
        $currDept = $isAdmin ? null : $deptModel->getUserDepartment($currentUser->ID)->getIdDepartment();

        $allDepts = $deptModel->getAllDepts();

        $information = $this->model->get($id);

        if (!(in_array('administrator', $currentUser->roles) || in_array('secretaire', $currentUser->roles) || $information->getAuthor()->getId() == $currentUser->ID)) {
            return $this->view->noInformation();
        }

        if (!is_null($information->getAdminId())) {
            return $this->view->informationNotAllowed();
        }

        $submit = filter_input(INPUT_POST, 'submit');
        if (isset($submit)) {
            $title = filter_input(INPUT_POST, 'title');
            $content = filter_input(INPUT_POST, 'content');
            $endDate = filter_input(INPUT_POST, 'expirationDate');

            $information->setTitle($title);
            $information->setExpirationDate($endDate);

            if ($information->getType() == 'text') {
                // On met en place une nouvelle information
                $information->setContent($content);
            } else {
                // On change le contenu
                if ($_FILES["contentFile"]['size'] != 0) {
                    echo $_FILES["contentFile"]['size'];
                    $filename = $_FILES["contentFile"]['name'];
                    if ($information->getType() == 'img') { // Si le type est une image
                        $explodeName = explode('.', $filename);
                        $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
                        if (in_array(end($explodeName), $goodExtension)) { // On vérifie que l'extension est correcte
                            $this->deleteFile($information->getId());
                            $this->registerFile($filename, $_FILES["contentFile"]['tmp_name'], $information);
                        } else {
                            $this->view->buildModal('Image non valide', '<p>Ce fichier est une image non valide, veuillez choisir une autre image</p>');
                        }
                    } else if ($information->getType() == 'pdf') { // Si le type est un PDF
                        $explodeName = explode('.', $filename);
                        if (end($explodeName) == 'pdf') { // On vérifie que l'extension est correcte
                            $this->deleteFile($information->getId());
                            $this->registerFile($filename, $_FILES["contentFile"]['tmp_name'], $information);
                        } else {
                            $this->view->buildModal('PDF non valide', '<p>Ce fichier est un PDF non valide, veuillez choisir un autre PDF</p>');
                        }
                    } else if ($information->getType() == 'video' || $information->getType() == 'short') {
                        $explodeName = explode('.', $filename);
                        $goodExtension = ['mp4', 'avi', 'mov'];
                        if (in_array(end($explodeName), $goodExtension)) { // On vérifie que l'extension est correcte
                            $this->deleteFile($information->getId());
                            $this->registerFile($filename, $_FILES["contentFile"]['tmp_name'], $information);

                        } else {
                            $this->view->buildModal('Vidéo non valide', '<p>Ce fichier est une vidéo non valide, veuillez choisir une autre vidéo</p>');
                        }
                    }
                }
            }


            if ($information->update()) {
                $this->view->displayModifyValidate();
            } else {
                $this->view->errorMessageCantAdd();
            }
        }


        $delete = filter_input(INPUT_POST, 'delete');
        if (isset($delete)) {
            $information->delete();
            $this->view->displayModifyValidate();
        }
        return $this->view->displayModifyInformationForm($information->getTitle(), $information->getContent(), $information->getExpirationDate(), $information->getType()
                    ,$allDepts, $isAdmin, $currDept);
    }


    /**
     * Enregistre un fichier téléchargé et met à jour l'entité associée.
     *
     * Cette méthode déplace un fichier temporaire vers un emplacement définitif
     * sur le serveur et met à jour l'entité donnée avec les informations du fichier.
     * Si l'entité n'a pas encore d'identifiant, elle sera insérée dans la base de données.
     * Sinon, les informations existantes seront mises à jour.
     *
     * Le nom du fichier est ensuite modifié pour inclure un hachage MD5 afin de
     * garantir l'unicité. Si l'enregistrement ou le téléchargement échoue, un message d'erreur
     * sera affiché à l'utilisateur.
     *
     * @param string $filename Le nom du fichier téléchargé.
     * @param string $tmpName Le nom temporaire du fichier sur le serveur.
     * @param Information $entity L'entité à laquelle le contenu du fichier est associé.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function registerFile(string $filename, string $tmpName, Information $entity): void {
        $id = 'temporary';
        $extension_upload = strtolower(substr(strrchr($filename, '.'), 1));
        $name = $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $id . '.' . $extension_upload;

        // Upload le fichier
        if ($result = move_uploaded_file($tmpName, $name)) {
            $entity->setContent('temporary content');
            if ($entity->getId() == null) {
                $id = $entity->insert();
            } else {
                $entity->update();
                $id = $entity->getId();
            }
        } else {
            $this->view->errorMessageCantAdd();
        }
        // If the file upload and the upload of the information in the database works
        if ($id != 0) {
            $entity->setId($id);

            $md5Name = $id . md5_file($name);
            rename($name, $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $md5Name . '.' . $extension_upload);

            $content = $md5Name . '.' . $extension_upload;

            $entity->setContent($content);
            if ($entity->update()) {
                $this->view->displayCreateValidate();
            } else {
                $this->view->errorMessageCantAdd();
            }
        }
    }

    /**
     * Supprime le fichier qui est lié à l'identifiant
     *
     * @param $id int Code
     */
    public function deleteFile($id) {
        $this->model = $this->model->get($id);
        $source = $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $this->model->getContent();
        wp_delete_file($source);
    }

    public function displayAll() {
        $numberAllEntity = $this->model->countAll();
        $url = $this->getPartOfUrl();
        $number = filter_input(INPUT_GET, 'number');
        $pageNumber = 1;

	    $deptModel = new Department();

        if (sizeof($url) >= 2 && is_numeric($url[1])) {
            $pageNumber = $url[1];
        }
        if (isset($number) && !is_numeric($number) || empty($number)) {
            $number = 25;
        }
        $begin = ($pageNumber - 1) * $number;
        $maxPage = ceil($numberAllEntity / $number);
        if ($maxPage <= $pageNumber && $maxPage >= 1) {
            $pageNumber = $maxPage;
        }
        $current_user = wp_get_current_user();
        if (in_array('administrator', $current_user->roles)) {
            $informationList = $this->model->getList($begin, $number);
        } else {
            $informationList = $this->model->getInformationsByDeptId($deptModel->getUserDepartment($current_user->ID)->getIdDepartment());
        }

        $name = 'Info';
        $header = ['Titre', 'Contenu', 'Date de création', 'Date d\'expiration', 'Auteur', 'Type', 'Département', 'Modifier'];
        $dataList = [];
        $row = $begin;
        $imgExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
        $videoExtension = ['mp4', 'avi', 'mov'];

        foreach ($informationList as $information) {
            ++$row;

            $contentExplode = explode('.', $information->getContent());

            $content = TV_UPLOAD_PATH;
            if (!is_null($information->getAdminId())) {
                $content = URL_WEBSITE_VIEWER . TV_UPLOAD_PATH;
            }

            if (in_array($information->getType(), ['img', 'pdf', 'event', 'video', 'short'])) {
                if (in_array($contentExplode[1], $imgExtension)) {
                    $content = '<img class="img-thumbnail img_table_ecran" src="' . $content . $information->getContent() . '" alt="' . $information->getTitle() . '">';
                } else if ($contentExplode[1] === 'pdf') {
                    $content = '[pdf-embedder url="' . TV_UPLOAD_PATH . $information->getContent() . '"]';
                }
                else if (in_array($contentExplode[1], $videoExtension)) {
                    $content = '<video src="' . $content . $information->getContent() . '" autoplay muted loop>';
                }

            } else {
                $content = $information->getContent();
            }

            $type = match ($information->getType()) {
                'img' => 'Image',
                'pdf' => 'PDF',
                'event' => 'Événement',
                'text' => 'Texte',
                'video' => 'Video',
                'short' => 'Short',
                default => 'Special',
            };

            $dataList[] = [
                $row,
                $this->view->buildCheckbox($name, $information->getId()),
                $information->getTitle(),
                $content,
                $information->getCreationDate(),
                $information->getExpirationDate(),
                $information->getAuthor()->getLogin(),
                $type,
                $deptModel->get($information->getIdDepartment())->getName(),
                $this->view->buildLinkForModify(
                    esc_url(get_permalink(get_page_by_title_custom('Modifier une information'))) . '?id=' . $information->getId()
                )
            ];

        }

        $submit = filter_input(INPUT_POST, 'delete');
        if (isset($submit)) {
            if (isset($_REQUEST['checkboxStatusInfo'])) {
                $checked_values = $_REQUEST['checkboxStatusInfo'];
                foreach ($checked_values as $id) {
                    $entity = $this->model->get($id);
                    if (in_array('administrator', $current_user->roles) || in_array('secretaire', $current_user->roles) || $entity->getAuthor()->getId() == $current_user->ID) {
                        $type = $entity->getType();
                        $types = ["img", "pdf", "event"];
                        if (in_array($type, $types)) {
                            $this->deleteFile($id);
                        }
                        $entity->delete();
                    }
                }
                $this->view->refreshPage();
            }
        }
        $returnString = "";
        if ($pageNumber == 1) {
            $returnString = $this->view->contextDisplayAll();
        }
        return $returnString . $this->view->displayAll($name, 'Informations', $header, $dataList) . $this->view->pageNumber($maxPage, $pageNumber, esc_url(get_permalink(get_page_by_title_custom('Gestion des informations'))), $number);
    }

    /**
     * Vérifie si la date de fin d'une information est dépassée.
     *
     * Si la date de fin est inférieure ou égale à la date actuelle,
     * l'information associée est supprimée, ainsi que son fichier.
     *
     * @param int $id L'identifiant de l'information à vérifier.
     * @param string $endDate La date de fin au format 'Y-m-d'.
     *
     * @return boolean
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function endDateCheckInfo($id, $endDate) {
        if ($endDate <= date("Y-m-d")) {
            $information = $this->model->get($id);
            $this->deleteFile($id);
            $information->delete();
			return true;
        }
		return false;
    }

    /**
     * Affiche les informations principales sous forme de diaporama.
     *
     * Récupère la liste des informations, vérifie si leur date d'expiration est dépassée,
     * et affiche chaque information en fonction de son type.
     * Pour les tableaux, le contenu est lu à partir d'un fichier et formaté pour l'affichage.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function informationMain() {
		$deptModel = new Department();
        $informations = $this->model->getInformationsByDeptId($deptModel->getUserDepartment(wp_get_current_user()->ID)->getIdDepartment());
        $infoScrapper = new information();
        $infoScrapper->setIdDepartment(1);
        $infoScrapper->setAuthor(1);
        $infoScrapper->setCreationDate(date("2024-12-18"));
        $infoScrapper->setId(27);
        $infoScrapper->setContent("scrapper");
        $infoScrapper->setAdminId(1);
        $infoScrapper->setTitle("Sans titre");
        $infoScrapper->setType("scrapper");
        $infoScrapper->setExpirationDate("2028-12-18");
        $informations[] = $infoScrapper;
        $this->view->displayStartSlideshow();
        foreach ($informations as $information) {
            $endDate = date('Y-m-d', strtotime($information->getExpirationDate()));
            if (!$this->endDateCheckInfo($information->getId(), $endDate)) {
                $adminSite = true;
                if (is_null($information->getAdminId())) {
                    $adminSite = false;
                }
                $this->view->displaySlide($information->getTitle(), $information->getContent(), $information->getType(), new Scrapper(), $adminSite);
            }
        }
        echo '</div>';
    }

    /**
     * Enregistre les nouvelles informations à partir du site administrateur.
     *
     * Récupère la liste des informations du site administrateur et compare
     * avec les informations existantes dans le modèle. Met à jour les informations
     * existantes si nécessaire, ou les supprime si elles ne sont plus présentes.
     * Ajoute également les nouvelles informations qui ne sont pas encore enregistrées.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function registerNewInformation() {
        $informationList = $this->model->getFromAdminWebsite();
        $myInformationList = $this->model->getAdminWebsiteInformation();
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
                $information->update();
            } else {
                $information->delete();
            }
        }
        foreach ($informationList as $information) {
            $exist = 0;
            foreach ($myInformationList as $myInformation) {
                if ($information->getId() == $myInformation->getAdminId()) {
                    ++$exist;
                }
            }
            if ($exist == 0) {
                $information->setAdminId($information->getId());
                $information->insert();
            }
        }
    }

    /**
     * Affiche les événements à partir de la liste d'informations d'événements.
     *
     * Récupère tous les événements enregistrés et les affiche en utilisant
     * le modèle de vue approprié. Les événements peuvent être des fichiers PDF
     * ou des images. Si un événement est un PDF, il est affiché dans une
     * zone spécifiée; sinon, l'image correspondante est affichée.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function displayEvent() {
        $events = $this->model->getListInformationEvent();
        $this->view->displayStartSlideEvent();
        foreach ($events as $event) {
            $this->view->displaySlideBegin();
            $extension = explode('.', $event->getContent());
            $extension = $extension[1];
            if ($extension == "pdf") {
                echo '
				<div class="canvas_pdf" id="' . $event->getContent() . '"></div>';
            } else {
                echo '<img src="' . TV_UPLOAD_PATH . $event->getContent() . '" alt="' . $event->getTitle() . '">';
            }
            echo '</div>';
        }
        echo '</div>';
    }
}
