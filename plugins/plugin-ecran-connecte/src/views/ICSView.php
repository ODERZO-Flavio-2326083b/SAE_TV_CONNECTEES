<?php

namespace Views;

use WP_User;

/**
 * Class ICSView
 *
 * Affiche l'emploi du temps.
 *
 * @package Views
 */
class ICSView extends View
{
    /**
     * Affiche l'emploi du temps.
     *
     * @param array $ics_data Données de l'emploi du temps.
     * @param string $title Titre de l'emploi du temps.
     * @param bool $allDay Indique si l'événement est d'une journée entière.
     *
     * @return string|bool Retourne le HTML de l'emploi du temps ou un message si aucun cours n'est disponible.
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
                            // EN-TÊTE
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
                                    // CONTENU
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
                                                if ($this->getContent($event)) {
                                                    ++$current_study;
                                                    $string .= $this->getContent($event);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            // PIÉCE DE FIN
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
            // AUCUN EMPLOI DU TEMPS
            if ($current_study < 1) {
                return $this->displayNoSchedule($title, $current_user);
            }
        } else {
            return $this->displayNoSchedule($title, $current_user);
        }

        return $string;
    }

    /**
     * Affiche l'en-tête de l'emploi du temps.
     *
     * @param WP_User $current_user L'utilisateur actuel.
     *
     * @return string Le code HTML de l'en-tête.
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
     * Donne la date de l'emploi du temps.
     *
     * @param int $day Jour.
     * @param int $month Mois.
     * @param int $year Année.
     *
     * @return string HTML de la date formatée.
     */
    public function giveDate($day, $month, $year) {
        $day_of_week = $day + 1;

        return '<h2>' . date_i18n('l j F', mktime(0, 0, 0, $month, $day_of_week, $year)) . '</h2>';
    }

    /**
     * Donne le contenu d'un événement.
     *
     * @param array $event Détails de l'événement.
     * @param int $day (optionnel) Jour, par défaut aujourd'hui.
     *
     * @return bool|string Retourne le HTML de la ligne d'emploi du temps ou false si l'événement n'est pas actif.
     */
    public function getContent($event, $day = 0) {
        if ($day == 0) {
            $day = date('j');
        }

        $time = date("H:i");
        $duration = str_replace(':', 'h', date("H:i", strtotime($event['deb']))) . ' - ' . str_replace(':', 'h', date("H:i", strtotime($event['fin'])));
        if ($day == date('j')) {
            $active = date("H:i", strtotime($event['deb'])) <= $time && $time < date("H:i", strtotime($event['fin']));
        }

        $label = (substr($event['label'], -3) == "alt") ? substr($event['label'], 0, -3) : $event['label'];
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
     * Crée une ligne pour l'emploi du temps.
     *
     * @param array $datas Données à afficher dans la ligne.
     * @param bool $active Indique si l'événement est actif.
     *
     * @return string HTML de la ligne.
     */
    public function displayLineSchedule($datas, $active = false) {
        $string = $active ? '<tr class="table-success" scope="row">' : '<tr scope="row">';

        foreach ($datas as $data) {
            $string .= '<td class="text-center">' . $data . '</td>';
        }

        return $string . '</tr>';
    }

    /**
     * Affiche le pied de page de l'emploi du temps.
     *
     * @return string HTML du pied de page.
     */
    public function displayEndSchedule() {
        return '</tbody>
             </table>
          </div>';
    }

    /**
     * Affiche un message s'il n'y a pas de cours.
     *
     * @param string $title Titre de l'emploi du temps.
     * @param WP_User $current_user L'utilisateur actuel.
     *
     * @return string|bool Retourne le message ou false.
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
