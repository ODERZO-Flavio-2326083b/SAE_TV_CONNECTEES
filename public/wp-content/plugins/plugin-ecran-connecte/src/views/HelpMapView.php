<?php

namespace views;

/**
 * Class HelpMapView
 *
 * Vue pour la carte affichant les lieux intéressants à proximité.
 *
 * @package views
 */
class HelpMapView extends View
{
    /**
     * Affiche la carte d'aide.
     *
     * @return string
     * Retourne le code HTML de la carte d'aide.
     */
    public function displayHelpMap() : string {
        return '<p>Hello, World!</p>';
    }
}
