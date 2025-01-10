<?php
// TODO : Missing file doc comment
namespace Views;

use Models\Department;
use Views\View;

class DepartmentView extends View // TODO : Missing doc comment for class DeparmentView
{

    /**
     * Rendu du formulaire d'ajout de Département
     *
     * @return string
     */
    public function renderAddForm()
    {
        return '
        <form method="post">
            <div class="form-group">
                <label for="dept_name">Nom du département</label>
                <input class="form-control" type="text" id="dept_name" 
                name="dept_name" placeholder="Nom du département" required="" 
                minlength="5" maxlength="60">
                <small id="passwordHelpBlock" class="form-text text-muted">Format : 
                Texte de 60 caractères maximum.</small>
            </div>
              <button type="submit" class="btn button_ecran" name="submit">Ajouter
              </button>
        </form>';
    }

    /**
     * Rendu du formulaire de modification d'un Département
     *
     * @param string $name TODO : Missing parameter comment (?)
     * @param int    $lat TODO : Missing parameter comment (?) / Superfluous parameter comment
     * @param int    $long TODO : Missing parameter comment (?) / Superfluous parameter comment
     *
     * @return string
     */
    public function renderModifForm(string $name) : string
    {
        $returnPage = get_page_by_title_custom('Gestion des départements');
        $linkManageCode = get_permalink($returnPage->ID);

        return '
        <a href="' . esc_url($linkManageCode) . '">Retour</a>
        <form method="post">
            <div class="form-group">
                <label for="dept_name">Nom du département</label>
                <input class="form-control" type="text" id="dept_name" 
                name="dept_name" placeholder="Nom du département" required="" 
                minlength="5" maxlength="60" value="'. $name .'">
            </div>
          <button type="submit" class="btn button_ecran" name="submit">Modifier
          </button>
          <a href="'. $linkManageCode .'">Annuler</a>
        </form>';
    }

    /**
     * Rendu de la table des départements et des boutons
     *
     * @param $deptList Department[]
     *
     * @return string
     */
    public function renderAllDeptsTable($deptList): string
    {
        $page = get_page_by_title_custom('Modifier un département');
        $linkModifDept = get_permalink($page->ID);

        $title = 'Départements existants';
        $name = 'Dept';
        $header = ["Nom du département", "Lien de MAJ"];

        $row = array();

        $count = 1;
        foreach ($deptList as $dept) {
            $row[] = [$count,
                $this->buildCheckbox($name, $dept->getIdDepartment()),
                $dept->getName(), $this->buildLinkForModify(
                    $linkModifDept.'?id='.$dept->getIdDepartment()
                )];
            ++$count;
        }

        return $this->displayAll($name, $title, $header, $row);
    }

    /**
     * Affiche un modal avec un message de confirmation
     *
     * @return void
     */
    public function successCreation()
    {
        $this->buildModal(
            "Ajout d'un département", "<p>Le département a bien été 
créé!</p>"
        );
    }

    /**
     * Affiche un modal de confirmation de mise à jour d'un département
     *
     * @return void
     */
    public function successUpdate()
    {
        $this->buildModal(
            "Modification d'un département", "<p>Le département a bien 
été modifié!</p>"
        );
    }

    /**
     * Affiche un modal d'erreur s'il y en a une lors de la création
     * du département.
     *
     * @return void
     */
    public function errorCreation()
    {
        $this->buildModal(
            'Erreur lors de la création du département', '<p>Erreur 
lors de l\'ajout du département.</p>'
        );
    }

    /**
     * Affiche un modal d'erreur lors de la mise à jour d'un département
     *
     * @return void
     */
    public function errorUpdate()
    {
        $this->buildModal(
            'Errur lors de la modification du département.', '<p>Erreur
 lors de la mise à jour du département.</p>'
        );
    }

    /**
     * Affiche un message d'erreur si le département existe déjà
     *
     * @return void
     */
    public function errorDuplicate()
    {
        echo '<p class="alert alert-danger"> Un département avec ce nom existe déjà 
</p>';
    }

    public function errorNothing(): string // TODO : Missing doc comment for function errorNothing()
    {
        $page = get_page_by_title_custom("Gestion des départements");
        $returnLink = get_permalink($page->ID);
        return '<p>Il n\'y a rien par ici</p><a href="' . $returnLink . '">Retour
</a>';
    }
}
