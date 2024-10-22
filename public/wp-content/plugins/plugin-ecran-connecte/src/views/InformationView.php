<?php

namespace Views;


use Controllers\InformationController;
use Models\Information;

/**
 * Class InformationView
 *
 * All view for Information (Forms, tables, messages)
 *
 * @package Views
 */
class InformationView extends View
{

    /**
     * Affiche un formulaire pour créer ou modifier un texte avec des champs pour le titre, le contenu,
     * et la date d'expiration.
     *
     * Le formulaire inclut des validations pour s'assurer que le titre est optionnel (minimum 4 caractères),
     * que le contenu est requis (maximum 280 caractères), et que la date d'expiration ne peut pas être antérieure
     * à la date actuelle.
     *
     * @param string|null $title      Le titre du texte à afficher dans le champ (optionnel).
     * @param string|null $content    Le contenu à afficher dans la zone de texte (optionnel).
     * @param string|null $endDate    La date d'expiration à afficher (optionnel).
     * @param string $type            Le type d'action à effectuer, par défaut "createText".
     *                                 Peut être "submit" pour soumettre le formulaire.
     *
     * @return string                 Une chaîne HTML contenant le formulaire.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayFormText($title = null, $content = null, $endDate = null, $type = "createText") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '
        <form method="post">
            <div class="form-group">
                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                <input id="info" class="form-control" type="text" name="title" minlength="4" maxlength="40" placeholder="Titre..." value="' . $title . '">
            </div>
            <div class="form-group">
                <label for="content">Contenu</label>
                <textarea class="form-control" id="content" name="content" rows="3" placeholder="280 caractères au maximum" maxlength="280" minlength="4" required>' . $content . '</textarea>
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required >
            </div>
            <button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer ou modifier une image avec des champs pour le titre, le fichier image,
     * et la date d'expiration.
     *
     * Le formulaire permet à l'utilisateur d'insérer un titre optionnel et de télécharger une image. Si une image
     * existe déjà, elle sera affichée avec une légende. Le champ de date d'expiration est requis et ne peut pas
     * être antérieur à la date actuelle.
     *
     * @param string|null $title      Le titre de l'image à afficher dans le champ (optionnel).
     * @param string|null $content    Le nom du fichier image à afficher (optionnel).
     * @param string|null $endDate    La date d'expiration à afficher (optionnel).
     * @param string $type            Le type d'action à effectuer, par défaut "createImg".
     *                                 Peut être "submit" pour soumettre le formulaire.
     *
     * @return string                 Une chaîne HTML contenant le formulaire.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayFormImg($title = null, $content = null, $endDate = null, $type = "createImg") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
					<div class="form-group">
		                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
		                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . $title . '">
		            </div>';
        if ($content != null) {
            $form .= '
		       	<figure class="text-center">
				  <img class="img-thumbnail" src="' . TV_UPLOAD_PATH . $content . '" alt="' . $title . '">
				  <figcaption>Image actuelle</figcaption>
				</figure>';
        }
        $form .= '
			<div class="form-group">
				<label for="contentFile">Ajouter une image</label>
		        <input class="form-control-file" id="contentFile" type="file" name="contentFile"/>
		        <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
	        </div>
	        <div class="form-group">
				<label for="expirationDate">Date d\'expiration</label>
				<input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required >
			</div>
			<button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer ou modifier un tableau à partir d'un fichier XLS/XLSX avec des champs
     * pour le titre, le fichier à télécharger et la date d'expiration.
     *
     * Le formulaire permet à l'utilisateur d'insérer un titre optionnel et de télécharger un fichier
     * de type Excel. Si un contenu est déjà présent, le tableau correspondant sera affiché.
     * Le champ de date d'expiration est requis et ne peut pas être antérieur à la date actuelle.
     *
     * @param string|null $title      Le titre du tableau à afficher dans le champ (optionnel).
     * @param string|null $content    Le nom du fichier du tableau à afficher (optionnel).
     * @param string|null $endDate    La date d'expiration à afficher (optionnel).
     * @param string $type            Le type d'action à effectuer, par défaut "createTab".
     *                                 Peut être "submit" pour soumettre le formulaire.
     *
     * @return string                 Une chaîne HTML contenant le formulaire.
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayFormTab($title = null, $content = null, $endDate = null, $type = "createTab") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
						<div class="form-group">
			                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
			                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . $title . '">
			            </div>';

        if ($content != null) {
            $info = new InformationController();
            $list = $info->readSpreadSheet(TV_UPLOAD_PATH . $content);
            foreach ($list as $table) {
                $form .= $table;
            }
        }

        $form .= '
			<div class="form-group">
                <label for="contentFile">Ajout du fichier Xls (ou xlsx)</label>
                <input class="form-control-file" id="contentFile" type="file" name="contentFile" />
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
                <small id="tabHelp" class="form-text text-muted">Nous vous conseillons de ne pas dépasser trois colonnes.</small>
                <small id="tabHelp" class="form-text text-muted">Nous vous conseillons également de ne pas mettre trop de contenu dans une cellule.</small>
            </div>
            <div class="form-group">
				<label for="expirationDate">Date d\'expiration</label>
				<input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required >
			</div>
			<button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer ou modifier un document PDF avec des champs pour le titre,
     * le fichier à télécharger et la date d'expiration.
     *
     * Le formulaire permet à l'utilisateur d'insérer un titre optionnel et de télécharger un fichier
     * PDF. Si un contenu est déjà présent, le PDF correspondant sera affiché dans un iframe.
     * Le champ de date d'expiration est requis et ne peut pas être antérieur à la date actuelle.
     *
     * @param string|null $title      Le titre du document PDF à afficher dans le champ (optionnel).
     * @param string|null $content    Le nom du fichier PDF à afficher (optionnel).
     * @param string|null $endDate    La date d'expiration à afficher (optionnel).
     * @param string $type            Le type d'action à effectuer, par défaut "createPDF".
     *                                 Peut être "submit" pour soumettre le formulaire.
     *
     * @return string                 Une chaîne HTML contenant le formulaire.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayFormPDF($title = null, $content = null, $endDate = null, $type = "createPDF") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
					<div class="form-group">
		                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
		                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . $title . '">
		            </div>';

        if ($content != null) {
            $form .= '
			<div class="embed-responsive embed-responsive-16by9">
			  <iframe class="embed-responsive-item" src="' . TV_UPLOAD_PATH . $content . '" allowfullscreen></iframe>
			</div>';
        }

        $form .= '
			<div class="form-group">
                <label>Ajout du fichier PDF</label>
                <input class="form-control-file" type="file" name="contentFile"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
            </div>
            <div class="form-group">
				<label for="expirationDate">Date d\'expiration</label>
				<input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required >
			</div>
			<button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer ou modifier un événement, permettant de télécharger des fichiers
     * et de spécifier une date d'expiration.
     *
     * Le formulaire permet à l'utilisateur de sélectionner plusieurs fichiers (images ou PDF) à
     * télécharger. La date d'expiration est requise et ne peut pas être antérieure à la date actuelle.
     *
     * @param string|null $endDate   La date d'expiration à afficher (optionnel).
     * @param string $type           Le type d'action à effectuer, par défaut "createEvent".
     *                               Peut être "submit" pour soumettre le formulaire.
     *
     * @return string                Une chaîne HTML contenant le formulaire.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayFormEvent($endDate = null, $type = "createEvent") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $form = '
		<form method="post" enctype="multipart/form-data">
			<div class="form-group">
                <label>Sélectionner les fichiers</label>
                <input class="form-control-file" multiple type="file" name="contentFile[]"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
                <small id="fileHelp" class="form-text text-muted">Images ou PDF</small>
        	</div>
        	<div class="form-group">
				<label for="expirationDate">Date d\'expiration</label>
				<input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required >
			</div>
			<button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }
        $form .= '</form>';

        return $form;
    }

    /**
     * Génère le contenu HTML décrivant le processus de création d'informations
     * à afficher sur les téléviseurs connectés.
     *
     * Cette méthode fournit des explications sur la création d'informations,
     * y compris comment elles sont publiées et affichées. Elle inclut également
     * une image illustrative représentant un téléviseur.
     *
     * @return string                Une chaîne HTML contenant des informations
     *                               sur le processus de création d'informations.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function contextCreateInformation() {
        return '
		<hr class="half-rule">
		<div>
			<h2>Les informations</h2>
			<p class="lead">Lors de la création de votre information, celle-ci est postée directement sur tous les téléviseurs qui utilisent ce site.</p>
			<p class="lead">Les informations que vous créez seront affichées avec les informations déjà présentes.</p>
			<p class="lead">Les informations sont affichées dans un diaporama défilant les informations une par une sur la partie droite des téléviseurs.</p>
			<div class="text-center">
				<figure class="figure">
					<img src="' . TV_PLUG_PATH . 'public/img/presentation.png" class="figure-img img-fluid rounded" alt="Représentation d\'un téléviseur">
					<figcaption class="figure-caption">Représentation d\'un téléviseur</figcaption>
				</figure>
			</div>
		</div>';
    }

    /**
     * Affiche un formulaire de modification d'informations en fonction du type d'information.
     *
     * Cette méthode génère un lien pour revenir à la page de gestion des informations, puis
     * affiche le formulaire correspondant au type d'information spécifié (texte, image, tableau, PDF ou événement).
     *
     * @param string $title      Le titre de l'information à modifier.
     * @param string $content    Le contenu de l'information à modifier (peut être une URL pour les images ou PDF).
     * @param string $endDate    La date d'expiration de l'information.
     * @param string $type       Le type d'information à modifier (valeurs possibles : 'text', 'img', 'tab', 'pdf', 'event').
     *
     * @return string           Une chaîne HTML contenant le lien de retour et le formulaire de modification
     *                          approprié pour le type d'information.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayModifyInformationForm($title, $content, $endDate, $type) {
        if ($type == "text") {
            return '<a href="' . esc_url(get_permalink(get_page_by_title('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormText($title, $content, $endDate, 'submit');
        } elseif ($type == "img") {
            return '<a href="' . esc_url(get_permalink(get_page_by_title('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormImg($title, $content, $endDate, 'submit');
        } elseif ($type == "tab") {
            return '<a href="' . esc_url(get_permalink(get_page_by_title('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormTab($title, $content, $endDate, 'submit');
        } elseif ($type == "pdf") {
            return '<a href="' . esc_url(get_permalink(get_page_by_title('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormPDF($title, $content, $endDate, 'submit');
        } elseif ($type == "event") {
            $extension = explode('.', $content);
            $extension = $extension[1];
            if ($extension == "pdf") {
                return '<a href="' . esc_url(get_permalink(get_page_by_title('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormPDF($title, $content, $endDate, 'submit');
            } else {
                return '<a href="' . esc_url(get_permalink(get_page_by_title('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormImg($title, $content, $endDate, 'submit');
            }
        } else {
            return $this->noInformation();
        }
    }

    /**
     * Affiche le début d'un conteneur pour un diaporama.
     *
     * Cette méthode génère une structure HTML pour le conteneur principal du diaporama,
     * permettant d'afficher une série d'images ou d'informations de manière interactive.
     *
     * @return void
     *
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayStartSlideshow() {
        echo '<div class="slideshow-container">';
    }

    /**
     * Affiche une diapositive dans le diaporama avec un titre, un contenu et un type spécifié.
     *
     * Cette méthode génère du HTML pour afficher une diapositive, qui peut contenir différents types de contenu
     * tels que du texte, des images ou des fichiers PDF. Elle gère également la distinction entre l'affichage
     * sur le site d'administration et l'affichage normal.
     *
     * @param string $title     Le titre de la diapositive, affiché en tant que en-tête si non vide.
     * @param string $content   Le contenu à afficher dans la diapositive (texte, image ou PDF).
     * @param string $type      Le type de contenu à afficher ('text', 'img', 'pdf', 'event', 'special').
     * @param bool   $adminSite Indique si la diapositive est affichée sur le site d'administration.
     *
     * @return void
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displaySlide($title, $content, $type, $adminSite = false) {
        echo '<div class="myInfoSlides text-center">';

        // If the title is empty
        if ($title != "Sans titre") {
            echo '<h2 class="titleInfo">' . $title . '</h2>';
        }

        $url = TV_UPLOAD_PATH;
        if ($adminSite) {
            $url = URL_WEBSITE_VIEWER . TV_UPLOAD_PATH;
        }

        if ($type == 'pdf' || $type == "event" || $type == "img") {
            $extension = explode('.', $content);
            $extension = $extension[1];
        }

        if ($type == 'pdf' || $type == "event" && $extension == "pdf") {
            echo '
			<div class="canvas_pdf" id="' . $content . '">
			</div>';
        } elseif ($type == "img" || $type == "event") {
            echo '<img class="img-thumbnail" src="' . $url . $content . '" alt="' . $title . '">';
        } else if ($type == 'text') {
            echo '<p class="lead">' . $content . '</p>';
        } else if ($type == 'special') {
            $func = explode('(Do this(function:', $content);
            $text = explode('.', $func[0]);
            foreach ($text as $value) {
                echo '<p class="lead">' . $value . '</p>';
            }
            $func = explode(')end)', $func[1]);
            echo $func[0]();
        } else {
            echo $content;
        }
        echo '</div>';
    }

    /**
     * Affiche le contexte et les instructions pour visualiser et gérer toutes les informations créées sur le site.
     *
     * Cette méthode génère un bloc HTML qui présente un aperçu des fonctionnalités disponibles pour les utilisateurs
     * concernant la gestion des informations. Elle inclut des détails sur la façon de visualiser, modifier ou supprimer
     * des informations, ainsi qu'un lien pour créer une nouvelle information.
     *
     * @return string Le code HTML généré pour afficher les instructions et le lien vers la page de création d'information.
     *
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function contextDisplayAll() {
        return '
		<div class="row">
			<div class="col-6 mx-auto col-md-6 order-md-2">
				<img src="' . TV_PLUG_PATH . 'public/img/info.png" alt="Logo information" class="img-fluid mb-3 mb-md-0">
			</div>
			<div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
				<p class="lead">Vous pouvez retrouver ici toutes les informations qui ont été créées sur ce site.</p>
				<p class="lead">Les informations sont triées de la plus vieille à la plus récente.</p>
				<p class="lead">Vous pouvez modifier une information en cliquant sur "Modifier" à la ligne correspondante à l\'information.</p>
				<p class="lead">Vous souhaitez supprimer une / plusieurs information(s) ? Cochez les cases des informations puis cliquez sur "Supprimer" le bouton ce situe en bas du tableau.</p>
			</div>
		</div>
		<a href="' . esc_url(get_permalink(get_page_by_title('Créer une information'))) . '">Créer une information</a>
		<hr class="half-rule">';
    }

    /**
     * Affiche un message lorsque l'information demandée n'est pas trouvée.
     *
     * Cette méthode génère un bloc HTML qui informe l'utilisateur qu'aucune information n'a été trouvée.
     * Elle inclut également un lien pour retourner à la gestion des informations et un autre lien pour créer
     * une nouvelle information.
     *
     * @return string Le code HTML généré pour afficher le message d'absence d'information et les liens associés.
     *
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function noInformation() {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title('Gestion des informations'))) . '">< Retour</a>
		<div>
			<h3>Information non trouvée</h3>
			<p>Cette information n\'éxiste pas, veuillez bien vérifier d\'avoir bien cliqué sur une information.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title('Créer une information'))) . '">Créer une information</a>
		</div>';
    }

    /**
     * Start the slideshow
     */
    public function displayStartSlideEvent() {
        echo '
            <div id="slideshow-container" class="slideshow-container">';
    }

