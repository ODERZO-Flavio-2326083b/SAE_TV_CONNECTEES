<?php

namespace Models;

use Models\Model;

class CSSCustomizer extends Model
{
    public function updateColor()
    {

        $cssfilename = 'global-info.css';
        // Chemin du fichier CSS à modifier
        $cssFile =WP_CONTENT_DIR.'/themes/theme-ecran-connecte/assets/css/'.$cssfilename;

        // Vérifier si le formulaire a été soumis
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Récupérer les valeurs soumises par le formulaire
            $backgroundColor = $_POST['background'];
            $fontSize = $_POST['taille'];

            // Générer le contenu du nouveau CSS
            $newCss = "
            body {
                --primary-background-color: $backgroundColor;
            }
            #texte {
                --primary-size: $fontSize px;
            }";

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