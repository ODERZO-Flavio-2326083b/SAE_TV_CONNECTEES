<?php

namespace Views;


use WP_User;

/**
 * Class ICSView
 *
 * Display the schedule
 *
 * @package Views
 */
class ICSView extends View
{
    /**
     * ADisplay the schedule
     *
     * @param $ics_data     array
     * @param $title        string
     *
     * @return bool
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
     * Display the header
     *
     * @param $current_user     WP_User
     *
     * @return string
     */
    public function displayStartSchedule($current_user) {
        $string = '<div class="table-responsive">
                   	<table class="table tabSchedule">
                    	<thead class="headerTab">
                        	<tr>
                            	<th scope="col" class="text-center">Horaire</th>';
        if (!in_array("technicien", $current_user->roles)) {
            $string .= '<th scope="col" class="text-center" >Cours</th>
                        <th scope="col" class="text-center">Groupe/Enseignant</th>';
        }
        $string .= '<th scope="col" class="text-center">Salle</th>
                 </tr>
              </thead>
           <tbody>';

        return $string;
    }

    /**
     * Give the date of the schedule
     *
     * @param $day
     * @param $month
     * @param $year
     *
     * @return string
     */
    public function giveDate($day, $month, $year) {
        $day_of_week = $day + 1;

        return '<h2>' . date_i18n('l j F', mktime(0, 0, 0, $month, $day_of_week, $year)) . '</h2>';
    }

    /**
     * Give the content of an event
     *
     * @param $event
     * @param int $day
     *
     * @return bool|string
     */
    public function getContent($event, $day = 0) {
        if ($day == 0) {
            $day = date('j');
        }

        $time = date("H:i");
        $duration = str_replace(':', 'h', date("H:i", strtotime($event['deb']))) . ' - ' . str_replace(':', 'h', date("H:i", strtotime($event['fin'])));
        if ($day == date('j')) {
            if (date("H:i", strtotime($event['deb'])) <= $time && $time < date("H:i", strtotime($event['fin']))) {
                $active = true;
            } else {
                $active = false;
            }
        }

        if (substr($event['label'], -3) == "alt") {
            $label = substr($event['label'], 0, -3);
        } else {
            $label = $event['label'];
        }
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
     * Create a line for the schedule
     *
     * @param $datas
     * @param bool $active
     *
     * @return string
     */
    public function displayLineSchedule($datas, $active = false) {
        if ($active) {
            $string = '<tr class="table-success" scope="row">';
        } else {
            $string = '<tr scope="row">';
        }
        foreach ($datas as $data) {
            $string .= '<td class="text-center">' . $data . '</td>';
        }

        return $string . '</tr>';
    }

    /**
     * Display the footer of the schedule
     *
     * @return string
     */
    public
    function displayEndSchedule() {
        return '</tbody>
             </table>
          </div>';
    }


    /**
     * Display an message if there is no lesson
     *
     * @param $title            string
     * @param $current_user     WP_User
     *
     * @return bool|string
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
