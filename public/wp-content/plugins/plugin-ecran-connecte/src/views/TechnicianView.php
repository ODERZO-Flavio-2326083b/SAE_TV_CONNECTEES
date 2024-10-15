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
     * Affiche un formulaire de création pour un technicien
     *
     * @return string Le code HTML du formulaire de création d'un compte technicien
     */
    public function displayFormTechnician() {
        return '
        <h2>Compte technicien</h2>
        <p class="lead">Pour créer des techniciens, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Tech');
    }

    /**
     * Affiche tous les techniciens dans un tableau
     *
     * @param User[] $users Tableau d'utilisateurs techniciens
     *
     * @return string Le code HTML du tableau affichant tous les techniciens
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
