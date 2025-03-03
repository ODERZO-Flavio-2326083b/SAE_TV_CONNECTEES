<?php

/**
 * Fichier InformationView.php
 *
 * Ce fichier contient la classe 'InformationView', qui est responsable de
 * l'affichage des vues liées aux informations dans l'application. 
 * Cette classe génère des formulaires permettant de créer ou de modifier des 
 * textes avec un titre, un contenu et une date d'expiration. Elle permet également
 * d'afficher des messages d'alerte et des tableaux pour gérer les informations.
 *
 * PHP version 8.3
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/InformationView
 * Documentation de la classe
 * @since    2025-01-13
 */

namespace views;


use controllers\InformationController;
use models\Information;
use models\Scrapper;

/**
 * Classe InformationView
 *
 * Cette classe gère l'affichage des vues liées à la gestion des informations.
 * Elle permet de créer, de modifier et d'afficher des informations, y compris les
 * titres, le contenu et la date d'expiration. Elle génère également des messages
 * d'alerte et des tableaux pour aider les utilisateurs à visualiser et gérer les
 * informations.
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/InformationView Documentation de la classe
 * @since    2025-01-13
 */
class InformationView extends View
{

    /**
     * Affiche un formulaire pour créer ou modifier un texte avec des champs pour le
     * titre, le contenu, et la date d'expiration.
     *
     * Le formulaire inclut des validations pour s'assurer que le titre est optionnel
     * (minimum 4 caractères), que le contenu est requis (maximum 280 caractères),
     * et que la date d'expiration ne peut pas être antérieure à la date actuelle.
     *
     * @param array       $allDepts Une liste de tous les départements
     *                              présents dans la base de données.
     * @param bool        $isAdmin  Un booléen correspondant à "true"
     *                              si l'utilisateur est un
     *                              administrateur, et "false" sinon.
     * @param int|null    $currDept Le numéro du
     *                              département actuel.
     * @param string|null $title    Le titre du texte à afficher dans le
     *                              champ (optionnel).
     * @param string|null $content  Le contenu à afficher dans la zone de
     *                              texte (optionnel).
     * @param string|null $endDate  La date d'expiration à
     *                              afficher (optionnel).
     * @param string      $type     Le type d'action à effectuer, par
     *                              défaut "createText". Peut être
     *                              "submit" pour soumettre le formulaire.
     *
     * @return string                 Une chaîne HTML contenant le formulaire.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayFormText(array $allDepts, bool $isAdmin = false,
        int $currDept = null, $title = null,
        $content = null, $endDate = null,
        $type = "createText") : string {

        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $disabled = $isAdmin ? '' : 'disabled';
        $form = '
        <form method="post">
            <div class="form-group">
                <label for="title">Titre <span class="text-muted">(Optionnel)</span>
                </label>
                <input id="info" class="form-control" type="text" name="title" 
                minlength="4" maxlength="40" placeholder="Titre..." value="'
            . $title . '">
            </div>
            <div class="form-group">
                <label for="content">Contenu</label>
                <textarea class="form-control" id="content" name="content" rows="3" 
                placeholder="280 caractères au maximum" maxlength="280" minlength="4"
                 required>' . $content . '</textarea>
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input id="expirationDate" class="form-control" type="date" 
                name="expirationDate" min="' . $dateMin . '" value="' . $endDate
            . '" required >
            </div>
            <div class="form-group">
                <label for="informationDept">Département</label>
                <br>    
                <select id="informationDept" name="informationDept" 
                class="form-control"' . $disabled . '>
                    ' . $this->buildDepartmentOptions($allDepts, $currDept) . '
                </select>
            </div>
            <button class="btn button_ecran" type="submit" name="' . $type . '">
            Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" 
name="delete" onclick="return confirm(
    \' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer ou modifier une image avec des champs pour le
     * titre, le fichier image, et la date d'expiration.
     *
     * Le formulaire permet à l'utilisateur d'insérer un titre optionnel et de
     * télécharger une image. Si une image existe déjà, elle sera affichée avec une
     * légende.
     * Le champ de date d'expiration est requis et ne peut pas être antérieur à la
     * date actuelle.
     *
     * @param array       $allDepts Une liste de tous les départements présents
     *                              dans la base de données.
     * @param bool        $isAdmin  Un booléen correspondant à "true"
     *                              si l'utilisateur est un
     *                              administrateur, et "false" sinon.
     * @param int|null    $currDept Le numéro du département
     *                              actuel.
     * @param string|null $title    Le titre de l'image à afficher dans le
     *                              champ (optionnel).
     * @param string|null $content  Le nom du fichier image à
     *                              afficher (optionnel).
     * @param string|null $endDate  La date d'expiration à
     *                              afficher (optionnel).
     * @param string      $type     Le type d'action à effectuer,
     *                              par défaut "createImg". Peut
     *                              être "submit" pour soumettre le
     *                              formulaire.
     *
     * @return string                 Une chaîne HTML contenant le formulaire.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayFormImg(array $allDepts, bool $isAdmin = false,
        int $currDept = null,$title = null,
        $content = null, $endDate = null,
        $type = "createImg"
    ) {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $disabled = $isAdmin ? '' : 'disabled';

        $form = '<form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Titre <span class="text-muted">(Optionnel)
                        </span></label>
                        <input id="title" class="form-control" type="text" 
                        name="title" placeholder="Inserer un titre" maxlength="60" 
                        value="' . $title . '">
                    </div>';
        if ($content != null) {
            $form .= '
                   <figure class="text-center">
                  <img class="img-thumbnail" src="' . TV_UPLOAD_PATH . $content . '" 
                  alt="' . $title . '">
                  <figcaption>Image actuelle</figcaption>
                </figure>';
        }
        $form .= '
            <div class="form-group">
                <label for="contentFile">Ajouter une image</label>
                <input class="form-control-file" id="contentFile" type="file" 
                name="contentFile"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
                <small id="tabHelp" class="form-text text-muted">Formats acceptés : 
                .jpg, .jpeg, .gif, .png, .svg</small>

            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input id="expirationDate" class="form-control" type="date" 
                name="expirationDate" min="' . $dateMin . '" value="' . $endDate
            . '" required >
            </div>
            <div class="form-group">
                <label for="informationDept">Département</label>
                <br>    
                <select id="informationDept" name="informationDept" 
                class="form-control"' . $disabled . '>
                    ' . $this->buildDepartmentOptions($allDepts, $currDept) . '
                </select>
            </div>
            <button class="btn button_ecran" type="submit" name="' . $type . '">
            Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" 
name="delete" onclick="return confirm(
    \' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer ou modifier une vidéo avec des champs pour le
     * titre, le fichier video, et la date d'expiration.
     *
     * Le formulaire permet à l'utilisateur d'insérer un titre optionnel et de
     * télécharger une video. Si une video existe déjà, elle sera affichée avec une
     * légende.
     * Le champ de date d'expiration est requis et ne peut pas être antérieur à la
     * date actuelle.
     *
     * @param array       $allDepts Une liste de tous les départements présents
     *                              dans la base de données.
     * @param bool        $isAdmin  Un booléen correspondant à "true"
     *                              si l'utilisateur est un
     *                              administrateur, et "false" sinon.
     * @param int|null    $currDept Le numéro du département
     *                              actuel.
     * @param string|null $title    Le titre de la video à afficher dans le
     *                              champ (optionnel).
     * @param string|null $content  Le nom du fichier video à
     *                              afficher (optionnel).
     * @param string|null $endDate  La date d'expiration à
     *                              afficher (optionnel).
     * @param string      $type     Le type d'action à effectuer,
     *                              par défaut "createVideo". Peut
     *                              être "submit" pour soumettre le
     *                              formulaire.
     *
     * @return string                 Une chaîne HTML contenant le formulaire.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayFormVideo(array $allDepts, bool $isAdmin = false,
        int $currDept = null, $title = null,
        $content = null, $endDate = null,
        $type = "createVideo"
    ) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $disabled = $isAdmin ? '' : 'disabled';

        $form = '<form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre <span class="text-muted">(Optionnel)
                    </span></label>
                    <input id="title" class="form-control" type="text" name="title" 
                    placeholder="Insérer un titre" maxlength="60" value="' . $title
            . '">
                </div>';
        if ($content != null) {
            $form .= '
               <figure class="text-center">
              <img class="img-thumbnail" src="' . TV_UPLOAD_PATH . $content . '" 
              alt="' . $title . '">
              <figcaption>Vidéo actuelle</figcaption>
            </figure>';
        }
        $form .= '
        <div class="form-group">
            <label for="contentFile">Ajouter une vidéo</label>
            <input class="form-control-file" id="contentFile" type="file" 
            name="contentFile"/>
            <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
            <small id="tabHelp" class="form-text text-muted">Formats acceptés :
             .mp4, .webm</small>
        </div>
        <div class="form-group">
			<label for="expirationDate">Date d\'expiration</label>
			<input id="expirationDate" class="form-control" type="date" 
			name="expirationDate" min="' . $dateMin . '" value="' . $endDate
            . '" required >
		</div>
		<div class="form-group">
                <label for="informationDept">Département</label>
                <br>    
                <select id="informationDept" name="informationDept" 
                class="form-control"' . $disabled . '>
                    ' . $this->buildDepartmentOptions($allDepts, $currDept) . '
                </select>
        </div>
		<button class="btn button_ecran" type="submit" name="' . $type . '">Valider
		</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" 
name="delete" onclick="return confirm(\'Voulez-vous supprimer cette vidéo ?\');">
Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer ou modifier une vidéo avec des champs pour le
     * titre, le fichier video, et la date d'expiration.
     *
     * Le formulaire permet à l'utilisateur d'insérer un titre optionnel et de
     * télécharger un short. Si un short existe déjà, elle sera affichée avec une
     * légende.
     * Le champ de date d'expiration est requis et ne peut pas être antérieur à la
     * date actuelle.
     *
     * @param array       $allDepts Une liste de tous les départements présents
     *                              dans la base de données.
     * @param bool        $isAdmin  Un booléen correspondant à "true"
     *                              si l'utilisateur est un
     *                              administrateur, et "false" sinon.
     * @param int|null    $currDept Le numéro du département
     *                              actuel.
     * @param string|null $title    Le titre du short à afficher dans le
     *                              champ (optionnel).
     * @param string|null $content  Le nom du fichier video à
     *                              afficher (optionnel).
     * @param string|null $endDate  La date d'expiration à
     *                              afficher (optionnel).
     * @param string      $type     Le type d'action à effectuer,
     *                              par défaut "createShort". Peut
     *                              être "submit" pour soumettre le
     *                              formulaire.
     *
     * @return string                 Une chaîne HTML contenant le formulaire.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayFormShort(array $allDepts, bool $isAdmin = false,
        int $currDept = null, $title = null,
        $content = null, $endDate = null,
        $type = "createShort"
    ) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $disabled = $isAdmin ? '' : 'disabled';

        $form = '<form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre <span class="text-muted">(Optionnel)
                    </span></label>
                    <input id="title" class="form-control" type="text" name="title" 
                    placeholder="Insérer un titre" maxlength="60" value="' . $title
            . '">
                </div>';
        if ($content != null) {
            $form .= '
               <figure class="text-center">
              <img class="img-thumbnail" src="' . TV_UPLOAD_PATH . $content . '" 
              alt="' . $title . '">
              <figcaption>Vidéo actuelle</figcaption>
            </figure>';
        }
        $form .= '
        <div class="form-group">
            <label for="contentFile">Ajouter une vidéo (short)</label>
            <input class="form-control-file" id="contentFile" type="file" 
            name="contentFile"/>
            <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
            <small id="tabHelp" class="form-text text-muted">Formats acceptés : .mp4,
             .webm</small>
            <small id="tabHelp" class="form-text text-muted">Un short est une courte 
            vidéo au format vertical.</small>
        </div>
        <div class="form-group">
			<label for="expirationDate">Date d\'expiration</label>
			<input id="expirationDate" class="form-control" type="date" 
			name="expirationDate" min="' . $dateMin . '" value="' . $endDate
            . '" required >
		</div>
		<div class="form-group">
            <label for="informationDept">Département</label>
            <br>    
            <select id="informationDept" name="informationDept" class="form-control"'
            . $disabled . '>
                ' . $this->buildDepartmentOptions($allDepts, $currDept) . '
            </select>
        </div>
		<button class="btn button_ecran" type="submit" name="' . $type . '">Valider
		</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" 
name="delete" onclick="return confirm(\'Voulez-vous supprimer cette vidéo ?\');">
Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer ou modifier un document PDF avec des champs
     * pour le titre, le fichier à télécharger et la date d'expiration.
     *
     * Le formulaire permet à l'utilisateur d'insérer un titre optionnel et de
     * télécharger un fichier PDF. Si un contenu est déjà présent, le PDF
     * correspondant sera affiché dans un iframe.
     * Le champ de date d'expiration est requis et ne peut pas être antérieur à la
     * date actuelle.
     *
     * @param array       $allDepts Une liste de tous les départements présents
     *                              dans la base de données.
     * @param bool        $isAdmin  Un booléen correspondant à "true"
     *                              si l'utilisateur est un
     *                              administrateur, et "false" sinon.
     * @param int|null    $currDept Le numéro du département
     *                              actuel.
     * @param string|null $title    Le titre du document PDF à afficher dans le
     *                              champ (optionnel).
     * @param string|null $content  Le nom du fichier PDF à
     *                              afficher (optionnel).
     * @param string|null $endDate  La date d'expiration à
     *                              afficher (optionnel).
     * @param string      $type     Le type d'action à effectuer, par
     *                              défaut "createPDF". Peut être "submit"
     *                              pour soumettre le formulaire.
     *
     * @return string                 Une chaîne HTML contenant le formulaire.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayFormPDF(array $allDepts, bool $isAdmin = false,
        int $currDept = null, $title = null,
        $content = null, $endDate = null,
        $type = "createPDF"
    ) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $disabled = $isAdmin ? '' : 'disabled';

        $form = '<form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Titre <span class="text-muted">(Optionnel)
                        </span></label>
                        <input id="title" class="form-control$title" type="text" 
                        name="title" placeholder="Inserer un titre" maxlength="60" 
                        value="' . $title . '">
                    </div>';
        if ($content != null) {
            $form .= '
            <div class="embed-responsive embed-responsive-16by9">
              <iframe class="embed-responsive-item" src="' . TV_UPLOAD_PATH
                . $content . '" allowfullscreen></iframe>
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
                <input id="expirationDate" class="form-control" type="date" 
                name="expirationDate" min="' . $dateMin . '" value="' . $endDate
            . '" required >
            </div>
            <div class="form-group">
                <label for="informationDept">Département</label>
                <br>    
                <select id="informationDept" name="informationDept" 
                class="form-control"' . $disabled . '>
                    ' . $this->buildDepartmentOptions($allDepts, $currDept) . '
                </select>
            </div>
            <button class="btn button_ecran" type="submit" name="' . $type . '">
            Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" 
name="delete" onclick="return confirm(
    \' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }
        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer ou modifier un événement, permettant de
     * télécharger des fichiers et de spécifier une date d'expiration.
     *
     * Le formulaire permet à l'utilisateur de sélectionner plusieurs fichiers
     * (images ou PDF) à télécharger.
     * La date d'expiration est requise et ne peut pas être antérieure à la date
     * actuelle.
     *
     * @param array       $allDepts Une liste de tous les départements présents
     *                              dans la base de données.
     * @param bool        $isAdmin  Un booléen correspondant à "true"
     *                              si l'utilisateur est un
     *                              administrateur, et "false" sinon.
     * @param int|null    $currDept Le numéro du département
     *                              actuel.
     * @param string|null $endDate  La date d'expiration à
     *                              afficher (optionnel).
     * @param string      $type     Le type d'action à effectuer, par
     *                              défaut "createEvent". Peut être
     *                              "submit" pour soumettre le formulaire.
     *
     * @return string                Une chaîne HTML contenant le formulaire.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayFormEvent( array $allDepts, bool $isAdmin = false,
        int $currDept = null, $endDate = null,
        $type = "createEvent"
    ) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $disabled = $isAdmin ? '' : 'disabled';

        $form = '
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Sélectionner les fichiers</label>
                <input class="form-control-file" multiple type="file" 
                name="contentFile[]"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
                <small id="fileHelp" class="form-text text-muted">Images ou PDF
                </small>
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input id="expirationDate" class="form-control" type="date" 
                name="expirationDate" min="' . $dateMin . '" value="' . $endDate
            . '" required >
            </div>
            <div class="form-group">
                <label for="informationDept">Département</label>
                <br>    
                <select id="informationDept" name="informationDept" 
                class="form-control"' . $disabled . '>
                    ' . $this->buildDepartmentOptions($allDepts, $currDept) . '
                </select>
            </div>
            <button class="btn button_ecran" type="submit" name="' . $type . '">
            Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" 
name="delete" onclick="return confirm(
    \' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }
        $form .= '</form>';
        return $form;
    }

    public function displayFormScrapping (array $allDepts, bool $isAdmin = false,
    int $currDept = null, $endDate = null, $title = null, $url = null,
    $type = "createScrapping") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $disabled = $isAdmin ? '' : 'disabled';
        $form = '
        <form method="post" enctype="multipart/form-data" id="registerScrappingForm">
            <div class="form-group">
                <label for="title">Titre du scrapping</label>
                <input id="title" class="form-control" type="text"
                name="title" placeholder="Inserer un titre" maxlength="60" 
                        value="' . $title . '">
            </div>
            <div class="form-group">
                <label for="url">Lien du site</label>
                <input id="url" class="form-control" type="url"
                name="content" placeholder="Inserer un lien" maxlength="255"
                        value="' . $url . '">
            </div>
            <div class="form-group" id="tagContainer">
                <label for="tag" id="tagDiv">Tag</label> ' . $this->buildTagOption() . '
            </div>
            <input type="button" class="btn button_ecran" onclick="addButtonTag()" 
            value="Ajouter des tags">
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input id="expirationDate" class="form-control" type="date" 
                name="expirationDate" min="' . $dateMin . '" value="' . $endDate
            . '" required >
            </div>
            <div class="form-group">
                <label for="informationDept">Département</label>
                <br>    
                <select id="informationDept" name="informationDept" 
                class="form-control"' . $disabled . '>
                    ' . $this->buildDepartmentOptions($allDepts, $currDept) . '
                </select>
            </div>
            <button class="btn button_ecran" type="submit" name="' . $type . '">
            Valider</button>';
        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" 
name="delete" onclick="return confirm(
    \' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }
        $form .= '</form>';
        return $form;
    }

    public static function buildTagOption() {
        return '  <div>
                       <input id="content" class="form-control" type="text" name="contentScrapper[]" placeholder="Inserer le tag" maxlength="255" required>
                       <select class="form-control firstSelect" id="tag" name="tag[]" required="">
                            <option value="default">Défault</option>
                            <option value="image">Image</option>
                            <option value="lien">Lien</option>
                            <option value="url">URL</option>
                            <option value="article">Article</option> 
                     </div>
                          ';
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
     * @version 1.0
     * @date    2024-10-15
     */
    public function contextCreateInformation() : string
    {
        return '
        <hr class="half-rule">
        <div>
            <h2>Les informations</h2>
            <p class="lead">Lors de la création de votre information, celle-ci est 
            postée directement sur tous les téléviseurs qui utilisent ce site.</p>
            <p class="lead">Les informations que vous créez seront affichées avec les
             informations déjà présentes.</p>
            <p class="lead">Les informations sont affichées dans un diaporama 
            défilant les informations une par une sur la partie droite des 
            téléviseurs.</p>
            <p class="lead">Les vidéos sont affichées dans un diaporama par-dessus 
            l\'emploi du temps.</p>
            <div class="text-center">
                <figure class="figure">
                    <img src="' . TV_PLUG_PATH . 'public/img/presentation.png" 
                    class="figure-img img-fluid rounded" 
                    alt="Représentation d\'un téléviseur">
                    <figcaption class="figure-caption">Représentation d\'un 
                    téléviseur</figcaption>
                </figure>
            </div>
        </div>';
    }

