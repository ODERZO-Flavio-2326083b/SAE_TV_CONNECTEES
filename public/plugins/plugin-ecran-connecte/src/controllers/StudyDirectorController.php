<?php

namespace Controllers;

use Models\User;
use Views\StudyDirectorView;

/**
 * Class StudyDirectorController
 *
 * Manage study director (Create, update, delete, display)
 *
 * @package Controllers
 */
class StudyDirectorController extends UserController implements Schedule
{

    /**
     * @var User
     */
    private $model;

    /**
     * @var StudyDirectorView
     */
    private $view;

    /**
     * Constructor of StudyDirectorController
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new StudyDirectorView();
    }

    /**
     * Display the schedule of the study director
     *
     * @return bool|mixed|string
     */
    public function displayMySchedule() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        if (sizeof($user->getCodes()) > 0) {
            return $this->displaySchedule($user->getCodes()[0]->getCode());
        } else {
            return $this->view->errorMessageNoCodeRegister();
        }
    }

    /**
     * Insert a study director in the database
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createDirec');

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginDirec');
            $password = filter_input(INPUT_POST, 'pwdDirec');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmDirec');
            $email = filter_input(INPUT_POST, 'emailDirec');
            $code = filter_input(INPUT_POST, 'codeDirec');

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('directeuretude');
                $this->model->setCodes($code);

                if ($this->model->insert()) {
                    $path = $this->getFilePath($code);
                    if (!file_exists($path)) {
                        $this->addFile($code);
                    }
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }
        return $this->view->displayCreateDirector();
    }

    /**
     * Modify the study director
     *
     * @param $user   User
     *
     * @return string
     */
    public function modify($user) {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $action = filter_input(INPUT_POST, 'modifValidate');

        if ($action === 'Valider') {
            $code = filter_input(INPUT_POST, 'modifCode');
            if (is_numeric($code)) {
                $user->setRole('directeuretude');
                $user->getCodes()[0]->setCode($code);

                if ($user->update()) {
                    $this->view->displayModificationValidate($linkManageUser);
                }
            }
        }
        return $this->view->displayModifyStudyDirector($user);
    }

    /**
     * Display all study directors
     */
    public function displayAllStudyDirector() {
        $users = $this->model->getUsersByRole('directeuretude');
        $users = $this->model->getMyCodes($users);
        return $this->view->displayAllStudyDirector($users);
    }
}
