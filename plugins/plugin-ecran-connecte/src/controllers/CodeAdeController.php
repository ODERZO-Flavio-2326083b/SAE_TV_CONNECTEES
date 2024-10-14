<?php

namespace Controllers;

use Models\CodeAde;
use Views\CodeAdeView;

/**
 * Class CodeAdeController
 *
 * Cette classe gère les codes ADE : création, modification, suppression et affichage.
 *
 * Gestion des opérations CRUD (Créer, Lire, Mettre à jour, Supprimer)
 * pour les codes ADE.
 *
 * Cette classe interagit avec le modèle CodeAde et la vue CodeAdeView
 * pour effectuer les opérations sur les codes ADE.
 *
 * @package Controllers
 */
class CodeAdeController extends Controller
{
    /**
     * Modèle associé au contrôleur CodeAde.
     *
     * @var CodeAde
     */
    private $model;

    /**
     * Vue associée au contrôleur CodeAde.
     *
     * @var CodeAdeView
     */
    private $view;

    /**
     * Constructeur de la classe CodeAdeController.
     *
     * Initialise les objets modèle et vue.
     */
    public function __construct() {
        $this->model = new CodeAde();
        $this->view = new CodeAdeView();
    }

    /**
     * Insère un code ADE dans la base de données et charge l'emploi du temps associé.
     *
     * Cette méthode traite les données soumises via un formulaire
     * pour ajouter un nouveau code ADE. Elle vérifie les données
     * entrées, s'assure qu'elles sont valides et les insère dans
     * la base de données.
     *
     * @return string Retourne le formulaire de création du code ADE.
     *
     * Exemple d'utilisation :
     * ```php
     * $controller = new CodeAdeController();
     * echo $controller->insert();
     * ```
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'submit');

        if (isset($action)) {
            // Types de codes valides
            $validType = ['year', 'group', 'halfGroup'];

            // Récupération et validation des données soumises
            $title = filter_input(INPUT_POST, 'title');
            $code = filter_input(INPUT_POST, 'code');
            $type = filter_input(INPUT_POST, 'type');

            // Vérification des valeurs
            if (is_string($title) && strlen($title) > 4 && strlen($title) < 30 &&
                is_numeric($code) && is_string($code) && strlen($code) < 20 &&
                in_array($type, $validType)) {

                // Mise à jour du modèle avec les données valides
                $this->model->setTitle($title);
                $this->model->setCode($code);
                $this->model->setType($type);

                // Vérification des doublons et insertion dans la base de données
                if (!$this->checkDuplicateCode($this->model) && $this->model->insert()) {
                    $this->view->successCreation();
                    $this->addFile($code);
                    $this->view->refreshPage();
                } else {
                    $this->view->displayErrorDoubleCode();
                }
            } else {
                $this->view->errorCreation();
            }
        }
        return $this->view->createForm();
    }

    /**
     * Modifie un code ADE existant.
     *
     * Cette méthode gère la modification des données d'un code ADE.
     * Elle récupère l'identifiant du code à modifier, vérifie
     * son existence et traite les données soumises pour la mise à jour.
     *
     * @return string Retourne le formulaire de modification ou un message d'erreur si le code est introuvable.
     *
     * @throws \Exception En cas d'erreur lors de la modification.
     */
    public function modify() {
        $id = $_GET['id'];
        // Vérification de la validité de l'ID
        if (is_numeric($id) && !$this->model->get($id)) {
            return $this->view->errorNobody();
        }

        $result = $codeAde = $this->model->get($id);

        $submit = filter_input(INPUT_POST, 'submit');
        if (isset($submit)) {
            // Types de codes valides
            $validType = ['year', 'group', 'halfGroup'];

            // Récupération et validation des données
            $title = filter_input(INPUT_POST, 'title');
            $code = filter_input(INPUT_POST, 'code');
            $type = filter_input(INPUT_POST, 'type');

            // Vérification des valeurs
            if (is_string($title) && strlen($title) > 4 && strlen($title) < 30 &&
                is_numeric($code) && is_string($code) && strlen($code) < 20 &&
                in_array($type, $validType)) {

                // Mise à jour du modèle
                $codeAde->setTitle($title);
                $codeAde->setCode($code);
                $codeAde->setType($type);

                // Vérification des doublons et mise à jour dans la base de données
                if (!$this->checkDuplicateCode($codeAde) && $codeAde->update()) {
                    if ($result->getCode() != $code) {
                        $this->addFile($code);
                    }
                    $this->view->successModification();
                } else {
                    $this->view->displayErrorDoubleCode();
                }
            } else {
                $this->view->errorModification();
            }
        }
        return $this->view->displayModifyCode($codeAde->getTitle(), $codeAde->getType(), $codeAde->getCode());
    }

    /**
     * Affiche tous les codes ADE dans un tableau.
     *
     * Récupère tous les codes par type et les passe à la vue pour
     * affichage. Utilisé pour présenter la liste complète des codes.
     *
     * @return string Retourne la vue avec tous les codes ADE classés par type (année, groupe, demi-groupe).
     */
    public function displayAllCodes() {
        $years = $this->model->getAllFromType('year');
        $groups = $this->model->getAllFromType('group');
        $halfGroups = $this->model->getAllFromType('halfGroup');

        return $this->view->displayAllCode($years, $groups, $halfGroups);
    }

    /**
     * Supprime des codes ADE sélectionnés.
     *
     * Cette méthode gère la suppression de codes ADE en fonction
     * des cases à cocher sélectionnées par l'utilisateur.
     *
     * @throws \Exception En cas d'erreur lors de la suppression.
     */
    public function deleteCodes() {
        $actionDelete = filter_input(INPUT_POST, 'delete');
        if (isset($actionDelete)) {
            if (isset($_REQUEST['checkboxStatusCode'])) {
                $checked_values = $_REQUEST['checkboxStatusCode'];
                foreach ($checked_values as $id) {
                    $this->model = $this->model->get($id);
                    $this->model->delete();
                    $this->view->refreshPage();
                }
            }
        }
    }

    /**
     * Vérifie si un code ADE existe déjà avec le même titre ou code.
     *
     * Cette méthode vérifie l'existence de doublons en comparant
     * le nouveau code ADE avec ceux déjà enregistrés.
     *
     * @param CodeAde $newCodeAde Le nouveau code ADE à vérifier.
     *
     * @return bool Retourne true si un doublon est trouvé, sinon false.
     *
     * Exemple d'utilisation :
     * ```php
     * $controller = new CodeAdeController();
     * $isDuplicate = $controller->checkDuplicateCode($codeAde);
     * ```
     */
    private function checkDuplicateCode(CodeAde $newCodeAde) {
        $codesAde = $this->model->checkCode($newCodeAde->getTitle(), $newCodeAde->getCode());

        $count = 0;
        foreach ($codesAde as $codeAde) {
            // Ignore le code actuel lors de la vérification des doublons
            if ($newCodeAde->getId() === $codeAde->getId()) {
                unset($codesAde[$count]);
            }
            ++$count;
        }

        return sizeof($codesAde) > 0;
    }
}