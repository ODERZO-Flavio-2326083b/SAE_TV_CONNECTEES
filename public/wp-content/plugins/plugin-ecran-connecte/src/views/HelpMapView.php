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
     * Cette méthode génère le code HTML pour afficher une carte d'aide à l'utilisateur.
     * Actuellement, elle retourne un simple message "Hello, World!" en HTML.
     * Cette carte peut être enrichie de contenu supplémentaire pour fournir une aide contextuelle ou des informations utiles à l'utilisateur.
     *
     * @return string
     * Retourne le code HTML de la carte d'aide.
     *
     * @version 1.0
     * @date 08-01-2025
     */
    public function displayHelpMap() : string {
        return '<p>Hello, World!</p>';
    }
}
