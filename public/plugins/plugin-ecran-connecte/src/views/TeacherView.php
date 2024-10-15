<?php

namespace Views;


use Models\User;

/**
 * Class TeacherView
 *
 * Contain all view for teacher (Forms, tables)
 *
 * @package Views
 */
class TeacherView extends UserView
{

    /**
     * Display a creation form
     */
    public function displayInsertImportFileTeacher() {
        return '
        <h2>Compte enseignant</h2>
        <p class="lead">Pour créer des enseignants, commencer par télécharger le fichier Excel en cliquant sur le lien ci-dessous.</p>
        <p class="lead">Remplissez les colonnes par les valeurs demandées, une ligne est égale à un utilisateur.</p>
        <p class="lead">Le code demandé est son code provenant de l\'ADE, pour avoir ce code, suivez ce petit tutoriel :</p>
        <ul>
            <li><p class="lead">Connectez vous sur l\'ADE</p></li>
            <li><p class="lead">...</p></li>
        </ul>
        <p class="lead">Lorsque vous avez remplis le fichier Excel, enregistrez le et cliquez sur "Parcourir" et sélectionnez votre fichier.</p>
        <p class="lead">Pour finir, validez l\'envoie du formulaire en cliquant sur "Importer le fichier"</p>
        <p class="lead">Lorsqu\'un enseignant est inscrit, un email lui est envoyé contenant son login et son mot de passe avec un lien du site.</p>
        <a href="' . TV_PLUG_PATH . 'public/files/Ajout Profs.xlsx" download="Ajout Prof.xlsx">Télécharger le fichier excel ! </a>
        <form id="Prof" method="post" enctype="multipart/form-data">
            <input type="file" name="excelProf" class="inpFil" required=""/>
            <button type="submit" class="btn button_ecran" name="importProf" value="Importer">Importer le fichier</button>
        </form>';
    }

    /**
     * Display form to modify a teacher
     *
     * @param $user   User
     *
     * @return string
     */
    public function modifyForm($user) {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        return '
        <a href="' . esc_url(get_permalink(get_page_by_title('Gestion des utilisateurs'))) . '">< Retour</a>
        <h2>' . $user->getLogin() . '</h2>
        <form method="post">
            <label for="modifCode">Code ADE</label>
            <input type="text" class="form-control" id="modifCode" name="modifCode" placeholder="Entrer le Code ADE" value="' . $user->getCodes()[0]->getCode() . '" required="">
            <button name="modifValidate" class="btn button_ecran" type="submit" value="Valider">Valider</button>
            <a href="' . $linkManageUser . '">Annuler</a>
        </form>';
    }

    /**
     * Display all teachers in a table
     *
     * @param $teachers    User[]
     *
     * @return string
     */
    public function displayAllTeachers($teachers) {
        $page = get_page_by_title('Modifier un utilisateur');
        $linkManageUser = get_permalink($page->ID);

        $title = 'Enseignants';
        $name = 'Teacher';
        $header = ['Numéro Ent', 'Code ADE', 'Modifier'];

        $row = array();
        $count = 0;
        foreach ($teachers as $teacher) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $teacher->getId()), $teacher->getLogin(), $teacher->getCodes()[0]->getCode(), $this->buildLinkForModify($linkManageUser . '?id=' . $teacher->getId())];
        }

        return $this->displayAll($name, $title, $header, $row, 'teacher');
    }
}