    /**
     * Affiche un formulaire de modification d'informations en fonction du type
     * d'information.
     *
     * Cette méthode génère un lien pour revenir à la page de gestion des
     * informations, puis affiche le formulaire correspondant au type d'information
     * spécifié (texte, image, vidéo, short, PDF ou événement).
     *
     * @param string   $title    Le titre de l'information
     *                           à modifier.
     * @param string   $content  Le contenu de l'information à modifier (peut
     *                           être une URL pour les images ou PDF).
     * @param string   $endDate  La date d'expiration de l'information.
     * @param string   $type     Le type d'information à modifier (valeurs
     *                           possibles : 'text', 'img', 'video', 'short', 'pdf',
     *                           'event').
     * @param array    $allDepts Une liste de tous les départements présents
     *                           dans la base de données.
     * @param bool     $isAdmin  Un booléen correspondant à "true"
     *                           si l'utilisateur est un
     *                           administrateur, et "false" sinon.
     * @param int|null $currDept Le numéro du département actuel.
     *
     * @return string           Une chaîne HTML contenant le lien de retour et le
     * formulaire de modification approprié pour le type d'information.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayModifyInformationForm( string $title, string $content,
        string $endDate, string $type,
        array $allDepts,
        bool $isAdmin = false,
        int $currDept = null
    ): string {

        switch ($type) {
        case "text":
            return '<a href="'
                . esc_url(
                    get_permalink(
                        get_page_by_title_custom('Gestion des informations')
                    )
                ) . '">< Retour</a>' .
                    $this->displayFormText(
                        $allDepts, $isAdmin, $currDept, $title, $content,
                        $endDate, 'submit'
                    );
        case "img":
            return '<a href="'
                . esc_url(
                    get_permalink(
                        get_page_by_title_custom('Gestion des informations')
                    )
                ) . '">< Retour</a>' .
                    $this->displayFormImg(
                        $allDepts, $isAdmin, $currDept, $title, $content, $endDate,
                        'submit'
                    );
        case "video":
            return '<a href="'
                . esc_url(
                    get_permalink(
                        get_page_by_title_custom('Gestion des informations')
                    )
                ) . '">< Retour</a>' .
                    $this->displayFormVideo(
                        $allDepts, $isAdmin, $currDept, $title,
                        $content, $endDate, 'submit'
                    );
        case "short":
            return '<a href="'
                . esc_url(
                    get_permalink(
                        get_page_by_title_custom('Gestion des informations')
                    )
                ) . '">< Retour</a>' .
                    $this->displayFormShort(
                        $allDepts, $isAdmin, $currDept, $title,
                        $content, $endDate, 'submit'
                    );
        case "pdf":
            return '<a href="'
                . esc_url(
                    get_permalink(
                        get_page_by_title_custom('Gestion des informations')
                    )
                ) . '">< Retour</a>' .
                    $this->displayFormPDF(
                        $allDepts, $isAdmin, $currDept, $title,
                        $content, $endDate, 'submit'
                    );
        case "scrapping":
            return '<a href="'
                . esc_url(
                    get_permalink(
                        get_page_by_title_custom('Gestion des informations')
                    )
                ) . '">< Retour</a>' .
                $this->displayFormScrapping(
                    $allDepts, $isAdmin, $currDept, $title, $endDate, 'submit'
                );
        case "event":
            $extension = explode('.', $content);
            $extension = $extension[1];
            if ($extension == "pdf") {
                return '<a href="'
                    . esc_url(
                        get_permalink(
                            get_page_by_title_custom('Gestion des informations')
                        )
                ) . '">< Retour</a>' . $this->displayFormPDF(
                        $allDepts, $isAdmin,
                        $currDept, $title, $content, $endDate, 'submit'
                    );
            } else {
                return '<a href="'
                    . esc_url(
                        get_permalink(
                            get_page_by_title_custom('Gestion des informations')
                        )
                    ) . '">< Retour</a>' . $this->displayFormImg(
                        $allDepts, $isAdmin,
                        $currDept, $title, $content, $endDate, 'submit'
                    );
            }
        default:
            return $this->noInformation();
        }
    }

    /**
     * Affiche le début d'un conteneur pour un diaporama.
     *
     * Cette méthode génère une structure HTML pour le conteneur principal du
     * diaporama, permettant d'afficher une série d'images ou d'informations de
     * manière interactive.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayStartSlideshow()
    {
        echo '<div class="slideshow-container">';
    }


    /**
     * Affiche le début d'un conteneur pour un diaporama de vidéos.
     *
     * Cette méthode génère une structure HTML pour le conteneur principal
     * du diaporama destiné uniquement aux vidéos, positionné à gauche de l'écran.
     *
     * @return  void
     * @version 1.0
     * @date    2024-12-29
     */
    public function displayStartSlideVideo()
    {
        echo '<div class="video-slideshow-container">';
    }

