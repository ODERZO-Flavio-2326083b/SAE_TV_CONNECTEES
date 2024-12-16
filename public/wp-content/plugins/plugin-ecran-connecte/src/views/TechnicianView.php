<?php

namespace Views;

use Models\User;

/**
 * Class TechnicianView
 *
 * Contient toutes les vues liées aux techniciens (Formulaires, tableaux)
 *
 * @package Views
 */
class TechnicianView extends UserView
{

    /**
     * Affiche le formulaire de création de compte pour techniciens.
     *
     * Cette méthode génère un formulaire HTML pour la création de nouveaux
     * comptes techniciens. Le formulaire inclut des champs nécessaires pour
     * fournir les informations requises pour un compte technicien.
     *
     * @return string Le code HTML du formulaire pour créer un compte technicien.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */

    public function displayFormTechnician($dept, $currDept) {
        return '
        <h2>Compte technicien</h2>
        <p class="lead">Pour créer des techniciens, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Tech', $dept, $currDept);
    }

    /**
     * Affiche la liste de tous les techniciens.
     *
     * Cette méthode génère un tableau HTML affichant tous les techniciens
     * présents dans le système. Chaque ligne du tableau inclut un numéro,
     * une case à cocher pour sélectionner le technicien, et le login du technicien.
     *
     * @param array $users Un tableau d'objets représentant les techniciens à afficher.
     *                     Chaque objet doit implémenter la méthode 'getId()' pour
     *                     obtenir l'identifiant et 'getLogin()' pour obtenir le login.
     *
     * @return string Le code HTML du tableau contenant la liste des techniciens.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayAllTechnicians($users) {
        $title = 'Techniciens';
        $name = 'Tech';
        $header = ['Login'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin()];
        }

        return $this->displayAll($name, $title, $header, $row, $name);
    }
}
