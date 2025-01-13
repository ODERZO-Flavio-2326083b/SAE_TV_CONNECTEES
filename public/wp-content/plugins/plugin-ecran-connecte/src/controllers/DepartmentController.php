<?php
/**
 * Fichier DepartmentController.php
 *
 * Ce fichier contient la classe `DepartmentController`, qui gère les
 * opérations CRUD
 * (Créer, Lire, Mettre à jour, Supprimer) pour les départements.
 * Elle inclut des méthodes pour insérer, modifier, supprimer et
 * afficher les départements,
 * ainsi que la gestion des doublons.
 *
 * PHP version 7.4 or later
 *
 * @category API
 * @package  Controllers
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/DepartmentController
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace controllers;

use controllers\Controller;
use models\Department;
use views\DepartmentView;


/**
 * Classe DepartmentController
 *
 * Cette classe gère les opérations CRUD (Créer, Lire, Mettre à jour, Supprimer) pour
 * les départements. Elle inclut des méthodes pour insérer, modifier,
 * supprimer et afficher les
 * départements, ainsi que la gestion des doublons.
 *
 * @category API
 * @package  Controllers
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/DepartmentController Documentation de
 * la classe
 * @since    2025-01-07
 */
class DepartmentController extends Controller
{
    /**
     * Modèle département
     *
     * @var Department
     */
    private $_model;

    /**
     * Vue département
     */
    private $_view;

    /**
     * Constructeur de la classe DepartmentController.
     *
     * Initialise le modèle et la vue associés au contrôleur des départements.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function __construct()
    {
        $this->_model = new Department();
        $this->_view = new DepartmentView();
    }

    /**
     * Insère un nouveau département dans la base de données.
     *
     * Cette méthode traite les données POST issues du formulaire de création de
     * département, filtre les entrées, vérifie les doublons et insère un nouveau
     * département si aucune duplication n'est détectée. Affiche également les
     * messages de succès ou d'erreur via la vue.
     *
     * @return string Retourne le formulaire de création avec les résultats du
     * traitement.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function insert(): string
    {
        $action = filter_input(INPUT_POST, 'submit');

        if (isset($action)) {
            $name = filter_input(INPUT_POST, 'dept_name');

            if (is_string($name)) {
                $this->_model->setName($name);

                if (!$this->checkDuplicate($this->_model)) {
                    $this->_model->insert();
                    $this->_view->successCreation();
                } else {
                    $this->_view->errorDuplicate();
                }
            } else {
                $this->_view->errorCreation();
            }

        }
        return $this->_view->renderAddForm();
    }

    /**
     * Modifie un département existant dans la base de données.
     *
     * Affiche un formulaire de modification basé sur l'identifiant fourni via GET,
     * traite les données POST soumises pour changer le nom du département et vérifie
     * les doublons avant d'enregistrer les modifications.
     *
     * @return string Retourne le formulaire de modification avec les résultats du
     * traitement.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function modify(): string
    {
        if (!isset($_GET['id'])) {
            return $this->_view->errorNothing();
        }

        $id = $_GET['id'];

        if (!is_numeric($id) || !$this->_model->get($id)) {
            return $this->_view->errorNothing();
        }

        $submit = filter_input(INPUT_POST, 'submit');

        if (isset($submit)) {
            $nvNom = filter_input(INPUT_POST, 'dept_name');

            if (is_string($nvNom)) {
                $this->_model->setIdDepartment($id);
                $this->_model->setName($nvNom);

                if (!$this->checkDuplicate($this->_model)) {
                    $this->_model->update();
                    $this->_view->successUpdate();
                } else {
                    $this->_view->errorDuplicate();
                }
            } else {
                $this->_view->errorUpdate();
            }
        }

        $name = $this->_model->get($id)->getName();
        return $this->_view->renderModifForm($name);
    }

    /**
     * Affiche la liste de tous les départements sous forme de tableau.
     *
     * Récupère les données de tous les départements à partir du modèle et transmet
     * ces informations à la vue pour affichage.
     *
     * @return string Retourne le tableau affiché des départements.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function displayDeptTable(): string
    {
        $allDepts = $this->_model->getAllDepts();

        return $this->_view->renderAllDeptsTable($allDepts);
    }

    /**
     * Supprime les départements sélectionnés via une requête POST.
     *
     * Vérifie si des départements ont été sélectionnés via des cases à cocher,
     * puis les supprime de la base de données. Rafraîchit ensuite la page.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function deleteDepts(): void
    {
        $action = filter_input(INPUT_POST, 'delete');
        if (isset($action)) {
            if (isset($_REQUEST['checkboxStatusDept'])) {
                $checked_values = $_REQUEST['checkboxStatusDept'];
                foreach ($checked_values as $id) {
                    $this->_model = $this->_model->get($id);
                    $this->_model->delete();
                    $this->_view->refreshPage();
                }
            }
        }
    }


    /**
     * Vérifie la duplication d'un département dans la base de données.
     *
     * Compare le nom d'un département donné avec ceux déjà existants
     * dans la base de données pour éviter les doublons.
     *
     * @param Department $department Objet représentant le département à vérifier.
     *
     * @return bool Retourne `true` si un doublon est trouvé, sinon `false`.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function checkDuplicate(Department $department): bool
    {
        $departments = $this->_model->getDepartmentByName($department->getName());

        foreach ($departments as $d) {
            if ($department->getName() === $d->getName()) {
                return true;
            }

        }
        return false;
    }
}
