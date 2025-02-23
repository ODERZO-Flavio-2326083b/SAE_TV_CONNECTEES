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

        return "<div><strong>{$duration}</strong><br>{$label}<br>{$description}<br>{$event['location']}</div>";
    }

    public function displaySchedule( $ics_data, $title, $allDay ) : string
    {
        setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
        $today = new DateTime();
        $firstDayOfWeek = clone $today;
        $firstDayOfWeek->modify('monday this week');

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = clone $firstDayOfWeek;
            $day->modify("+{$i} days");
            $dateFormatted = strftime('%A %d %B', $day->getTimestamp());
            $dayEvents = $ics_data['events'][$day->format('Y')][$day->format('m')][$day->format('d')] ?? [];

            $eventsHtml = "";
            if (!empty($dayEvents)) {
                foreach ($dayEvents as $events) {
                    foreach ($events as $event) {
                        $eventsHtml .= $this->getContent($event) ?: "<div>Erreur d'affichage</div>";
                    }
                }
            } else {
                $eventsHtml = "<div>Aucun événement</div>";
            }

            $days[] = "<td><strong>{$dateFormatted}</strong><br>{$eventsHtml}</td>";
        }

        return "<h1>{$title}</h1><table border='1' style='width:100%; text-align:center;'><tr>" . implode('', $days) . "</tr></table>";
    }

}