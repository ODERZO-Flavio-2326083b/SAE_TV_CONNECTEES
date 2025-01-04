<?php

namespace views;

use models\CodeAde;
use models\Department;
use models\User;

/**
 * Class TelevisionView
 *
 * Contient toutes les vues liées à la télévision (Formulaires, tableaux)
 *
 * @package views
 */
class TelevisionView extends UserView
{
    /**
     * Affiche le formulaire de création d'un compte télévision.
     *
     * Cette méthode génère un formulaire HTML permettant de créer un compte télévision.
     * Les utilisateurs peuvent entrer un login, un mot de passe, et sélectionner
     * des emplois du temps. Un bouton permet d'ajouter plusieurs emplois du temps
     * si nécessaire.
     *
     * @param array $years Un tableau d'objets représentant les années disponibles.
     *                     Chaque objet doit implémenter les méthodes nécessaires
     *                     pour l'affichage des informations.
     * @param array $groups Un tableau d'objets représentant les groupes disponibles.
     *                      Chaque objet doit implémenter les méthodes nécessaires
     *                      pour l'affichage des informations.
     * @param array $halfGroups Un tableau d'objets représentant les demi-groupes disponibles.
     *                          Chaque objet doit implémenter les méthodes nécessaires
     *                          pour l'affichage des informations.
     * @param Department[] $allDepts Liste de tous les objets départements
     * @param bool $isAdmin Booléen, true si l'utilisateur est un admin
     * @param int|null $currDept (optionnel) Département actuel de l'utilisateur, null s'il est un admin
     *
     * @return string Le code HTML du formulaire de création de compte télévision.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayFormTelevision(array $years, array $groups, array $halfGroups, array $allDepts, bool $isAdmin = false, int $currDept = null): string {
        $disabled = $isAdmin ? '' : 'disabled';

		return '
        <h2> Compte télévision</h2>
        <p class="lead">Pour créer des télévisions, remplissez ce formulaire avec les valeurs demandées.</p>
        <p class="lead">Vous pouvez mettre autant d\'emploi du temps que vous souhaitez, cliquez sur "Ajouter des emplois du temps"</p>
        <form method="post" id="registerTvForm">
            <div class="form-group">
            	<label for="loginTv">Login</label>
            	<input type="text" class="form-control" name="loginTv" placeholder="Nom de compte" required="">
            	<small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractère</small>
            </div>
            <div class="form-group">
            	<label for="pwdTv">Mot de passe</label>
            	<input type="password" class="form-control" id="pwdTv" name="pwdTv" placeholder="Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("Tv")>
            	<input type="password" class="form-control" id="pwdConfTv" name="pwdConfirmTv" placeholder="Confirmer le Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("Tv")>
            	<small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractère</small>
            </div>
            <div class="form-group">
                <label for="deptIdTv">Département</label>
                <br>    
                <select id="deptIdTv" name="deptIdTv" class="form-control"' . $disabled . '>
                    ' . $this->buildDepartmentOptions($allDepts, $currDept) . '
                </select>
            </div>
            <div class="form-group">
            	<label>Premier emploi du temps</label>' .
            $this->buildSelectCode($years, $groups, $halfGroups, $allDepts) . '
            </div>
            <input type="button" class="btn button_ecran" onclick="addButtonTv()" value="Ajouter des emplois du temps">
            <button type="submit" class="btn button_ecran" id="validTv" name="createTv">Créer</button>
        </form>';
    }

    /**
     * Affiche tous les comptes de télévision dans un tableau.
     *
     * Cette méthode génère un tableau HTML récapitulatif de tous les utilisateurs de type
     * télévision. Pour chaque utilisateur, elle affiche le login, le nombre d'emplois du temps
     * associés, ainsi qu'un lien pour modifier les informations de l'utilisateur.
     *
     * @param array $users Un tableau d'objets représentant les utilisateurs de type télévision.
     *                     Chaque objet doit implémenter les méthodes nécessaires pour récupérer
     *                     le login et les codes d'emploi du temps associés.
     * @param array $userDeptList Tableau contenant tous les noms des départements dans le même ordre
     *                            que le tableau $users
     *
     * @return string Le code HTML du tableau affichant les utilisateurs de télévision.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayAllTv(array $users, array $userDeptList): string {
        $page = get_page_by_title_custom('Modifier un utilisateur');
        $linkManageUser = get_permalink($page->ID);

        $title = 'Televisions';
        $name = 'Tele';
        $header = ['Login', 'Nombre d\'emplois du temps ', 'Département', 'Modifier'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count,
                $this->buildCheckbox($name, $user->getId()),
                $user->getLogin(), sizeof($user->getCodes()), $userDeptList[$count-1],
                $this->buildLinkForModify($linkManageUser . '?id=' . $user->getId())];
        }

        return $this->displayAll($name, $title, $header, $row, 'tele');
    }

    /**
     * Affiche le formulaire de modification pour un utilisateur de type télévision.
     *
     * Cette méthode génère un formulaire HTML permettant de modifier les emplois du temps
     * associés à un utilisateur spécifique. Elle remplit les champs de sélection avec les
     * années, groupes et demi-groupes disponibles, tout en pré-remplissant les informations
     * existantes de l'utilisateur.
     *
     * @param User $user L'utilisateur à modifier, qui doit implémenter les méthodes
     *                     nécessaires pour récupérer le login et les codes d'emploi du temps.
     * @param array<CodeAde> $years Un tableau d'objets représentant les années disponibles.
     * @param array<CodeAde> $groups Un tableau d'objets représentant les groupes disponibles.
     * @param array<CodeAde> $halfGroups Un tableau d'objets représentant les demi-groupes disponibles.
     * @param array<Department> $allDepts Tableau d'objets de tous les Department de la base de données.
     *
     * @return string Le code HTML du formulaire de modification de l'utilisateur.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function modifyForm(User $user, array $years, array $groups, array $halfGroups, array $allDepts) : string {
        $count = 0;
        $string = '
        <a href="' . esc_url(get_permalink(get_page_by_title_custom('Gestion des utilisateurs'))) . '">< Retour</a>
        <h2>' . $user->getLogin() . '</h2>
         <form method="post" id="registerTvForm">
            <label id="selectId1"> Emploi du temps</label>';

        foreach ($user->getCodes() as $code) {
            $count = $count + 1;
            if ($count == 1) {
                $string .= $this->buildSelectCode($years, $groups, $halfGroups, $allDepts, $code, $count);
            } else {
                $string .= '
					<div class="row">' .
                    $this->buildSelectCode($years, $groups, $halfGroups, $allDepts, $code, $count) .
                    '<input type="button" id="selectId' . $count . '" onclick="deleteRow(this.id)" class="btn button_ecran" value="Supprimer">
					</div>';
            }
        }

        if ($count == 0) {
            $string .= $this->buildSelectCode($years, $groups, $halfGroups, $allDepts, null, $count);
        }

        $page = get_page_by_title_custom('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);
        $string .= '
            <input type="button" class="btn button_ecran" onclick="addButtonTv()" value="Ajouter des emplois du temps">
            <button name="modifValidate" class="btn button_ecran" type="submit" id="validTv">Valider</button>
            <a href="' . $linkManageUser . '">Annuler</a>
        </form>';
        return $string;
    }

    /**
     * Génère un élément '<select>' HTML pour sélectionner des emplois du temps.
     *
     * Cette méthode crée un menu déroulant contenant des options pour les années,
     * groupes et demi-groupes. Si un code d'emploi du temps est fourni, il sera
     * pré-sélectionné dans le menu déroulant.
     *
     * @param array<CodeAde> $years Un tableau d'objets représentant les années disponibles.
     * @param array<CodeAde> $groups Un tableau d'objets représentant les groupes disponibles.
     * @param array<CodeAde> $halfGroups Un tableau d'objets représentant les demi-groupes disponibles.
     * @param CodeAde|null $code Un objet représentant le code d'emploi du temps à pré-sélectionner (facultatif).
     * @param int $count Un compteur utilisé pour générer un ID unique pour le '<select>' (par défaut à 0).
     *
     * @return string Le code HTML du menu déroulant pour sélectionner un emploi du temps.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function buildSelectCode(array $years, array $groups, array $halfGroups, array $allDepts, CodeAde $code = null, int $count = 0): string {
        $select = '<select class="form-control firstSelect" id="selectId' . $count . '" name="selectTv[]" required="">';

        if (!is_null($code)) {
            $select .= '<option value="' . $code->getCode() . '">' . $code->getTitle() . '</option>';
        } else {
            $select .= '<option disabled selected value>Sélectionnez un code ADE</option>';
        }

        $allOptions = [];

        foreach ($years as $year) {
            $allOptions[$year->getDeptId()][] = [
                'code' => $year->getCode(),
                'title' => $year->getTitle(),
                'type' => 'Année'
            ];
        }

        foreach ($groups as $group) {
            $allOptions[$group->getDeptId()][] = [
                'code' => $group->getCode(),
                'title' => $group->getTitle(),
                'type' => 'Groupe'
            ];
        }

        foreach ($halfGroups as $halfGroup) {
            $allOptions[$halfGroup->getDeptId()][] = [
                'code' => $halfGroup->getCode(),
                'title' => $halfGroup->getTitle(),
                'type' => 'Demi groupe'
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

            // trier les options au sein de chaque département par type puis par titre
            usort($options, function ($a, $b) {
                return [$a['type'], $a['title']] <=> [$b['type'], $b['title']];
            });

            foreach ($options as $option) {
                $select .= '<option value="' . $option['code'] . '">'
                           . $option['type'] . ' - ' . $option['title'] . '</option>';
            }

            $select .= '</optgroup>';
        }

        $select .= '</select>';

        return $select;
    }

    /**
     * Génère un formulaire HTML pour la modification du mot de passe.
     *
     * Ce formulaire permet à un utilisateur de saisir un nouveau mot de passe
     * et de le confirmer. Il utilise une validation de longueur minimale pour
     * assurer la sécurité du mot de passe.
     *
     * @return string Le code HTML du formulaire pour la modification du mot de passe.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function modifyPassword() : string {
        return '
		<form method="post">
		<label>Nouveau mot de passe </label>
            <input  minlength="4" type="password" class="form-control text-center modal-sm" id="pwdTv" name="pwdTv" placeholder="Nouveau mot de passe" onkeyup=checkPwd("Tv")>
            <input  minlength="4" type="password" class="form-control text-center modal-sm" id="pwdConfTv" name="pwdConfirmTv" placeholder="Confirmer le nouveau mot de passe" onkeyup=checkPwd("Tv")>
		</form>';
    }

    /**
     * Génère le conteneur HTML pour le diaporama.
     *
     * Cette méthode crée un div avec l'ID 'slideshow-container' qui sert de
     * conteneur principal pour le diaporama d'images ou de contenu. Ce conteneur
     * est stylisé par des classes CSS associées pour gérer l'affichage du diaporama.
     *
     * @return string Le code HTML du conteneur du diaporama.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayStartSlide() : string {
        return '<div id="slideshow-container" class="slideshow-container">';
    }

    /**
     * Génère un conteneur HTML pour une diapositive dans le diaporama.
     *
     * Cette méthode crée un div avec la classe 'mySlides', qui est utilisé pour
     * encapsuler le contenu d'une diapositive individuelle dans un diaporama.
     * Chaque diapositive peut contenir des images, du texte ou d'autres éléments
     * HTML qui sont affichés à l'utilisateur dans une séquence.
     *
     * @return string Le code HTML du conteneur de la diapositive.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayMidSlide() : string {
        return '<div class="mySlides">';
    }
}
