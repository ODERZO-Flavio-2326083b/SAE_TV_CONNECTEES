<?php

namespace Views;

/**
 * Class HelpMapView
 *
 * Classe responsable de l'affichage de la carte
 * montrant les lieux d'intérêt à proximité.
 *
 * @package Views
 */
class HelpMapView extends View
{
    /**
     * Affiche la carte d'aide.
     *
     * Cette méthode génère le code HTML pour afficher une carte d'aide.
     * Actuellement, elle affiche simplement un message de test.
     *
     * @return string Le code HTML à afficher pour la carte d'aide.
     *
     * @example
     * $view = new HelpMapView();
     * echo $view->displayHelpMap();
     */
    public function displayHelpMap() {
        return '<p>Hello, World!</p>';
    }
}
