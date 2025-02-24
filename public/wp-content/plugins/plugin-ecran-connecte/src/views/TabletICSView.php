<?php

namespace views;

use DateTime;
use IntlDateFormatter;

/**
 * Classe TabletICSView
 *
 * Cette classe gère l'affichage des vues liées à
 * l'emploi du temps de la semaine sur une tablette.
 * Elle génère le HTML nécessaire
 * pour afficher un emploi du temps contenant
 * les différents événements ou activités planifiés.
 * L'utilisateur peut ainsi visualiser ses horaires de manière organisée et claire.
 *
 * @category View
 * @package  Views
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/TabletICSView Documentation de la classe
 * @since    2025-01-13
 */
class TabletICSView extends ICSView {
    public function getContent( $event, $day = 0 ) : string|false
    {
        $duration = str_replace(':', 'h', date("H:i", strtotime($event['deb'])))
                    . ' - ' . str_replace(':', 'h', date("H:i", strtotime($event['fin'])));

        if (str_ends_with($event['label'], "alt")) {
            $label = substr($event['label'], 0, -3);
        } else {
            $label = $event['label'];
        }

        $description = substr($event['description'], 0, -30);

        return "<div style='border:solid'><strong>$duration</strong><br>$label<br>$description<br>{$event['location']}</div>";
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function displaySchedule( $ics_data, $title, $allDay ) : string
    {
        $today = new DateTime();
        $firstDayOfWeek = clone $today;
        $firstDayOfWeek->modify('monday this week');

        $days = ["<th scope='col'>Heures</th>"];
        $eventsByHour = [];
        $hoursRange = range(7, 20);
        for ($i = 0; $i < 6; $i++) {
            $day = clone $firstDayOfWeek;
            $day->modify("+$i days");
            $dateFormatted = $this->formatDateFr($day->format("Y-m-d"));
            $days[] = "<th scope='col'>$dateFormatted</th>";

            foreach ($hoursRange as $hour) {
                $eventsByHour[$hour][$i] = "<td style='border: 1px solid #ccc; height: 50px;'></td>";
            }

            $dayEvents = $ics_data['events'][$day->format('Y')][$day->format('m')][$day->format('d')] ?? [];
            if (!empty($dayEvents)) {
                foreach ($dayEvents as $eventsGroup) {
                    foreach ($eventsGroup as $event) {
                        $startHour = (int)date("H", strtotime($event['deb']));
                        $endHour = (int)date("H", strtotime($event['fin']));
                        $duration = max(1, $endHour - $startHour);

                        $eventsByHour[$startHour][$i] = "<td rowspan='$duration' style='background-color:#cce5ff; border: 1px solid #007bff; vertical-align: middle; text-align: center;'>" .
                                                        ($this->getContent($event) ?: "<div>Erreur d'affichage</div>") . "</td>";

                        for ($h = $startHour + 1; $h < $endHour; $h++) {
                            $eventsByHour[$h][$i] = "";
                        }
                    }
                }
            }
        }

        $thead = $this->theadBuilder($days);

        $tbody = "<tbody>";
        foreach ($hoursRange as $hour) {
            $tbody .= "<tr><td><strong>" . sprintf('%02d:00', $hour) . "</strong></td>";
            for ($j = 0; $j < 6; $j++) {
                $tbody .= $eventsByHour[$hour][$j] ?? "<td style='border: 1px solid; height: 50px;'></td>";
            }
            $tbody .= "</tr>";
        }
        $tbody .= "</tbody>";

        return "<h1 style='text-align:center;'>$title</h1>
            <table style='width:100%; border-collapse: collapse; text-align:center;'>
                $thead
                $tbody
            </table>";
    }

    function theadBuilder($days) {
        return '<thead><tr>' . implode('', $days) . '</tr></thead>';
    }


    /**
     * Renvoie une date sous forme francophone à partir d'une date numérique
     *
     * @param $dateStr string Chaine de caractères de la date
     * sous la forme 'YYYY-MM-DD'
     *
     * @return string
     * @throws \DateMalformedStringException
     */
    function formatDateFr($dateStr): string {
        $fr = ['Sunday' => 'Dimanche', 'Monday' => 'Lundi', 'Tuesday' => 'Mardi',
               'Wednesday' => 'Mercredi', 'Thursday' => 'Jeudi',
               'Friday' => 'Vendredi', 'Saturday' => 'Samedi',
               'January' => 'Janvier', 'February' => 'Février',
               'March' => 'Mars', 'April' => 'Avril', 'May' => 'Mai',
               'June' => 'Juin', 'July' => 'Juillet', 'August' => 'Août',
               'September' => 'Septembre', 'October' => 'Octobre',
               'November' => 'Novembre', 'December' => 'Décembre'];

        $date = new DateTime($dateStr);
        return str_replace(array_keys($fr), array_values($fr), $date->format('l d F'));
    }
}