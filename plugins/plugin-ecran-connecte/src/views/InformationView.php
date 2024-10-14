<?php

namespace Views;

use Controllers\InformationController;
use Models\Information;

/**
 * Class InformationView
 *
 * Handles all views related to Information (Forms, tables, messages).
 *
 * @package Views
 */
class InformationView extends View
{
    /**
     * Display a form to create an information with text
     *
     * @param string|null $title    Title of the information (optional)
     * @param string|null $content  Content of the information (optional)
     * @param string|null $endDate  Expiration date for the information (optional)
     * @param string      $type     Form submission type (default is "createText")
     *
     * @return string HTML form for text information creation
     */
    public function displayFormText($title = null, $content = null, $endDate = null, $type = "createText") {
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
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . htmlspecialchars($endDate) . '" required>
            </div>
            <button class="btn button_ecran" type="submit" name="' . htmlspecialchars($type) . '">Valider</button>';

        if ($type === 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Display a form to create an information with an image
     *
     * @param string|null $title    Title of the information (optional)
     * @param string|null $content  Content of the information (optional)
     * @param string|null $endDate  Expiration date for the information (optional)
     * @param string      $type     Form submission type (default is "createImg")
     *
     * @return string HTML form for image information creation
     */
    public function displayFormImg($title = null, $content = null, $endDate = null, $type = "createImg") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . htmlspecialchars($title) . '">
            </div>';

        // Display current image if available
        if ($content !== null) {
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
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . htmlspecialchars($endDate) . '" required>
            </div>
            <button class="btn button_ecran" type="submit" name="' . htmlspecialchars($type) . '">Valider</button>';

        if ($type === 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Display a form to create an information with a table
     *
     * @param string|null $title    Title of the information (optional)
     * @param string|null $content  Content of the information (optional)
     * @param string|null $endDate  Expiration date for the information (optional)
     * @param string      $type     Form submission type (default is "createTab")
     *
     * @return string HTML form for table information creation
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function displayFormTab($title = null, $content = null, $endDate = null, $type = "createTab") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . htmlspecialchars($title) . '">
            </div>';

        // Display table if content is available
        if ($content !== null) {
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
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . htmlspecialchars($endDate) . '" required>
            </div>
            <button class="btn button_ecran" type="submit" name="' . htmlspecialchars($type) . '">Valider</button>';

        if ($type === 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Display a form to create an information with a PDF
     *
     * @param string|null $title    Title of the information (optional)
     * @param string|null $content  Content of the information (optional)
     * @param string|null $endDate  Expiration date for the information (optional)
     * @param string      $type     Form submission type (default is "createPDF")
     *
     * @return string HTML form for PDF information creation
     */
    public function displayFormPDF($title = null, $content = null, $endDate = null, $type = "createPDF") {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . htmlspecialchars($title) . '">
            </div>';

        // Display current PDF if available
        if ($content !== null) {
            $form .= '
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="' . TV_UPLOAD_PATH . htmlspecialchars($content) . '"></iframe>
            </div>';
        }

        $form .= '
            <div class="form-group">
                <label for="contentFile">Ajouter un fichier PDF</label>
                <input class="form-control-file" id="contentFile" type="file" name="contentFile" accept=".pdf"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . htmlspecialchars($endDate) . '" required>
            </div>
            <button class="btn button_ecran" type="submit" name="' . htmlspecialchars($type) . '">Valider</button>';

        if ($type === 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Display a confirmation message to the user.
     *
     * @param string $message Message to display to the user
     *
     * @return string HTML formatted confirmation message
     */
    public function displayConfirmation($message) {
        return '<div class="alert alert-success" role="alert">' . htmlspecialchars($message) . '</div>';
    }

    /**
     * Display an error message to the user.
     *
     * @param string $message Message to display to the user
     *
     * @return string HTML formatted error message
     */
    public function displayError($message) {
        return '<div class="alert alert-danger" role="alert">' . htmlspecialchars($message) . '</div>';
    }

    /**
     * Display all information records in a table format.
     *
     * @param array $list List of information records
     *
     * @return string HTML formatted table of information records
     */
    public function displayInformation(array $list) {
        $table = '<table class="table table-striped table-bordered"><thead><tr>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Date d\'ajout</th>
                    <th>Date d\'expiration</th>
                    <th>Action</th>
                </tr></thead><tbody>';

        foreach ($list as $info) {
            $table .= '<tr>
                <td>' . htmlspecialchars($info['title']) . '</td>
                <td>' . htmlspecialchars($info['type']) . '</td>
                <td>' . htmlspecialchars($info['created_at']) . '</td>
                <td>' . htmlspecialchars($info['expiration_date']) . '</td>
                <td>
                    <a class="btn btn-primary" href="?action=edit&id=' . htmlspecialchars($info['id']) . '">Modifier</a>
                    <a class="btn btn-danger" href="?action=delete&id=' . htmlspecialchars($info['id']) . '" onclick="return confirm(\'Voulez-vous vraiment supprimer cette information ?\');">Supprimer</a>
                </td>
            </tr>';
        }

        return $table . '</tbody></table>';
    }

    /**
     * Display the main view with information and corresponding forms.
     *
     * @param string|null $message Optional message to display (for confirmation or error)
     * @param array|null  $informationList List of information records to display in the table
     * @param array|null  $formData Optional form data for pre-filling form fields
     *
     * @return string Complete HTML view of the information management page
     */
    public function displayView($message = null, array $informationList = null, array $formData = null) {
        $output = '<h1>Gestion des Informations</h1>';

        if ($message !== null) {
            $output .= $this->displayConfirmation($message);
        }

        $output .= '<h2>Ajouter une Information</h2>';

        if (isset($formData['type'])) {
            switch ($formData['type']) {
                case 'text':
                    $output .= $this->displayFormText($formData['title'], $formData['content'], $formData['expirationDate']);
                    break;
                case 'img':
                    $output .= $this->displayFormImg($formData['title'], $formData['content'], $formData['expirationDate']);
                    break;
                case 'tab':
                    $output .= $this->displayFormTab($formData['title'], $formData['content'], $formData['expirationDate']);
                    break;
                case 'pdf':
                    $output .= $this->displayFormPDF($formData['title'], $formData['content'], $formData['expirationDate']);
                    break;
            }
        }

        $output .= '<h2>Informations Existantes</h2>';
        if ($informationList !== null && !empty($informationList)) {
            $output .= $this->displayInformation($informationList);
        } else {
            $output .= '<p>Aucune information disponible.</p>';
        }

        return $output;
    }
}