<?php
/**
 * Fichier InformationController.php
 *
 * Ce fichier contient la classe 'InformationController', qui gère les informations
 * avec ses différentes fonctions (créer, mettre à jour, supprimer, afficher).
 *
 * PHP version 8.3
 *
 * @category API
 * @package  Controllers
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/InformationController
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace controllers;

use getID3;
use models\CodeAde;
use models\Department;
use models\Information;
use models\Scraper;
use models\User;
use views\InformationView;


/**
 * Class InformationController
 *
 * Gère les informations avec ces différentes fonctions : créer, mettre à jour,
 * supprimer, afficher.
 *
 * @category API
 * @package  Controllers
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/InformationController Documentation de
 * la classe
 * @since    2025-01-07
 */
class InformationController extends Controller
{

    /**
     * Permet d'utiliser le model de l'Information
     *
     * @var Information
     */
    private $_model;

    /**
     * Permet d'utiliser la vue de l'Information
     *
     * @var InformationView
     */
    private $_view;

    private $_modelScraping;

    /**
     * Constructeur de la classe.
     *
     * Ce constructeur initialise les instances des modèles et des vues
     * nécessaires pour gérer les informations. Il crée une nouvelle
     * instance de la classe 'Information' pour le modèle et une
     * instance de 'InformationView' pour la vue.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function __construct()
    {
        $this->_model = new Information();
        $this->_view = new InformationView();
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
    public function create(): string
    {
        $currentUser = wp_get_current_user();
        $deptModel = new Department();
        $userModel = new User();

        $allDepts = $deptModel->getAllDepts();

        // Pour toutes les actions
        $actionText = filter_input(INPUT_POST, 'createText');
        $actionImg = filter_input(INPUT_POST, 'createImg');
        $actionPDF = filter_input(INPUT_POST, 'createPDF');
        $actionEvent = filter_input(INPUT_POST, 'createEvent');
        $actionVideo = filter_input(INPUT_POST, 'createVideo');
        $actionShort = filter_input(INPUT_POST, 'createShort');
        $actionScraping = filter_input(INPUT_POST, 'createScraping');

        // Variables
        $title = filter_input(INPUT_POST, 'title');
        $content = filter_input(INPUT_POST, 'content');
        $endDate = filter_input(INPUT_POST, 'expirationDate');
        $creationDate = date('Y-m-d');
        // Si l'utilisateur est un admin, il peut choisir un département, sinon on
        // prend le dpt de l'utilisateur

        $codes = filter_input(
            INPUT_POST, 'informationCodes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY
        );

        // Si le titre est vide
        if ($title == '') {
            $title = 'Sans titre';
        }
        $information = $this->_model;
        $information->setContent($content);
        $information->setTitle($title);
        $information->setAuthor($userModel->get($currentUser->ID));
        $information->setCreationDate($creationDate);
        $information->setExpirationDate($endDate);
        $information->setAdminId(null);
        $information->setDuration(10000);   // Durée par défaut
                                                    // d'une information

        $codeAde = new CodeAde();

        $codesObjects = array();

        if (isset($codes)) {
            foreach ( $codes as $code ) {
                if (is_numeric($code) && $code > 0 ) {
                    if (is_null($codeAde->getByCode($code)->getId()) ) {
                        return 'error';
                    } else {
                        $codesObjects[] = $codeAde->getByCode($code);
                    }
                }
            }
            $information->setCodesAde($codesObjects);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (! empty($codesObjects) ) {
                if (isset($actionText) ) {   // Si l'information est un texte

                    $information->setType("text");
                    if ($information->insert() ) {
                        $this->_view->displayCreateValidate();
                    } else {
                        $this->_view->displayErrorInsertionInfo();
                    }
                }
                if (isset($actionImg) ) {  // Si l'information est une image
                    $type = "img";
                    $information->setType($type);
                    $filename      = $_FILES['contentFile']['name'];
                    $fileTmpName   = $_FILES['contentFile']['tmp_name'];
                    $explodeName   = explode('.', $filename);
                    $goodExtension = [ 'jpg', 'jpeg', 'gif', 'png', 'svg' ];
                    // On définit les extensions valides pour nos images
                    if (in_array(end($explodeName), $goodExtension) ) {
                        $this->registerFile($filename, $fileTmpName, $information);
                    } else {
                        $this->_view->buildModal(
                            'Image non valide', '<p>Ce fichier est une 
image non valide, veuillez choisir une autre image</p>'
                        );
                    }
                }
                if (isset($actionPDF) ) { // Si l'information est un PDF
                    $type = "pdf";
                    $information->setType($type);
                    $filename    = $_FILES['contentFile']['name'];
                    $explodeName = explode('.', $filename);
                    if (end($explodeName) == 'pdf' ) {
                        $fileTmpName = $_FILES['contentFile']['tmp_name'];
                        $this->registerFile($filename, $fileTmpName, $information);
                    } else {
                        $this->_view->buildModal(
                            'PDF non valide', '<p>Ce fichier est un PDF 
non valide, veuillez choisir un autre PDF.</p>'
                        );
                    }
                }
                if (isset($actionEvent) ) { // Si l'information est un événement
                    $type = 'event';
                    $information->setType($type);
                    $countFiles = count($_FILES['contentFile']['name']);
                    for ( $i = 0; $i < $countFiles; $i ++ ) {
                        $this->_model->setId(null);
                        $filename      = $_FILES['contentFile']['name'][ $i ];
                        $fileTmpName   = $_FILES['contentFile']['tmp_name'][ $i ];
                        $explodeName   = explode('.', $filename);
                        $goodExtension = [
                            'jpg',
                            'jpeg',
                            'gif',
                            'png',
                            'svg',
                            'pdf'
                        ];
                        // On définit les extensions valides pour nos événements
                        if (in_array(end($explodeName), $goodExtension) ) {
                            $this->registerFile(
                                $filename, $fileTmpName,
                                $information
                            );
                        } else {
                            $this->_view->buildModal(
                                'Fichiers non valide', '<p>Ce fichier 
n\'est pas valide, merci de choisir d\'autres fichiers.</p>'
                            );
                        }
                    }
                }
                if (isset($actionShort) || isset($actionVideo) ) {
                    // Si l'information est un short ou une vidéo
                    isset($actionShort) ? $type = "short" : $type = "video";
                    $information->setType($type);
                    $filename      = $_FILES['contentFile']['name'];
                    $fileTmpName   = $_FILES['contentFile']['tmp_name'];
                    $explodeName   = explode('.', $filename);
                    $goodExtension = [ 'mp4', 'webm' ];
                    // On définit les extensions valides pour nos vidéos/shorts
                    if (in_array(end($explodeName), $goodExtension) ) {
                        $this->registerFile($filename, $fileTmpName, $information);
                    } else {
                        $this->_view->buildModal(
                            'Vidéo non valide', '<p>Ce fichier est une 
vidéo non valide, veuillez choisir une autre vidéo</p>'
                        );
                    }
                }
                if (isset($actionScraping)) {
                    $information->setType("scraping");
                    $tags = filter_input(
                        INPUT_POST, 'tag',
                        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY
                    );
                    $contentsScraper = filter_input(
                        INPUT_POST, 'contentScraper',
                        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY
                    );

                    if ($id = $information->insert()) {
                        $information->setId($id);
                        $information->insertScrapingTags($tags, $contentsScraper);
                        $this->_view->displayCreateValidate();
                    } else {
                        $this->_view->displayErrorInsertionInfo();
                    }
                }

            } else {
                $this->_view->buildModal(
                    'Emplois du temps insuffisants',
                    "Aucun emploi du temps n'a été fourni, 
                merci d'en fournir au moins un." 
                );
            }
        }

        $buildArgs = $this->getAllAvailableCodes();
        $titles = ['Image', 'PDF', 'Événement', 'Vidéo', 'Short', 'Scraping'];
        $contentTypes = ['image', 'pdf', 'event', 'video', 'short', 'scraping'];


        // immonde à lire, mais map la fonction displayTitleSelect
        // pour chaque title et contentType, et concatène un contentType à
        // 'displayForm' pour avoir une fonction (ex: displayFormText())
        // pour éviter les répétitions. - flavio
        return $this->_view->displayStartMultiSelect() .
               $this->_view->displayTitleSelect('text', 'Texte', true).
            implode(
                '', array_map(
                    fn($title, $type) =>
                     $this->_view->displayTitleSelect($type, $title),
                    $titles, $contentTypes
                )
            ) .
               $this->_view->displayEndOfTitle() .
            $this->_view->displayContentSelect(
                'text',
                $this->_view->displayFormText($allDepts, $buildArgs),
                true
            ) .
            implode(
                '', array_map(
                    fn($type)
                    => $this->_view->displayContentSelect(
                        $type,
                        $this->_view->{"displayForm" . ucfirst($type)}(
                            $allDepts,
                            $buildArgs
                        )
                    ),
                    $contentTypes
                )
            ).
               '</div></div>' .
               $this->_view->contextCreateInformation();
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

        if (empty($id) || is_numeric($id) && !$this->_model->get($id)) {
            return $this->_view->noInformation();
        }

        $deptModel = new Department();
        $currentUser = wp_get_current_user();

        $isAdmin = current_user_can('admin_perms');

        $allDepts = $deptModel->getAllDepts();

        $information = $this->_model->get($id);

        if (!(current_user_can('edit_information') 
            || $information->getAuthor()->getId() == $currentUser->ID)
        ) {
            return $this->_view->noInformation();
        }

        if (!is_null($information->getAdminId())) {
            return $this->_view->informationNotAllowed();
        }

        $submit = filter_input(INPUT_POST, 'submit');
        if (isset($submit)) {
            $title   = filter_input(INPUT_POST, 'title');
            $content = filter_input(INPUT_POST, 'content');
            $endDate = filter_input(INPUT_POST, 'expirationDate');
            $information->setTitle($title);
            $information->setExpirationDate($endDate);

            $codes = filter_input(
                INPUT_POST, 'informationCodes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY
            );

            $codeAde = new CodeAde();

            $codesObjects = array();
            foreach ( (array)$codes as $code ) {
                if (is_numeric($code) && $code > 0 ) {
                    if (is_null($codeAde->getByCode($code)->getId()) ) {
                        return 'error'; // Code invalide
                    } else {
                        $codesObjects[] = $codeAde->getByCode($code);
                    }
                }
            }

            $information->setCodesAde($codesObjects);

            if ($information->getType() == 'text' ) {
                // On met en place une nouvelle information
                $information->setContent($content);
            } else {
                // On change le contenu
                if ($_FILES["contentFile"]['size'] != 0 ) {
                    echo $_FILES["contentFile"]['size'];
                    $filename = $_FILES["contentFile"]['name'];
                    if ($information->getType() == 'img' ) {
                        // Si le type est une image
                        $explodeName   = explode('.', $filename);
                        $goodExtension = [ 'jpg', 'jpeg', 'gif', 'png', 'svg' ];
                        if (in_array(end($explodeName), $goodExtension) ) {
                            // On vérifie que l'extension est correcte
                            $this->deleteFile($information->getId());
                            $this->registerFile(
                                $filename, $_FILES["contentFile"]['tmp_name'],
                                $information
                            );
                        } else {
                            $this->_view->buildModal(
                                'Image non valide',
                                '<p>Ce fichier est une image non 
                                valide, veuillez choisir une autre image</p>'
                            );
                        }
                    } else if ($information->getType() == 'pdf' ) {
                        // Si le type est un PDF
                        $explodeName = explode('.', $filename);
                        if (end($explodeName) == 'pdf' ) {
                            // On vérifie que l'extension est correcte
                            $this->deleteFile($information->getId());
                            $this->registerFile(
                                $filename, $_FILES["contentFile"]['tmp_name'],
                                $information
                            );
                        } else {
                            $this->_view->buildModal(
                                'PDF non valide',
                                '<p>Ce fichier est un PDF non 
                                valide, veuillez choisir un autre PDF</p>'
                            );
                        }
                    } else if ($information->getType() == 'video'
                        || $information->getType() == 'short'
                    ) {
                        $explodeName   = explode('.', $filename);
                        $goodExtension = [ 'mp4', 'webm' ];
                        if (in_array(end($explodeName), $goodExtension) ) {
                            // On vérifie que l'extension est correcte
                            $this->deleteFile($information->getId());
                            $this->registerFile(
                                $filename,
                                $_FILES["contentFile"]['tmp_name'], $information
                            );

                        } else {
                            $this->_view->buildModal(
                                'Vidéo non valide',
                                '<p>Ce fichier est une vidéo non 
                                valide, veuillez choisir une autre vidéo</p>'
                            );
                        }
                    }
                }
            }

            if (empty($codes) ) {
                $this->_view->buildModal(
                    'Aucun emploi du temps',
                    '<p>Aucun emploi du temps n\'a été selectionné, 
                             merci d\'en choisir au moins un.</p>'
                );
            } else {
                if ($information->update() ) {
                    $this->_view->displayModifyValidate();
                } else {
                    $this->_view->errorMessageCantAdd();
                }
            }
        }


        $delete = filter_input(INPUT_POST, 'delete');
        if (isset($delete)) {
            $information->delete();
            $information->deleteScrapingTags();
            $this->_view->displayModifyValidate();
        }
        return $this->_view->displayModifyInformationForm(
            $information->getTitle(), $information->getContent(),
            $information->getExpirationDate(), $information->getType(),
            $allDepts, $this->getAllAvailableCodes()
        );
    }

    /**
     * Récupère tous les codes disponibles en fonction des
     * permissions de l'utilisateur.
     *
     * Cette méthode retourne une liste des codes ADE disponibles, classés par type :
     * années, groupes et demi-groupes. Si l'utilisateur dispose de la permission
     * "information_to_any_code", il obtient tous les codes disponibles. Sinon,
     * les codes sont filtrés en fonction du département de l'utilisateur.
     *
     * @return array Tableau contenant trois listes :
     *               - Années disponibles
     *               - Groupes disponibles
     *               - Demi-groupes disponibles
     */
    public static function getAllAvailableCodes(): array
    {
        $codeAde = new CodeAde();
        $deptModel = new Department();

        if (current_user_can('information_to_any_code')) {
            $years = $codeAde->getAllFromType('year');
            $groups = $codeAde->getAllFromType('group');
            $halfGroups = $codeAde->getAllFromType('halfGroup');
        } else {
            $currDept = $deptModel->getUserDepartment(get_current_user_id());

            $years = $codeAde->getAllFromTypeAndDept(
                $currDept->getIdDepartment(),
                'year'
            );
            $groups = $codeAde->getAllFromTypeAndDept(
                $currDept->getIdDepartment(),
                'group'
            );
            $halfGroups = $codeAde->getAllFromTypeAndDept(
                $currDept->getIdDepartment(), 'halfGroup'
            );
        }
        return array($years, $groups, $halfGroups);
    }


