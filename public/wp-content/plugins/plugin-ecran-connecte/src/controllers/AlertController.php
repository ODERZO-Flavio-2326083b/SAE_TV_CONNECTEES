<?php

namespace controllers;

use models\Alert;
use models\CodeAde;
use models\Department;
use views\AlertView;

/**
 * Class AlertController
 *
 * Gère les alertes (création, modification, suppression, affichage)
 *
 * @package controllers
 */
class AlertController extends Controller
{

    
    /**
     * @var Alert Modèle pour gérer les alertes
     */
    private Alert $_model;

    
    /**
     * @var AlertView Vue pour afficher les alertes
     */
    private AlertView $_view;

    /**
     * Constructeur de la classe AlertController
     * Initialise le modèle et la vue des alertes.
     */
    public function __construct()
    {
        $this->_model = new Alert();
        $this->_view = new AlertView();
    }

    /**
     * Insère une nouvelle alerte après validation des données du formulaire.
     *
     * Cette méthode vérifie si le formulaire d'ajout d'alerte a été soumis et valide
     * les champs tels que les codes ADE, le contenu de l'alerte et la date
     * d'expiration.
     * Si la validation réussit, l'alerte est insérée dans la base de données.
     * Si une erreur survient lors de l'insertion, un message d'erreur est affiché.
     *
     * @return string Le formulaire de création d'alerte ou un message de
     * confirmation/erreur.
     *
     * @version 1.0
     * @date    16-09-2024
     */
    public function insert(): string
    {
        $codeAde = new CodeAde();
        $action = filter_input(INPUT_POST, 'submit');
        if (isset($action)) {
            $codes = filter_input(
                INPUT_POST, 'selectAlert', FILTER_DEFAULT,
                FILTER_REQUIRE_ARRAY
            );
            $content = filter_input(INPUT_POST, 'content');
            $endDate = filter_input(INPUT_POST, 'expirationDate');

            $creationDate = date('Y-m-d');
            $endDateString = strtotime($endDate);
            $creationDateString = strtotime(date('Y-m-d', time()));


            $codesAde = array();
            foreach ($codes as $code) {
                if (is_null($codeAde->getByCode($code)->getId())) {
                    $this->_view->errorMessageInvalidForm();
                } else {
                    $codesAde[] = $codeAde->getByCode($code);
                }

            }

            if (is_string(
                $content
            ) && strlen(
                $content
            ) >= 4 && strlen(
                $content
            ) <= 280 && $this->isRealDate(
                $endDate
            ) && $creationDateString < $endDateString
            ) {
                $current_user = wp_get_current_user();

                // Définir l'alerte
                $this->_model->setAuthor($current_user->ID);
                $this->_model->setContent($content);
                $this->_model->setCreationDate($creationDate);
                $this->_model->setExpirationDate($endDate);
                $this->_model->setCodes($codesAde);

                // Insérer l'alerte
                if ($this->_model->insert()) {
                    $this->_view->displayAddValidate();
                } else {
                    $this->_view->errorMessageCantAdd();
                }
            } else {
                $this->_view->errorMessageInvalidForm();
            }
        }

        // Récupération des types de codes pour le formulaire
        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        $deptModel = new Department();
        $allDepts = $deptModel->getAllDepts();

        return $this->_view->creationForm($years, $groups, $halfGroups, $allDepts);
    }

