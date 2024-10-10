<?php

namespace Controllers;

use Models\CodeAde;
use Models\User;
use Views\TelevisionView;

/**
 * Class TelevisionController
 *
 * Manage televisions (Create, update, delete, display, display schedules)
 *
 * @package Controllers
 */
class TelevisionController extends UserController implements Schedule
{

    /**
     * @var User
     */
    private $model;

    /**
     * @var TelevisionView
     */
    private $view;

    /**
     * Constructor of TelevisionController
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TelevisionView();
    }

    /**
     * Insert a television in the database
     *
     * @return string
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createTv');

        $codeAde = new CodeAde();

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginTv');
            $password = filter_input(INPUT_POST, 'pwdTv');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmTv');
            $codes = $_POST['selectTv'];

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm) {

                $codesAde = array();
                foreach ($codes as $code) {
                    if (is_numeric($code) && $code > 0) {
                        if (is_null($codeAde->getByCode($code)->getId())) {
                            return 'error';
                        } else {
                            $codesAde[] = $codeAde->getByCode($code);
                        }
                    }
                }

                $this->model->setLogin($login);
                $this->model->setEmail($login . '@' . $login . '.fr');
                $this->model->setPassword($password);
                $this->model->setRole('television');
                $this->model->setCodes($codesAde);

                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorLogin();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayFormTelevision($years, $groups, $halfGroups);
    }

    /**
     * Modify a television
     *
     * @param $user User
     *
     * @return string
     */
    public function modify($user) {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $codeAde = new CodeAde();

        $action = filter_input(INPUT_POST, 'modifValidate');

        if (isset($action)) {
            $codes = $_POST['selectTv'];

            $codesAde = array();
            foreach ($codes as $code) {
                if (is_null($codeAde->getByCode($code)->getId())) {
                    return 'error';
                } else {
                    $codesAde[] = $codeAde->getByCode($code);
                }
            }

            $user->setCodes($codesAde);

            if ($user->update()) {
                $this->view->displayModificationValidate($linkManageUser);
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->modifyForm($user, $years, $groups, $halfGroups);
    }

    /**
     * Display all televisions in a table
     *
     * @return string
     */
    public function displayAllTv() {
        $users = $this->model->getUsersByRole('television');
        return $this->view->displayAllTv($users);
    }

    /**
     * Display a list a schedule
     *
     * @return mixed|string
     */
    public function displayMySchedule() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        $user = $this->model->getMycodes([$user])[0];

        $string = "";
        if (sizeof($user->getCodes()) > 1) {
            if (get_theme_mod('ecran_connecte_schedule_scroll', 'vert') == 'vert') {
                $string .= '<div class="ticker1">
						<div class="innerWrap tv-schedule">';
                foreach ($user->getCodes() as $code) {
                    $path = $this->getFilePath($code->getCode());
                    if (file_exists($path)) {
                        if ($this->displaySchedule($code->getCode())) {
                            $string .= '<div class="list">';
                            $string .= $this->displaySchedule($code->getCode());
                            $string .= '</div>';
                        }
                    }
                }
                $string .= '</div></div>';
            } else {
                $string .= $this->view->displayStartSlide();
                foreach ($user->getCodes() as $code) {
                    $path = $this->getFilePath($code->getCode());
                    if (file_exists($path)) {
                        if ($this->displaySchedule($code->getCode())) {
                            $string .= $this->view->displayMidSlide();
                            $string .= $this->displaySchedule($code->getCode());
                            $string .= $this->view->displayEndDiv();
                        }
                    }
                }
                $string .= $this->view->displayEndDiv();
            }
        } else {
            if (!empty($user->getCodes()[0])) {
                $string .= $this->displaySchedule($user->getCodes()[0]->getCode());
            } else {
                $string .= '<p>Vous n\'avez pas cours </p>';
            }
        }
        return $string;
    }
}
