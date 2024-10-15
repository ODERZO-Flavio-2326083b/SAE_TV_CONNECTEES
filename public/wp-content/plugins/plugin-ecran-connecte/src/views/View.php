<?php

namespace Views;

/**
 * Class View
 *
 * Main class View,
 * got basics functions for all views
 *
 * @package Views
 */
class View
{

    /**
     * Génère un tableau HTML affichant une liste d'éléments avec des options de tri et de recherche.
     *
     * Cette méthode crée un tableau HTML avec des en-têtes dynamiques et des données
     * fournies en entrée. Elle inclut également des fonctionnalités de recherche,
     * de sélection multiple, et de suppression d'éléments.
     *
     * @param string $name      Le nom du tableau, utilisé pour les fonctionnalités de sélection.
     * @param string $title     Le titre du tableau à afficher en haut.
     * @param array  $dataHeader Tableau des en-têtes de colonnes.
     * @param array  $dataList  Liste des données à afficher dans le tableau,
     *                          chaque élément étant un tableau représentant une ligne.
     * @param string $idTable   (Optionnel) Identifiant unique pour le tableau
     *                          et les fonctionnalités associées (recherche, tri).
     *
     * @return string Le code HTML du tableau généré.
     *
     * @example
     * // Affiche un tableau avec des données d'utilisateurs :
     * $users = [
     *     ['1', '<input type="checkbox" />', 'user1@example.com'],
     *     ['2', '<input type="checkbox" />', 'user2@example.com'],
     * ];
     * $headers = ['ID', 'Sélection', 'Email'];
     * echo $this->displayAll('User', 'Liste des utilisateurs', $headers, $users, 'UserTable');
     *
     * @version 1.0
     * @date 2024-10-15
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
     * Génère une pagination pour naviguer à travers les pages d'une liste d'éléments.
     *
     * Cette méthode crée un ensemble de liens de pagination qui permettent à l'utilisateur
     * de naviguer entre différentes pages de résultats. Elle inclut des liens vers la page
     * précédente, les numéros de pages environnantes, et la page suivante, tout en gérant
     * les cas où les pages sont nombreuses.
     *
     * @param int    $pageNumber    Le nombre total de pages disponibles.
     * @param int    $currentPage   Le numéro de la page actuelle.
     * @param string $url           L'URL de base pour générer les liens de pagination.
     * @param int|null $numberElement (Optionnel) Le nombre d'éléments par page, utilisé
     *                                pour construire l'URL si nécessaire.
     *
     * @return string Le code HTML de la pagination générée.
     *
     * @example
     * // Affiche une pagination pour 10 pages, avec la page actuelle à 4 :
     * echo $this->pageNumber(10, 4, 'https://example.com/page');
     *
     * @version 1.0
     * @date 2024-10-15
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
     * Génère un lien HTML pour modifier un élément.
     *
     * Cette méthode crée un lien qui redirige vers une page de modification spécifiée.
     * Le lien est affiché sous la forme d'un élément `<a>` HTML avec le texte "Modifier".
     *
     * @param string $link L'URL vers laquelle le lien doit rediriger pour modifier l'élément.
     *
     * @return string Le code HTML du lien de modification.
     *
     * @example
     * // Crée un lien pour modifier un élément avec l'URL spécifiée :
     * echo $this->buildLinkForModify('https://example.com/modify/1');
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function buildLinkForModify($link) {
        return '<a href="' . $link . '">Modifier</a>';
    }

    /**
     * Génère un élément de case à cocher HTML.
     *
     * Cette méthode crée un input de type checkbox avec un nom et un identifiant spécifiés.
     * La case à cocher est utilisée pour sélectionner un ou plusieurs éléments dans un formulaire.
     *
     * @param string $name Le nom de la case à cocher, utilisé pour regrouper les cases dans le formulaire.
     * @param string|int $id L'identifiant unique associé à cette case à cocher, utilisé comme valeur lorsqu'elle est sélectionnée.
     *
     * @return string Le code HTML de l'élément checkbox.
     *
     * @example
     * // Crée une case à cocher avec le nom "option" et l'id 1 :
     * echo $this->buildCheckbox('option', 1);
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function buildCheckbox($name, $id) {
        return '<input type="checkbox" name="checkboxStatus' . $name . '[]" value="' . $id . '"/>';
    }

    /**
     * Génère le début d'un conteneur de sélection multiple sous forme de navigation par onglets.
     *
     * Cette méthode crée un élément HTML `<nav>` contenant une structure de navigation par onglets
     * pour les sélections multiples. Cela peut être utilisé pour organiser des options
     * ou des catégories, permettant à l'utilisateur de choisir plusieurs éléments de manière intuitive.
     *
     * @return string Le code HTML d'ouverture pour une navigation par onglets.
     *
     * @example
     * // Affiche le début d'un conteneur de sélection multiple :
     * echo $this->displayStartMultiSelect();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayStartMultiSelect() {
        return '<nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">';
    }

    /**
     * Génère un élément de titre de navigation pour un onglet dans une interface utilisateur.
     *
     * Cette méthode crée un lien HTML qui sert de titre pour un onglet de navigation.
     * Il peut être marqué comme actif en fonction de l'argument fourni.
     *
     * @param string $id L'identifiant de l'onglet, utilisé pour les attributs `id` et `href`.
     * @param string $title Le texte affiché pour le titre de l'onglet.
     * @param bool $active Indique si l'onglet doit être marqué comme actif (true) ou non (false). Par défaut, il est false.
     *
     * @return string Le code HTML de l'élément de titre de l'onglet.
     *
     * @example
     * // Affiche un onglet de navigation intitulé "Paramètres" avec l'identifiant "settings" :
     * echo $this->displayTitleSelect('settings', 'Paramètres', true);
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayTitleSelect($id, $title, $active = false) {
        $string = '<a class="nav-item nav-link';
        if ($active) $string .= ' active';
        $string .= '" id="nav-' . $id . '-tab" data-toggle="tab" href="#nav-' . $id . '" role="tab" aria-controls="nav-' . $id . '" aria-selected="false">' . $title . '</a>';
        return $string;
    }

    /**
     * Génère la fin d'un conteneur de navigation pour un ensemble d'onglets.
     *
     * Cette méthode crée le code HTML nécessaire pour fermer les éléments de navigation
     * et ouvrir le conteneur de contenu associé aux onglets. Elle est généralement utilisée
     * après l'ajout de tous les titres d'onglets pour compléter la structure de l'interface
     * utilisateur.
     *
     * @return string Le code HTML de la fermeture de la navigation et du début du conteneur de contenu des onglets.
     *
     * @example
     * // Utilisation typique après l'affichage des titres d'onglets :
     * echo $this->displayEndOfTitle();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayEndOfTitle() {
        return '
            </div>
        </nav>
        <br/>
        <div class="tab-content" id="nav-tabContent">';
    }

    /**
     * Génère le contenu d'un onglet dans une interface utilisateur.
     *
     * Cette méthode crée le code HTML nécessaire pour afficher le contenu d'un
     * onglet dans un conteneur tabulé. Elle permet de spécifier si l'onglet doit
     * être actif lors du rendu, en ajoutant les classes CSS appropriées pour
     * le style et le comportement des onglets.
     *
     * @param string $id L'identifiant unique de l'onglet, utilisé pour le lien
     *                   entre l'onglet et son contenu.
     * @param string $content Le contenu à afficher dans l'onglet.
     * @param bool $active Indique si cet onglet doit être actif (visible par défaut).
     *                     Par défaut, il est à false.
     *
     * @return string Le code HTML du contenu de l'onglet.
     *
     * @example
     * // Affiche le contenu d'un onglet avec un identifiant et un contenu spécifiques :
     * echo $this->displayContentSelect('example', '<p>Ceci est le contenu de l\'onglet.</p>', true);
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayContentSelect($id, $content, $active = false) {
        $string = '<div class="tab-pane fade show';
        if ($active) $string .= ' active';
        $string .= '" id="nav-' . $id . '" role="tabpanel" aria-labelledby="nav-' . $id . '-tab">' . $content . '</div>';
        return $string;
    }

    /**
     * Refresh the page
     */
    public function refreshPage() {
        echo '<meta http-equiv="refresh" content="0">';
    }