    /**
     * Modifie une alerte existante après validation des données du formulaire.
     *
     * Cette méthode vérifie l'existence de l'alerte à partir de l'ID fourni et
     * s'assure que l'utilisateur actuel a les permissions nécessaires
     * (administrateur, secrétaire ou auteur de l'alerte) pour modifier l'alerte.
     * Elle permet également de modifier le contenu, la date d'expiration et les
     * codes ADE associés à l'alerte. Si la modification réussit, une confirmation
     * est affichée, sinon un message d'erreur est renvoyé.
     *
     * La méthode permet aussi de supprimer l'alerte si l'utilisateur en fait la
     * demande.
     *
     * @return string Le formulaire de modification de l'alerte ou un message de
     * confirmation/erreur.
     *
     * @version 1.0
     * @date    16-09-2024
     */
    public function modify() : string
    {
        $id = filter_input(INPUT_GET, 'id');

        if (!is_numeric($id) || !$this->_model->get($id)) {
            return $this->_view->noAlert();
        }
        $current_user = wp_get_current_user();
        $alert = $this->_model->get($id);
        if (current_user_can('edit_alert')
            && $alert->getAuthor()->getId() != $current_user->ID
        ) {
            return $this->_view->alertNotAllowed();
        }

        if ($alert->getAdminId()) {
            return $this->_view->alertNotAllowed();
        }

        $codeAde = new CodeAde();

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        $deptModel = new Department();
        $allDepts = $deptModel->getAllDepts();

        $submit = filter_input(INPUT_POST, 'submit');
        if (isset($submit)) {
            $error = false;
            // Récupérer les valeurs
            $content = filter_input(INPUT_POST, 'content');
            $expirationDate = filter_input(
                INPUT_POST,
                'expirationDate');
            $codes = filter_input(
                INPUT_POST, 'selectAlert', FILTER_DEFAULT,
                FILTER_REQUIRE_ARRAY
            );

            $codesAde = array();
            if (!empty($codes)) {
                foreach ($codes as $code) {
                    if (is_null($codeAde->getByCode($code)->getId())) {
                        $error = true;
                    } else {
                        $codesAde[] = $codeAde->getByCode($code);
                    }
                }
            } else {
                $error = true;
            }

            if ($error) {
                $this->_view->errorMessageCantAdd();
                return $this->_view->modifyForm(
                    $alert, $years, $groups, $halfGroups, $allDepts
                );
            }

            $alert->setContent($content);
            $alert->setExpirationDate($expirationDate);
            $alert->setCodes($codesAde);

            if ($alert->update()) {
                $this->_view->displayModifyValidate();
            } else {
                $this->_view->errorMessageCantAdd();
            }
        }

        $delete = filter_input(INPUT_POST, 'delete');
        if (isset($delete)) {
            $alert->delete();
            $this->_view->displayModifyValidate();
        }

        return $this->_view->modifyForm(
            $alert, $years, $groups, $halfGroups, $allDepts
        );
    }


    /**
     * Affiche la liste paginée des alertes pour l'utilisateur actuel.
     *
     * Cette méthode récupère et affiche une liste d'alertes en fonction des
     * permissions de l'utilisateur connecté.
     * Si l'utilisateur est un administrateur ou un secrétaire, toutes les alertes
     * sont affichées.
     * Sinon, seules les alertes créées par l'utilisateur sont listées.
     * Elle gère également la pagination, le nombre d'alertes par page et permet la
     * suppression des alertes sélectionnées.
     *
     * @return string Le contenu HTML de la liste des alertes, incluant les options
     * de pagination et de suppression.
     *
     * @version 1.0
     * @date    16-09-2024
     */
    public function displayAll() : string
    {
        $numberAllEntity = $this->_model->countAll();
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
        if (current_user_can('view_alerts')) {
            $alertList = $this->_model->getList($begin, $number);
        } else {
            $alertList = $this->_model->getAuthorListAlert(
                $current_user->ID, $begin,  $number
            );
        }
        $name = 'Alert';
        $header = ['Contenu', 'Date de création', 'Date d\'expiration', 'Auteur',
            'Modifier'];
        $dataList = [];
        $row = $begin;
        foreach ($alertList as $alert) {
            ++$row;
            $dataList[] = [
                $row,
                $this->_view->buildCheckbox($name, $alert->getId()),
                $alert->getContent(),
                $alert->getCreationDate(),
                $alert->getExpirationDate(),
                $alert->getAuthor()->getLogin(),
                $this->_view->buildLinkForModify(
                    esc_url(
                        get_permalink(
                            get_page_by_title_custom('Modifier une alerte')
                        )
                    ) . '?id=' . $alert->getId()
                )];
        }

        // Suppression d'alertes sélectionnées
        $submit = filter_input(INPUT_POST, 'delete');
        if (isset($submit)) {
            if (isset($_REQUEST['checkboxStatusAlert'])) {
                $checked_values = $_REQUEST['checkboxStatusAlert'];
                foreach ($checked_values as $id) {
                    $entity = $this->_model->get($id);
                    $entity->delete();
                }
                $this->_view->refreshPage();
            }
        }
        if ($pageNumber == 1) {
            $returnString = $this->_view->contextDisplayAll();
        }
        return $returnString . $this->_view->displayAll(
            $name, 'Alertes', $header, $dataList
        ) .
            $this->_view->pageNumber(
                $maxPage, $pageNumber, esc_url(
                    get_permalink(
                        get_page_by_title_custom('Gestion des alertes')
                    )
                ), $number
            );
    }


