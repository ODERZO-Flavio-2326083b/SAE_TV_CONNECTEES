<?php

namespace Models;

use Models\Model;

class CSSCustomizer extends Model
{
    public function updateColor()
    {

        $cssfilename = 'global-info.css';
        // Chemin du fichier CSS à modifier
        $cssFile =WP_CONTENT_DIR.'/themes/theme-ecran-connecte/assets/css/global/'.$cssfilename;

        // Vérifier si le formulaire a été soumis
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Récupérer les valeurs soumises par le formulaire
            $backgroundColor1 = $_POST['background1'];
            $backgroundColor2 = $_POST['background2'];
            $layout = $_POST['layout'];
            $title = $_POST['title'];
            $link = $_POST['link'];
            $buttonBorder = $_POST['buttonBorder'];
            $button = $_POST['button'];
            $sideBar = $_POST['sideBar'];
            $backgroundLogin = $_POST['backgroundLogin'];
            $formBackground1 = $_POST['formBackground1'];
            $formBackground2 = $_POST['formBackground2'];
            $inputBackground1 = $_POST['inputBackground1'];
            $inputBackground2 = $_POST['inputBackground2'];
            $messageLoginBackground = $_POST['messageLoginBackground'];
            $passwordFormLoginbackground = $_POST['passwordFormLoginbackground'];
            $formLoginBorder = $_POST['formLoginBorder'];



            // Générer le contenu du nouveau CSS
            $newCss = "
body, .container {
    --primary-background-color: $backgroundColor1;
    --secondary-background-color:  $backgroundColor2;
}

.footer_ecran, .nav_ecran  {
    --primary-layout-background-color:  $layout;
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
            echo "File path: " . $cssFile . "<br>";
            if (file_put_contents($cssFile, $newCss)) {
                echo "salut <br>";
                echo $newCss;
            } else {
                echo "Erreur lors de la modification du fichier CSS.";
            }
}


    }


}