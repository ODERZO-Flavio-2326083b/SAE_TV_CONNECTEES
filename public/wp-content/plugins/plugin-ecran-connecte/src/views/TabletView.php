<?php
/**
 * Fichier TabletView.php
 *
 * Ce fichier contient la classe 'TabletView', qui est responsable
 * de l'affichage des vues
 * associées aux utilisateurs de type "tablette" dans l'application.
 * Elle permet la gestion
 * des formulaires de création, l'affichage des tablettes, ainsi que
 * la sélection de départements
 * et d'emplois du temps pour chaque utilisateur de type "tablette".
 * Cette classe étend la classe
 * 'UserView' pour réutiliser certaines méthodes communes
 * liées à l'affichage des utilisateurs.
 *
 * PHP version 8.3
 *
 * @category Views
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://www.example.com/docs/TabletView
 * @since    2024-03-03
 */
namespace views;

use models\CodeAde;
use models\Department;
use models\User;

/**
 * Class TabletView
 *
 * La classe TabletView est utilisée pour afficher les vues associées
 * à la gestion des tablettes dans l'application.
 * Elle permet d'afficher le formulaire de création d'un utilisateur
 * de type "tablette", ainsi que l'affichage des
 * tablettes existantes et de leurs emplois du temps.
 *
 * @category Views
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://www.example.com/docs/TabletView
 * @since    2024-03-03
 */
class TabletView extends UserView
{

    /**
     * Affiche le formulaire de création d'une tablette.
     *
     * Cette méthode génère un formulaire permettant la création d'un utilisateur
     * de type "tablette", avec des champs pour le login, le mot de passe,
     * le département et l'emploi du temps à choisir.
     *
     * @param array $classes  Liste des classes disponibles pour
     *                        la sélection de l'emploi du temps.
     * @param array $allDepts Liste de tous les départements.
     * @param bool  $isAdmin  Indicateur pour savoir si l'utilisateur
     *                        est administrateur.
     * @param int   $currDept Département actuel de l'utilisateur.
     *
     * @return string Le code HTML du formulaire de création de tablette.
     *
     * @version 1.0
     * @date    2024-03-03
     */
    public function displayFormTablet(array $classes, array $allDepts,
        bool $isAdmin = false,
        int $currDept = null
    ): string {
        $disabled = $isAdmin ? '' : 'disabled';

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
                <select id="deptIdTa" name="deptIdTa" class="form-control"'
            . $disabled . '>
                    ' . $this->buildDepartmentOptions($allDepts, $currDept) . '
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
     * Génère le HTML pour la sélection des codes ADE dans un formulaire.
     *
     * Cette méthode crée un champ de sélection HTML pour choisir un emploi du temps
     * à partir des classes disponibles. Elle inclut un tri des départements et
     * des options par type et titre.
     *
     * @param array   $classes  Liste des classes à afficher dans la sélection.
     * @param array   $allDepts Liste de tous les départements.
     * @param CodeAde $code     Objet CodeAde à afficher (si fourni).
     * @param int     $count    Le numéro du groupe de sélection (pour ID unique).
     *
     * @return string Le code HTML du champ de sélection des codes ADE.
     *
     * @version 1.0
     * @date    2024-03-03
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
     * Affiche toutes les tablettes existantes dans le système.
     *
     * Cette méthode génère le tableau HTML pour afficher les informations sur
     * les utilisateurs
     * de type "tablette", incluant leur login et leur département. Elle permet
     * également la sélection
     * multiple des tablettes grâce à une case à cocher.
     *
     * @param array $users        Liste des utilisateurs (tablettes) à afficher.
     * @param array $userDeptList Liste des départements des utilisateurs.
     *
     * @return string Le code HTML du tableau des tablettes.
     *
     * @version 1.0
     * @date    2024-03-03
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
