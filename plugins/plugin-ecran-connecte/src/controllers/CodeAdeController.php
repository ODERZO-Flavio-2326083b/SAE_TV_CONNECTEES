<?php

namespace Controllers;

use Models\CodeAde;
use Views\CodeAdeView;

/**
 * Class CodeAdeController
 *
 * Cette classe gère les codes ADE : création, modification, suppression et affichage.
 *
 * @package Controllers
 */
class CodeAdeController extends Controller
{

    /**
     * Modèle associé au contrôleur CodeAde.
     * @var CodeAde
     */
    private $model;

    /**
     * Vue associée au contrôleur CodeAde.
     * @var CodeAdeView
     */
    private $view;

    /**
     * Constructeur du contrôleur CodeAdeController.
     * Initialise le modèle et la vue associés.
     */
    public function __construct() {
        $this->model = new CodeAde();
        $this->view = new CodeAdeView();
    }

    /**
     * Insère un code ADE dans la base de données et charge l'emploi du temps associé.
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

            $validType = ['year', 'group', 'halfGroup'];

            $title = filter_input(INPUT_POST, 'title');
            $code = filter_input(INPUT_POST, 'code');
            $type = filter_input(INPUT_POST, 'type');

            if (is_string($title) && strlen($title) > 4 && strlen($title) < 30 &&
                is_numeric($code) && is_string($code) && strlen($code) < 20 &&
                in_array($type, $validType)) {

                $this->model->setTitle($title);
                $this->model->setCode($code);
                $this->model->setType($type);

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
     * @return string Retourne le formulaire de modification ou un message d'erreur si le code est introuvable.
     */
    public function modify() {
        $id = $_GET['id'];
        if (is_numeric($id) && !$this->model->get($id)) {
            return $this->view->errorNobody();
        }

        $result = $codeAde = $this->model->get($id);

        $submit = filter_input(INPUT_POST, 'submit');
        if (isset($submit)) {
            $validType = ['year', 'group', 'halfGroup'];

            $title = filter_input(INPUT_POST, 'title');
            $code = filter_input(INPUT_POST, 'code');
            $type = filter_input(INPUT_POST, 'type');

            if (is_string($title) && strlen($title) > 4 && strlen($title) < 30 &&
                is_numeric($code) && is_string($code) && strlen($code) < 20 &&
                in_array($type, $validType)) {

                $codeAde->setTitle($title);
                $codeAde->setCode($code);
                $codeAde->setType($type);

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
     * Cette fonction supprime les codes ADE cochés dans une interface de gestion, puis rafraîchit la page.
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
            if ($newCodeAde->getId() === $codeAde->getId()) {
                unset($codesAde[$count]);
            }
            ++$count;
        }

        if (sizeof($codesAde) > 0) {
            return true;
        }

        return false;
    }
}
