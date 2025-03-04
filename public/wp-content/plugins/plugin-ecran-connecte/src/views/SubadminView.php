<?php
/**
 * Fichier SubadminView.php
 *
 * Ce fichier contient la classe 'SubadminView',
 * qui est responsable de l'affichage des vues
 * liées à la gestion des administrateurs de département dans
 * l'application. Cette classe génère des formulaires
 * permettant de créer des comptes pour des administrateurs de département,
 * ainsi que des tableaux et des messages
 * pour gérer et afficher ces comptes.
 *
 * PHP version 8.3
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/SubadminView
 * Documentation de la classe
 * @since    2025-01-13
 */
namespace views;

/**
 * Classe SubadminView
 *
 * Cette classe gère l'affichage des vues
 * liées à la gestion des comptes administrateurs de département.
 * Elle fournit des formulaires permettant
 * de créer et de gérer les comptes des administrateurs de département,
 * ainsi que des tableaux pour afficher
 * les administrateurs de département existants et leurs informations.
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/SubadminView Documentation de la classe
 * @since    2025-01-13
 */
class SubadminView extends UserView
{

    /**
     * Affiche le formulaire de création de compte pour
     * les administrateurs de département.
     *
     * Cette méthode génère un formulaire HTML pour la création de nouveaux
     * comptes techniciens. Le formulaire inclut des champs nécessaires pour
     * fournir les informations requises pour un
     * compte administrateur de département.
     *
     * @param array $dept     Liste de tous les
     *                        départements
     * @param int   $currDept ID du département de l'utilisateur actuel; null si
     *                        c'est un admin
     * @param bool  $isAdmin  Un booléen correspondant
     *                        à "true" si l'utilisateur
     *                        est un administrateur, et
     *                        "false" sinon.
     *
     * @return string Le code HTML du formulaire pour créer
     * un compte administrateur de département.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayFormSubadmin(array $dept, int $currDept, bool $isAdmin):
    string
    {
        return '
        <h2>Compte administrateur de département</h2>
        <p class="lead">Pour créer des administrateurs
         de département, remplissez ce formulaire avec 
        	les valeurs demandées.</p>
        ' . $this->displayBaseForm('Subadmin', $dept, $isAdmin, $currDept);
    }

    /**
     * Affiche un tableau des administrateurs de département avec leurs informations.
     * Cette méthode crée un tableau HTML contenant les informations suivantes pour
     * chaque technicien : un checkbox, le login, et le département.
     *
     * @param array $users        Liste des utilisateurs à afficher.
     *                            Chaque utilisateur
     *                            doit avoir une méthode 'getId()' et 'getLogin()'.
     * @param array $userDeptList Liste des départements
     *                            associés à chaque utilisateur.
     *                            Le nombre d'éléments
     *                            doit correspondre à la taille de
     *                            '$users'.
     *
     * @return string Retourne le code HTML complet du
     * tableau avec les utilisateurs et
     *                leurs départements associés.
     */
    public function displayAllSubadmin(array $users, array $userDeptList):
    string
    {
        $title = 'Administrateur de département';
        $name = 'Subadmin';
        $header = ['Login', 'Département'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [
            $count,
            $this->buildCheckbox($name, $user->getId()),
            $user->getLogin(),
            $userDeptList[$count-1]];
        }

        return $this->displayAll($name, $title, $header, $row, $name);
    }
}
