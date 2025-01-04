<?php

namespace controllers;

use models\CodeAde;
use views\CodeAdeView;

/**
 * Class CodeAdeController
 *
 * Gère les codes ADE (création, mise à jour, suppression, affichage)
 *
 * @package controllers
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
     * Constructeur de la classe.
     *
     * Ce constructeur initialise les instances du modèle 'CodeAde' et de la vue
     * 'CodeAdeView'.
     *
     * @version 1.0
     * @date 2024-09-16
     */
    public function __construct() {
        $this->model = new CodeAde();
        $this->view = new CodeAdeView();
    }

    /**
     * Insère un nouveau code ADE.
     *
     * Cette méthode récupère les données soumises via un formulaire, valide les
     * entrées, et insère un nouveau code ADE dans le modèle si les données sont
     * valides. Elle gère également l'affichage des messages de succès ou d'erreur
     * selon le résultat de l'insertion.
     *
     * @return string Formulaire de création de Code
     *
     * @version 1.0
     * @date 2024-09-16
     */
    public function insert() : string {
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
     * Cette méthode récupère l'ID d'un code ADE à modifier via les paramètres
     * d'URL, valide l'existence de ce code, puis traite les données soumises
     * par le formulaire de modification. Les entrées sont validées et, si
     * elles sont correctes, le code ADE est mis à jour dans le modèle.
     * Les doublons sont également vérifiés. Des messages de succès ou d'erreur
     * sont affichés selon le résultat de l'opération.
     *
     * @return string Le rendu du formulaire de modification ou un message d'erreur.
     *
     * @version 1.0
     * @date 2024-09-16
     */
    public function modify(): string {
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
     * Affiche tous les codes ADE classés par type.
     *
     * Cette méthode récupère tous les codes ADE de chaque type (année,
     * groupe, demi-groupe) via le modèle. Les données sont ensuite passées à
     * la vue pour être affichées.
     *
     * @return string Le rendu HTML des codes ADE classés par type.
     *
     * @version 1.0
     * @date 2024-09-16
     */
    public function displayAllCodes() : string {
        $years = $this->model->getAllFromType('year');
        $groups = $this->model->getAllFromType('group');
        $halfGroups = $this->model->getAllFromType('halfGroup');

        return $this->view->displayAllCode($years, $groups, $halfGroups);
    }

    /**
     * Supprime les codes ADE sélectionnés.
     *
     * Cette méthode vérifie si une demande de suppression a été effectuée.
     * Si c'est le cas, elle récupère les IDs des codes sélectionnés et
     * les supprime un par un en appelant la méthode 'delete' du modèle.
     * La page est rafraîchie après la suppression.
     *
     * @return void
     *
     * @version 1.0
     * @date 2024-09-16
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
     * Vérifie si le code ADE à insérer est déjà présent dans la base de données.
     *
     * Cette méthode compare le code ADE passé en paramètre avec les enregistrements
     * existants dans la base de données. Elle exclut l'enregistrement actuel si
     * l'ID correspond et renvoie vrai s'il existe d'autres codes similaires.
     *
     * @param CodeAde $newCodeAde L'objet CodeAde à vérifier pour les doublons.
     * @return bool Retourne vrai si un doublon est trouvé, sinon faux.
     *
     * @version 1.0
     * @date 2024-09-16
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
