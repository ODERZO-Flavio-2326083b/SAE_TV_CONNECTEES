<?php

namespace Models;

use Models\Model;

class CSSCustomizer extends Model
{
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
            $cssFile =WP_CONTENT_DIR.'/themes/theme-ecran-connecte/assets/css/global/global-'.$cssFileName.'.css';





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
            if (file_put_contents($cssFile, $newCss)) {
              //  echo $newCss;
            } else {
               // echo "Erreur lors de la modification du fichier CSS.";
            }
}


    }


}