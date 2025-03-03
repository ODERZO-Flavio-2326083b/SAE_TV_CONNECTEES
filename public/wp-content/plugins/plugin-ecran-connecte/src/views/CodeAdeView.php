<?php
/**
 * Fichier CodeAdeView.php
 *
 * Ce fichier contient la classe 'CodeAdeView', qui est responsable de la gestion
 * des vues liées aux codes ADE dans l'application. Cela inclut la gestion des
 * formulaires de création et de modification des codes ADE, l'affichage des tableaux
 * des codes ADE, et la gestion des messages d'erreur ou de succès.
 *
 * PHP version 8.3
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/CodeAdeView
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace views;

use models\CodeAde;
use models\Department;

/**
 * Class CodeAdeView
 *
 * Gère toutes les vues liées aux codes ADE dans l'application.
 * Cela comprend les formulaires
 * de création et de modification des codes ADE,
 * l'affichage des codes ADE dans un tableau,
 * ainsi que les messages d'information, d'erreur ou de succès.
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/CodeAdeView Documentation de la classe
 * @since    2025-01-07
 */
class CodeAdeView extends View
{

    /**
     * Affiche le formulaire pour créer un code ADE.
     *
     * Cette méthode génère un formulaire HTML pour la création d'un code ADE. Le
     * formulaire inclut des champs pour saisir un titre, un code ADE et un type de
     * code (Année, Groupe, Demi-groupe).
     * Les champs sont soumis à des validations basiques comme des contraintes de
     * longueur et de type pour les champs texte.
     *
     * @param array    $allDepts Une liste de tous les départements présents
     *                           dans la base de données.
     * @param bool     $isAdmin  Un booléen correspondant à "true"
     *                           si l'utilisateur est un
     *                           administrateur, et "false" sinon.
     * @param int|null $currDept Le numéro du département actuel.
     *
     * @return string
     * Retourne le code HTML du formulaire de création de code ADE.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function createForm(array $allDepts, bool $isAdmin = false,
        int $currDept = null
    ) : string {
        $disabled = $isAdmin ? '' : 'disabled';
        return '
        <form method="post">
            <div class="form-group">
                <label for="title">Titre</label>
                <input class="form-control" type="text" id="title" name="title" 
                placeholder="Titre" required="" minlength="5" maxlength="29">
            </div>
            <div class="form-group">
                <label for="code">Code ADE</label>
                <input class="form-control" type="text" id="code" name="code" 
                placeholder="Code ADE" required="" maxlength="19" pattern="\d+">
            </div>
            <div class="form-group">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" 
                    id="year" value="year">
                    <label class="form-check-label" for="year">Année</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" 
                    id="group" value="group">
                    <label class="form-check-label" for="group">Groupe</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" 
                    id="halfGroup" value="halfGroup">
                    <label class="form-check-label" for="halfGroup">Demi-groupe
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" 
                    id="class" value="class">
                    <label class="form-check-label" for="class">Salle
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="dept">Département</label>
                <br>    
                <select id="dept" name="dept" class="form-control"' . $disabled . '>
                    ' . $this->buildCodesOptions($allDepts, $currDept) . '
                </select>
            </div>
          <button type="submit" class="btn button_ecran" name="submit">Ajouter
          </button>
        </form>';
    }

    /**
     * Affiche un formulaire pour modifier un code ADE.
     *
     * Cette méthode génère un formulaire HTML permettant de modifier un code ADE
     * existant. Le formulaire est pré-rempli avec le titre, le code et le type du
     * code ADE à modifier. L'utilisateur peut ajuster ces valeurs avant de soumettre
     * le formulaire pour effectuer les modifications. Un lien de retour vers la page
     * de gestion des codes ADE est également fourni.
     *
     * @param string   $title    Titre du code ADE
     *                           à modifier.
     * @param string   $type     Type du code ADE
     *                           à modifier.
     * @param int      $code     Code ADE
     *                           à
     *                           modifier.
     * @param array    $allDepts Une liste de tous les départements présents
     *                           dans la base de données.
     * @param bool     $isAdmin  Un booléen correspondant à "true"
     *                           si l'utilisateur est un
     *                           administrateur, et "false" sinon.
     * @param int|null $currDept Le numéro du département actuel.
     *
     * @return string
     * Retourne le code HTML du formulaire de modification de code ADE.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayModifyCode($title, $type, $code, array $allDepts,
        bool $isAdmin = false,
        int $currDept = null
    ) : string {
        $disabled = $isAdmin ? '' : 'disabled';
        $page = get_page_by_title_custom('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);

        return '
        <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Gestion des codes ADE')
                )
            ) . '">< Retour</a>
         <form method="post">
         	<div class="form-group">
            	<label for="title">Titre</label>
            	<input class="form-control" type="text" id="title" name="title" 
            	placeholder="Titre" value="' . $title . '">
            </div>
            <div class="form-group">
            	<label for="code">Code</label>
            	<input type="text" class="form-control" id="code" name="code" 
            	placeholder="Code" value="' . $code . '">
            </div>
            <div class="form-group">
            	<label for="type">Selectionner un type</label>
             	<select class="form-control" id="type" name="type">
                    ' . $this->createTypeOption($type) . '
                </select>
            </div>
            <div class="form-group">
                <label for="dept">Département</label>
                <br>    
                <select id="dept" name="dept" class="form-control"' . $disabled . '>
                    ' . $this->buildCodesOptions($allDepts, $currDept) . '
                </select>
            </div>
            <button type="submit" class="btn button_ecran" name="submit">Modifier
            </button>
            <a href="' . $linkManageCode . '">Annuler</a>
         </form>';
    }

    /**
     * Affiche les options pour sélectionner un type de code.
     *
     * Cette méthode génère les options HTML pour un élément '<select>', permettant
     * à l'utilisateur de choisir un type de code parmi les options disponibles
     * (Année, Groupe, Demi-Groupe). Elle met en surbrillance l'option qui est
     * actuellement sélectionnée, basée sur le type passé en paramètre.
     *
     * @param string $selectedType Type actuellement sélectionné.
     *
     * @return string
     * Retourne le code HTML des options de sélection de type de code.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function createTypeOption($selectedType) : string
    {
        $result = '';

        // Déclare les types de code disponibles
        $types = array(
            array(
                'value' => 'year',
                'title' => 'Année',
            ),
            array(
                'value' => 'group',
                'title' => 'Groupe',
            ),
            array(
                'value' => 'halfGroup',
                'title' => 'Demi-Groupe',
            ),
            array(
                'value' => 'class',
                'title' => 'Salle',
            )
        );

        // Construit la liste des options
        foreach ($types as $type) {
            $result .= '<option value="' . $type['value'] . '"';

            if ($selectedType === $type['value']) {
                $result .= ' selected';
            }

            $result .= '>' . $type['title'] . '</option>' . PHP_EOL;
        }

        return $result;
    }

    /**
     * Affiche toutes les informations des codes ADE.
     *
     * Cette méthode génère et retourne le code HTML affichant tous les codes ADE
     * présents. Elle regroupe les codes ADE par année, groupe et demi-groupe, et
     * permet d'afficher des informations telles que le titre, le code, le type et un
     * lien de modification pour chaque code. La méthode construit également une
     * table avec ces informations.
     *
     * @param CodeAde[] $years      Liste des codes ADE de type
     *                              année.
     * @param CodeAde[] $groups     Liste des codes ADE de type groupe.
     * @param CodeAde[] $halfGroups Liste des codes ADE de type demi-groupe.
     *
     * @return string
     * Retourne le code HTML affichant tous les codes ADE sous forme de tableau.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayAllCode($years, $groups, $halfGroups, $class) : string
    {
        $deptModel = new Department();

        $page = get_page_by_title_custom('Modifier un code ADE');
        $linkManageCodeAde = get_permalink($page->ID);

        $title = 'Codes ADE';
        $name = 'Code';
        $header = ['Titre', 'Code', 'Type', 'Département', 'Modifier'];

        $codesAde = [$years, $groups, $halfGroups, $class];

        $row = array();
        $count = 0;

        foreach ($codesAde as $codeAde) {
            foreach ($codeAde as $code) {
                if ($code->getType() === 'year') {
                    $code->setType('Année');
                } elseif ($code->getType() === 'group') {
                    $code->setType('Groupe');
                } elseif ($code->getType() === 'halfGroup') {
                    $code->setType('Demi-groupe');
                } elseif ($code->getType() === 'class') {
                    $code->setType('Salle');
                }
                ++$count;
                $row[] = [$count,
                    $this->buildCheckbox($name, $code->getId()),
                    $code->getTitle(),
                    $code->getCode(),
                    $code->getType(),
                    $deptModel->get($code->getDeptId())->getName(),
                    $this->buildLinkForModify(
                        $linkManageCodeAde . '?id='
                        . $code->getId()
                    )];
            }
        }

        return $this->displayAll($name, $title, $header, $row, 'code');
    }

    /**
     * Affiche un message de succès pour la création d'un nouveau code ADE.
     *
     * Cette méthode génère une modale indiquant que le code ADE a été ajouté avec
     * succès. Le message de succès est affiché à l'utilisateur pour lui confirmer
     * l'ajout du code.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function successCreation()
    {
        $this->buildModal(
            'Ajout du code ADE', '<p>Le code ADE a bien été ajouté
</p>'
        );
    }

    /**
     * Affiche un message de succès pour la modification d'un code ADE.
     *
     * Cette méthode génère une modale indiquant que le code ADE a été modifié avec
     * succès. Un message de confirmation est affiché, et un lien est fourni pour
     * rediriger l'utilisateur vers la page de gestion des codes ADE.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function successModification()
    {
        $page = get_page_by_title_custom('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);
        $this->buildModal(
            'Modification du code ADE', '<p>Le code ADE a bien été 
modifié</p>', $linkManageCode
        );
    }

    /**
     * Affiche un message d'erreur lors de la création d'un code ADE.
     *
     * Cette méthode génère une modale indiquant qu'une erreur s'est produite lors de
     * l'ajout d'un code ADE. Un message d'erreur est affiché pour informer
     * l'utilisateur que le code n'a pas pu être ajouté.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function errorCreation()
    {
        $this->buildModal(
            'Erreur lors de l\'ajout du code ADE', '<p>Le code ADE a 
rencontré une erreur lors de son ajout</p>'
        );
    }

    /**
     * Affiche un message d'erreur lors de la modification d'un code ADE.
     *
     * Cette méthode génère une modale indiquant qu'une erreur s'est produite lors de
     * la modification d'un code ADE. Un message d'erreur est affiché pour informer
     * l'utilisateur que la modification n'a pas pu être effectuée.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function errorModification()
    {
        $this->buildModal(
            'Erreur lors de la modification du code ADE', '<p>Le code 
ADE a rencontré une erreur lors de sa modification</p>'
        );
    }

    /**
     * Affiche un message d'erreur si le titre ou le code existe déjà.
     *
     * Cette méthode affiche un message d'alerte indiquant que le titre ou le code
     * spécifié existe déjà dans la base de données.
     * L'utilisateur est informé qu'il doit choisir un titre ou un code différent
     * pour éviter les doublons.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayErrorDoubleCode()
    {
        echo '<p class="alert alert-danger"> Ce code ou ce titre existe déjà</p>';
    }

    /**
     * Affiche un message si aucun code n'est disponible.
     *
     * Cette méthode affiche un message informant l'utilisateur qu'aucun code ADE
     * n'est disponible dans la liste.
     * Elle inclut également un lien permettant à l'utilisateur de revenir à la page
     * de gestion des codes ADE.
     *
     * @return string
     * Retourne le code HTML contenant le message et le lien de retour vers la page
     * de gestion des codes ADE.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function errorNobody()
    {
        $page = get_page_by_title_custom('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);
        return '<p>Il n\'y a rien par ici</p><a href="' . $linkManageCode . '">
Retour</a>';
    }
}
