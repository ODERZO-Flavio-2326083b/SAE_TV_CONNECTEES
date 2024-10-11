<?php

namespace Controllers;

use Exception;

/**
 * Class Controller
 *
 * Classe principale contenant des fonctions de base pour la gestion des opérations.
 *
 * @package Controllers
 */
class Controller
{
    /**
     * Explode l'URL par '/'.
     *
     * @return array Tableau des parties de l'URL nettoyées.
     */
    public function getPartOfUrl() {
        $url = $_SERVER['REQUEST_URI'];
        $urlExplode = explode('/', $url);
        $cleanUrl = array();

        foreach ($urlExplode as $part) {
            if ($part !== '' && $part !== '/') {
                $cleanUrl[] = $part;
            }
        }

        return $cleanUrl;
    }

    /**
     * Écrit les erreurs dans un fichier de log.
     *
     * @param string $event L'événement ou l'erreur à enregistrer dans le log.
     */
    public function addLogEvent($event) {
        $time = date("D, d M Y H:i:s");
        $logEntry = "[" . $time . "] " . $event . PHP_EOL;
        file_put_contents(ABSPATH . TV_PLUG_PATH . "fichier.log", $logEntry, FILE_APPEND);
    }

    /**
     * Obtient l'URL pour télécharger un fichier ICS.
     *
     * @param int $code Le code ADE pour lequel obtenir l'URL.
     *
     * @return string L'URL pour télécharger le fichier ICS.
     */
    public function getUrl($code) {
        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime('+6 days'));
        $url = 'https://ade-web-consult.univ-amu.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?projectId=8&resources=' . $code . '&calType=ical&firstDate=' . $start . '&lastDate=' . $end;

        return $url;
    }

    /**
     * Obtient le chemin d'un fichier basé sur un code.
     *
     * @param int $code Le code ADE pour lequel obtenir le chemin.
     *
     * @return string Le chemin vers le fichier ICS.
     */
    public function getFilePath($code) {
        $base_path = ABSPATH . TV_ICSFILE_PATH;

        // Vérifie si le fichier local existe
        for ($i = 0; $i <= 3; ++$i) {
            $file_path = $base_path . 'file' . $i . '/' . $code . '.ics';
            if (file_exists($file_path) && filesize($file_path) > 100) {
                return $file_path;
            }
        }

        // Aucune version locale, téléchargeons-en une
        $this->addFile($code);
        return $base_path . "file0/" . $code . '.ics';
    }

    /**
     * Télécharge un fichier ICS.
     *
     * @param int $code Le code ADE pour lequel télécharger le fichier.
     */
    public function addFile($code) {
        try {
            $path = ABSPATH . TV_ICSFILE_PATH . "file0/" . $code . '.ics';
            $url = $this->getUrl($code);
            $contents = '';

            // Récupération du contenu du fichier
            if (($handler = @fopen($url, "r")) !== FALSE) {
                while (!feof($handler)) {
                    $contents .= fread($handler, 8192);
                }
                fclose($handler);
            } else {
                throw new Exception('Échec d\'ouverture du fichier.');
            }

            // Écriture du contenu dans le fichier
            if ($handle = fopen($path, "w")) {
                fwrite($handle, $contents);
                fclose($handle);
            } else {
                throw new Exception('Échec d\'ouverture du fichier.');
            }
        } catch (Exception $e) {
            $this->addLogEvent($e->getMessage());
        }
    }

    /**
     * Vérifie si l'entrée est une date valide.
     *
     * @param string $date La date à vérifier.
     *
     * @return bool Vrai si la date est valide, sinon faux.
     */
    public function isRealDate($date) {
        if (false === strtotime($date)) {
            return false;
        }
        list($year, $month, $day) = explode('-', $date);
        return checkdate($month, $day, $year);
    }
}
