<?php

namespace Views;

use Models\User;

/**
 * Class TechnicianView
 *
 * Classe dédiée à l'affichage des vues pour les techniciens, y compris les formulaires
 * et les tableaux de gestion des utilisateurs.
 *
 * @package Views
 */
class TechnicianView extends UserView
{
    /**
     * Affiche un formulaire pour créer un technicien.
     *
     * @return string Renvoie le formulaire HTML à afficher à l'utilisateur.
     *
     * Exemple d'utilisation :
     * $view = new TechnicianView();
     * echo $view->displayFormTechnician();
     */
    public function displayFormTechnician() {
        return '
        <h2>Compte technicien</h2>
        <p class="lead">Pour créer des techniciens, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Tech');
    }

    /**
     * Affiche tous les techniciens dans un tableau.
     *
     * @param User[] $users Un tableau d'objets User représentant les techniciens.
     * @return string Renvoie le tableau HTML avec les informations des techniciens.
     *
     * Exemple d'utilisation :
     * $view = new TechnicianView();
     * echo $view->displayAllTechnicians($technicianList);
     *
     * Gestion des exceptions :
     * Lance une exception si la liste des techniciens est vide ou non valide.
     */
    public function displayAllTechnicians($users) {
        if (empty($users) || !is_array($users)) {
            throw new \InvalidArgumentException('La liste des techniciens doit être un tableau non vide.');
        }

        $title = 'Techniciens';
        $name = 'Tech';
        $header = ['Login'];

        $row = [];
        $count = 0;

        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin()];
        }

        return $this->displayAll($name, $title, $header, $row, $name);
    }
}