    /**
     * Affiche les alertes pertinentes pour l'utilisateur actuel.
     *
     * Cette méthode récupère les alertes spécifiques à l'utilisateur connecté ainsi
     * que les alertes publiques destinées à tout le monde. Elle vérifie ensuite si
     * chaque alerte est toujours valide en comparant la date d'expiration. Enfin,
     * elle affiche le contenu des alertes si des alertes existent.
     *
     * @return void
     *
     * @version 1.0
     * @date    16-09-2024
     */
    public function alertMain()
    {
        // Récupérer les codes de l'utilisateur actuel
        $current_user = wp_get_current_user();
        $alertsUser = $this->_model->getForUser($current_user->ID);
        //$alertsUser = array_unique($alertsUser); // Supprimer les doublons

        $contentList = array();
        foreach ($alertsUser as $alert) {
            $endDate = date('Y-m-d', strtotime($alert->getExpirationDate()));
            $this->endDateCheckAlert($alert->getId(), $endDate); // Vérifier l'alerte

            $content = $alert->getContent() . '&emsp;&emsp;&emsp;&emsp;';
            array_push($contentList, $content);
        }

        if (isset($content)) {
            $this->_view->displayAlertMain($contentList);
        }
    }

    /**
     * Synchronise les alertes du site administrateur avec les alertes locales.
     *
     * Cette méthode récupère les alertes provenant du site administrateur et les
     * compare avec les alertes locales.
     * Si une alerte existe en local mais diffère de celle du site administrateur,
     * elle est mise à jour avec les nouvelles informations (contenu et date
     * d'expiration). Si une alerte du site administrateur n'existe pas localement,
     * elle est ajoutée. Les alertes qui ne sont plus présentes sur le site
     * administrateur sont supprimées localement.
     *
     * @return void
     *
     * @version 1.0
     * @date    16-09-2024
     */
    public function registerNewAlert()
    {
        $alertList = $this->_model->getFromAdminWebsite();
        $myAlertList = $this->_model->getAdminWebsiteAlert();
        foreach ($myAlertList as $alert) {
            if ($adminInfo = $this->_model->getAlertFromAdminSite($alert->getId())) {
                if ($alert->getContent() != $adminInfo->getContent()) {
                    $alert->setContent($adminInfo->getContent());
                }
                if ($alert->getExpirationDate() != $adminInfo->getExpirationDate()) {
                    $alert->setExpirationDate($adminInfo->getExpirationDate());
                }
                $alert->setCodes([]);
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
     * Vérifie et supprime les alertes expirées.
     *
     * Cette méthode compare la date d'expiration de l'alerte avec la date actuelle.
     * Si l'alerte est expirée (date d'expiration égale ou antérieure à la date
     * actuelle), elle est supprimée de la base de données.
     *
     * @param int    $id      L'identifiant unique de l'alerte
     *                        à vérifier.
     * @param string $endDate La date d'expiration de l'alerte au format 'Y-m-d'.
     *
     * @return void
     *
     * @version 1.0
     * @date    16-09-2024
     */
    public function endDateCheckAlert($id, $endDate)
    {
        if ($endDate <= date("Y-m-d")) {
            $alert = $this->_model->get($id);
            $alert->delete();
        }
    } // endDateCheckAlert()
}
