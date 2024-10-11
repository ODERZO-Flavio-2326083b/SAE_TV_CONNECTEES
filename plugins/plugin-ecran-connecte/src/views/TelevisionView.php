<?php

namespace Views;

use Models\CodeAde;
use Models\User;

/**
 * Class TelevisionView
 *
 * Contient toutes les vues pour la télévision (formulaires, tableaux).
 *
 * @package Views
 */
class TelevisionView extends UserView
{
    /**
     * Affiche un formulaire pour créer une télévision.
     *
     * @param CodeAde[] $years     Liste des années.
     * @param CodeAde[] $groups    Liste des groupes.
     * @param CodeAde[] $halfGroups Liste des demi-groupes.
     * @return string Renvoie le formulaire HTML pour créer une télévision.
     *
     * Exemple d'utilisation :
     * $view = new TelevisionView();
     * echo $view->displayFormTelevision($years, $groups, $halfGroups);
     */
    public function displayFormTelevision($years, $groups, $halfGroups) {
        $form = '
        <h2> Compte télévision</h2>
        <p class="lead">Pour créer des télévisions, remplissez ce formulaire avec les valeurs demandées.</p>
        <p class="lead">Vous pouvez mettre autant d\'emploi du temps que vous souhaitez, cliquez sur "Ajouter des emplois du temps</p>
        <form method="post" id="registerTvForm">
            <div class="form-group">
            	<label for="loginTv">Login</label>
            	<input type="text" class="form-control" name="loginTv" placeholder="Nom de compte" required="">
            	<small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractères</small>
            </div>
            <div class="form-group">
            	<label for="pwdTv">Mot de passe</label>
            	<input type="password" class="form-control" id="pwdTv" name="pwdTv" placeholder="Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("Tv")>
            	<input type="password" class="form-control" id="pwdConfTv" name="pwdConfirmTv" placeholder="Confirmer le Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("Tv")>
            	<small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractères</small>
            </div>
            <div class="form-group">
            	<label>Premier emploi du temps</label>' .
            $this->buildSelectCode($years, $groups, $halfGroups) . '
            </div>
            <input type="button" class="btn button_ecran" onclick="addButtonTv()" value="Ajouter des emplois du temps">
            <button type="submit" class="btn button_ecran" id="validTv" name="createTv">Créer</button>
        </form>';

        return $form;
    }

