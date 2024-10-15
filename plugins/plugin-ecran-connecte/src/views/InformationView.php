<?php

namespace Views;

use Controllers\InformationController;
use Models\Information;

/**
 * Class InformationView
 *
 * Gère toutes les vues liées aux informations (Formulaires, tableaux, messages).
 *
 * @package Views
 */
class InformationView extends View
{
    /**
     * Affiche un formulaire pour créer une information de type texte.
     *
     * @param string $title Titre de l'information (optionnel).
     * @param string $content Contenu de l'information.
     * @param string $endDate Date d'expiration de l'information.
     * @param string $type Type de soumission, par défaut "createText".
     *
     * @return string Le code HTML du formulaire.
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
     * Affiche un formulaire pour créer une information avec une image.
     *
     * @param string $title Titre de l'information (optionnel).
     * @param string $content Chemin de l'image existante.
     * @param string $endDate Date d'expiration de l'information.
     * @param string $type Type de soumission, par défaut "createImg".
     *
     * @return string Le code HTML du formulaire.
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
     * Affiche un formulaire pour créer une information avec un tableau Excel.
     *
     * @param string $title Titre de l'information (optionnel).
     * @param string $content Contenu du tableau (chemin du fichier).
     * @param string $endDate Date d'expiration de l'information.
     * @param string $type Type de soumission, par défaut "createTab".
     *
     * @return string Le code HTML du formulaire.
     * @throws \PhpOffice\PhpSpreadsheet\Exception En cas d'erreur lors de la lecture du fichier.
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception En cas d'erreur du lecteur de fichier.
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
     * Affiche un formulaire pour créer une information avec un PDF.
     *
     * @param string $title Titre de l'information (optionnel).
     * @param string $content Chemin du PDF existant.
     * @param string $endDate Date d'expiration de l'information.
     * @param string $type Type de soumission, par défaut "createPDF".
     *
     * @return string Le code HTML du formulaire.
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
}