    /**
     * Enregistre un fichier téléchargé et met à jour l'entité associée.
     *
     * Cette méthode déplace un fichier temporaire vers un emplacement définitif
     * sur le serveur et met à jour l'entité donnée avec les informations du fichier.
     * Si l'entité n'a pas encore d'identifiant, elle sera insérée dans la base de
     * données.
     * Sinon, les informations existantes seront mises à jour.
     *
     * Le nom du fichier est ensuite modifié pour inclure un hachage MD5 afin de
     * garantir l'unicité. Si l'enregistrement ou le téléchargement échoue, un
     * message d'erreur sera affiché à l'utilisateur.
     *
     * @param string      $filename Le nom du fichier
     *                              téléchargé.
     * @param string      $tmpName  Le nom temporaire du fichier sur le serveur.
     * @param Information $entity   L'entité à laquelle le contenu du fichier est
     *                              associé.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function registerFile(string $filename, string $tmpName,
        Information $entity
    ): void {
        $id = 'temporary';
        $extension_upload = strtolower(
            substr(
                strrchr($filename, '.'), 1
            )
        );
        $name = $_SERVER['DOCUMENT_ROOT']
            . TV_UPLOAD_PATH . $id . '.' . $extension_upload;
        $entity->setDuration(10000); // durée par défaut d'une information

        if ($entity->getType() == 'video' || $entity->getType() == 'short') {
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($tmpName);
            if (isset($fileInfo['playtime_seconds'])) {
                $duration = $fileInfo['playtime_seconds'];
                $entity->setDuration(floor($duration*1000));
            }
        }

        // Upload le fichier
        if (move_uploaded_file($tmpName, $name)) {
            $entity->setContent('temporary content');
            if ($entity->getId() == null) {
                $id = $entity->insert();
            } else {
                $entity->update();
                $id = $entity->getId();
            }
        } else {
            $this->_view->errorMessageCantAdd();
        }
        // If the file upload and the upload of the information in the database works
        if ($id != 0) {
            $entity->setId($id);

            $md5Name = $id . md5_file($name);
            rename(
                $name, $_SERVER['DOCUMENT_ROOT']
                . TV_UPLOAD_PATH . $md5Name . '.' . $extension_upload
            );

            $content = $md5Name . '.' . $extension_upload;

            $entity->setContent($content);
            if ($entity->update()) {
                $this->_view->displayCreateValidate();
            } else {
                $this->_view->errorMessageCantAdd();
            }
        }
    }

    /**
     * Supprime le fichier qui est lié à l'identifiant
     *
     * @param $id int Code
     *
     * @return void
     */
    public function deleteFile($id)
    {
        $this->_model = $this->_model->get($id);
        $source = $_SERVER['DOCUMENT_ROOT']
            . TV_UPLOAD_PATH . $this->_model->getContent();
        wp_delete_file($source);
    }

