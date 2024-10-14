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
     * Explose l'URL par /
     *
     * @return array Retourne un tableau contenant les différentes parties de l'URL.
     */
    public function getPartOfUrl() {
        $url = $_SERVER['REQUEST_URI'];
        $urlExplode = explode('/', $url);
        $cleanUrl = array();
        for ($i = 0; $i < sizeof($urlExplode); ++$i) {
            if ($urlExplode[$i] != '/' && $urlExplode[$i] != '') {
                $cleanUrl[] = $urlExplode[$i];
            }
        }
        return $cleanUrl;
    }

    /**
     * Écrit des erreurs dans un fichier journal.
     *
     * @param $event    string Événement à enregistrer dans le journal.
     */
    public function addLogEvent($event) {
        $time = date("D, d M Y H:i:s");
        $time = "[" . $time . "] ";
        $event = $time . $event . "\n";
        file_put_contents(ABSPATH . TV_PLUG_PATH . "fichier.log", $event, FILE_APPEND);
    }

    /**
     * Obtient l'URL pour télécharger un fichier ICS.
     *
     * @param $code     int Code ADE à utiliser dans l'URL.
     *
     * @return string Retourne l'URL pour le fichier ICS.
     */
    public function getUrl($code) {
        $str = strtotime("now");
        $str2 = strtotime(date("Y-m-d", strtotime('now')) . " +6 day");
        $start = date('Y-m-d', $str);
        $end = date('Y-m-d', $str2);
        $url = 'https://ade-web-consult.univ-amu.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?projectId=8&resources=' . $code . '&calType=ical&firstDate=' . $start . '&lastDate=' . $end;
        return $url;
    }

    /**
     * Obtient le chemin d'un fichier pour un code donné.
     *
     * @param $code     int Code ADE pour lequel le chemin doit être obtenu.
     *
     * @return string Retourne le chemin du fichier ICS correspondant au code.
     */
    public function getFilePath($code) {
        $base_path = ABSPATH . TV_ICSFILE_PATH;

        // Vérifie si le fichier local existe
        for ($i = 0; $i <= 3; ++$i) {
            $file_path = $base_path . 'file' . $i . '/' . $code . '.ics';
            // TODO: Demander à propos de la taille du fichier
            if (file_exists($file_path) && filesize($file_path) > 100)
                return $file_path;
        }

        // Pas de version locale, téléchargeons-en une
        $this->addFile($code);
        return $base_path . "file0/" . $code . '.ics';
    }

    /**
     * Télécharge un fichier ICS.
     *
     * @param $code     int Code ADE à télécharger.
     */
    public function addFile($code) {
        try {
            $path = ABSPATH . TV_ICSFILE_PATH . "file0/" . $code . '.ics';
            $url = $this->getUrl($code);
            //file_put_contents($path, fopen($url, 'r'));
            $contents = '';
            if (($handler = @fopen($url, "r")) !== FALSE) {
                while (!feof($handler)) {
                    $contents .= fread($handler, 8192);
                }
                fclose($handler);
            } else {
                throw new Exception('Échec de l\'ouverture du fichier.');
            }
            if ($handle = fopen($path, "w")) {
                fwrite($handle, $contents);
                fclose($handle);
            } else {
                throw new Exception('Échec de l\'ouverture du fichier.');
            }
        } catch (Exception $e) {
            $this->addLogEvent($e);
        }
    }

    /**
     * Vérifie si l'entrée est une date valide.
     *
     * @param $date string La date à vérifier.
     *
     * @return bool Retourne vrai si la date est valide, faux sinon.
     */
    public function isRealDate($date) {
        if (false === strtotime($date)) {
            return false;
        }
        list($year, $month, $day) = explode('-', $date);
        return checkdate($month, $day, $year);
    }
}
