<?php
/**
 * Fichier CommuniquantView.php
 *
 * Ce fichier contient la classe 'CommuniquantView',
 * qui est responsable de l'affichage des vues liées à
 * la gestion des comptes des communicants dans l'application.
 * La classe fournit des méthodes permettant de créer un compte communicant
 * ainsi que d'afficher tous les comptes communicants
 * existants sous forme de tableau.
 *
 * PHP version 8.3
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://www.example.com/docs/CommuniquantView Documentation de la classe
 * @since    2025-01-13
 */

namespace views;

/**
 * Classe CommuniquantView
 *
 * Cette classe gère l'affichage des vues liées à
 * la gestion des comptes communicants.
 * Elle fournit des formulaires permettant de créer des comptes communicants,
 * ainsi que des tableaux pour afficher les informations des comptes existants.
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/CommuniquantView Documentation de la classe
 * @since    2025-01-13
 */
class CommuniquantView extends UserView
{

    /**
     * Affiche le formulaire pour la création d'un compte communicant.
     *
     * @param array $dept     Liste des départements
     *                        disponibles
     * @param int   $currDept Département actuel
     * @param bool  $isAdmin  Indicateur de droits administratifs
     *
     * @return string Le code HTML du formulaire
     */
    public function displayFormCommunicant(array $dept, int $currDept,
        bool $isAdmin
    ): string {
        return '
        <h2>Compte communicant</h2>
        <p class="lead">Pour créer des communiquants, remplissez ce formulaire 
        avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Comm', $dept, $isAdmin, $currDept);
    }

    /**
     * Affiche la liste de tous les communicants existants sous forme de tableau.
     *
     * @param array $users        Liste des utilisateurs (communicants)
     * @param array $userDeptList Liste des départements
     *                            correspondants aux utilisateurs
     *
     * @return string Le code HTML du tableau des communicants
     */
    public function displayAllCommunicants(array $users,
        array $userDeptList
    ): string {
        $title = 'Communicant';
        $name = 'Comm';
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