    /**
     * Affiche une diapositive dans le diaporama avec un titre, un contenu et un type
     * spécifié.
     *
     * Cette méthode génère du HTML pour afficher une diapositive, qui peut contenir
     * différents types de contenu tels que du texte, des images, des vidéos ou des
     * fichiers PDF. Elle gère également la distinction entre l'affichage sur le site
     * d'administration et l'affichage normal.
     *
     * @param string $title     Le titre de la diapositive, affiché en tant que
     *                          en-tête si non vide.
     * @param string $content   Le contenu à afficher dans la diapositive
     *                          (texte, image ou PDF).
     * @param string $type      Le type de contenu à afficher
     *                          ('text', 'img', 'video', 'short', 'pdf', 'event').
     * @param bool   $adminSite Indique si la diapositive est affichée sur le site
     *                          d'administration.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displaySlideVideo($title, $content, $type, $adminSite = false)
    {
        echo '<div class="myVideoSlides text-center" style="display: block;">';

        // If the title is empty
        if ($title != "Sans titre") {
            echo '<h2 class="titleInfo">' . $title . '</h2>';
        }

        $url = $adminSite ? URL_WEBSITE_VIEWER . TV_UPLOAD_PATH : TV_UPLOAD_PATH;

        echo '<video class="video_container" src="' . $url . $content . '
              " autoplay loop muted></video>';



        echo '</div>';
    }

    /**
     * Affiche une diapositive dans le diaporama avec un titre, un contenu et un type
     * spécifié.
     *
     * Cette méthode génère du HTML pour afficher une diapositive, qui peut contenir
     * différents types de contenu tels que du texte, des images, des vidéos ou des
     * fichiers PDF. Elle gère également la distinction entre l'affichage sur le site
     * d'administration et l'affichage normal.
     *
     * @param string   $title     Le titre de la diapositive, affiché en tant
     *                            que en-tête si non vide.
     * @param string   $content   Le contenu à afficher dans la diapositive
     *                            (texte, image ou PDF).
     * @param string   $type      Le type de contenu à afficher ('text',
     *                            'img', 'video', 'short', 'pdf', 'event').
     * @param Scrapper $scrapper  Un objet 'Scrapper' permettant de scraper du
     *                            contenu depuis un site web.
     * @param bool     $adminSite Indique si la diapositive est affichée sur le
     *                            site d'administration.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displaySlide($title, $content, $type, $scrapper,
        $adminSite = false
    ) {
        echo '<div class="myInfoSlides text-center">';

        if ($title != "Sans titre") {
            echo '<h2 class="titleInfo">' . $title . '</h2>';
        }

        $url = $adminSite ? URL_WEBSITE_VIEWER . TV_UPLOAD_PATH : TV_UPLOAD_PATH;

        if (in_array($type, ['pdf', 'event', 'img', 'short', 'video', 'scrapping'])) {
            $extension = explode('.', $content);
            $extension = $extension[1];
        }


        // Utilisation de switch pour afficher les contenus
        switch ($type) {
        case 'pdf':
        case 'event':
            if ($extension == 'pdf') {
                echo '<div class="canvas_pdf" id="' . $content . '"></div>';
            }
            break;

        case 'img':
            echo '<img class="img-thumbnail" src="' . $url . $content . '" alt="' .
                $title . '">';
            break;

        case 'short':
            echo '<video class="short_container" src="' . $url . $content . '" id="'
                .$title . '" autoplay loop muted></video>';
            break;

        case 'text':
            echo '<p class="lead">' . $content . '</p>';
            break;

        case 'scrapper':
            // ✅ EXEMPLE 1 : Scrapping "Informatiquenews"
            $scrapper1 = new Scrapper(
                'https://www.informatiquenews.fr/news', // URL du site à scraper
                "//article[@class='post hnews hentry']",  // Sélecteur pour l'article
                [
                    'title' => "//h2[@class='entry-title']/a",  // Sélecteur pour le titre
                    'content' => "//div[@class='post-content entry-content']",  // Sélecteur pour le contenu
                    'image' => "//header[@class='post-heading']//img",  // Sélecteur pour l'image
                    'link' => "//h2[@class='entry-title']/a",  // Sélecteur pour le lien
                    'author' => "//footer[@class='meta']//em[@class='author vcard']//a",  // Sélecteur pour l'auteur
                ]
            );

            $scrapper2 = new Scrapper(
                'https://www.lemonde.fr', // URL du site à scraper
                "//div[@class='article article--featured']",  //
                // Sélecteur pour l'article
                [
                    'title' => "//p[@class='article__title article__title--inline']",  // Sélecteur pour
                    'content' => "//div[@class='article__wrapper']//p[@class='article__desc']",  // Sélecteur
                    'image' => "//picture[@class='article__media']//img",
                    'link' => "//h2[@class='entry-title']/a",  // Sélecteur pour le lien
                    'author' => "//footer[@class='meta']//em[@class='author vcard']//a",  // Sélecteur pour l'auteur
                    'duree' => "//p[@class='article__footer-info']"
                ]
            );

            $scrapper3 = new Scrapper(
                'https://www.lefigaro.fr/', // URL du site à scraper
                "//article[@class='fig-ensemble__first-article']",  //
                // Sélecteur pour l'article
                [

                    'title' => "//h2[@class='fig-ensemble__title']",  // Titre
                    'content' => "//p[@class='fig-ensemble__standfirst fig-ensemble__standfirst--photo']",  // Description
                    'image' => "//figure[contains(@class, 'fig-ensemble__media')]//img/@srcset",
                    'link' => "//a[@class='fig-ensemble__first-article-link']/@href"


                ]
            );

            $scrapper4 = new Scrapper(
                'https://boutique.ed-diamond.com/3_gnu-linux-magazine', // URL du site à scraper
                "//li[contains(@class, 'ajax_block_product mb-4 col-6 col-lg-3')]",  //
                // Sélecteur pour l'article
                [

                    'image' => "//picture[@class='']//img[@class='img-fluid']",


                ]
            );
            $scrapper2->printWebsite();

            break;

        case 'special':
            $func = explode('(Do this(function:', $content);
            $text = explode('.', $func[0]);
            foreach ($text as $value) {
                echo '<p class="lead">' . $value . '</p>';
            }
            $func = explode(')end)', $func[1]);
            echo $func[0]();
            break;

        default:
            echo $content;
            break;
        }

        echo '</div>';
    }


    /**
     * Affiche le contexte et les instructions pour visualiser et gérer toutes les
     * informations créées sur le site.
     *
     * Cette méthode génère un bloc HTML qui présente un aperçu des fonctionnalités
     * disponibles pour les utilisateurs concernant la gestion des informations.
     * Elle inclut des détails sur la façon de visualiser, modifier ou supprimer des
     * informations, ainsi qu'un lien pour créer une nouvelle information.
     *
     * @return string Le code HTML généré pour afficher les instructions et le lien
     * vers la page de création d'information.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function contextDisplayAll() : string
    {
        return '
        <div class="row">
            <div class="col-6 mx-auto col-md-6 order-md-2">
                <img src="' . TV_PLUG_PATH . 'public/img/info.png" 
                alt="Logo information" class="img-fluid mb-3 mb-md-0">
            </div>
            <div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
                <p class="lead">Vous pouvez retrouver ici toutes les informations qui
                 ont été créées sur ce site.</p>
                <p class="lead">Les informations sont triées de la plus vieille à la 
                plus récente.</p>
                <p class="lead">Vous pouvez modifier une information en cliquant sur 
                "Modifier" à la ligne correspondante à l\'information.</p>
                <p class="lead">Vous souhaitez supprimer une / plusieurs 
                information(s) ? Cochez les cases des informations puis cliquez sur 
                "Supprimer" le bouton ce situe en bas du tableau.</p>
                <p class="lead">Il faut également penser à créer un département avant
                 afin d\'associer cette information à ce département.</p>
            </div>
        </div>
        <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Créer une information')
                )
            ) . '">Créer une information</a>
        <hr class="half-rule">';
    }

    /**
     * Affiche un message lorsque l'information demandée n'est pas trouvée.
     *
     * Cette méthode génère un bloc HTML qui informe l'utilisateur qu'aucune
     * information n'a été trouvée.
     * Elle inclut également un lien pour retourner à la gestion des informations et
     * un autre lien pour créer une nouvelle information.
     *
     * @return string Le code HTML généré pour afficher le message d'absence
     * d'information et les liens associés.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function noInformation() : string
    {
        return '
        <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Gestion des informations')
                )
            ) . '">< Retour</a>
        <div>
            <h3>Information non trouvée</h3>
            <p>Cette information n\'existe pas, veuillez bien vérifier d\'avoir bien 
            cliqué sur une information.</p>
            <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Créer une information')
                )
            ) . '">Créer une information</a>
        </div>';
    }

    /**
     * Démarre le diaporama
     *
     * @return void
     */
    public function displayStartSlideEvent()
    {
        echo '
            <div id="slideshow-container" class="slideshow-container">';
    }

