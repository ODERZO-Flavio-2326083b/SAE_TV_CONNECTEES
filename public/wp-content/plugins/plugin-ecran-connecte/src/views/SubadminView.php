<?php

namespace views;

/**
 * Class SubadminView
 *
 * Contient toutes les vues liées aux sous-administrateurs (Formulaires, tableaux)
 *
 * @package views
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

    // TODO : Ajouter la doc pour cette fonction
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
