<?php

namespace Controllers;

include __DIR__ . '/../utils/OneSignalPush.php';

use Models\Alert;
use Models\CodeAde;
use Utils\OneSignalPush;
use Views\AlertView;

/**
 * Class AlertController
 *
 * Gère les alertes (création, modification, suppression, affichage)
 *
 * @package Controllers
 */
class AlertController extends Controller
{

    /**
     * @var Alert Modèle pour gérer les alertes
     */
    private $model;

    /**
     * @var AlertView Vue pour afficher les alertes
     */
    private $view;

    /**
     * Constructeur de la classe AlertController
     * Initialise le modèle et la vue des alertes.
     */
    public function __construct() {
        $this->model = new Alert();
        $this->view = new AlertView();
    }

    /**
     * Insère une alerte dans la base de données
     *
     * Cette méthode récupère les informations nécessaires pour créer une alerte,
     * les valide, et les insère dans la base de données.
     *
     * @return string Affiche le formulaire de création d'alerte
     */
    public function insert() {
        $codeAde = new CodeAde();
        $action = filter_input(INPUT_POST, 'submit');
        if (isset($action)) {
            $codes = $_POST['selectAlert'];
            $content = filter_input(INPUT_POST, 'content');
            $endDate = filter_input(INPUT_POST, 'expirationDate');

            $creationDate = date('Y-m-d');
            $endDateString = strtotime($endDate);
            $creationDateString = strtotime(date('Y-m-d', time()));

            $this->model->setForEveryone(0);

            $codesAde = array();
            foreach ($codes as $code) {
                if ($code != 'all' && $code != 0) {
                    if (is_null($codeAde->getByCode($code)->getId())) {
                        $this->view->errorMessageInvalidForm();
                        return;
                    } else {
                        $codesAde[] = $codeAde->getByCode($code);
                    }
                } else if ($code == 'all') {
                    $this->model->setForEveryone(1);
                }
            }

            if (is_string($content) && strlen($content) >= 4 && strlen($content) <= 280 && $this->isRealDate($endDate) && $creationDateString < $endDateString) {
                $current_user = wp_get_current_user();

                // Définir l'alerte
                $this->model->setAuthor($current_user->ID);
                $this->model->setContent($content);
                $this->model->setCreationDate($creationDate);
                $this->model->setExpirationDate($endDate);
                $this->model->setCodes($codesAde);

                // Insérer l'alerte
                if ($id = $this->model->insert()) {
                    $this->view->displayAddValidate();

                    // Envoyer la notification push
                    $oneSignalPush = new OneSignalPush();

                    if ($this->model->isForEveryone()) {
                        $oneSignalPush->sendNotification(null, $this->model->getContent());
                    } else {
                        $oneSignalPush->sendNotification($codesAde, $this->model->getContent());
                    }
                } else {
                    $this->view->errorMessageCantAdd();
                }
            } else {
                $this->view->errorMessageInvalidForm();
            }
        }

        // Récupération des types de codes pour le formulaire
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->creationForm($years, $groups, $halfGroups);
    }

    /**
     * Modifie une alerte existante
     *
     * Cette méthode permet de modifier une alerte en fonction de son ID.
     * Elle valide également les permissions de l'utilisateur avant de procéder.
     *
     * @return string Affiche le formulaire de modification d'alerte
     */
    public function modify() {
        $id = $_GET['id'];

        if (!is_numeric($id) || !$this->model->get($id)) {
            return $this->view->noAlert();
        }
        $current_user = wp_get_current_user();
        $alert = $this->model->get($id);
        if (!in_array('administrator', $current_user->roles) && !in_array('secretaire', $current_user->roles) && $alert->getAuthor()->getId() != $current_user->ID) {
            return $this->view->alertNotAllowed();
        }

        if ($alert->getAdminId()) {
            return $this->view->alertNotAllowed();
        }

        $codeAde = new CodeAde();

        $submit = filter_input(INPUT_POST, 'submit');
        if (isset($submit)) {
            // Récupérer les valeurs
            $content = filter_input(INPUT_POST, 'content');
            $expirationDate = filter_input(INPUT_POST, 'expirationDate');
            $codes = $_POST['selectAlert'];

            $alert->setForEveryone(0);

            $codesAde = array();
            foreach ($codes as $code) {
                if ($code != 'all' && $code != 0) {
                    if (is_null($codeAde->getByCode($code)->getId())) {
                        $this->view->errorMessageInvalidForm();
                        return;
                    } else {
                        $codesAde[] = $codeAde->getByCode($code);
                    }
                } else if ($code == 'all') {
                    $alert->setForEveryone(1);
                }
            }

            // Définir l'alerte
            $alert->setContent($content);
            $alert->setExpirationDate($expirationDate);
            $alert->setCodes($codesAde);

            if ($alert->update()) {
                $this->view->displayModifyValidate();
            } else {
                $this->view->errorMessageCantAdd();
            }
        }

        // Supprimer l'alerte si demandé
        $delete = filter_input(INPUT_POST, 'delete');
        if (isset($delete)) {
            $alert->delete();
            $this->view->displayModifyValidate();
        }

        // Récupération des types de codes pour le formulaire
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->modifyForm($alert, $years, $groups, $halfGroups);
    }


