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
 * Cette classe gère la gestion des alertes, y compris la création, la modification et la suppression des alertes.
 *
 * @package Controllers
 */
class AlertController extends Controller
{
    /**
     * @var Alert $model Modèle pour gérer les alertes.
     */
    private $model;

    /**
     * @var AlertView $view Vue pour afficher les alertes.
     */
    private $view;

    /**
     * Constructeur de AlertController
     *
     * Initialise le modèle et la vue pour la gestion des alertes.
     */
    public function __construct() {
        $this->model = new Alert();
        $this->view = new AlertView();
    }

    /**
     * Insère une nouvelle alerte dans la base de données.
     *
     * Cette méthode récupère les données d'entrée du formulaire, les valide et
     * insère l'alerte dans la base de données. Elle gère également les notifications push.
     *
     * @return mixed Retourne le formulaire de création si l'insertion échoue, sinon null.
     */
    public function insert() {
        $codeAde = new CodeAde();
        $action = filter_input(INPUT_POST, 'submit');

        if (isset($action)) {
            $codes = $_POST['selectAlert'];
            $content = filter_input(INPUT_POST, 'content');
            $endDate = filter_input(INPUT_POST, 'expirationDate');

            // Initialisation des variables de date
            $creationDate = date('Y-m-d');
            $endDateString = strtotime($endDate);
            $creationDateString = strtotime(date('Y-m-d', time()));

            $this->model->setForEveryone(0);
            $codesAde = array();

            // Traitement des codes d'alerte sélectionnés
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

            // Validation du contenu et des dates
            if (is_string($content) && strlen($content) >= 4 && strlen($content) <= 280 && $this->isRealDate($endDate) && $creationDateString < $endDateString) {
                $current_user = wp_get_current_user();

                // Définition des attributs de l'alerte
                $this->model->setAuthor($current_user->ID);
                $this->model->setContent($content);
                $this->model->setCreationDate($creationDate);
                $this->model->setExpirationDate($endDate);
                $this->model->setCodes($codesAde);

                // Insertion de l'alerte dans la base de données
                if ($id = $this->model->insert()) {
                    $this->view->displayAddValidate();

                    // Envoi de notification push
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

        // Récupération des codes d'alerte pour le formulaire
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->creationForm($years, $groups, $halfGroups);
    }

    /**
     * Modifie une alerte existante.
     *
     * Cette méthode récupère l'alerte par ID, vérifie les autorisations,
     * et met à jour les attributs de l'alerte si autorisé.
     *
     * @return mixed Retourne le formulaire de modification ou un message d'erreur si l'alerte n'existe pas.
     */
    public function modify() {
        $id = $_GET['id'];

        // Validation de l'ID de l'alerte
        if (!is_numeric($id) || !$this->model->get($id)) {
            return $this->view->noAlert();
        }
        $current_user = wp_get_current_user();
        $alert = $this->model->get($id);

        // Vérification des permissions
        if (!in_array('administrator', $current_user->roles) && !in_array('secretaire', $current_user->roles) && $alert->getAuthor()->getId() != $current_user->ID) {
            return $this->view->alertNotAllowed();
        }

        // Vérification de l'autorisation d'alerte
        if ($alert->getAdminId()) {
            return $this->view->alertNotAllowed();
        }

        $codeAde = new CodeAde();
        $submit = filter_input(INPUT_POST, 'submit');

        if (isset($submit)) {
            // Récupération et validation des valeurs du formulaire
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

            // Mise à jour des attributs de l'alerte
            $alert->setContent($content);
            $alert->setExpirationDate($expirationDate);
            $alert->setCodes($codesAde);

            if ($alert->update()) {
                $this->view->displayModifyValidate();
            } else {
                $this->view->errorMessageCantAdd();
            }
        }

        // Suppression de l'alerte
        $delete = filter_input(INPUT_POST, 'delete');
        if (isset($delete)) {
            $alert->delete();
            $this->view->displayModifyValidate();
        }

        // Récupération des codes d'alerte pour le formulaire de modification
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->modifyForm($alert, $years, $groups, $halfGroups);
    }

    /**
     * Affiche toutes les alertes avec pagination.
     *
     * Récupère les alertes du modèle et les prépare pour l'affichage.
     *
     * @return mixed Retourne le HTML des alertes paginées.
     */
    public function displayAll() {
        $numberAllEntity = $this->model->countAll();
        $url = $this->getPartOfUrl();
        $number = filter_input(INPUT_GET, 'number');
        $pageNumber = 1;

        // Gestion de la pagination
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

        // Récupération des alertes selon le rôle de l'utilisateur
        if (in_array('administrator', $current_user->roles) || in_array('secretaire', $current_user->roles)) {
            $alertList = $this->model->getList($begin, $number);
        } else {
            $alertList = $this->model->getAuthorListAlert($current_user->ID, $begin, $number);
        }

        $name = 'Alert';
        $header = ['Contenu', 'Date de création', 'Date d\'expiration', 'Auteur', 'Modifier'];
        $dataList = [];
        $row = $begin;

        // Préparation des données pour l'affichage
        foreach ($alertList as $alert) {
            $dataList[] = [
                'content' => $alert->getContent(),
                'creationDate' => $alert->getCreationDate(),
                'expirationDate' => $alert->getExpirationDate(),
                'author' => $alert->getAuthor()->getName(),
                'modify' => "<a href='?action=alert&method=modify&id={$alert->getId()}'>Modifier</a>",
            ];
            $row++;
        }

        return $this->view->table($header, $dataList, $name, $maxPage, $pageNumber, $numberAllEntity);
    }

    /**
     * Valide si une chaîne est une date réelle au format 'Y-m-d'.
     *
     * @param string $date La chaîne de date à valider.
     * @return bool Retourne true si la date est valide, sinon false.
     */
    private function isRealDate($date) {
        return (bool) strtotime($date);
    }
}
