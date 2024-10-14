<?php

namespace Views;

use Models\CodeAde;

/**
 * Class CodeAdeView
 *
 * Cette classe est responsable de l'affichage des vues pour les codes ADE.
 * Elle gère les formulaires, les tableaux et les messages associés.
 *
 * @package Views
 */
class CodeAdeView extends View
{
    /**
     * Affiche le formulaire pour créer un code ADE.
     *
     * @return string Le code HTML du formulaire de création de code ADE.
     *
     * @example
     * $view = new CodeAdeView();
     * echo $view->createForm();
     */
    public function createForm() {
        return '
        <form method="post">
            <div class="form-group">
                <label for="title">Titre</label>
                <input class="form-control" type="text" id="title" name="title" placeholder="Titre" required minlength="5" maxlength="29">
            </div>
            <div class="form-group">
                <label for="code">Code ADE</label>
                <input class="form-control" type="text" id="code" name="code" placeholder="Code ADE" required maxlength="19" pattern="\d+">
            </div>
            <div class="form-group">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="year" value="year">
                    <label class="form-check-label" for="year">Année</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="group" value="group">
                    <label class="form-check-label" for="group">Groupe</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="halfGroup" value="halfGroup">
                    <label class="form-check-label" for="halfGroup">Demi-groupe</label>
                </div>
            </div>
          <button type="submit" class="btn button_ecran" name="submit">Ajouter</button>
        </form>';
    }

    /**
     * Affiche le formulaire pour modifier un code ADE existant.
     *
     * @param string $title Le titre du code ADE à modifier.
     * @param string $type Le type du code ADE (année, groupe, demi-groupe).
     * @param int $code Le code ADE à modifier.
     *
     * @return string Le code HTML du formulaire de modification de code ADE.
     *
     * @example
     * $view = new CodeAdeView();
     * echo $view->displayModifyCode('Titre Exemple', 'group', 123);
     */
    public function displayModifyCode($title, $type, $code) {
        $page = get_page_by_title('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);

        return '
        <a href="' . esc_url(get_permalink(get_page_by_title('Gestion des codes ADE'))) . '">< Retour</a>
         <form method="post">
         	<div class="form-group">
            	<label for="title">Titre</label>
            	<input class="form-control" type="text" id="title" name="title" placeholder="Titre" value="' . $title . '" required minlength="5" maxlength="29">
            </div>
            <div class="form-group">
            	<label for="code">Code</label>
            	<input type="text" class="form-control" id="code" name="code" placeholder="Code" value="' . $code . '" required maxlength="19">
            </div>
            <div class="form-group">
            	<label for="type">Sélectionner un type</label>
             	<select class="form-control" id="type" name="type">
                    ' . $this->createTypeOption($type) . '
                </select>
            </div>
            <button type="submit" class="btn button_ecran" name="submit">Modifier</button>
            <a href="' . $linkManageCode . '">Annuler</a>
         </form>';
    }

    /**
     * Affiche les options pour sélectionner un type de code.
     *
     * @param string $selectedType Le type actuellement sélectionné.
     *
     * @return string Les options HTML pour le sélecteur de type.
     */
    private function createTypeOption($selectedType) {
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
        );

        // Construit la liste d'options
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
     * @param CodeAde[] $years Les codes ADE de type année.
     * @param CodeAde[] $groups Les codes ADE de type groupe.
     * @param CodeAde[] $halfGroups Les codes ADE de type demi-groupe.
     *
     * @return string Le code HTML pour afficher tous les codes ADE.
     *
     * @example
     * $view = new CodeAdeView();
     * echo $view->displayAllCode($yearsArray, $groupsArray, $halfGroupsArray);
     */
    public function displayAllCode($years, $groups, $halfGroups) {
        $page = get_page_by_title('Modifier un code ADE');
        $linkManageCodeAde = get_permalink($page->ID);

        $title = 'Codes Ade';
        $name = 'Code';
        $header = ['Titre', 'Code', 'Type', 'Modifier'];

        $codesAde = [$years, $groups, $halfGroups];

        $row = array();
        $count = 0;

        foreach ($codesAde as $codeAde) {
            foreach ($codeAde as $code) {
                // Conversion des types en format lisible
                if ($code->getType() === 'year') {
                    $code->setType('Année');
                } else if ($code->getType() === 'group') {
                    $code->setType('Groupe');
                } else if ($code->getType() === 'halfGroup') {
                    $code->setType('Demi-groupe');
                }
                ++$count;
                $row[] = [
                    $count,
                    $this->buildCheckbox($name, $code->getId()),
                    $code->getTitle(),
                    $code->getCode(),
                    $code->getType(),
                    $this->buildLinkForModify($linkManageCodeAde . '?id=' . $code->getId())
                ];
            }
        }

        return $this->displayAll($name, $title, $header, $row, 'code');
    }

    /**
     * Affiche un message de succès pour la création d'un nouveau code ADE.
     */
    public function successCreation() {
        $this->buildModal('Ajout du code ADE', '<p>Le code ADE a bien été ajouté</p>');
    }

    /**
     * Affiche un message de succès pour la modification d'un code ADE.
     */
    public function successModification() {
        $page = get_page_by_title('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);
        $this->buildModal('Modification du code ADE', '<p>Le code ADE a bien été modifié</p>', $linkManageCode);
    }

    /**
     * Affiche un message d'erreur lors de la création d'un code ADE.
     */
    public function errorCreation() {
        $this->buildModal('Erreur lors de l\'ajout du code ADE', '<p>Le code ADE a rencontré une erreur lors de son ajout</p>');
    }

    /**
     * Affiche un message d'erreur lors de la modification d'un code ADE.
     */
    public function errorModification() {
        $this->buildModal('Erreur lors de la modification du code ADE', '<p>Le code ADE a rencontré une erreur lors de sa modification</p>');
    }

    /**
     * Affiche un message d'erreur si le titre ou le code existe déjà.
     */
    public function displayErrorDoubleCode() {
        echo '<p class="alert alert-danger"> Ce code ou ce titre existe déjà</p>';
    }

    /**
     * Affiche un message si aucune donnée n'est disponible.
     */
    public function errorNobody() {
        $page = get_page_by_title('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);
        echo '<p>Il n\'y a rien par ici</p><a href="' . $linkManageCode . '">Retour</a>';
    }
}
