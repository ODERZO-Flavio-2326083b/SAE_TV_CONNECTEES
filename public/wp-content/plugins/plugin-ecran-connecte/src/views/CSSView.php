<?php

namespace Views;

use Models\Department;
use Views\View;

class CSSView extends View
{
    public function displayCssCustomizer($listDepName)
    {
        echo "<h1>Personnalisation du CSS</h1>";
        echo "<form method=\"POST\">


    <div class=\"form-group\">
        <label for=\"cssFileSelector\">Choisir le css a modifier</label>
        <select id=\"cssFileSelector\" name=\"cssFileSelector\">
            <option value=\"default\">default</option>";
            $this->fomulaireDep($listDepName);
        echo "
        </select>
        
        
        
    </div>
    <div class=\"form-group\">
        <label for=\"background1\">Couleur de l'arrière-plan 1 :</label>
        <input class=\"form-control\" type=\"color\" id=\"background1\" name=\"background1\" value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"background2\">Couleur de l'arrière-plan 2 :</label>
        <input class=\"form-control\" type=\"color\" id=\"background2\" name=\"background2\" value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"layout\">Couleur de la mise en page :</label>
        <input class=\"form-control\" type=\"color\" id=\"layout\" name=\"layout\" value=\"\">
    </div>
        <div class=\"form-group\">
        <label for=\"layout\">Couleur de l'écriture :</label>
        <input class=\"form-control\" type=\"color\" id=\"layoutColor\" name=\"layoutColor\" value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"title\">Couleur du titre :</label>
        <input class=\"form-control\" type=\"color\" id=\"title\" name=\"title\" value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"link\">Couleur des liens :</label>
        <input class=\"form-control\" type=\"color\" id=\"link\" name=\"link\" value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"buttonBorder\">Couleur de la bordure du bouton :</label>
        <input class=\"form-control\" type=\"color\" id=\"buttonBorder\" name=\"buttonBorder\" value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"button\">Couleur du bouton :</label>
        <input class=\"form-control\" type=\"color\" id=\"button\" name=\"button\" value=\"\">
    </div>
    <div class=\"form-group\">
        <label for=\"sideBar\">Couleur de la barre latérale :</label>
        <input class=\"form-control\" type=\"color\" id=\"sideBar\" name=\"sideBar\" value=\"\">
    </div>
    <button type=\"submit\" class=\"btn btn-primary\">Envoyer</button>
</form>

";
    }

    public function fomulaireDep($listDepName) {
        foreach ($listDepName as $dep) {
            echo "<option value=\"$dep\">$dep</option>";
        }
    }
}