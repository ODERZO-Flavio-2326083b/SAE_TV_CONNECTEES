<?php

namespace controllers;

use Exception;

/**
 * Class Controller
 *
 * Contrôleur principal contenant toutes les fonctions de base.
 *
 * @package Controllers
 */
class Controller
{

    /**
     * Récupère les segments de l'URL de la requête actuelle.
     *
     * Cette méthode extrait l'URL de la requête en cours, la divise
     * en segments basés sur le caractère '/' et nettoie les segments
     * vides ou non valides. Elle retourne un tableau contenant les
     * segments pertinents de l'URL.
     *
     * @return array Tableau des segments nettoyés de l'URL.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function getPartOfUrl()
    {
        $url = $_SERVER['REQUEST_URI']; // Récupère l'URL de la requête
        $urlExplode = explode('/', $url); // Sépare l'URL par le caractère /
        $cleanUrl = array(); // Tableau pour stocker les segments nettoyés
        for ($i = 0; $i < sizeof($urlExplode); ++$i) {
            if ($urlExplode[$i] != '/' && $urlExplode[$i] != '') {
                // Ajoute les segments non vides au tableau
                $cleanUrl[] = $urlExplode[$i];
            }
        }
        return $cleanUrl; // Retourne le tableau des segments nettoyés
    }

    /**
     * Ajoute un événement au fichier de log.
     *
     * Cette méthode enregistre un événement dans un fichier de log,
     * en préfixant le message avec la date et l'heure actuelles.
     * Le message est écrit à la fin du fichier spécifié, permettant
     * de conserver un historique des événements enregistrés.
     *
     * @param string $event Le message de l'événement à enregistrer.
     *
     * @return void
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function addLogEvent($event)
    {
        $time = date("D, d M Y H:i:s"); // Obtient l'heure actuelle
        $time = "[" . $time . "] "; // Formate l'heure pour le log
        $event = $time . $event . "\n"; // Prépare le message d'erreur
        file_put_contents(
            ABSPATH . TV_PLUG_PATH . "fichier.log", $event,
            FILE_APPEND
        ); // Écrit dans le fichier log
    }

    /**
     * Génère une URL pour accéder à un calendrier iCalendar.
     *
     * Cette méthode crée une URL basée sur le code fourni,
     * pour accéder à un calendrier qui affiche les événements
     * pour une période de 7 jours à partir d'aujourd'hui.
     *
     * @param string $code Le code des ressources à inclure dans l'URL.
     *
     * @return string L'URL générée pour le calendrier iCalendar.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function getUrl($code) : string
    {
        $str = strtotime("now"); // Récupère le timestamp actuel
        $str2 = strtotime(
            date("Y-m-d", strtotime('now'))
            . " +6 day"
        ); // Timestamp pour 6 jours dans le futur
        $start = date('Y-m-d', $str); // Date de début (aujourd'hui)
        $end = date('Y-m-d', $str2); // Date de fin (dans 6 jours)
        $url = 'https://ade-web-consult.univ-amu.fr/jsp/custom/
            modules/plannings/anonymous_cal.jsp?projectId=8&resources='
            . $code . '&calType=ical&firstDate=' . $start
            . '&lastDate=' . $end; // Génère l'URL
        return $url; // Retourne l'URL
    }

    /**
     * Récupère le chemin d'accès d'un fichier iCalendar local.
     *
     * Cette méthode vérifie l'existence d'un fichier iCalendar
     * correspondant au code donné, dans plusieurs répertoires.
     * Si un fichier valide est trouvé, son chemin est retourné.
     * Sinon, un nouveau fichier est téléchargé et son chemin
     * est également retourné.
     *
     * @param string $code Le code des ressources utilisé pour
     *                     identifier le fichier iCalendar.
     *
     * @return string Le chemin du fichier iCalendar, qu'il
     *                soit localement existant ou nouvellement
     *                téléchargé.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function getFilePath($code) : string
    {
        $base_path = ABSPATH . TV_ICSFILE_PATH; // Définit le chemin de base

        // Vérifie si le fichier local existe
        for ($i = 0; $i <= 3; ++$i) {
            // Crée le chemin du fichier
            $file_path = $base_path . 'file' . $i . '/' . $code . '.ics';
            // Vérifie si le fichier existe et a une taille suffisante
            if (file_exists($file_path) && filesize($file_path) > 100) {
                return $file_path; // Retourne le chemin du fichier
            }
        }

        // Pas de version locale, téléchargeons-en une
        $this->addFile($code); // Appelle la méthode pour ajouter le fichier
        // Retourne le chemin du fichier téléchargé
        return $base_path . "file0/" . $code . '.ics';
    }

    /**
     * Télécharge et enregistre un fichier iCalendar à partir d'une URL.
     *
     * Cette méthode construit l'URL du fichier iCalendar en utilisant le code
     * fourni, puis tente de télécharger le fichier. Si le téléchargement est
     * réussi, le contenu est enregistré localement dans un fichier avec
     * le nom correspondant. En cas d'échec à ouvrir le fichier ou à
     * télécharger le contenu, une exception est levée et enregistrée dans le log.
     *
     * @param string $code Le code des ressources utilisé pour générer
     *                     l'URL du fichier iCalendar à télécharger.
     *
     * @return void
     *
     * @throws Exception Si l'ouverture du fichier échoue lors du téléchargement
     *                   ou de l'enregistrement.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function addFile($code)
    {
        try {
            // Définit le chemin du fichier à créer
            $path = ABSPATH . TV_ICSFILE_PATH . "file0/" . $code . '.ics';
            $url = $this->getUrl($code); // Obtient l'URL du fichier à télécharger
            //file_put_contents($path, fopen($url, 'r'));
            $contents = ''; // Initialise une variable pour stocker le contenu
            if (($handler = @fopen($url, "r")) !== false) { // Ouvre l'URL
                while (!feof($handler)) {
                    // Lit le contenu en morceaux
                    $contents .= fread($handler, 8192);
                }
                fclose($handler); // Ferme le gestionnaire de fichier
            } else {
                // Lève une exception si l'ouverture échoue
                throw new Exception('File open failed.');
            }
            if ($handle = fopen($path, "w")) { // Ouvre le fichier pour écrire
                fwrite($handle, $contents); // Écrit le contenu dans le fichier
                fclose($handle); // Ferme le gestionnaire de fichier
            } else {
                // Lève une exception si l'ouverture échoue
                throw new Exception('File open failed.');
            }
        } catch (Exception $e) {
            $this->addLogEvent($e); // Enregistre l'exception dans le log
        }
    }

    /**
     * Vérifie si une chaîne de caractères représente une date valide.
     *
     * Cette méthode tente de convertir la chaîne de caractères donnée
     * en timestamp. Si la conversion échoue, elle retourne faux.
     * Sinon, elle décompose la date en année, mois et jour, et utilise
     * la fonction 'checkdate' pour valider la date.
     *
     * @param string $date La chaîne de caractères représentant la date à vérifier
     *                     au format 'YYYY-MM-DD'.
     *
     * @return bool Retourne vrai si la date est valide, faux sinon.
     *
     * @version 1.0
     * @date    2024-10-16
     */
    public function isRealDate($date) : bool
    {
        // Vérifie si la date peut être convertie en timestamp
        if (false === strtotime($date)) {
            return false; // Retourne faux si la date n'est pas valide
        }
        // Sépare la date en année, mois et jour
        list($year, $month, $day) = explode('-', $date);
        return checkdate($month, $day, $year); // Vérifie si la date est valide
    }
}