    /**
     * Affiche tous les utilisateurs de type télévision dans un tableau.
     *
     * @param User[] $users Liste des utilisateurs.
     * @return string Renvoie le tableau HTML avec les informations des télévisions.
     *
     * Exemple d'utilisation :
     * $view = new TelevisionView();
     * echo $view->displayAllTv($tvUsers);
     */
    public function displayAllTv($users) {
        $page = get_page_by_title('Modifier un utilisateur');
        $linkManageUser = get_permalink($page->ID);

        $title = 'Télévisions';
        $name = 'Tele';
        $header = ['Login', 'Nombre d\'emplois du temps', 'Modifier'];

        $row = array();
        $count = 0;

        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin(), sizeof($user->getCodes()), $this->buildLinkForModify($linkManageUser . '?id=' . $user->getId())];
        }

        return $this->displayAll($name, $title, $header, $row, 'tele');
    }

    /**
     * Affiche un formulaire pour modifier une télévision.
     *
     * @param User     $user         L'utilisateur à modifier.
     * @param CodeAde[] $years       Liste des années.
     * @param CodeAde[] $groups      Liste des groupes.
     * @param CodeAde[] $halfGroups  Liste des demi-groupes.
     * @return string Renvoie le formulaire HTML pour modifier une télévision.
     *
     * Exemple d'utilisation :
     * $view = new TelevisionView();
     * echo $view->modifyForm($tvUser, $years, $groups, $halfGroups);
     */
    public function modifyForm($user, $years, $groups, $halfGroups) {
        $count = 0;
        $string = '
        <a href="' . esc_url(get_permalink(get_page_by_title('Gestion des utilisateurs'))) . '">< Retour</a>
        <h2>' . $user->getLogin() . '</h2>
         <form method="post" id="registerTvForm">
            <label id="selectId1"> Emploi du temps</label>';

        foreach ($user->getCodes() as $code) {
            $count++;
            if ($count == 1) {
                $string .= $this->buildSelectCode($years, $groups, $halfGroups, $code, $count);
            } else {
                $string .= '
					<div class="row">' .
                    $this->buildSelectCode($years, $groups, $halfGroups, $code, $count) .
                    '<input type="button" id="selectId' . $count . '" onclick="deleteRow(this.id)" class="btn button_ecran" value="Supprimer">
					</div>';
            }
        }

        if ($count == 0) {
            $string .= $this->buildSelectCode($years, $groups, $halfGroups, null, $count);
        }

        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);
        $string .= '
            <input type="button" class="btn button_ecran" onclick="addButtonTv()" value="Ajouter des emplois du temps">
            <button name="modifValidate" class="btn button_ecran" type="submit" id="validTv">Valider</button>
            <a href="' . $linkManageUser . '">Annuler</a>
        </form>';
        return $string;
    }

    /**
     * Construit un élément select avec tous les codes Ade.
     *
     * @param CodeAde[] $years      Liste des années.
     * @param CodeAde[] $groups     Liste des groupes.
     * @param CodeAde[] $halfGroups Liste des demi-groupes.
     * @param CodeAde|null $code    Le code actuel à sélectionner (optionnel).
     * @param int $count             Le numéro de séquence pour l'élément select.
     * @return string Renvoie le code HTML de l'élément select.
     *
     * Exemple d'utilisation :
     * $selectHtml = $view->buildSelectCode($years, $groups, $halfGroups);
     */
    public function buildSelectCode($years, $groups, $halfGroups, $code = null, $count = 0) {
        $select = '<select class="form-control firstSelect" id="selectId' . $count . '" name="selectTv[]" required="">';

        if (!is_null($code)) {
            $select .= '<option value="' . $code->getCode() . '">' . $code->getTitle() . '</option>';
        }

        $select .= '<option value="0">Aucun</option>
            		<optgroup label="Année">';

        foreach ($years as $year) {
            $select .= '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
        }
        $select .= '</optgroup><optgroup label="Groupe">';

        foreach ($groups as $group) {
            $select .= '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
        }
        $select .= '</optgroup><optgroup label="Demi groupe">';

        foreach ($halfGroups as $halfGroup) {
            $select .= '<option value="' . $halfGroup->getCode() . '">' . $halfGroup->getTitle() . '</option>';
        }
        $select .= '</optgroup>
			</select>';

        return $select;
    }

    /**
     * Affiche un formulaire pour modifier le mot de passe d'une télévision.
     *
     * @return string Renvoie le formulaire HTML pour changer le mot de passe.
     *
     * Exemple d'utilisation :
     * $view = new TelevisionView();
     * echo $view->modifyPassword();
     */
    public function modifyPassword() {
        return '
		<form method="post">
		<label>Nouveau mot de passe </label>
            <input minlength="4" type="password" class="form-control text-center modal-sm" id="pwdTv" name="pwdTv" placeholder="Nouveau mot de passe" onkeyup=checkPwd("Tv")>
            <input minlength="4" type="password" class="form-control text-center modal-sm" id="pwdConfTv" name="pwdConfirmTv" placeholder="Confirmer le nouveau mot de passe" onkeyup=checkPwd("Tv")>
		</form>';
    }

    /**
     * Démarre un diaporama.
     *
     * @return string Renvoie le conteneur HTML pour le diaporama.
     *
     * Exemple d'utilisation :
     * echo $view->displayStartSlide();
     */
    public function displayStartSlide() {
        return '<div id="slideshow-container" class="slideshow-container">';
    }

    /**
     * Sépare chaque diapositive avec ce conteneur.
     *
     * @return string Renvoie le conteneur HTML pour une diapositive.
     *
     * Exemple d'utilisation :
     * echo $view->displayMidSlide();
     */
    public function displayMidSlide() {
        return '<div class="mySlides">';
    }
}
