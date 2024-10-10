<?php

namespace Controllers;

use Models\CodeAde;
use Views\CodeAdeView;

/**
 * Class CodeAdeController
 *
 * Manage codes ade (create, update, delete, display)
 *
 * @package Controllers
 */
class CodeAdeController extends Controller
{

    /**
     * Model of CodeAdeController
     * @var CodeAde
     */
    private $model;

    /**
     * View of CodeAdeController
     * @var CodeAdeView
     */
    private $view;

    /**
     * Constructor of CodeAdeController.
     */
    public function __construct() {
        $this->model = new CodeAde();
        $this->view = new CodeAdeView();
    }

    /**
     * Insert a code Ade in the database and upload the schedule of this code
     *
     * @return string
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
     * Modify code Ade
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
     * Display all codes Ade in a table
     *
     * @return string
     */
    public function displayAllCodes() {
        $years = $this->model->getAllFromType('year');
        $groups = $this->model->getAllFromType('group');
        $halfGroups = $this->model->getAllFromType('halfGroup');

        return $this->view->displayAllCode($years, $groups, $halfGroups);
    }

    /**
     * Delete codes
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
     * Check if a code Ade already exists with the same title or code
     *
     * @param CodeAde $newCodeAde
     *
     * @return bool
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