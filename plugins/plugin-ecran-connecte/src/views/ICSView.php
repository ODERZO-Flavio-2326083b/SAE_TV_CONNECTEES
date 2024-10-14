<?php

namespace Views;

use WP_User;

/**
 * Class ICSView
 *
 * Classe responsable de l'affichage du calendrier.
 *
 * @package Views
 */
class ICSView extends View
{
    /**
     * Affiche le calendrier à partir des données ICS.
     *
     * Cette méthode génère un tableau HTML pour afficher
     * les événements programmés en fonction des données ICS fournies.
     *
     * @param array $ics_data Données ICS contenant les événements.
     * @param string $title Titre à afficher en haut du calendrier.
     * @param bool $allDay Indique si l'affichage concerne des événements sur toute la journée.
     *
     * @return string|bool Code HTML du calendrier ou false si aucun événement n'est trouvé.
     *
     * @example
     * $icsView = new ICSView();
     * $html = $icsView->displaySchedule($ics_data, 'Mon Calendrier', false);
     * echo $html;
     */
    public function displaySchedule($ics_data, $title, $allDay) {
        $current_user = wp_get_current_user();
        if (isset($ics_data['events'])) {
            $string = '<h1>' . $title . '</h1>';
            $current_study = 0;
            foreach (array_keys((array)$ics_data['events']) as $year) {
                for ($m = 1; $m <= 12; $m++) {
                    $month = $m < 10 ? '0' . $m : '' . $m;
                    if (array_key_exists($month, (array)$ics_data['events'][$year])) {
                        foreach ((array)$ics_data['events'][$year][$month] as $day => $day_events) {
                            // HEADER
                            if ($current_study > 9) {
                                break;
                            }
                            if ($allDay) {
                                if ($day == date('j')) {
                                    $string .= $this->displayStartSchedule($current_user);
                                }
                            } else if (in_array('television', $current_user->roles) || in_array('technicien', $current_user->roles)) {
                                if ($day == date('j')) {
                                    $string .= $this->displayStartSchedule($current_user);
                                }
                            } else {
                                $string .= $this->giveDate($day, $month, $year);
                                $string .= $this->displayStartSchedule($current_user);
                            }
                            foreach ($day_events as $day_event => $events) {
                                foreach ($events as $event) {
                                    // CONTENT
                                    if ($allDay) {
                                        if ($day == date('j')) {
                                            if ($current_study > 9) {
                                                break;
                                            }
                                            if ($this->getContent($event)) {
                                                ++$current_study;
                                                $string .= $this->getContent($event);
                                            }
                                        }
                                    } else {
                                        if (in_array('television', $current_user->roles) || in_array('technicien', $current_user->roles)) {
                                            if ($day == date('j')) {
                                                if ($current_study > 9) {
                                                    break;
                                                }
                                                if ($this->getContent($event)) {
                                                    ++$current_study;
                                                    $string .= $this->getContent($event);
                                                }
                                            }
                                        } elseif (in_array('enseignant', $current_user->roles) || in_array('directeuretude', $current_user->roles)
                                            || in_array('etudiant', $current_user->roles)) {
                                            if ($current_study > 9) {
                                                break;
                                            }
                                            if ($this->getContent($event)) {
                                                ++$current_study;
                                                $string .= $this->getContent($event, $day);
                                            }
                                        } else {
                                            if ($current_study > 9) {
                                                break;
                                            }
                                            if ($day == date('j')) {
                                                if ($current_study > 9) {
                                                    break;
                                                }
                                                if ($this->getContent($event)) {
                                                    ++$current_study;
                                                    $string .= $this->getContent($event);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            // FOOTER
                            if (in_array('television', $current_user->roles) || in_array('technicien', $current_user->roles)) {
                                if ($day == date('j')) {
                                    $string .= $this->displayEndSchedule();
                                }
                            } else {
                                $string .= $this->displayEndSchedule();
                            }
                        }
                    }
                }
            }
            // IF NO SCHEDULE
            if ($current_study < 1) {
                return $this->displayNoSchedule($title, $current_user);
            }
        } else {
            return $this->displayNoSchedule($title, $current_user);
        }

        return $string;
    }

    /**
     * Affiche l'en-tête du calendrier.
     *
     * @param WP_User $current_user L'utilisateur actuel.
     *
     * @return string Le code HTML de l'en-tête du calendrier.
     *
     * @example
     * $header = $this->displayStartSchedule($current_user);
     */
    public function displayStartSchedule($current_user) {
        $string = '<div class="table-responsive">
                    <table class="table tabSchedule">
                        <thead class="headerTab">
                            <tr>
                                <th scope="col" class="text-center">Horaire</th>';
        if (!in_array("technicien", $current_user->roles)) {
            $string .= '<th scope="col" class="text-center">Cours</th>
                        <th scope="col" class="text-center">Groupe/Enseignant</th>';
        }
        $string .= '<th scope="col" class="text-center">Salle</th>
                 </tr>
              </thead>
           <tbody>';

        return $string;
    }

    /**
     * Donne la date du calendrier.
     *
     * @param int $day Le jour du mois.
     * @param string $month Le mois en cours.
     * @param int $year L'année en cours.
     *
     * @return string La date formatée en HTML.
     *
     * @example
     * $dateHtml = $this->giveDate(15, '03', 2024);
     */
    public function giveDate($day, $month, $year) {
        $day_of_week = $day + 1;

        return '<h2>' . date_i18n('l j F', mktime(0, 0, 0, $month, $day_of_week, $year)) . '</h2>';
    }

    /**
     * Donne le contenu d'un événement.
     *
     * @param array $event L'événement à afficher.
     * @param int $day Le jour pour lequel l'événement est affiché (0 par défaut).
     *
     * @return bool|string Le code HTML de l'événement ou false si l'événement est passé.
     *
     * @example
     * $contentHtml = $this->getContent($event);
     */
    public function getContent($event, $day = 0) {
        if ($day == 0) {
            $day = date('j');
        }

        $time = date("H:i");
        $duration = str_replace(':', 'h', date("H:i", strtotime($event['deb']))) . ' - ' . str_replace(':', 'h', date("H:i", strtotime($event['fin'])));
        if ($day == date('j')) {
            $active = (date("H:i", strtotime($event['deb'])) <= $time && $time < date("H:i", strtotime($event['fin'])));
        } else {
            $active = false;
        }

        $label = substr($event['label'], -3) == "alt" ? substr($event['label'], 0, -3) : $event['label'];
        $description = substr($event['description'], 0, -30);
        if (!(date("H:i", strtotime($event['fin'])) <= $time) || $day != date('j')) {
            $current_user = wp_get_current_user();
            if (in_array("technicien", $current_user->roles)) {
                return $this->displayLineSchedule([$duration, $event['location']], $active);
            } else {
                return $this->displayLineSchedule([$duration, $label, $description, $event['location']], $active);
            }
        }

        return false;
    }

    /**
     * Crée une ligne pour le calendrier.
     *
     * @param array $datas Données à afficher dans la ligne.
     * @param bool $active Indique si la ligne est active (true si l'événement est en cours).
     *
     * @return string Le code HTML de la ligne du calendrier.
     *
     * @example
     * $lineHtml = $this->displayLineSchedule(['10h-12h', 'Mathématiques', 'M. Dupont', 'Salle 101'], true);
     */
    public function displayLineSchedule($datas, $active = false) {
        $string = $active ? '<tr class="table-success" scope="row">' : '<tr scope="row">';
        foreach ($datas as $data) {
            $string .= '<td class="text-center">' . $data . '</td>';
        }

        return $string . '</tr>';
    }

    /**
     * Affiche le pied de page du calendrier.
     *
     * @return string Le code HTML de la fin du tableau.
     *
     * @example
     * $footerHtml = $this->displayEndSchedule();
     */
    public function displayEndSchedule() {
        return '</tbody>
             </table>
          </div>';
    }

    /**
     * Affiche un message si aucun événement n'est prévu.
     *
     * @param string $title Titre du calendrier.
     * @param WP_User $current_user L'utilisateur actuel.
     *
     * @return bool|string Le message HTML ou false si aucune condition n'est remplie.
     *
     * @example
     * $noScheduleMessage = $this->displayNoSchedule('Mon Calendrier', $current_user);
     */
    public function displayNoSchedule($title, $current_user) {
        if (get_theme_mod('ecran_connecte_schedule_msg', 'show') == 'show' && in_array('television', $current_user->roles)) {
            return '<h1>' . $title . '</h1><p> Vous n\'avez pas cours !</p>';
        } else if (!in_array('television', $current_user->roles)) {
            return '<h1>' . $title . '</h1><p> Vous n\'avez pas cours !</p>';
        } else {
            return false;
        }
    }
}
