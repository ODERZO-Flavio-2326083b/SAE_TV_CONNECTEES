<?php
/**
 * Fichier CSSView.php
 *
 * Ce fichier contient la classe 'CSSView',
 * qui est responsable de l'affichage des vues liées
 * à la personnalisation du CSS dans l'application.
 * Cette classe génère des formulaires permettant
 * aux utilisateurs de modifier les couleurs des éléments
 * de la page web tels que l'arrière-plan,
 * la mise en page, les titres, les liens, les boutons,
 * etc. Elle permet également l'affichage dynamique
 * des options disponibles pour la personnalisation du CSS.
 *
 * PHP version 8.3
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/CSSView
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace views;

use models\Department;
use views\View;

/**
 * Classe CSSView
 *
 * Cette classe gère l'affichage
 * des vues liées à la personnalisation du CSS. Elle inclut
 * des formulaires pour modifier les couleurs
 * des éléments sur la page web (comme les titres,
 * les arrière-plans, les liens, les boutons, etc.)
 * et génère le HTML nécessaire pour ces
 * personnalisations. Elle permet également de gérer
 * l'affichage dynamique des options disponibles
 * pour la personnalisation du CSS.
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/CSSView Documentation de la classe
 * @since    2025-01-07
 */
class CSSView extends View
{


    /**
     * Affiche le contexte de personnalisation du CSS.
     *
     * Cette méthode génère une section explicative permettant à l'utilisateur de
     * comprendre les possibilités de personnalisation des télévisions par
     * département. Elle décrit les étapes nécessaires, notamment la création de
     * départements, la sélection d'un département à personnaliser, et la
     * visualisation des modifications en se connectant à la télévision
     * correspondante.
     *
     * Le contenu affiché sert d'introduction et d'explication pour guider les
     * utilisateurs dans l'utilisation de la fonctionnalité de personnalisation.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayContextCSS()
    {
        echo "
        <h2>Personnalisation</h2>
        <div class='row'>
                <p class='lead'>Sur cette page, il est possible de personnaliser la 
                télévision d'un département au choix.</p>
                <p class='lead'>Après avoir créé les départements, vous pouvez en 
                sélectionner un afin de modifier les couleurs des télévisions de ce 
                département.</p>
               <p class='lead'>Une fois votre choix validé, connectez-vous à la 
               télévision du département sélectionné afin de voir les changements.
               </p>
            </div>
        <hr class='half-rule'>";
    }



    /**
     * Affiche le formulaire de personnalisation du CSS.
     *
     * Cette méthode génère un formulaire permettant à l'utilisateur de personnaliser
     * les couleurs des différents éléments d'une page web, tels que l'arrière-plan,
     * la mise en page, les titres, les liens, les boutons, et la barre latérale.
     * Le formulaire permet également de sélectionner un fichier CSS à modifier.
     *
     * @param array $listDepName Liste des noms des départements, utilisés pour
     *                           remplir une liste déroulante dans le formulaire.
     *
     * @return void
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayCssCustomizer($listDepName)
    {
        echo "<form method=\"POST\">


    <div class=\"form-group\">
        <label for=\"cssFileSelector\">Choisir le CSS à modifier : </label>
        <select id=\"cssFileSelector\" name=\"cssFileSelector\">
            <option value=\"default\">default</option>";
            $this->fomulaireDep($listDepName);
        echo "
        </select>
        
        
        
    </div>
    <div class=\"form-group\">
        <label for=\"background1\">Couleur de l'arrière-plan 1 :</label>
        <input class=\"form-control\" type=\"color\" id=\"background1\" 
        name=\"background1\" value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"background2\">Couleur de l'arrière-plan 2 :</label>
        <input class=\"form-control\" type=\"color\" id=\"background2\" 
        name=\"background2\" value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"layout\">Couleur de la mise en page :</label>
        <input class=\"form-control\" type=\"color\" id=\"layout\" name=\"layout\" 
        value=\"\">
    </div>
        <div class=\"form-group\">
        <label for=\"layout\">Couleur de l'écriture :</label>
        <input class=\"form-control\" type=\"color\" id=\"layoutColor\" 
        name=\"layoutColor\" value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"title\">Couleur du titre :</label>
        <input class=\"form-control\" type=\"color\" id=\"title\" name=\"title\" 
        value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"link\">Couleur des liens :</label>
        <input class=\"form-control\" type=\"color\" id=\"link\" name=\"link\" 
        value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"buttonBorder\">Couleur de la bordure du bouton :</label>
        <input class=\"form-control\" type=\"color\" id=\"buttonBorder\" 
        name=\"buttonBorder\" value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"button\">Couleur du bouton :</label>
        <input class=\"form-control\" type=\"color\" id=\"button\" name=\"button\" 
        value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"sideBar\">Couleur de la barre latérale :</label>
        <input class=\"form-control\" type=\"color\" id=\"sideBar\" name=\"sideBar\" 
        value=\"\">
    </div>
    <button type=\"submit\" class=\"btn btn-primary\">Envoyer</button>
</form>

";
    }

    /**
     * Affiche les options de départements dans un formulaire.
     *
     * Cette méthode génère dynamiquement les options d'un menu déroulant (select)
     * pour chaque département contenu dans la liste '$listDepName'. Chaque
     * département est affiché comme une option dans le formulaire, permettant à
     * l'utilisateur de sélectionner un département parmi les choix proposés.
     *
     * @param array $listDepName Liste des départements à afficher dans le
     *                           formulaire.
     *
     * @return void
     * La méthode ne retourne aucune valeur. Elle génère directement du code HTML
     * pour l'affichage des options.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function fomulaireDep($listDepName)
    {
        foreach ($listDepName as $dep) {
            echo "<option value=\"$dep\">$dep</option>";
        }
    }
}
