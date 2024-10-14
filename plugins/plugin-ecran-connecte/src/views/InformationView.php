<?php

namespace Views;

use Controllers\InformationController;
use Models\Information;

/**
 * Class InformationView
 *
 * Représente la vue pour les informations (formulaires, tableaux, messages).
 *
 * @package Views
 */
class InformationView extends View
{
    /**
     * Affiche un formulaire pour créer une information avec du texte.
     *
     * @param string|null $title     Le titre de l'information (optionnel).
     * @param string|null $content   Le contenu de l'information (requis).
     * @param string|null $endDate   La date d'expiration de l'information (requis).
     * @param string $type           Le type d'opération (par défaut "createText").
     *
     * @return string                Le code HTML du formulaire.
     *
     * @example
     * $view = new InformationView();
     * echo $view->displayFormText("Mon titre", "Mon contenu", "2024-12-31");
     */
    public function displayFormText($title = null, $content = null, $endDate = null, $type = "createText") {
        // Calcule la date minimale pour le champ date
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '
        <form method="post">
            <div class="form-group">
                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                <input id="info" class="form-control" type="text" name="title" minlength="4" maxlength="40" placeholder="Titre..." value="' . htmlspecialchars($title) . '">
            </div>
            <div class="form-group">
                <label for="content">Contenu</label>
                <textarea class="form-control" id="content" name="content" rows="3" placeholder="280 caractères au maximum" maxlength="280" minlength="4" required>' . htmlspecialchars($content) . '</textarea>
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . htmlspecialchars($endDate) . '" required >
            </div>
            <button class="btn button_ecran" type="submit" name="' . htmlspecialchars($type) . '">Valider</button>';

        // Ajoute le bouton de suppression si le type est 'submit'
        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer une information avec une image.
     *
     * @param string|null $title     Le titre de l'information (optionnel).
     * @param string|null $content   Le contenu de l'information (requis).
     * @param string|null $endDate   La date d'expiration de l'information (requis).
     * @param string $type           Le type d'opération (par défaut "createImg").
     *
     * @return string                Le code HTML du formulaire.
     *
     * @example
     * $view = new InformationView();
     * echo $view->displayFormImg("Mon titre", "image.jpg", "2024-12-31");
     */
    public function displayFormImg($title = null, $content = null, $endDate = null, $type = "createImg") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                        <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . htmlspecialchars($title) . '">
                    </div>';
        if ($content != null) {
            $form .= '
                <figure class="text-center">
                  <img class="img-thumbnail" src="' . TV_UPLOAD_PATH . htmlspecialchars($content) . '" alt="' . htmlspecialchars($title) . '">
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
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . htmlspecialchars($endDate) . '" required >
            </div>
            <button class="btn button_ecran" type="submit" name="' . htmlspecialchars($type) . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer une information avec un tableau.
     *
     * @param string|null $title     Le titre de l'information (optionnel).
     * @param string|null $content   Le contenu de l'information (requis).
     * @param string|null $endDate   La date d'expiration de l'information (requis).
     * @param string $type           Le type d'opération (par défaut "createTab").
     *
     * @return string                Le code HTML du formulaire.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     *
     * @example
     * $view = new InformationView();
     * echo $view->displayFormTab("Mon titre", "data.xlsx", "2024-12-31");
     */
    public function displayFormTab($title = null, $content = null, $endDate = null, $type = "createTab") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                        <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . htmlspecialchars($title) . '">
                    </div>';

        // Récupère le contenu du fichier et affiche le tableau s'il existe
        if ($content != null) {
            $info = new InformationController();
            $list = $info->readSpreadSheet(TV_UPLOAD_PATH . htmlspecialchars($content));
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
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . htmlspecialchars($endDate) . '" required >
            </div>
            <button class="btn button_ecran" type="submit" name="' . htmlspecialchars($type) . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Affiche un formulaire pour créer une information avec un fichier PDF.
     *
     * @param string|null $title     Le titre de l'information (optionnel).
     * @param string|null $content   Le contenu de l'information (requis).
     * @param string|null $endDate   La date d'expiration de l'information (requis).
     * @param string $type           Le type d'opération (par défaut "createPdf").
     *
     * @return string                Le code HTML du formulaire.
     *
     * @example
     * $view = new InformationView();
     * echo $view->displayFormPdf("Mon titre", "document.pdf", "2024-12-31");
     */
    public function displayFormPdf($title = null, $content = null, $endDate = null, $type = "createPdf") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                        <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . htmlspecialchars($title) . '">
                    </div>';
        if ($content != null) {
            $form .= '
                <figure class="text-center">
                  <iframe src="' . TV_UPLOAD_PATH . htmlspecialchars($content) . '" width="100%" height="300" frameborder="0"></iframe>
                  <figcaption>Document PDF actuel</figcaption>
                </figure>';
        }
        $form .= '
            <div class="form-group">
                <label for="contentFile">Ajouter un fichier PDF</label>
                <input class="form-control-file" id="contentFile" type="file" name="contentFile" />
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . htmlspecialchars($endDate) . '" required >
            </div>
            <button class="btn button_ecran" type="submit" name="' . htmlspecialchars($type) . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }
}
