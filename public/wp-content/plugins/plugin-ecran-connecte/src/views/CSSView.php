<?php

namespace Views;

use Views\View;

class CSSView extends View
{
    public function displayCssCustomizer()
    {
        echo "
<form action='' method='POST'>
<label>Entré votre couleur : </label>
<input type='color' name='background' value=''>
<label>Entré votre taile : </label>
<input type='number' name='taille' value=''>
<input type='submit' value='Envoyer'>
</form>
";
    }
}