<?php

namespace views;

/**
 * Class TechnicianView
 *
 * Contient toutes les vues liées aux techniciens (Formulaires, tableaux)
 *
 * @package views
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
     * @param array $dept     Liste de tous les départements.
     * @param int   $currDept ID du département de l'utilisateur actuel;
     *                        null si c'est un admin.
     * @param bool  $isAdmin  Un booléen correspondant
     *                        à "true" si l'utilisateur
     *                        est un administrateur, et
     *                        "false" sinon.
     *
     * @return string Le code HTML du formulaire pour créer un compte technicien.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayFormTechnician(array $dept, int $currDept,
        bool $isAdmin
    ): string {
        return '
        <h2>Compte agent d\'entretien</h2>
        <p class="lead">Pour créer des agents d\'entretien, remplissez ce formulaire 
        avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Tech', $dept, $isAdmin, $currDept);
    }

    /**
     * Affiche la liste de tous les techniciens.
     *
     * Cette méthode génère un tableau HTML affichant tous les techniciens
     * présents dans le système. Chaque ligne du tableau inclut un numéro,
     * une case à cocher pour sélectionner le technicien, et le login du technicien.
     *
     * @param array $users        Un tableau d'objets représentant les techniciens
     *                            à afficher. Chaque objet doit implémenter la
     *                            méthode 'getId()' pour obtenir l'identifiant et
     *                            'getLogin()' pour obtenir le login.
     * @param array $userDeptList Liste de tous les noms de départements dans le même
     *                            ordre aue le tableau $users
     *
     * @return string Le code HTML du tableau contenant la liste des techniciens.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayAllTechnicians(array $users,
        array $userDeptList
    ): string {
        $title = 'Agent d\'entretien';
        $name = 'Tech';
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
