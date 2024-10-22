<?php

namespace Views;

use Views\View;

class CSSView extends View
{
    public function displayCssCustomizer()
    {
        echo "
<form method='POST'>
    <label>Entrez votre couleur : </label>
    <input type='color' name='background' value=''>
    <label>Entrez votre taille : </label>
    <input type='number' name='taille' value=''>
    <input type='submit' value='Envoyer'>
</form>
";
    }
}