    /**
     * Affiche toutes les informations avec pagination et gestion des
     * permissions utilisateur.
     *
     * Cette fonction récupère toutes les informations en fonction des
     * permissions de l'utilisateur
     * (administrateur ou utilisateur d'un département spécifique).
     * Elle génère également une table paginée
     * avec les informations à afficher, permettant de modifier ou
     * de supprimer des informations.
     * La suppression d'informations vérifie les droits de l'utilisateur
     * avant d'effectuer une suppression,
     * y compris la suppression des fichiers associés.
     *
     * @return string Le code HTML généré pour afficher la liste
     * des informations avec pagination.
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function displayAll()
    {
        $numberAllEntity = $this->_model->countAll();
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
        if (current_user_can('admin_perms')
            || in_array("communicant", $current_user->roles)) {
            $informationList = $this->_model->getList($begin, $number);
        } else {
            $informationList = $this->_model->getInformationsByDeptId(
                $deptModel->getUserDepartment($current_user->ID)->getIdDepartment()
            );
        }

        $name = 'Info';
        $header = ['Titre', 'Contenu', 'Date de création', 'Date d\'expiration',
            'Auteur', 'Type', 'Modifier'];
        $dataList = [];
        $row = $begin;
        $imgExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
        $videoExtension = ['mp4', 'webm'];

        foreach ($informationList as $information) {
            ++$row;

            $contentExplode = explode('.', $information->getContent());

            $content = TV_UPLOAD_PATH;
            if (!is_null($information->getAdminId())) {
                $content = URL_WEBSITE_VIEWER . TV_UPLOAD_PATH;
            }

            if (in_array(
                $information->getType(), ['img', 'pdf', 'event', 'video', 'short']
            )
            ) {
                if (in_array($contentExplode[1], $imgExtension)) {
                    $content = '<img class="img-thumbnail img_table_ecran" src="'
                        . $content
                        . $information->getContent()
                        . '" alt="' . $information->getTitle() . '">';
                } else if ($contentExplode[1] === 'pdf') {
                    $content = '[pdf-embedder url="'
                        . TV_UPLOAD_PATH . $information->getContent() . '"]';
                } else if (in_array($contentExplode[1], $videoExtension)) {
                    $content = '<video style=\'max-height:300px;\' src="'
                        . $content
                        . $information->getContent() . '" autoplay muted loop>';
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
                'scraping' => 'Scraping',
                default => 'Special',
            };

            $dataList[] = [
                $row,
                $this->_view->buildCheckbox($name, $information->getId()),
                $information->getTitle(),
                $content,
                $information->getCreationDate(),
                $information->getExpirationDate(),
                $information->getAuthor()->getLogin(),
                $type,
                $this->_view->buildLinkForModify(
                    esc_url(
                        get_permalink(
                            get_page_by_title_custom('Modifier une information')
                        )
                    ) . '?id=' . $information->getId()
                )
            ];

        }

        $submit = filter_input(INPUT_POST, 'delete');
        if (isset($submit)) {
            if (isset($_REQUEST['checkboxStatusInfo'])) {
                $checked_values = $_REQUEST['checkboxStatusInfo'];
                foreach ($checked_values as $id) {
                    $entity = $this->_model->get($id);
                    if (current_user_can('edit_information')
                        || $entity->getAuthor()->getId() == $current_user->ID
                    ) {
                        $type = $entity->getType();
                        $types = ["img", "pdf", "event"];
                        if (in_array($type, $types)) {
                            $this->deleteFile($id);
                        }
                        $entity->delete();
                    }
                }
                $this->_view->refreshPage();
            }
        }
        $returnString = "";
        if ($pageNumber == 1) {
            $returnString = $this->_view->contextDisplayAll();
        }
        return $returnString . $this->_view->displayAll(
            $name, 'Informations', $header, $dataList
        )
            . $this->_view->pageNumber(
                $maxPage, $pageNumber, esc_url(
                    get_permalink(
                        get_page_by_title_custom('Gestion des informations')
                    )
                ), $number
            );
    }

    /**
     * Vérifie si la date de fin d'une information est dépassée.
     *
     * Si la date de fin est inférieure ou égale à la date actuelle,
     * l'information associée est supprimée, ainsi que son fichier.
     *
     * @param int    $id      L'identifiant de l'information
     *                        à vérifier.
     * @param string $endDate La date de fin au format 'Y-m-d'.
     *
     * @return boolean
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function endDateCheckInfo($id, $endDate)
    {
        if ($endDate <= date("Y-m-d")) {
            $information = $this->_model->get($id);
            $this->deleteFile($id);
            $information->delete();
            return true;
        }
        return false;
    }

    /**
     * Affiche les informations principales sous forme de diaporama.
     *
     * Récupère la liste des informations, vérifie si leur date d'expiration est
     * dépassée, et affiche chaque information en fonction de son type.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function informationMain()
    {
        $user = new User();
        $user = $user->get(get_current_user_id());
        $user = $user->getMyCodes([$user])[0];

        $informations = array();
        $codeAdeIds = array_map(fn($code) => $code->getId(), $user->getCodes());
        $informations += $this->_model->getInformationsByCodeAdeIds($codeAdeIds);
        $this->_view->displayStartSlideshow();

        foreach ($informations as $information) {
            $endDate = date(
                'Y-m-d', strtotime(
                    $information->getExpirationDate()
                )
            );
            if (!$this->endDateCheckInfo($information->getId(), $endDate)) {
                $adminSite = true;
                if (is_null($information->getAdminId())) {
                    $adminSite = false;
                }
                // Affiche les informations sauf les vidéos
                if ($information->getType() !== 'video') {
                    if ($information->getType() === 'scraping') {
                        $this->_view->displaySlide(
                            'Sans titre',
                            $this->createScraper($information->getId()),
                            $information->getType()
                        );
                    } else {
                        $this->_view->displaySlide(
                            $information->getTitle(),
                            $information->getContent(), $information->getType(),
                            $adminSite
                        );
                    }
                }
            }
        }
        echo '</div>';
    }

    /**
     * Affiche les vidéos associées aux informations d'un département.
     *
     * Cette fonction récupère les informations d'un utilisateur
     * en fonction de son département,
     * vérifie que la date d'expiration de chaque information
     * n'est pas dépassée, et affiche
     * uniquement les vidéos sous forme de diaporama. Si
     * l'information n'a pas de date d'expiration valide,
     * elle est ignorée. De plus, la fonction vérifie si
     * l'utilisateur est un administrateur pour afficher
     * des informations spécifiques en fonction de son rôle.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2025-01-13
     */
    public function displayVideo()
    {
        $deptModel = new Department();
        $informations = $this->_model->getInformationsByDeptId(
            $deptModel->getUserDepartment(
                wp_get_current_user()->ID
            )->getIdDepartment()
        );

        // Début du conteneur pour les vidéos
        $this->_view->displayStartSlideVideo();
        foreach ($informations as $information) {
            $endDate = date(
                'Y-m-d', strtotime(
                    $information->getExpirationDate()
                )
            );
            if (!$this->endDateCheckInfo($information->getId(), $endDate)) {
                $adminSite = true;
                if (is_null($information->getAdminId())) {
                    $adminSite = false;
                }
                // Affiche uniquement les vidéos
                if ($information->getType() === 'video') {
                    $this->_view->displaySlideVideo(
                        $information->getTitle(),
                        $information->getContent(),
                        $information->getType(), $adminSite
                    );
                }
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
     * Ajoute également les nouvelles informations qui ne sont pas encore
     * enregistrées.
     *
     * @return void
     *
     * @version 1.0.0
     * @date    2024-10-16
     */
    public function registerNewInformation()
    {
        $informationList = $this->_model->getFromAdminWebsite();
        $myInformationList = $this->_model->getAdminWebsiteInformation();
        foreach ($myInformationList as $information) {
            if ($adminInfo = $this->_model->getInformationFromAdminSite(
                $information->getId()
            )
            ) {
                if ($information->getTitle() != $adminInfo->getTitle()) {
                    $information->setTitle($adminInfo->getTitle());
                }
                if ($information->getContent() != $adminInfo->getContent()) {
                    $information->setContent($adminInfo->getContent());
                }
                if ($information->getExpirationDate(
                ) != $adminInfo->getExpirationDate()
                ) {
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
    public function displayEvent()
    {
        $events = $this->_model->getListInformationEvent();
        $this->_view->displayStartSlideEvent();
        foreach ($events as $event) {
            $this->_view->displaySlideBegin();
            $extension = explode('.', $event->getContent());
            $extension = $extension[1];
            if ($extension == "pdf") {
                echo '
				<div class="canvas_pdf" id="' . $event->getContent() . '"></div>';
            } else {
                echo '<img src="'
                    . TV_UPLOAD_PATH
                    . $event->getContent() . '" alt="' . $event->getTitle() . '">';
            }
            echo '</div>';
        }
        echo '</div>';
    }

    /**
     * Crée un objet de type "Scraper" et récupère les informations d'un site web.
     *
     * Cette méthode initialise un scraper avec les balises HTML nécessaires
     * pour extraire des informations depuis une URL spécifique. Elle récupère
     * les sélecteurs HTML associés à un identifiant donné, les organise en tableau,
     * puis les transmet au scraper pour récupérer et afficher le contenu du site.
     *
     * @param int $id Identifiant permettant de
     *                récupérer les balises et l'URL associées.
     *
     * @return string Retourne le contenu extrait
     * du site web après exécution du scraper.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function createScraper($id): string
    {
        $information = $this->_model;
        list($url, $balises, $types) = $information->getScrapingTags($id);

        $arrayArg = array();
        for ($i=0; $i<count($balises); $i++) {
                $arrayArg[$types[$i]] = $balises[$i];
        }

        $article = $arrayArg['article'];
        unset($arrayArg['article']);

        $scraper1 = new Scraper(
            $url, // URL du site à scraper
            $article,  // Sélecteur pour l'article
            $arrayArg
        );
        return $scraper1->printWebsite();
    }
}