    /**
     * Génère un modal Bootstrap avec un titre et un contenu personnalisés.
     *
     * Cette méthode crée le code HTML nécessaire pour afficher un modal,
     * qui peut être utilisé pour afficher des informations, des alertes ou
     * des options supplémentaires à l'utilisateur. Le modal comprend également
     * un bouton de fermeture qui peut rediriger l'utilisateur vers une autre
     * page si un lien est fourni.
     *
     * @param string $title Le titre du modal, affiché en haut de la fenêtre modale.
     * @param string $content Le contenu à afficher à l'intérieur du modal.
     * @param string|null $redirect (optionnel) L'URL vers laquelle l'utilisateur
     *                               sera redirigé après avoir cliqué sur le bouton
     *                               de fermeture. Si aucune URL n'est fournie,
     *                               le bouton fermera simplement le modal sans redirection.
     *
     * @return void Cette méthode n'a pas de valeur de retour, elle affiche directement
     *               le modal à l'utilisateur.
     *
     * @example
     * // Affiche un modal avec un titre et un contenu, sans redirection :
     * $this->buildModal('Titre du Modal', '<p>Ceci est le contenu du modal.</p>');
     *
     * // Affiche un modal avec une redirection vers une autre page :
     * $this->buildModal('Titre du Modal', '<p>Ceci est le contenu du modal.</p>', 'https://example.com');
     *
     * @version 1.0
     * @date 2024-10-15
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
     * Close a div
     *
     * @return string
     */
    public function displayEndDiv() {
        return '</div>';
    }

