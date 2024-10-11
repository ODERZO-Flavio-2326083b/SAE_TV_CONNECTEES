<?php

namespace Views;

/**
 * Class View
 *
 * La classe principale de vue, fournissant des fonctions de base pour toutes les vues.
 *
 * @package Views
 */
class View
{

    /**
     * Affiche une table contenant tous les éléments d'une base de données.
     *
     * @param string $name         Le nom de la table pour identification.
     * @param string $title        Le titre à afficher au-dessus de la table.
     * @param array  $dataHeader   Un tableau contenant les en-têtes de colonne.
     * @param array  $dataList     Un tableau de données à afficher dans le corps de la table.
     * @param string $idTable      (Optionnel) Un identifiant unique pour la table, utilisé pour le tri et la recherche.
     *
     * @return string              HTML de la table générée.
     *
     * @example
     * $view = new View();
     * echo $view->displayAll('users', 'Liste des utilisateurs', ['ID', 'Nom', 'Email'], $userList);
     */
    public function displayAll($name, $title, $dataHeader, $dataList, $idTable = '') {
        $name = '\'' . $name . '\'';
        $table = '
        <h2>' . $title . '</h2>
        <input type="text" id="key' . $idTable . '" name="key" onkeyup="search(\'' . $idTable . '\')" placeholder="Recherche...">
        <form method="post">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="table' . $idTable . '">
                    <thead>
                        <tr class="text-center">
                            <th width="5%" class="text-center" onclick="sortTable(0, \'' . $idTable . '\')">#</th>
                            <th scope="col" width="5%" class="text-center"><input type="checkbox" onClick="toggle(this, ' . $name . ')" /></th>';
        $count = 1;
        foreach ($dataHeader as $data) {
            ++$count;
            $table .= '<th scope="col" class="text-center" onclick="sortTable(' . $count . ', \'' . $idTable . '\')">' . $data . '</th>';
        }
        $table .= '
            </tr>
        </thead>
        <tbody>';
        foreach ($dataList as $data) {
            $table .= '<tr>';
            foreach ($data as $column) {
                $table .= '<td class="text-center">' . $column . '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn delete_button_ecran" value="Supprimer" name="delete" onclick="return confirm(\' Voulez-vous supprimer le(s) élément(s) sélectionné(s) ?\');">Supprimer</button>
        </form>';
        return $table;
    }

    /**
     * Gère la pagination des éléments affichés.
     *
     * @param int    $pageNumber     Le nombre total de pages.
     * @param int    $currentPage    La page actuelle.
     * @param string $url            L'URL de la page pour la navigation.
     * @param int|null $numberElement (Optionnel) Nombre d'éléments par page.
     *
     * @return string                HTML de la navigation de pagination.
     *
     * @example
     * $view = new View();
     * echo $view->pageNumber(10, 3, '/items', 20);
     */
    public function pageNumber($pageNumber, $currentPage, $url, $numberElement = null) {
        $pagination = '
        <nav aria-label="Page navigation example">
            <ul class="pagination">';

        if ($currentPage > 1) {
            $pagination .= '
            <li class="page-item">
              <a class="page-link" href="' . $url . '/' . ($currentPage - 1) . '/?number=' . $numberElement . '" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            <li class="page-item"><a class="page-link" href="' . $url . '/1/?number=' . $numberElement . '">1</a></li>';
        }
        if ($currentPage > 3) {
            $pagination .= '<li class="page-item page-link disabled">...</li>';
        }
        for ($i = $currentPage - 3; $i < $currentPage; ++$i) {
            if ($i > 1) {
                $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . $i . '/?number=' . $numberElement . '">' . $i . '</a></li>';
            }
        }
        $pagination .= '
        <li class="page-item active_ecran" aria-current="page">
          <a class="page-link" href="' . $url . $currentPage . '/?number=' . $numberElement . '">' . $currentPage . '<span class="sr-only">(current)</span></a>
        </li>';
        for ($i = $currentPage + 1; $i < $currentPage + 3; ++$i) {
            if ($i < $pageNumber) {
                $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . '/' . $i . '/?number=' . $numberElement . '">' . $i . '</a></li>';
            }
        }
        if ($currentPage < $pageNumber) {
            if ($pageNumber - $currentPage > 3) {
                $pagination .= '<li class="page-item page-link disabled">...</li>';
            }
            $pagination .= '
            <li class="page-item"><a class="page-link" href="' . $url . '/' . $pageNumber . '/?number=' . $numberElement . '">' . $pageNumber . '</a></li>
            <li class="page-item">
              <a class="page-link" href="' . $url . '/' . ($currentPage + 1) . '/?number=' . $numberElement . '" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>';
        }
        $pagination .= '
          </ul>
        </nav>';
        return $pagination;
    }

    /**
     * Crée un lien pour modifier un élément.
     *
     * @param string $link L'URL du lien de modification.
     *
     * @return string      HTML du lien de modification.
     *
     * @example
     * $view = new View();
     * echo $view->buildLinkForModify('/items/edit/1');
     */
    public function buildLinkForModify($link) {
        return '<a href="' . $link . '">Modifier</a>';
    }

    /**
     * Crée une case à cocher.
     *
     * @param string $name Le nom de la case à cocher.
     * @param int    $id   L'identifiant de l'élément associé.
     *
     * @return string      HTML de la case à cocher.
     *
     * @example
     * $view = new View();
     * echo $view->buildCheckbox('user', 1);
     */
    public function buildCheckbox($name, $id) {
        return '<input type="checkbox" name="checkboxStatus' . $name . '[]" value="' . $id . '"/>';
    }

    /**
     * Crée le début d'une sélection multiple.
     *
     * @return string HTML du début de la sélection multiple.
     *
     * @example
     * $view = new View();
     * echo $view->displayStartMultiSelect();
     */
    public function displayStartMultiSelect() {
        return '<nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">';
    }

    /**
     * Crée un onglet pour la sélection multiple.
     *
     * @param string $id      L'identifiant de l'onglet.
     * @param string $title   Le titre de l'onglet.
     * @param bool   $active  (Optionnel) Affiche l'onglet comme actif.
     *
     * @return string        HTML de l'onglet.
     *
     * @example
     * $view = new View();
     * echo $view->displayTitleSelect('tab1', 'Tab 1', true);
     */
    public function displayTitleSelect($id, $title, $active = false) {
        $string = '<a class="nav-item nav-link';
        if ($active) $string .= ' active';
        $string .= '" id="nav-' . $id . '-tab" data-toggle="tab" href="#nav-' . $id . '" role="tab" aria-controls="nav-' . $id . '" aria-selected="false">' . $title . '</a>';
        return $string;
    }

    /**
     * Ferme la création d'un nouvel onglet.
     *
     * @return string HTML de la fermeture de l'onglet.
     *
     * @example
     * $view = new View();
     * echo $view->displayEndOfTitle();
     */
    public function displayEndOfTitle() {
        return '
            </div>
        </nav>
        <br/>
        <div class="tab-content" id="nav-tabContent">';
    }

    /**
     * Crée le contenu pour un onglet.
     *
     * @param string $id      L'identifiant de l'onglet.
     * @param string $content Le contenu à afficher dans l'onglet.
     * @param bool   $active  (Optionnel) Affiche l'onglet comme actif.
     *
     * @return string        HTML du contenu de l'onglet.
     *
     * @example
     * $view = new View();
     * echo $view->displayContentSelect('tab1', '<p>Contenu de Tab 1</p>', true);
     */
    public function displayContentSelect($id, $content, $active = false) {
        $string = '<div class="tab-pane fade show';
        if ($active) $string .= ' active';
        $string .= '" id="nav-' . $id . '" role="tabpanel" aria-labelledby="nav-' . $id . '-tab">' . $content . '</div>';
        return $string;
    }

    /**
     * Rafraîchit la page actuelle.
     *
     * @return void
     *
     * @example
     * $view = new View();
     * $view->refreshPage();
     */
    public function refreshPage() {
        echo '<meta http-equiv="refresh" content="0">';
    }

    /**
     * Crée un modal pour afficher des messages d'erreur ou d'information.
     *
     * @param string $title    Le titre du modal.
     * @param string $content  Le contenu à afficher dans le modal.
     * @param string|null $redirect (Optionnel) URL pour redirection après la fermeture.
     *
     * @return void
     *
     * @example
     * $view = new View();
     * $view->buildModal('Erreur', 'Une erreur s\'est produite.', '/home');
     */
    public function buildModal($title, $content, $redirect = null) {
        $modal = '
        <!-- MODAL -->
        <div class="modal" id="myModal" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">' . $title . '</h5>
              </div>
              <div class="modal-body">
                ' . $content . '
              </div>
              <div class="modal-footer">';
        if (empty($redirect)) {
            $modal .= '<button type="button" class="btn button_ecran" onclick="$(\'#myModal\').hide();">Fermer</button>';
        } else {
            $modal .= '<button type="button" class="btn button_ecran" onclick="document.location.href =\' ' . $redirect . ' \'">Fermer</button>';
        }
        $modal .= '</div>
            </div>
          </div>
        </div>
        
        <script>
            $(\'#myModal\').show();
        </script>';

        echo $modal;
    }

    /**
     * Ferme une div.
     *
     * @return string HTML de la fermeture de la div.
     *
     * @example
     * $view = new View();
     * echo $view->displayEndDiv();
     */
    public function displayEndDiv() {
        return '</div>';
    }

    /**
     * Affiche un message d'erreur si les deux mots de passe ne correspondent pas.
     *
     * @return void
     *
     * @example
     * $view = new View();
     * $view->displayBadPassword();
     */
    public function displayBadPassword() {
        $this->buildModal('Mauvais mot de passe', '<p class=\'alert alert-danger\'>Les deux mots de passe ne sont pas correctes </p>');
    }

    /**
     * Affiche un message d'erreur si un utilisateur existe déjà.
     *
     * @param array $doubles  Un tableau de logins en double.
     *
     * @return void
     *
     * @example
     * $view = new View();
     * $view->displayErrorDouble(['utilisateur1', 'utilisateur2']);
     */
    public function displayErrorDouble($doubles) {
        $content = "";
        foreach ($doubles as $double) {
            $content .= '<p class="alert alert-danger">' . $double . ' a rencontré un problème lors de l\'enregistrement, vérifié son login et son email !</p>';
        }
        $this->buildModal('Erreur durant l\'inscription', $content);
    }

    /**
     * Affiche un message de validation si l'inscription est réussie.
     *
     * @return void
     *
     * @example
     * $view = new View();
     * $view->displayInsertValidate();
     */
    public function displayInsertValidate() {
        $this->buildModal('Inscription validée', '<p class=\'alert alert-success\'>Votre inscription a été validée.</p>');
    }

    /**
     * Affiche un message d'erreur si l'extension du fichier est incorrecte.
     *
     * @return void
     *
     * @example
     * $view = new View();
     * $view->displayWrongExtension();
     */
    public function displayWrongExtension() {
        $this->buildModal('Mauvais fichier !', '<p class="alert alert-danger"> Mauvaise extension de fichier !</p>');
    }

    /**
     * Affiche un message d'erreur si le fichier est incorrect.
     *
     * @return void
     *
     * @example
     * $view = new View();
     * $view->displayWrongFile();
     */
    public function displayWrongFile() {
        $this->buildModal('Mauvais fichier !', '<p class="alert alert-danger"> Vous utilisez un mauvais fichier excel / ou vous avez changé le nom des colonnes</p>');
    }

    /**
     * Affiche un message de validation si la modification est réussie.
     *
     * @param string|null $redirect (Optionnel) URL pour redirection après la fermeture.
     *
     * @return void
     *
     * @example
     * $view = new View();
     * $view->displayModificationValidate('/home');
     */
    public function displayModificationValidate($redirect = null) {
        $this->buildModal('Modification réussie', '<p class="alert alert-success"> La modification a été appliquée</p>', $redirect);
    }

    /**
     * Affiche un message d'erreur si la création d'un utilisateur a échoué.
     *
     * @return void
     *
     * @example
     * $view = new View();
     * $view->displayErrorInsertion();
     */
    public function displayErrorInsertion() {
        $this->buildModal('Erreur lors de l\'inscription', '<p class="alert alert-danger"> Le login ou l\'adresse mail est déjà utilisé(e) </p>');
    }

    /**
     * Affiche un message d'erreur si le formulaire n'a pas été rempli correctement.
     *
     * @return void
     *
     * @example
     * $view = new View();
     * $view->errorMessageInvalidForm();
     */
    public function errorMessageInvalidForm() {
        $this->buildModal('Le formulaire n\'a pas été correctement remplie', '<p class="alert alert-danger">Le formulaire a été mal remplie, veuillez revoir les données rentrées et réessayez.</p>');
    }

    /**
     * Affiche un message d'erreur si l'ajout a échoué.
     *
     * @return void
     *
     * @example
     * $view = new View();
     * $view->errorMessageCantAdd();
     */
    public function errorMessageCantAdd() {
        $this->buildModal('L\'ajout a échoué', '<p class="alert alert-danger">Une erreur s\'est produite lors de l\'envoie du formulaire, veuillez réessayer après avoir vérifié vos informations.</p>');
    }
}
