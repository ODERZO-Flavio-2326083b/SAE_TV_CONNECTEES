<?php

namespace Views;

use Models\User;

/**
 * Class TeacherView
 *
 * Classe dédiée à l'affichage des vues pour les enseignants, y compris les formulaires
 * et les tableaux de gestion des utilisateurs.
 *
 * @package Views
 */
class TeacherView extends UserView
{
    /**
     * Affiche un formulaire pour télécharger un fichier Excel contenant les informations des enseignants.
     *
     * @return string Renvoie le formulaire HTML à afficher à l'utilisateur.
     *
     * Exemple d'utilisation :
     * $view = new TeacherView();
     * echo $view->displayInsertImportFileTeacher();
     */
    public function displayInsertImportFileTeacher() {
        return '
        <h2>Compte enseignant</h2>
        <p class="lead">Pour créer des enseignants, commencer par télécharger le fichier Excel en cliquant sur le lien ci-dessous.</p>
        <p class="lead">Remplissez les colonnes par les valeurs demandées, une ligne est égale à un utilisateur.</p>
        <p class="lead">Le code demandé est son code provenant de l\'ADE, pour avoir ce code, suivez ce petit tutoriel :</p>
        <ul>
            <li><p class="lead">Connectez-vous sur l\'ADE</p></li>
            <li><p class="lead">...</p></li>
        </ul>
        <p class="lead">Lorsque vous avez rempli le fichier Excel, enregistrez-le et cliquez sur "Parcourir" et sélectionnez votre fichier.</p>
        <p class="lead">Pour finir, validez l\'envoi du formulaire en cliquant sur "Importer le fichier"</p>
        <p class="lead">Lorsqu\'un enseignant est inscrit, un email lui est envoyé contenant son login et son mot de passe avec un lien du site.</p>
        <a href="' . TV_PLUG_PATH . 'public/files/Ajout Profs.xlsx" download="Ajout Prof.xlsx">Télécharger le fichier excel ! </a>
        <form id="Prof" method="post" enctype="multipart/form-data">
            <input type="file" name="excelProf" class="inpFil" required=""/>
            <button type="submit" class="btn button_ecran" name="importProf" value="Importer">Importer le fichier</button>
        </form>';
    }

    /**
     * Affiche un formulaire pour modifier un enseignant.
     *
     * @param User $user L'objet User représentant l'enseignant à modifier.
     * @return string Renvoie le formulaire HTML pour modifier les informations de l'enseignant.
     *
     * Exemple d'utilisation :
     * $view = new TeacherView();
     * echo $view->modifyForm($user);
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
     * Affiche tous les enseignants dans un tableau.
     *
     * @param User[] $teachers Un tableau d'objets User représentant les enseignants.
     * @return string Renvoie le tableau HTML avec les informations des enseignants.
     *
     * Exemple d'utilisation :
     * $view = new TeacherView();
     * echo $view->displayAllTeachers($teacherList);
     *
     * Gestion des exceptions :
     * Lance une exception si la liste des enseignants est vide ou non valide.
     */
    public function displayAllTeachers($teachers) {
        if (empty($teachers) || !is_array($teachers)) {
            throw new \InvalidArgumentException('La liste des enseignants doit être un tableau non vide.');
        }

        $page = get_page_by_title('Modifier un utilisateur');
        $linkManageUser = get_permalink($page->ID);

        $title = 'Enseignants';
        $name = 'Teacher';
        $header = ['Numéro Ent', 'Code ADE', 'Modifier'];

        $row = [];
        $count = 0;

        foreach ($teachers as $teacher) {
            ++$count;
            $row[] = [
                $count,
                $this->buildCheckbox($name, $teacher->getId()),
                $teacher->getLogin(),
                $teacher->getCodes()[0]->getCode(),
                $this->buildLinkForModify($linkManageUser . '?id=' . $teacher->getId())
            ];
        }

        return $this->displayAll($name, $title, $header, $row, 'teacher');
    }
}