    /**
     * Start a slide
     */
    public function displaySlideBegin() {
        echo '
			<div class="mySlides event-slide">';
    }

    /**
     * Affiche un modal de confirmation après l'ajout d'une nouvelle information.
     *
     * Cette méthode génère un modal indiquant que l'information a été ajoutée avec succès.
     * Elle crée également un lien vers la page de gestion des informations pour permettre à l'utilisateur
     * d'y accéder facilement après l'ajout.
     *
     * @return void
     *
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayCreateValidate() {
        $page = get_page_by_title('Gestion des informations');
        $linkManageInfo = get_permalink($page->ID);
        $this->buildModal('Ajout d\'information validé', '<p class="alert alert-success"> L\'information a été ajoutée </p>', $linkManageInfo);
    }

    /**
     * Affiche un modal de confirmation après la modification d'une information.
     *
     * Cette méthode génère un modal indiquant que l'information a été modifiée avec succès.
     * Elle crée également un lien vers la page de gestion des informations pour permettre à l'utilisateur
     * d'y accéder facilement après la modification.
     *
     * @return void
     *
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayModifyValidate() {
        $page = get_page_by_title('Gestion des informations');
        $linkManageInfo = get_permalink($page->ID);
        $this->buildModal('Modification d\'information validée', '<p class="alert alert-success"> L\'information a été modifiée </p>', $linkManageInfo);
    }

    /**
     * Display a message if the insertion of the information doesn't work
     */
    public function displayErrorInsertionInfo() {
        echo '<p>Il y a eu une erreur durant l\'insertion de l\'information</p>';
    }

    /**
     * Affiche un message indiquant que l'utilisateur ne peut pas modifier une alerte.
     *
     * Cette méthode génère une réponse HTML qui informe l'utilisateur qu'il ne
     * peut pas modifier une information, car celle-ci appartient à un autre utilisateur.
     * Un lien de retour vers la page de gestion des informations et un lien pour
     * créer une nouvelle information sont également fournis.
     *
     * @return string Retourne le code HTML à afficher pour l'information non modifiable.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function informationNotAllowed() {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title('Gestion des informations'))) . '">< Retour</a>
		<div>
			<h3>Vous ne pouvez pas modifier cette alerte</h3>
			<p>Cette information appartient à quelqu\'un d\'autre, vous ne pouvez donc pas modifier cette information.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title('Créer une information'))) . '">Créer une information</a>
		</div>';
    }
}
