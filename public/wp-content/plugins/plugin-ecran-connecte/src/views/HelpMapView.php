<?php
/**
 * Fichier HelpMapView.php
 *
 * Ce fichier contient la classe `HelpMapView`,
 * qui est responsable de l'affichage des vues liées
 * à la carte interactive montrant les lieux intéressants
 * à proximité. Cette classe génère
 * l'affichage d'une carte interactive, permettant
 * à l'utilisateur de visualiser les points d'intérêt
 * à proximité de sa localisation ou d'une autre localisation spécifiée.
 *
 * PHP version 7.4 or later
 *
 * @category View
 * @package  Views
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/HelpMapView
 * Documentation de la classe
 * @since    2025-01-13
 */
namespace views;

/**
 * Classe HelpMapView
 *
 * Cette classe gère l'affichage des vues liées à la
 * carte interactive des lieux intéressants.
 * Elle génère le HTML nécessaire pour afficher
 * une carte interactive contenant des points d'intérêt
 * à proximité de l'utilisateur. La carte peut inclure
 * des itinéraires, des lieux d'intérêt, et des
 * fonctionnalités interactives pour aider l'utilisateur
 * à visualiser les emplacements des différents
 * lieux.
 *
 * @category View
 * @package  Views
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/HelpMapView Documentation de la classe
 * @since    2025-01-13
 */
class HelpMapView extends View
{
    /**
     * Affiche la carte d'aide.
     *
     * Cette méthode génère le code HTML pour afficher une carte d'aide à
     * l'utilisateur.
     * Actuellement, elle retourne un simple message "Hello, World!" en HTML.
     * Cette carte peut être enrichie de contenu supplémentaire pour fournir une aide
     * contextuelle ou des informations utiles à l'utilisateur.
     *
     * @return string
     * Retourne le code HTML de la carte d'aide.
     *
     * @version 1.0
     * @date    08-01-2025
     */
    public function displayHelpMap() : string
    {
        return '<p>Hello, World!</p>';
    }
}
