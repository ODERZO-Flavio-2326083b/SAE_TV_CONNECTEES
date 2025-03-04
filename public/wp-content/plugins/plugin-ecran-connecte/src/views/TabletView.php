<?php
/**
 * Fichier TabletView.php
 *
 * Ce fichier contient la classe 'TabletView',
 * qui est responsable de l'affichage des vues liées à la gestion
 * des comptes tablettes
 * dans l'application. Cette classe génère des formulaires permettant de
 * créer des comptes tablettes,
 * ainsi que des tableaux et des messages pour gérer et afficher ces comptes.
 *
 * PHP version 8.3
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/TabletView
 * Documentation de la classe
 * @since    2025-01-13
 */

namespace views;

use models\CodeAde;
use models\Department;
use models\User;

/**
 * Classe TabletView
 *
 * Cette classe gère l'affichage des vues liées à la gestion des comptes tablettes.
 * Elle fournit des formulaires permettant de créer des comptes
 * tablettes, ainsi que des tableaux
 * pour afficher les informations des comptes créés.
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/TabletView Documentation de la classe
 * @since    2025-01-13
 */
class TabletView extends UserView
{

    /**
     * Affiche le formulaire pour créer une tablette.
     *
     * Cette méthode génère un formulaire HTML permettant à l'utilisateur de
     * créer une tablette, en fournissant des informations telles que le login,
     * le mot de passe, le département et l'emploi du temps.
     *
     * @param array    $classes  Liste des classes disponibles
     *                           pour l'emploi du temps.
     * @param array    $allDepts Liste des départements disponibles pour la
     *                           sélection.
     * @param int|null $currDept L'identifiant du département actuellement
     *                           sélectionné (facultatif, par défaut null).
     *
     * @return string Retourne le code HTML du formulaire de création de tablette.
     */
    public function displayFormTablet(array $classes, array $allDepts,
        int $currDept = null
    ): string {

        return '
        <h2> Compte tablettes</h2>
        <p class="lead">Pour créer des tablettes, remplissez ce formulaire avec les
         valeurs demandées.</p>
        <p class="lead">Vous pouvez choisir un emploi du temps par tablette</p>
        <form method="post" id="registerTvForm">
            <div class="form-group">
            	<label for="loginTa">Login</label>
            	<input type="text" class="form-control" name="loginTa" 
            	placeholder="Nom de compte" required="">
            	<small id="passwordHelpBlock" class="form-text text-muted">Votre 
            	login doit contenir entre 4 et 25 caractère</small>
            </div>
            <div class="form-group">
            	<label for="pwdTa">Mot de passe</label>
            	<input type="password" class="form-control" id="pwdTa" name="pwdTa" 
            	placeholder="Mot de passe" minlength="8" maxlength="25" required="" 
            	onkeyup=checkPwd("Ta")>
            	<input type="password" class="form-control" id="pwdConfTa" 
            	name="pwdConfirmTa" placeholder="Confirmer le Mot de passe" 
            	minlength="8" maxlength="25" required="" onkeyup=checkPwd("Ta")>
            	<small id="passwordHelpBlock" class="form-text text-muted">Votre mot 
            	de passe doit contenir entre 8 et 25 caractère</small>
            </div>
            <div class="form-group">
                <label for="deptIdTa">Département</label>
                <br>    
                <select id="deptIdTa" name="deptIdTa" class="form-control">
                    ' . $this->buildCodesOptions($allDepts, $currDept) . '
                </select>
            </div>
            <div class="form-group">
            	<label>Choisir l\'emploi du temps</label>' .
            $this->buildSelectCode($classes, $allDepts) . '
            </div>
            <button type="submit" class="btn button_ecran" id="validTa" 
            name="createTa">Créer</button>
        </form>';
    }

    /**
     * Génère un menu déroulant HTML pour sélectionner un code ADE.
     *
     * Cette méthode crée un menu `<select>` contenant les codes ADE regroupés
     * par département, et triés par type puis titre. Si un code ADE est passé
     * en paramètre, il sera sélectionné par défaut.
     *
     * @param array        $classes  Liste des classes
     *                               disponibles pour l'emploi du temps.
     * @param array        $allDepts Liste des départements disponibles pour la
     *                               sélection.
     * @param CodeAde|null $code     Code ADE à sélectionner par défaut
     *                               (facultatif).
     * @param int          $count    Compteur pour générer un identifiant unique pour
     *                               le select.
     *
     * @return string Retourne le code HTML du menu déroulant des codes ADE.
     */
    public static function buildSelectCode(array $classes, array $allDepts,
        CodeAde $code = null,
        int $count = 0
    ): string {
        $select = '<select class="form-control firstSelect" id="selectId' . $count
            . '" name="selectTa[]" required="">';

        if (!is_null($code)) {
            $select .= '<option value="' . $code->getCode() . '">'
                . $code->getTitle() . '</option>';
        } else {
            $select .= '<option disabled selected value>Sélectionnez un code ADE
</option>';
        }

        $allOptions = [];

        foreach ($classes as $class) {
            $allOptions[$class->getDeptId()][] = [
                'code' => $class->getCode(),
                'title' => $class->getTitle(),
                'type' => 'Salle'
            ];
        }

        // trier les départements par id
        ksort($allOptions);

        foreach ($allOptions as $deptId => $options) {
            $deptName = 'Département inconnu';
            foreach ($allDepts as $dept) {
                if ($dept->getIdDepartment() === $deptId) {
                    $deptName = $dept->getName();
                    break;
                }
            }
            $select .= '<optgroup label="Département ' . $deptName . '">';

            //trier les options au sein de chaque département par type puis par titre
            usort(
                $options, function ($a, $b) {
                    return [$a['type'], $a['title']] <=> [$b['type'], $b['title']];
                }
            );

            foreach ($options as $option) {
                $select .= '<option value="' . $option['code'] . '">'
                    . $option['type'] . ' - ' . $option['title']
                    . '</option>';
            }

            $select .= '</optgroup>';
        }

        $select .= '</select>';

        return $select;
    }


    /**
     * Affiche toutes les tablettes avec leurs informations.
     *
     * Cette méthode génère un tableau HTML affichant toutes les tablettes créées,
     * avec leurs informations telles que le login et le département associé.
     *
     * @param array $users        Liste des utilisateurs (tablettes) à
     *                            afficher.
     * @param array $userDeptList Liste des départements associés aux utilisateurs.
     *
     * @return string Retourne le code HTML affichant toutes
     * les tablettes sous forme de tableau.
     */
    public function displayAllTablets(array $users, array $userDeptList)
    {
        $title = 'Tablette';
        $name = 'Tablette';
        $header = ['Login', 'Département'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()),
                $user->getLogin(), $userDeptList[$count-1]];
        }

        return $this->displayAll($name, $title, $header, $row, $name);
    }
}
