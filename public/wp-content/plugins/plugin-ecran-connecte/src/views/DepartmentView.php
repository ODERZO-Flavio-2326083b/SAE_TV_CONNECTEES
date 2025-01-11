<?php
// TODO : Ajouter la doc du fichier
namespace views;

use models\Department;
use views\View;

// TODO : Ajouter la doc de la classe
class DepartmentView extends View
{

    /**
     * Vue pour la gestion des départements.
     *
     * Cette classe fait partie du package `views`, qui contient toutes les vues
     * utilisées dans l'application pour afficher les données de manière appropriée
     * à l'utilisateur.
     * La vue des départements permet de rendre les formulaires pour ajouter ou
     * modifier des départements, de gérer l'affichage des départements existants,
     * ainsi que d'afficher des messages de confirmation ou d'erreur.
     *
     * @package views
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
                minlength="2" maxlength="60">
                <small id="passwordHelpBlock" class="form-text text-muted">
                Format : Texte de 60 caractères maximum.</small>
            </div>
              <button type="submit" class="btn button_ecran" name="submit">Ajouter
              </button>
        </form>';
    }

    /**
     * Rendu du formulaire de modification d'un Département.
     *
     * Cette méthode génère un formulaire HTML permettant à l'utilisateur de modifier
     * un département existant.
     * Le formulaire comprend un champ pour modifier le nom du département avec une
     * validation de longueur de texte comprise entre 5 et 60 caractères. Un bouton
     * "Modifier" est inclus pour soumettre les changements, ainsi qu'un lien
     * permettant de revenir à la gestion des départements.
     *
     * @param string $name Nom actuel du département à modifier.
     *
     * @return string
     * Retourne le code HTML du formulaire de modification de département.
     *
     * @version 1.0
     * @date    08-01-2025
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
          <button type="submit" class="btn button_ecran" name="submit">
          
</button>
          <a href="'. $linkManageCode .'">Annuler</a>
        </form>';
    }

    /**
     * Rendu de la table des départements et des boutons.
     *
     * Cette méthode génère une table HTML affichant la liste des départements
     * existants. Pour chaque département, elle affiche son nom et un lien permettant
     * de le modifier. Un bouton de mise à jour est également inclus pour chaque
     * département. La méthode utilise les données d'une liste de départements pour
     * afficher ces informations.
     *
     * @param Department[] $deptList Liste des départements à afficher.
     *
     * @return string
     * Retourne le code HTML de la table des départements avec les boutons de mise à
     * jour.
     *
     * @version 1.0
     * @date    08-01-2025
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
                    $linkModifDept.'?id='
                    .$dept->getIdDepartment()
                )];
            ++$count;
        }

        return $this->displayAll($name, $title, $header, $row);
    }

    /**
     * Affiche un modal avec un message de confirmation de la création d'un
     * département.
     *
     * Cette méthode génère un modal qui informe l'utilisateur que le département a
     * bien été créé. Le message de succès est affiché dans le modal pour confirmer
     * l'opération réussie.
     *
     * @return void
     * Cette méthode ne retourne rien, elle se contente d'afficher un modal de
     * confirmation.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function successCreation()
    {
        $this->buildModal(
            "Ajout d'un département", "<p>Le département a bien été 
créé !</p>"
        );
    }

    /**
     * Affiche un modal de confirmation de la mise à jour d'un département.
     *
     * Cette méthode génère un modal qui informe l'utilisateur que le département a
     * bien été modifié. Le message de succès est affiché dans le modal pour
     * confirmer l'opération réussie.
     *
     * @return void
     * Cette méthode ne retourne rien, elle se contente d'afficher un modal de
     * confirmation.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function successUpdate()
    {
        $this->buildModal(
            "Modification d'un département", "<p>Le département a bien 
été modifié !</p>"
        );
    }

    /**
     * Affiche un modal d'erreur lors de la création d'un département.
     *
     * Cette méthode génère un modal qui informe l'utilisateur qu'une erreur s'est
     * produite lors de l'ajout du département. Le message d'erreur est affiché dans
     * le modal pour indiquer à l'utilisateur qu'une difficulté est survenue.
     *
     * @return void
     * Cette méthode ne retourne rien, elle se contente d'afficher un modal d'erreur.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function errorCreation()
    {
        $this->buildModal(
            'Erreur lors de la création du département', '<p>Erreur 
lors de l\'ajout du département.</p>'
        );
    }

    /**
     * Affiche un modal d'erreur lors de la mise à jour d'un département.
     *
     * Cette méthode génère un modal informant l'utilisateur qu'une erreur est
     * survenue lors de la modification du département.
     * Le message d'erreur est affiché dans le modal pour signaler un échec de la
     * mise à jour.
     *
     * @return void
     * Cette méthode ne retourne rien, elle se contente d'afficher un modal d'erreur.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function errorUpdate()
    {
        $this->buildModal(
            'Errur lors de la modification du département.', '<p>Erreur
 lors de la mise à jour du département.</p>'
        );
    }

    /**
     * Affiche un message d'erreur si le département existe déjà.
     *
     * Cette méthode génère un message d'alerte indiquant que le département avec le
     * nom spécifié existe déjà dans la base de données. Ce message est affiché pour
     * informer l'utilisateur qu'il ne peut pas créer un département avec un nom en
     * double.
     *
     * @return void
     * Cette méthode ne retourne rien, elle affiche simplement un message d'erreur à
     * l'utilisateur.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function errorDuplicate()
    {
        echo '<p class="alert alert-danger"> Un département avec ce nom existe déjà. 
</p>';
    }

    /**
     * Affiche un message indiquant qu'il n'y a aucun département disponible.
     *
     * Cette méthode génère un message informant l'utilisateur qu'aucun département
     * n'a été trouvé et lui fournit un lien pour retourner à la page de gestion des
     * départements.
     *
     * @return string
     * Retourne le code HTML contenant le message d'erreur et un lien pour retourner
     * à la page de gestion des départements.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function errorNothing(): string
    {
        $page = get_page_by_title_custom("Gestion des départements");
        $returnLink = get_permalink($page->ID);
        return '<p>Il n\'y a rien par ici</p><a href="' . $returnLink . '">Retour
</a>';
    }
}
