<?php

namespace Views;

use Models\CodeAde;

/**
 * Class CodeAdeView
 *
 * All view for code ade (Forms, table, messages)
 *
 * @package Views
 */
class CodeAdeView extends View
{

    /**
     * Display form for create code ade
     *
     * @return string
     */
    public function createForm() {
        return '
        <form method="post">
            <div class="form-group">
                <label for="title">Titre</label>
                <input class="form-control" type="text" id="title" name="title" placeholder="Titre" required="" minlength="5" maxlength="29">
            </div>
            <div class="form-group">
                <label for="code">Code ADE</label>
                <input class="form-control" type="text" id="code" name="code" placeholder="Code ADE" required="" maxlength="19" pattern="\d+">
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
     * Display a form for modify a code ade
     *
     * @param $title    string
     * @param $type     string
     * @param $code     int
     *
     * @return string
     */
    public function displayModifyCode($title, $type, $code) {
        $page = get_page_by_title('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);

        return '
        <a href="' . esc_url(get_permalink(get_page_by_title('Gestion des codes ADE'))) . '">< Retour</a>
         <form method="post">
         	<div class="form-group">
            	<label for="title">Titre</label>
            	<input class="form-control" type="text" id="title" name="title" placeholder="Titre" value="' . $title . '">
            </div>
            <div class="form-group">
            	<label for="code">Code</label>
            	<input type="text" class="form-control" id="code" name="code" placeholder="Code" value="' . $code . '">
            </div>
            <div class="form-group">
            	<label for="type">Selectionner un type</label>
             	<select class="form-control" id="type" name="type">
                    ' . $this->createTypeOption($type) . '
                </select>
            </div>
            <button type="submit" class="btn button_ecran" name="submit">Modifier</button>
            <a href="' . $linkManageCode . '">Annuler</a>
         </form>';
    }

    /**
     * Display options for selecting a code type
     *
     * @param string $selectedType Currently selected type of the code
     *
     * @return string
     */
    private function createTypeOption($selectedType) {
        $result = '';

        // Declare available code types
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

        // Build option list
        foreach ($types as $type) {
            $result .= '<option value="' . $type['value'] . '"';

            if ($selectedType === $type['value'])
                $result .= ' selected';

            $result .= '>' . $type['title'] . '</option>' . PHP_EOL;
        }

        return $result;
    }

    /**
     * Display all informations of a code ade
     *
     * @param $years        CodeAde[]
     * @param $groups       CodeAde[]
     * @param $halfGroups   CodeAde[]
     *
     * @return          string
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
                if ($code->getType() === 'year') {
                    $code->setType('Année');
                } else if ($code->getType() === 'group') {
                    $code->setType('Groupe');
                } else if ($code->getType() === 'halfGroup') {
                    $code->setType('Demi-groupe');
                }
                ++$count;
                $row[] = [$count, $this->buildCheckbox($name, $code->getId()), $code->getTitle(), $code->getCode(), $code->getType(), $this->buildLinkForModify($linkManageCodeAde . '?id=' . $code->getId())];
            }
        }

        return $this->displayAll($name, $title, $header, $row, 'code');
    }

    /**
     * Display a success message for the creation of a new code ADE
     */
    public function successCreation() {
        $this->buildModal('Ajout du code ADE', '<p>Le code ADE a bien été ajouté</p>');
    }

    /**
     * Display a success message for the modification of a code ADE
     */
    public function successModification() {
        $page = get_page_by_title('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);
        $this->buildModal('Modification du code ADE', '<p>Le code ADE a bien été modifié</p>', $linkManageCode);
    }

    /**
     * Display an error message for the creation of a code ADE
     */
    public function errorCreation() {
        $this->buildModal('Erreur lors de l\'ajout du code ADE', '<p>Le code ADE a rencontré une erreur lors de son ajout</p>');
    }

    /**
     * Display an error message for the modification of a code ADE
     */
    public function errorModification() {
        $this->buildModal('Erreur lors de la modification du code ADE', '<p>Le code ADE a rencontré une erreur lors de sa modification</p>');
    }

    /**
     * Error message if title or code exist
     */
    public function displayErrorDoubleCode() {
        echo '<p class="alert alert-danger"> Ce code ou ce titre existe déjà</p>';
    }

    /**
     * Display an message if there is nothing
     */
    public function errorNobody() {
        $page = get_page_by_title('Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);
        echo '<p>Il n\'y a rien par ici</p><a href="' . $linkManageCode . '">Retour</a>';
    }
}