    /**
     * Lance une diapositive
     *
     * @return void
     */
    public function displaySlideBegin()
    {
        echo '
            <div class="mySlides event-slide">';
    }

    /**
     * Affiche un modal de confirmation après l'ajout d'une nouvelle information.
     *
     * Cette méthode génère un modal indiquant que l'information a été ajoutée avec
     * succès.
     * Elle crée également un lien vers la page de gestion des informations pour
     * permettre à l'utilisateur d'y accéder facilement après l'ajout.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayCreateValidate()
    {
        $page = get_page_by_title_custom('Gestion des informations');
        $linkManageInfo = get_permalink($page->ID);
        $this->buildModal(
            'Ajout d\'information validé', '<p 
class="alert alert-success"> L\'information a été ajoutée. </p>', $linkManageInfo
        );
    }

    /**
     * Affiche un modal de confirmation après la modification d'une information.
     *
     * Cette méthode génère un modal indiquant que l'information a été modifiée
     * avec succès.
     * Elle crée également un lien vers la page de gestion des informations pour
     * permettre à l'utilisateur d'y accéder facilement après la modification.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displayModifyValidate()
    {
        $page = get_page_by_title_custom('Gestion des informations');
        $linkManageInfo = get_permalink($page->ID);
        $this->buildModal(
            'Modification d\'information validée', '<p 
class="alert alert-success"> L\'information a été modifiée. </p>', $linkManageInfo
        );
    }

    /**
     * Affiche un message s'il y a une erreur lors de l'insertion.
     *
     * @return void
     */
    public function displayErrorInsertionInfo()
    {
        echo '<p>Il y a eu une erreur durant l\'insertion de l\'information.</p>';
    }

    /**
     * Affiche un message indiquant que l'utilisateur ne peut pas modifier une
     * alerte.
     *
     * Cette méthode génère une réponse HTML qui informe l'utilisateur qu'il ne peut
     * pas modifier une information, car celle-ci appartient à un autre utilisateur.
     * Un lien de retour vers la page de gestion des informations et un lien pour
     * créer une nouvelle information sont également fournis.
     *
     * @return string Retourne le code HTML à afficher pour l'information non
     * modifiable.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function informationNotAllowed() : string
    {
        return '
        <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Gestion des informations')
                )
            ) . '">< Retour</a>
        <div>
            <h3>Vous ne pouvez pas modifier cette alerte.</h3>
            <p>Cette information appartient à quelqu\'un d\'autre, vous ne pouvez 
            donc pas modifier cette information.</p>
            <a href="'
            . esc_url(
                get_permalink(
                    get_page_by_title_custom('Créer une information')
                )
            ) . '">Créer une information</a>
        </div>';
    }
}
