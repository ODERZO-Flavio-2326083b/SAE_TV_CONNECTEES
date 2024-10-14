<?php

namespace Controllers;

use Models\CodeAde;
use Views\CodeAdeView;

/**
 * Class CodeAdeController
 *
 * Gère les codes ADE (création, mise à jour, suppression, affichage)
 *
 * @package Controllers
 */
class CodeAdeController extends Controller
{

    /**
     * Modèle de CodeAdeController
     * @var CodeAde
     */
    private $model;

    /**
     * Vue de CodeAdeController
     * @var CodeAdeView
     */
    private $view;

    /**
     * Constructeur de CodeAdeController.
     */
    public function __construct() {
        $this->model = new CodeAde();
        $this->view = new CodeAdeView();
    }

    /**
     * Insère un code ADE dans la base de données et télécharge le planning de ce code.
     *
     * @return string Le formulaire de création.
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'submit');

        if (isset($action)) {
            $validType = ['year', 'group', 'halfGroup'];

            $title = filter_input(INPUT_POST, 'title');
            $code = filter_input(INPUT_POST, 'code');
            $type = filter_input(INPUT_POST, 'type');

            // Validation des entrées
            if (is_string($title) && strlen($title) > 4 && strlen($title) < 30 &&
                is_numeric($code) && is_string($code) && strlen($code) < 20 &&
                in_array($type, $validType)) {

                $this->model->setTitle($title);
                $this->model->setCode($code);
                $this->model->setType($type);

                // Vérifie les doublons et insère le code
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
     * @return string La vue du formulaire de modification.
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

            // Validation des entrées
            if (is_string($title) && strlen($title) > 4 && strlen($title) < 30 &&
                is_numeric($code) && is_string($code) && strlen($code) < 20 &&
                in_array($type, $validType)) {

                $codeAde->setTitle($title);
                $codeAde->setCode($code);
                $codeAde->setType($type);

                // Vérifie les doublons et met à jour le code
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
     * @return string Le tableau contenant tous les codes.
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
     * @return void
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
     * @param CodeAde $newCodeAde L'instance du nouveau code ADE à vérifier.
     * @return bool Vrai si un doublon existe, faux sinon.
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

        return sizeof($codesAde) > 0; // Renvoie vrai s'il y a des doublons
    }
}
