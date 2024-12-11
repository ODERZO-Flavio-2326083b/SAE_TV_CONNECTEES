<?php

namespace Views;

use Models\Department;
use Models\User;

/**
 * Class StudyDirectorView
 *
 * Cette classe contient toutes les vues relatives au directeur d'études (Formulaires, tableaux).
 *
 * @package Views
 */
class StudyDirectorView extends UserView
{

    /**
     * Affiche le formulaire de création d'un compte directeur d'études.
     *
     * Cette méthode génère un formulaire HTML pour la création d'un directeur d'études.
     * Le formulaire inclut des champs pour le login, l'email, le mot de passe,
     * la confirmation du mot de passe et le code ADE. Des instructions sont fournies
     * pour aider l'utilisateur à remplir correctement les champs requis.
     *
     * @return string Le code HTML du formulaire de création d'un directeur d'études.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayCreateDirector($dept) {
		// TODO : currDept
        return '
        <h2> Compte directeur d\'études</h2>
        <p class="lead">Pour créer des directeurs d\'études, remplissez ce formulaire avec les valeurs demandées.</p>
        <p class="lead">Le code ADE demandé est son code provenant de l\'ADE, pour avoir ce code, suivez le ce trouvant dans la partie pour créer un enseignant.</p>
        <form class="cadre" method="post">
            <div class="form-group">
                <label for="loginDirec">Login</label>
                <input minlength="4" type="text" class="form-control" name="loginDirec" placeholder="Login" required="">
                <small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractère</small>
            </div>
            <div class="form-group">
                <label for="emailDirec">Email</label>
                <input type="email" class="form-control" name="emailDirec" placeholder="Email" required="">
            </div>
            <div class="form-group">
                <label for="pwdDirec">Mot de passe</label>
                <input type="password" class="form-control" id="pwdDirec" name="pwdDirec" minlength="8" maxlength="25" placeholder="Mot de passe" required="" onkeyup=checkPwd("Direc")>
                <input type="password" class="form-control" id="pwdConfDirec" name="pwdConfirmDirec" minlength="8" maxlength="25" placeholder="Confirmer le Mot de passe" required="" onkeyup=checkPwd("Direc")>
                <small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractère</small>
            </div>
            <div class="form-group">
                <label for="departementDirec">Département</label>
                <br>    
                <select name="deptDirec" id="deptDirec" class="form-control" required="">
                    ' . $this->displayAllDepartement($dept, "temp") . '
                </select>
            </div>
            <div class="form-group">
                <label for="codeADEDirec"> Code ADE</label>
                <input type="text" class="form-control" placeholder="Code ADE" name="codeDirec" required="">
            </div>
            <button type="submit" class="btn button_ecran" id="validDirec" name="createDirec" value="Créer">Créer</button>
        </form>';
    }

    /**
     * Affiche la liste de tous les directeurs d'études.
     *
     * Cette méthode génère un tableau HTML affichant tous les directeurs d'études
     * avec leurs informations, y compris leur numéro d'établissement, leur code ADE,
     * et un lien pour modifier leurs informations. Elle utilise une pagination
     * pour une navigation facile lorsque la liste est longue.
     *
     * @param array $users Liste des directeurs d'études à afficher.
     *                     Chaque élément doit être un objet représentant un directeur d'études.
     *
     * @return string Le code HTML du tableau des directeurs d'études.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayAllStudyDirector($users) {
        $page = get_page_by_title_custom('Modifier un utilisateur');
        $linkManageUser = get_permalink($page->ID);

        $title = 'Directeur d\'études';
        $name = 'Direc';
        $header = ['Numéro Ent', 'Code ADE', 'Modifier'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {

            if (sizeof($user->getCodes()) == 0) {
                $code = 'Aucun code';
            } else {
                $code = $user->getCodes()[0]->getCode();
            }

            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin(), $code, $this->buildLinkForModify($linkManageUser . '?id=' . $user->getId())];
        }

        return $this->displayAll($name, $title, $header, $row, 'director');
    }

    /**
     * Affiche le formulaire de modification pour un directeur d'études.
     *
     * Cette méthode génère un formulaire pré-rempli permettant de modifier
     * les informations d'un directeur d'études spécifique. Les informations
     * incluent le login, l'email, le mot de passe, le code ADE et d'autres
     * détails pertinents. Le formulaire est prérempli avec les valeurs actuelles
     * du directeur d'études.
     *
     * @param object $user L'objet représentant le directeur d'études à modifier.
     *                     Cet objet doit contenir les propriétés et méthodes
     *                     nécessaires pour récupérer les informations du directeur.
     *
     * @return string Le code HTML du formulaire de modification.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayModifyStudyDirector($user) {
        $page = get_page_by_title_custom('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $code = 'Aucun code';
        if (sizeof($user->getCodes()) > 0) {
            $code = $user->getCodes()[0]->getCode();
        }

        return '
        <a href="' . esc_url(get_permalink(get_page_by_title_custom('Gestion des utilisateurs'))) . '">< Retour</a>
        <h2>' . $user->getLogin() . '</h2>
        <form method="post">
            <div class="form-group">
                <label for="modifCode">Code ADE</label>
                <input type="text" class="form-control" id="modifCode" name="modifCode" placeholder="Entrer le Code ADE" value="' . $code . '" required="">
            </div>
            <button class="btn button_ecran" name="modifValidate" type="submit" value="Valider">Valider</button>
            <a class="btn delete_button_ecran" href="' . $linkManageUser . '">Annuler</a>
        </form>';
    }
}
