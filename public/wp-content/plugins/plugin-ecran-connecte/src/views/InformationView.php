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
use models\CodeAde;
use models\Department;
use models\Information;
use models\Scraper;

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
     * Affiche un formulaire permettant de créer
     * ou modifier un texte associé à un emploi du temps.
     *
     * Ce formulaire permet de spécifier
     * un titre (optionnel), un contenu (obligatoire),
     * une date d'expiration, et de lier le texte à un emploi du temps spécifique.
     * Le formulaire génère du HTML incluant
     * les champs nécessaires pour la saisie du texte,
     * et permet d'ajouter ou de supprimer des emplois du temps associés au texte.
     *
     * @param array  $allDepts  Liste des départements
     *                          disponibles.
     * @param array  $buildArgs Liste des arguments
     *                          de construction,
     *                          contenant les années, groupes et demi-groupes.
     * @param string $title     Titre du texte (optionnel).
     * @param string $content   Contenu du texte.
     * @param string $endDate   Date d'expiration du texte.
     * @param string $type      Type de l'action,
     *                          par défaut "createText" (peut être "submit" pour une
     *                          modification).
     *
     * @return string Le code HTML généré pour le formulaire.
     */
    public function displayFormText(array $allDepts, array $buildArgs, $title = null,
        $content = null, $endDate = null, $type = "createText"
    ) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));


        list($years, $groups, $halfGroups) = $buildArgs;
        $codeSelect = $this->buildSelectCode(
            $years,
            $groups, $halfGroups, $allDepts
        );

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
                <label>Emploi(s) du temps</label>
                <br>    
                <div id="codeContainertexte">' . $codeSelect . '</div>
                <input type="button" class="btn button_ecran"
                 onclick="codeAddRow(\'texte\')" 
            value="Ajouter un emploi du temps">
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
     * Affiche un formulaire permettant de créer
     * ou modifier une image associée à un emploi du temps.
     *
     * Ce formulaire permet de spécifier
     * un titre (optionnel), d'ajouter une nouvelle image,
     * d'afficher une image existante si elle est
     * fournie, et de lier l'image à un emploi du temps spécifique.
     * Le formulaire inclut des champs pour la
     * saisie du titre, le téléchargement de l'image,
     * et la sélection de la date d'expiration.
     * Il permet également d'ajouter des emplois du temps à l'image.
     *
     * @param array  $allDepts  Liste des départements
     *                          disponibles.
     * @param array  $buildArgs Liste des arguments de construction,
     *                          contenant les années, groupes et demi-groupes.
     * @param string $title     Titre de l'image (optionnel).
     * @param string $content   Contenu de l'image (nom du fichier de l'image).
     * @param string $endDate   Date d'expiration de l'image.
     * @param string $type      Type de l'action, par défaut
     *                          "createImg" (peut être "submit" pour
     *                          une modification).
     *
     * @return string Le code HTML généré pour le formulaire.
     */
    public function displayFormImage(array $allDepts,
        array $buildArgs, $title = null,
        $content = null, $endDate = null, $type = "createImg"
    ) {
        $dateMin = date('Y-m-d', strtotime("+1 day"));


        list($years, $groups, $halfGroups) = $buildArgs;
        $codeSelect = $this->buildSelectCode(
            $years, $groups,
            $halfGroups, $allDepts
        );

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
                <label>Emploi(s) du temps</label>
                <br>    
                <div id="codeContainerimage">' . $codeSelect . '</div>
                <input type="button" class="btn button_ecran"
                 onclick="codeAddRow(\'image\')" 
            value="Ajouter un emploi du temps">
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
     * Affiche un formulaire permettant de créer ou modifier
     * une vidéo associée à un emploi du temps.
     *
     * Ce formulaire permet à l'utilisateur de spécifier
     * un titre (optionnel), d'ajouter une nouvelle vidéo,
     * d'afficher une vidéo existante si elle est fournie,
     * et de lier la vidéo à un emploi du temps spécifique.
     * Le formulaire comprend un champ pour le titre,
     * un champ pour télécharger une vidéo, un champ pour définir la
     * date d'expiration de la vidéo, ainsi qu'une option
     * pour lier cette vidéo à un emploi du temps.
     * Il inclut également un bouton de soumission pour
     * valider les informations et un bouton de suppression pour
     * supprimer une vidéo existante.
     *
     * @param array  $allDepts  Liste des départements
     *                          disponibles.
     * @param array  $buildArgs Liste des arguments nécessaires
     *                          pour construire les
     *                          options des années, groupes, et demi-groupes.
     * @param string $title     Titre de la vidéo
     *                          (optionnel).
     * @param string $content   Contenu de la vidéo (nom du fichier vidéo existant,
     *                          facultatif).
     * @param string $endDate   Date d'expiration de la
     *                          vidéo.
     * @param string $type      Type de l'action, par défaut
     *                          "createVideo". Si "submit",
     *                          permet de modifier la vidéo
     *                          existante.
     *
     * @return string Le code HTML généré pour le formulaire.
     */
    public function displayFormVideo(array $allDepts,
        array $buildArgs, $title = null,
        $content = null, $endDate = null, $type = "createVideo"
    ) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));


        list($years, $groups, $halfGroups) = $buildArgs;
        $codeSelect = $this->buildSelectCode(
            $years,
            $groups, $halfGroups, $allDepts
        );

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
                <label>Emploi(s) du temps</label>
                <br>    
                <div id="codeContainervideo">' . $codeSelect . '</div>
                <input type="button" class="btn button_ecran"
                 onclick="codeAddRow(\'video\')" 
            value="Ajouter un emploi du temps">
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
     * Affiche un formulaire
     * permettant de créer ou modifier une vidéo courte (short).
     *
     * Ce formulaire permet à l'utilisateur de spécifier
     * un titre (optionnel), de télécharger une vidéo courte au format vertical,
     * d'afficher une vidéo actuelle si elle est fournie,
     * et de lier cette vidéo à un emploi du temps spécifique.
     * Le formulaire comprend un champ pour le titre, un champ
     * pour télécharger une vidéo courte, un champ pour définir la
     * date d'expiration de la vidéo, ainsi qu'une option pour
     * lier cette vidéo à un emploi du temps.
     * Il inclut également un bouton de soumission pour valider
     * les informations et un bouton de suppression pour
     * supprimer une vidéo existante.
     *
     * @param array  $allDepts  Liste des départements
     *                          disponibles.
     * @param array  $buildArgs Liste des arguments nécessaires
     *                          pour construire les options des années,
     *                          groupes, et demi-groupes.
     * @param string $title     Titre de la vidéo
     *                          (optionnel).
     * @param string $content   Contenu de la vidéo (nom du
     *                          fichier vidéo existant,
     *                          facultatif).
     * @param string $endDate   Date d'expiration de la
     *                          vidéo.
     * @param string $type      Type de l'action, par défaut
     *                          "createShort". Si "submit", permet de
     *                          modifier la vidéo existante.
     *
     * @return string Le code HTML généré pour le formulaire.
     */
    public function displayFormShort(array $allDepts,
        array $buildArgs, $title = null,
        $content = null, $endDate = null, $type = "createShort"
    ) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        list($years, $groups, $halfGroups) = $buildArgs;
        $codeSelect = $this->buildSelectCode(
            $years, $groups,
            $halfGroups, $allDepts
        );

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
                <label>Emploi(s) du temps</label>
                <br>    
                <div id="codeContainershort">' . $codeSelect . '</div>
                <input type="button" class="btn button_ecran" 
                onclick="codeAddRow(\'short\')" 
            value="Ajouter un emploi du temps">
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
     * Affiche un formulaire permettant de créer ou modifier un fichier PDF.
     *
     * Ce formulaire permet à l'utilisateur de spécifier un titre (optionnel),
     * de télécharger un fichier PDF,
     * d'afficher un fichier PDF actuel si déjà téléchargé, et de lier ce fichier
     * à un emploi du temps spécifique.
     * Le formulaire comprend un champ pour le titre, un champ pour télécharger un
     * fichier PDF, un champ pour définir la
     * date d'expiration du PDF, ainsi qu'une option pour lier ce
     * fichier à un emploi du temps.
     * Il inclut également un bouton de soumission pour valider les
     * informations et un bouton de suppression pour
     * supprimer le fichier PDF existant.
     *
     * @param array  $allDepts  Liste des départements
     *                          disponibles.
     * @param array  $buildArgs Liste des arguments nécessaires pour
     *                          construire les options des années,
     *                          groupes, et demi-groupes.
     * @param string $title     Titre du fichier PDF (optionnel).
     * @param string $content   Contenu du fichier PDF (nom du fichier
     *                          PDF existant, facultatif).
     * @param string $endDate   Date d'expiration du fichier PDF.
     * @param string $type      Type de l'action, par défaut
     *                          "createPDF". Si "submit", permet de
     *                          modifier le fichier PDF existant.
     *
     * @return string Le code HTML généré pour le formulaire.
     */
    public function displayFormPDF(array $allDepts, array $buildArgs, $title = null,
        $content = null, $endDate = null, $type = "createPDF"
    ) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));


        list($years, $groups, $halfGroups) = $buildArgs;
        $codeSelect = $this->buildSelectCode(
            $years, $groups,
            $halfGroups, $allDepts
        );

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
                <label>Emploi(s) du temps</label>
                <br>    
                <div id="codeContainerpdf">' . $codeSelect . '</div>
                <input type="button" class="btn button_ecran"
                 onclick="codeAddRow(\'pdf\')" 
            value="Ajouter un emploi du temps">
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
     * Affiche un formulaire pour créer
     * ou modifier un événement avec des fichiers joints.
     *
     * Ce formulaire permet à l'utilisateur de
     * télécharger plusieurs fichiers (images ou PDF),
     * de spécifier une date d'expiration pour l'événement,
     * et d'associer cet événement à un emploi du temps.
     * L'utilisateur peut aussi ajouter un ou plusieurs emplois
     * du temps en utilisant un bouton pour ajouter des lignes
     * de sélection d'emplois du temps.
     *
     * @param array  $allDepts  Liste des départements
     *                          disponibles.
     * @param array  $buildArgs Liste des arguments nécessaires pour construire les
     *                          options des années, groupes, et demi-groupes.
     * @param string $endDate   Date d'expiration de
     *                          l'événement.
     * @param string $type      Type de l'action, par défaut "createEvent". Si
     *                          "submit", permet de modifier l'événement existant.
     *
     * @return string Le code HTML généré pour le formulaire.
     */
    public function displayFormEvent( array $allDepts, array $buildArgs,
        $endDate = null, $type = "createEvent"
    ) : string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        list($years, $groups, $halfGroups) = $buildArgs;
        $codeSelect = $this->buildSelectCode(
            $years, $groups, $halfGroups,
            $allDepts
        );

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
                <label>Emploi(s) du temps</label>
                <br>    
                <div id="codeContainerevent">' . $codeSelect . '</div>
                <input type="button" class="btn button_ecran" 
                onclick="codeAddRow(\'event\')" 
            value="Ajouter un emploi du temps">
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

    /**
     * Affiche un formulaire pour créer ou modifier un scraping.
     *
     * Ce formulaire permet à l'utilisateur de spécifier un titre,
     * un lien URL, de choisir des tags associés,
     * de spécifier une date d'expiration pour le scraping,
     * et de lier ce scraping à un emploi du temps.
     * L'utilisateur peut aussi ajouter un ou plusieurs tags ainsi que des emplois
     * du temps en utilisant des boutons pour ajouter des lignes
     * de tags ou d'emplois du temps. Le formulaire inclut également un champ pour
     * uploader des fichiers et une option de
     * suppression si l'utilisateur est en mode "submit".
     *
     * @param array  $allDepts  Liste des départements
     *                          disponibles.
     * @param array  $buildArgs Liste des arguments nécessaires pour construire les
     *                          options des années, groupes, et demi-groupes.
     * @param string $endDate   Date d'expiration du scraping.
     * @param string $title     Titre du scraping.
     * @param string $url       URL du site pour le scraping.
     * @param string $type      Type de l'action, par défaut
     *                          "createScraping". Si "submit", permet de
     *                          modifier le scraping existant.
     *
     * @return string Le code HTML généré pour le formulaire.
     */
    public function displayFormScraping(array $allDepts, array $buildArgs,
        $endDate = null, $title = null, $url = null,
        $type = "createScraping"
    ) {

        list($years, $groups, $halfGroups) = $buildArgs;
        $codeSelect = $this->buildSelectCode(
            $years, $groups, $halfGroups,
            $allDepts
        );

        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $form = '
        <form method="post" enctype="multipart/form-data" id="registerScrapingForm">
            <div class="form-group">
                <label for="title">Titre du scraping</label>
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
                <label for="tag" id="tagDiv">Tag</label> ' .
            $this->buildTagOption() . '
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
                <label>Emploi(s) du temps</label>
                <br>    
                <div id="codeContainerscraping">' . $codeSelect . '</div>
                <input type="button" class="btn button_ecran"
                 onclick="codeAddRow(\'scraping\')" 
            value="Ajouter un emploi du temps">
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

    /**
     * Génère un bloc HTML pour un champ de saisie de tags avec un
     * sélecteur pour spécifier leur type.
     * Ce bloc contient un champ de texte pour insérer un tag et une
     * liste déroulante pour sélectionner
     * le type de tag associé au scraping.
     *
     * Le champ de texte permet de saisir un tag, tandis que la liste
     * déroulante propose plusieurs types
     * de tags possibles : 'default', 'image', 'lien', 'url', et 'article'.
     *
     * Cette méthode est utilisée pour ajouter des tags à un formulaire
     * de scraping où l'utilisateur
     * peut associer des informations spécifiques (par exemple, des liens,
     * des images, des articles) aux données
     * récupérées par un scraping.
     *
     * @return string Le bloc HTML contenant un champ de texte et un
     * liste déroulante pour les tags.
     */
    public static function buildTagOption()
    {
        return '  <div>
                       <input id="content" class="form-control" type="text" 
                       name="contentScraper[]" placeholder="Inserer le tag"
                        maxlength="255" required>
                       <select class="form-control firstSelect" id="tag"
                        name="tag[]" required="">
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
     * Affiche un formulaire de modification pour différents types
     * de contenu (texte, image, vidéo, etc.).
     *
     * Cette méthode génère un formulaire permettant à l'utilisateur de
     * modifier des informations selon le type spécifié.
     * Elle gère différents types de contenu tels que du texte, une image,
     * une vidéo, un short, un fichier PDF, du scraping, et des événements.
     * Selon le type de contenu, un formulaire approprié sera affiché pour
     * permettre la modification.
     *
     * Le lien "Retour" est également généré pour rediriger l'utilisateur vers
     * la page de gestion des informations.
     *
     * @param string $title     Le titre de l'information à
     *                          modifier.
     * @param string $content   Le contenu à modifier (peut être un lien
     *                          vers un fichier, du texte, etc.).
     * @param string $endDate   La date d'expiration de l'information.
     * @param string $type      Le type de contenu à modifier (texte,
     *                          image, vidéo, etc.).
     * @param array  $allDepts  Liste des
     *                          départements
     *                          disponibles.
     * @param array  $buildArgs Les arguments nécessaires pour
     *                          construire les champs du formulaire.
     *
     * @return string Le code HTML pour afficher le formulaire de
     * modification approprié.
     */
    public function displayModifyInformationForm( string $title, string $content,
        string $endDate, string $type,
        array $allDepts, array $buildArgs,
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
                        $allDepts, $buildArgs, $title, $content,
                        $endDate, 'submit'
                    );
        case "img":
            return '<a href="'
                . esc_url(
                    get_permalink(
                        get_page_by_title_custom('Gestion des informations')
                    )
                ) . '">< Retour</a>' .
                    $this->displayFormImage(
                        $allDepts, $buildArgs, $title, $content, $endDate,
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
                        $allDepts, $buildArgs, $title,
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
                        $allDepts, $buildArgs, $title,
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
                        $allDepts, $buildArgs, $title,
                        $content, $endDate, 'submit'
                    );
        case "scraping":
            return '<a href="'
                . esc_url(
                    get_permalink(
                        get_page_by_title_custom('Gestion des informations')
                    )
                ) . '">< Retour</a>' .
                $this->displayFormScraping(
                    $allDepts, $buildArgs, $title, $endDate, 'submit'
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
                        $allDepts, $buildArgs,
                        $title, $content, $endDate, 'submit'
                    );
            } else {
                return '<a href="'
                    . esc_url(
                        get_permalink(
                            get_page_by_title_custom('Gestion des informations')
                        )
                    ) . '">< Retour</a>' . $this->displayFormImage(
                        $allDepts, $buildArgs,
                        $title, $content, $endDate, 'submit'
                    );
            }
        default:
            return $this->noInformation();
        }
    }

    /**
     * Génère un élément '<select>' HTML pour sélectionner des emplois du temps.
     *
     * Cette méthode crée un menu déroulant contenant des options pour les années,
     * groupes et demi-groupes. Si un code d'emploi du temps est fourni, il sera
     * pré-sélectionné dans le menu déroulant.
     *
     * @param array<CodeAde> $years      Un tableau d'objets représentant les années
     *                                   disponibles.
     * @param array<CodeAde> $groups     Un tableau d'objets représentant les groupes
     *                                   disponibles.
     * @param array<CodeAde> $halfGroups Un tableau d'objets représentant les
     *                                   demi-groupes disponibles.
     * @param array          $allDepts   Une liste de tous les départements
     *                                   présents dans la base de données.
     * @param CodeAde|null   $code       Un objet représentant le code d'emploi du
     *                                   temps à pré-sélectionner (facultatif).
     *
     * @return string Le code HTML du menu déroulant pour sélectionner un emploi du
     *                temps.
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public static function buildSelectCode(array $years, array $groups,
        array $halfGroups, array $allDepts,
        CodeAde $code = null
    ): string {
        $select
            = '<select class="form-control departmentSelect"
 name="informationCodes[]">';

        if (!is_null($code)) {
            $select .= '<option value="' . $code->getCode() . '">'
                       . $code->getTitle() . '</option>';
        } else {
            $select .= '<option disabled selected value>Sélectionnez un code ADE
</option>';
        }

        $allOptions = [];

        foreach ($years as $year) {
            $allOptions[$year->getDeptId()][] = [
                'code' => $year->getCode(),
                'title' => $year->getTitle(),
                'type' => 'Année'
            ];
        }

        foreach ($groups as $group) {
            $allOptions[$group->getDeptId()][] = [
                'code' => $group->getCode(),
                'title' => $group->getTitle(),
                'type' => 'Groupe'
            ];
        }

        foreach ($halfGroups as $halfGroup) {
            $allOptions[$halfGroup->getDeptId()][] = [
                'code' => $halfGroup->getCode(),
                'title' => $halfGroup->getTitle(),
                'type' => 'Demi groupe'
            ];
        }

        // trier les départements par id
        ksort($allOptions);

        foreach ($allOptions as $deptId => $options) {
            $deptName = 'Département inconnu';
            foreach ($allDepts as $dept) {
                if ($dept->getIdDepartment() === $deptId) {
                    $deptName = $dept->getName();
                    break;
                }
            }
            $select .= '<optgroup label="Département ' . $deptName . '">';

            //trier les options au sein de chaque département par type puis par titre
            usort(
                $options, function ($a, $b) {
                    return [$a['type'], $a['title']] <=> [$b['type'], $b['title']];
                }
            );

            foreach ($options as $option) {
                $select .= '<option value="' . $option['code'] . '">'
                           . $option['type'] . ' - ' . $option['title']
                           . '</option>';
            }

            $select .= '</optgroup>';
        }

        $select .= '</select>';

        return $select;
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
     * Affiche un lecteur vidéo avec un titre et un contenu vidéo dans un diaporama.
     *
     * Cette méthode génère un bloc HTML contenant
     * un lecteur vidéo qui peut être affiché
     * avec ou sans titre en fonction de la variable
     * `$title`. Elle est principalement utilisée
     * pour afficher une vidéo avec des options de lecture automatique,
     * en boucle et en mode muet.
     * Selon que l'utilisateur est un administrateur ou non
     * (déterminé par `$adminSite`),
     * le chemin d'accès à la vidéo peut varier pour pointer vers un
     * site de prévisualisation ou un chemin local.
     *
     * @param string $title     Le titre de la vidéo. Si vide ou égal à "Sans
     *                          titre", aucun titre n'est affiché.
     * @param string $content   Le nom du fichier vidéo à
     *                          afficher.
     * @param bool   $adminSite Indique si l'utilisateur est un administrateur
     *                          (utilisé pour ajuster le chemin d'accès).
     *
     * @return void
     */
    public function displaySlideVideo($title, $content, $adminSite = false)
    {
        echo '<div class="myVideoSlides text-center" style="display: block;">';

        // If the title is empty
        if ($title != "Sans titre") {
            echo '<h2 class="titleInfo">' . $title . '</h2>';
        }

        $url = $adminSite ? URL_WEBSITE_VIEWER . TV_UPLOAD_PATH : TV_UPLOAD_PATH;

        echo '<video class="video_container" src="' . $url . $content . '
              " autoplay loop muted style="max-height: 70vh;"></video>';



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
     * @param string $title     Le titre de la diapositive, affiché en
     *                          tant que en-tête si non vide.
     * @param string $content   Le contenu à afficher dans la
     *                          diapositive (texte, image ou PDF).
     * @param string $type      Le type de contenu à afficher
     *                          ('text', 'img', 'video', 'short',
     *                          'pdf', 'event', 'scraper')
     * @param bool   $adminSite Indique si la diapositive est affichée sur
     *                          le site d'administration.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-15
     */
    public function displaySlide($title, $content, $type,
        $adminSite = false
    ) {
        echo '<div class="myInfoSlides text-center">';

        if ($title != "Sans titre") {
            echo '<h2 class="titleInfo">' . $title . '</h2>';
        }

        $url = $adminSite ? URL_WEBSITE_VIEWER . TV_UPLOAD_PATH : TV_UPLOAD_PATH;

        if (in_array($type, ['pdf', 'event', 'img', 'short', 'video', 'scraping'])) {
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
                "Supprimer" le bouton se situant en bas du tableau.</p>
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
