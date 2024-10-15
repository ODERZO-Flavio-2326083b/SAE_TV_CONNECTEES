<?php

namespace Controllers;

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
     * Extrait les parties de l'URL en fonction du caractère /
     *
     * @return array Retourne un tableau contenant les segments de l'URL.
     */
    public function getPartOfUrl() {
        $url = $_SERVER['REQUEST_URI']; // Récupère l'URL de la requête
        $urlExplode = explode('/', $url); // Sépare l'URL par le caractère /
        $cleanUrl = array(); // Tableau pour stocker les segments nettoyés
        for ($i = 0; $i < sizeof($urlExplode); ++$i) {
            if ($urlExplode[$i] != '/' && $urlExplode[$i] != '') {
                $cleanUrl[] = $urlExplode[$i]; // Ajoute les segments non vides au tableau
            }
        }
        return $cleanUrl; // Retourne le tableau des segments nettoyés
    }

    /**
     * Écrit les erreurs dans un fichier de log.
     *
     * @param string $event Événement ou message d'erreur à enregistrer.
     */
    public function addLogEvent($event) {
        $time = date("D, d M Y H:i:s"); // Obtient l'heure actuelle
        $time = "[" . $time . "] "; // Formate l'heure pour le log
        $event = $time . $event . "\n"; // Prépare le message d'erreur
        file_put_contents(ABSPATH . TV_PLUG_PATH . "fichier.log", $event, FILE_APPEND); // Écrit dans le fichier log
    }

    /**
     * Obtient l'URL pour télécharger un fichier ICS.
     *
     * @param int $code Code pour lequel l'URL est générée.
     *
     * @return string Retourne l'URL pour le fichier ICS.
     */
    public function getUrl($code) {
        $str = strtotime("now"); // Récupère le timestamp actuel
        $str2 = strtotime(date("Y-m-d", strtotime('now')) . " +6 day"); // Timestamp pour 6 jours dans le futur
        $start = date('Y-m-d', $str); // Date de début (aujourd'hui)
        $end = date('Y-m-d', $str2); // Date de fin (dans 6 jours)
        $url = 'https://ade-web-consult.univ-amu.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?projectId=8&resources=' . $code . '&calType=ical&firstDate=' . $start . '&lastDate=' . $end; // Génère l'URL
        return $url; // Retourne l'URL
    }

    /**
     * Obtient le chemin d'un fichier à partir d'un code.
     *
     * @param int $code Code pour lequel le chemin du fichier est recherché.
     *
     * @return string Retourne le chemin du fichier ICS.
     */
    public function getFilePath($code) {
        $base_path = ABSPATH . TV_ICSFILE_PATH; // Définit le chemin de base

        // Vérifie si le fichier local existe
        for ($i = 0; $i <= 3; ++$i) {
            $file_path = $base_path . 'file' . $i . '/' . $code . '.ics'; // Crée le chemin du fichier
            // TODO: Demander a propos du filesize
            if (file_exists($file_path) && filesize($file_path) > 100) // Vérifie si le fichier existe et a une taille suffisante
                return $file_path; // Retourne le chemin du fichier
        }

        // Pas de version locale, téléchargeons-en une
        $this->addFile($code); // Appelle la méthode pour ajouter le fichier
        return $base_path . "file0/" . $code . '.ics'; // Retourne le chemin du fichier téléchargé
    }

    /**
     * Télécharge un fichier ICS.
     *
     * @param int $code Code ADE pour le fichier à télécharger.
     */
    public function addFile($code) {
        try {
            $path = ABSPATH . TV_ICSFILE_PATH . "file0/" . $code . '.ics'; // Définit le chemin du fichier à créer
            $url = $this->getUrl($code); // Obtient l'URL du fichier à télécharger
            //file_put_contents($path, fopen($url, 'r'));
            $contents = ''; // Initialise une variable pour stocker le contenu
            if (($handler = @fopen($url, "r")) !== FALSE) { // Ouvre l'URL
                while (!feof($handler)) {
                    $contents .= fread($handler, 8192); // Lit le contenu en morceaux
                }
                fclose($handler); // Ferme le gestionnaire de fichier
            } else {
                throw new Exception('File open failed.'); // Lève une exception si l'ouverture échoue
            }
            if ($handle = fopen($path, "w")) { // Ouvre le fichier pour écrire
                fwrite($handle, $contents); // Écrit le contenu dans le fichier
                fclose($handle); // Ferme le gestionnaire de fichier
            } else {
                throw new Exception('File open failed.'); // Lève une exception si l'ouverture échoue
            }
        } catch (Exception $e) {
            $this->addLogEvent($e); // Enregistre l'exception dans le log
        }
    }

    /**
     * Vérifie si l'entrée est une date valide.
     *
     * @param string $date La date à vérifier.
     *
     * @return bool Retourne vrai si la date est valide, faux sinon.
     */
    public function isRealDate($date) {
        if (false === strtotime($date)) { // Vérifie si la date peut être convertie en timestamp
            return false; // Retourne faux si la date n'est pas valide
        }
        list($year, $month, $day) = explode('-', $date); // Sépare la date en année, mois et jour
        return checkdate($month, $day, $year); // Vérifie si la date est valide
    }
}