    /**
     * Affiche un modal d'erreur pour indiquer que les mots de passe fournis sont incorrects.
     *
     * Cette méthode utilise la fonction `buildModal` pour générer un modal Bootstrap
     * avec un message d'alerte en rouge, signalant à l'utilisateur que les deux mots
     * de passe qu'il a saisis ne correspondent pas ou ne sont pas corrects.
     *
     * @return void Cette méthode n'a pas de valeur de retour, elle affiche directement
     *               le modal à l'utilisateur.
     *
     * @example
     * // Affiche un modal indiquant que les mots de passe ne correspondent pas :
     * $this->displayBadPassword();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayBadPassword() {
        $this->buildModal('Mauvais mot de passe', '<p class=\'alert alert-danger\'>Les deux mots de passe ne sont pas correctes </p>');
    }

    /**
     * Affiche un modal d'erreur indiquant les problèmes d'enregistrement des utilisateurs.
     *
     * Cette méthode génère un message d'alerte pour chaque utilisateur dont l'enregistrement a échoué
     * en raison d'un problème avec le login ou l'email. Les messages sont affichés dans un modal
     * Bootstrap avec une alerte rouge pour signaler les erreurs.
     *
     * @param array $doubles Un tableau contenant les identifiants des utilisateurs qui ont rencontré des problèmes d'enregistrement.
     *
     * @return void Cette méthode n'a pas de valeur de retour, elle affiche directement
     *               le modal d'erreur à l'utilisateur.
     *
     * @example
     * // Affiche un modal pour signaler les erreurs d'enregistrement :
     * $this->displayErrorDouble(['user1', 'user2']);
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayErrorDouble($doubles) {
        $content = "";
        foreach ($doubles as $double) {
            $content .= '<p class="alert alert-danger">' . $double . ' a rencontré un problème lors de l\'enregistrement, vérifié son login et son email !</p>';
        }
        $this->buildModal('Erreur durant l\'inscription', $content);
    }

    /**
     * Affiche un modal de confirmation indiquant que l'inscription a été validée.
     *
     * Cette méthode génère un message de succès dans un modal Bootstrap, confirmant
     * à l'utilisateur que son inscription a été correctement enregistrée.
     *
     * @return void Cette méthode n'a pas de valeur de retour, elle affiche directement
     *               le modal de confirmation à l'utilisateur.
     *
     * @example
     * // Affiche un modal pour confirmer l'inscription de l'utilisateur :
     * $this->displayInsertValidate();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayInsertValidate() {
        $this->buildModal('Inscription validée', '<p class=\'alert alert-success\'>Votre inscription a été validée.</p>');
    }

    /**
     * Display a message if the extension of the file is wrong
     */
    public function displayWrongExtension() {
        $this->buildModal('Mauvais fichier !', '<p class="alert alert-danger"> Mauvaise extension de fichier !</p>');
    }

    /**
     * Display a message if the file isn't a good file
     */
    public function displayWrongFile() {
        $this->buildModal('Mauvais fichier !', '<p class="alert alert-danger"> Vous utilisez un mauvais fichier excel / ou vous avez changé le nom des colonnes</p>');
    }

    /**
     * Display a message if the modification is a success
     */
    public function displayModificationValidate($redirect = null) {
        $this->buildModal('Modification réussie', '<p class="alert alert-success"> La modification a été appliquée</p>', $redirect);
    }

    /**
     * Display a message if the creation of an user has failed
     */
    public function displayErrorInsertion() {
        $this->buildModal('Erreur lors de l\'inscription', '<p class="alert alert-danger"> Le login ou l\'adresse mail est déjà utilisé(e) </p>');
    }

    /**
     * Affiche un modal d'erreur indiquant que le formulaire n'a pas été correctement rempli.
     *
     * Cette méthode génère un message d'erreur dans un modal Bootstrap, informant
     * l'utilisateur que le formulaire contient des données incorrectes et l'invite
     * à vérifier et à corriger les informations saisies avant de réessayer.
     *
     * @return void Cette méthode n'a pas de valeur de retour, elle affiche directement
     *               le modal d'erreur à l'utilisateur.
     *
     * @example
     * // Affiche un modal d'erreur lorsque le formulaire est mal rempli :
     * $this->errorMessageInvalidForm();
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function errorMessageInvalidForm() {
        $this->buildModal('Le formulaire n\'a pas été correctement remplie', '<p class="alert alert-danger">Le formulaire a été mal remplie, veuillez revoir les données rentrées et réessayez.</p>');
    }


    public function errorMessageCantAdd() {
        $this->buildModal('L\'ajout a échoué', '<p class="alert alert-danger">Une erreur s\'est produite lors de l\'envoie du formulaire, veuillez réessayer après avoir vérifié vos informations.</p>');
    }
}