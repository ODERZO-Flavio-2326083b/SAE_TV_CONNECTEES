<?php
/**
 * Fichier CSSCustomizer.php
 *
 * Ce fichier contient la classe 'CSSCustomizer',
 * qui gère la personnalisation du fichier
 * CSS global en fonction des données soumises par l'utilisateur via un formulaire.
 * Cette classe permet de mettre à jour les couleurs,
 * les mises en page et les autres éléments
 * de style, et d'enregistrer ces modifications dans un fichier CSS spécifique.
 *
 * PHP version 8.3
 *
 * @category Model
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/CSSCustomizer
 * Documentation de la classe
 * @since    2025-01-07
 */

namespace models;

use models\Model;

/**
 * Classe CSSCustomizer
 *
 * Cette classe gère la personnalisation du fichier CSS global en fonction
 * des données soumises par l'utilisateur via un formulaire. Elle permet
 * de mettre à jour les couleurs, les mises en page et les autres éléments
 * de style sur la base des choix de l'utilisateur, et d'enregistrer ces
 * modifications dans un fichier CSS spécifique.
 *
 * @category Model
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/CSSCustomizer Documentation de la classe
 * @since    2025-01-07
 */
class CSSCustomizer extends Model
{
    /**
     * Met à jour le fichier CSS avec les valeurs soumises par le formulaire.
     *
     * Cette méthode récupère les valeurs envoyées par le formulaire via
     * la méthode POST et génère un nouveau contenu CSS en fonction de ces
     * valeurs. Elle écrit ensuite ce nouveau contenu dans le fichier CSS
     * spécifié.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-12-18
     */
    public function updateColor()
    {

        $cssfilename = 'global-info.css';
        // Chemin du fichier CSS à modifier

        // Vérifier si le formulaire a été soumis
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Récupérer les valeurs soumises par le formulaire
            $cssFileName = $_POST['cssFileSelector'];
            $backgroundColor1 = $_POST['background1'];
            $backgroundColor2 = $_POST['background2'];
            $layout = $_POST['layout'];
            $layoutColor = $_POST['layoutColor'];
            $title = $_POST['title'];
            $link = $_POST['link'];
            $buttonBorder = $_POST['buttonBorder'];
            $button = $_POST['button'];
            $sideBar = $_POST['sideBar'];
            $cssFile =WP_CONTENT_DIR
                .'/themes/theme-ecran-connecte/assets/css/global/global-'
                .$cssFileName.'.css';





            // Générer le contenu du nouveau CSS
            $newCss = "
body, .container {
    --primary-background-color: $backgroundColor1;
    --secondary-background-color:  $backgroundColor2;
}

.footer_ecran, .nav_ecran  {
    --primary-layout-background-color:  $layout;
    --primary-layout-color:  $layoutColor;
}

h1, h2, h3 ,h4, h5, h6 {
    --primary-title-color:  $title;
}

a, a:hover, a:link, a:active {
    --primary-link-color:  $link;
}

.button_presentation_ecran {
    --primary-button-border-color:  $buttonBorder;
    --primary-button-color:   $button;
}

.sidebar {
    --primary-sidebar-color:  $sideBar;
}
";

            // Écrire le nouveau contenu dans le fichier CSS
            if (!file_put_contents($cssFile, $newCss)) {
                echo "Erreur lors de la modification du fichier CSS.";
            }
        }


    }


}
