<?php
/**
 * Fichier SubadminView.php
 *
 * Ce fichier contient la classe `SubadminView`,
 * qui est responsable de l'affichage des vues
 * liées à la gestion des sous-administrateurs dans
 * l'application. Cette classe génère des formulaires
 * permettant de créer des comptes pour des sous-administrateurs,
 * ainsi que des tableaux et des messages
 * pour gérer et afficher ces comptes.
 *
 * PHP version 7.4 or later
 *
 * @category View
 * @package  Views
 * @author   John Doe <johndoe@example.com>
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
 * liées à la gestion des comptes sous-administrateurs.
 * Elle fournit des formulaires permettant
 * de créer et de gérer les comptes des sous-administrateurs,
 * ainsi que des tableaux pour afficher
 * les sous-administrateurs existants et leurs informations.
 *
 * @category View
 * @package  Views
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/SubadminView Documentation de la classe
 * @since    2025-01-13
 */
class SubadminView extends UserView
{

    /**
     * Affiche le formulaire de création de compte pour techniciens.
     *
     * Cette méthode génère un formulaire HTML pour la création de nouveaux
     * comptes techniciens. Le formulaire inclut des champs nécessaires pour
     * fournir les informations requises pour un compte technicien.
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
     * @return string Le code HTML du formulaire pour créer un compte sous-admin.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayFormSubadmin(array $dept, int $currDept, bool $isAdmin):
    string
    {
        return '
        <h2>Compte sous-admin</h2>
        <p class="lead">Pour créer des sous-admins, remplissez ce formulaire avec 
        	les valeurs demandées.</p>
        ' . $this->displayBaseForm('Subadmin', $dept, $isAdmin, $currDept);
    }

    /**
     * Affiche un tableau des techniciens (Sous-admins) avec leurs informations.
     * Cette méthode crée un tableau HTML contenant les informations suivantes pour
     * chaque technicien : un checkbox, le login, et le département.
     *
     * @param array $users        Liste des utilisateurs à afficher.
     *                            Chaque utilisateur
     *                            doit avoir une méthode `getId()` et `getLogin()`.
     * @param array $userDeptList Liste des départements
     *                            associés à chaque utilisateur.
     *                            Le nombre d'éléments
     *                            doit correspondre à la taille de
     *                            `$users`.
     *
     * @return string Retourne le code HTML complet du
     * tableau avec les utilisateurs et
     *                leurs départements associés.
     */
    public function displayAllTechnicians(array $users, array $userDeptList):
    string
    {
        $title = 'Sous-admins';
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