    /**
     * Affiche toutes les alertes
     *
     * Cette méthode récupère toutes les alertes en fonction des permissions de l'utilisateur et les affiche.
     *
     * @return string Affiche la liste des alertes
     */
    public function displayAll() {
        $numberAllEntity = $this->model->countAll();
        $url = $this->getPartOfUrl();
        $number = filter_input(INPUT_GET, 'number');
        $pageNumber = 1;
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
        if (in_array('administrator', $current_user->roles) || in_array('secretaire', $current_user->roles)) {
            $alertList = $this->model->getList($begin, $number);
        } else {
            $alertList = $this->model->getAuthorListAlert($current_user->ID, $begin, $number);
        }
        $name = 'Alert';
        $header = ['Contenu', 'Date de création', 'Date d\'expiration', 'Auteur', 'Modifier'];
        $dataList = [];
        $row = $begin;
        foreach ($alertList as $alert) {
            ++$row;
            $dataList[] = [$row, $this->view->buildCheckbox($name, $alert->getId()), $alert->getContent(), $alert->getCreationDate(), $alert->getExpirationDate(), $alert->getAuthor()->getLogin(), $this->view->buildLinkForModify(esc_url(get_permalink(get_page_by_title('Modifier une alerte'))) . '?id=' . $alert->getId())];
        }

        // Suppression d'alertes sélectionnées
        $submit = filter_input(INPUT_POST, 'delete');
        if (isset($submit)) {
            if (isset($_REQUEST['checkboxStatusAlert'])) {
                $checked_values = $_REQUEST['checkboxStatusAlert'];
                foreach ($checked_values as $id) {
                    $entity = $this->model->get($id);
                    $entity->delete();
                }
                $this->view->refreshPage();
            }
        }
        if ($pageNumber == 1) {
            $returnString = $this->view->contextDisplayAll();
        }
        return $returnString . $this->view->displayAll($name, 'Alertes', $header, $dataList) . $this->view->pageNumber($maxPage, $pageNumber, esc_url(get_permalink(get_page_by_title('Gestion des alertes'))), $number);
    }


    /**
     * Affiche toutes les alertes pour l'utilisateur
     *
     * Cette méthode récupère et affiche toutes les alertes pour l'utilisateur courant,
     * y compris celles destinées à tout le monde.
     */
    public function alertMain() {
        // Récupérer les codes de l'utilisateur actuel
        $current_user = wp_get_current_user();
        $alertsUser = $this->model->getForUser($current_user->ID);
        //$alertsUser = array_unique($alertsUser); // Supprimer les doublons

        foreach ($this->model->getForEveryone() as $alert) {
            $alertsUser[] = $alert;
        }

        $contentList = array();
        foreach ($alertsUser as $alert) {
            $endDate = date('Y-m-d', strtotime($alert->getExpirationDate()));
            $this->endDateCheckAlert($alert->getId(), $endDate); // Vérifier l'alerte

            $content = $alert->getContent() . '&emsp;&emsp;&emsp;&emsp;';
            array_push($contentList, $content);
        }

        if (isset($content)) {
            $this->view->displayAlertMain($contentList);
        }
    }

    /**
     * Enregistre une nouvelle alerte à partir du site administrateur
     *
     * Cette méthode synchronise les alertes de l'administration avec celles de l'utilisateur,
     * en mettant à jour ou en supprimant les alertes existantes.
     */
    public function registerNewAlert() {
        $alertList = $this->model->getFromAdminWebsite();
        $myAlertList = $this->model->getAdminWebsiteAlert();
        foreach ($myAlertList as $alert) {
            if ($adminInfo = $this->model->getAlertFromAdminSite($alert->getId())) {
                if ($alert->getContent() != $adminInfo->getContent()) {
                    $alert->setContent($adminInfo->getContent());
                }
                if ($alert->getExpirationDate() != $adminInfo->getExpirationDate()) {
                    $alert->setExpirationDate($adminInfo->getExpirationDate());
                }
                $alert->setCodes([]);
                $alert->setForEveryone(1);
                $alert->update();
            } else {
                $alert->delete();
            }
        }
        foreach ($alertList as $alert) {
            $exist = 0;
            foreach ($myAlertList as $myAlert) {
                if ($alert->getId() == $myAlert->getAdminId()) {
                    ++$exist;
                }
            }
            if ($exist == 0) {
                $alert->setAdminId($alert->getId());
                $alert->setCodes([]);
                $alert->insert();
            }
        }
    }

    /**
     * Vérifie la date de fin d'une alerte
     *
     * Si la date de fin est dépassée, l'alerte est supprimée.
     *
     * @param int $id Identifiant de l'alerte à vérifier
     * @param string $endDate Date de fin de l'alerte
     */
    public function endDateCheckAlert($id, $endDate) {
        if ($endDate <= date("Y-m-d")) {
            $alert = $this->model->get($id);
            $alert->delete();
        }
    } // endDateCheckAlert()
}
