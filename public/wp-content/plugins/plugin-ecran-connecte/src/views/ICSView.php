<?php

namespace Views;

use Controllers\InformationController;
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
     * Affiche l'emploi du temps basé sur les données ICS, les rôles des utilisateurs et les événements.
     *
     * Cette méthode génère et affiche l'emploi du temps en fonction des événements fournis dans les
     * données ICS. Elle ajuste également l'affichage en fonction du rôle de l'utilisateur connecté et
     * prend en compte si l'affichage doit se faire pour toute la journée ou non.
     *
     * @param array $ics_data    Les données ICS contenant les événements organisés par année et mois.
     * @param string $title      Le titre à afficher en haut de l'emploi du temps.
     * @param bool $allDay       Si true, seuls les événements couvrant toute la journée seront affichés.
     *
     * @return string Renvoie une chaîne HTML contenant l'emploi du temps ou un message s'il n'y a pas d'emploi du temps.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displaySchedule($ics_data, $title, $allDay) {
        $current_user = wp_get_current_user();
        $controller = new InformationController();
        $video_data = $controller->getVideoData();

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

        if (!empty($video_data['video_url'])) {
            $string .= '<button onclick="openModal()">Voir la vidéo</button>';

            // Construire le modal avec la vidéo
            $string .= $this->buildModal("Vidéo associée", '<iframe src="https://www.youtube.com/embed/5G6ianWzDNk" name="iframe_a" title="Iframe Example"></iframe>');
        }
        return $string;
    }




    /**
     * Génère le début de l'affichage de l'emploi du temps en tant que tableau HTML.
     *
     * Cette méthode crée le tableau HTML pour afficher l'emploi du temps, avec des colonnes
     * dif
     *
     * férentes en fonction des rôles de l'utilisateur. Si l'utilisateur n'est pas un technicien,
     * des colonnes supplémentaires sont ajoutées pour le cours et le groupe/enseignant.
     *
     * @param WP_User $current_user  L'utilisateur actuel, utilisé pour déterminer les colonnes à afficher.
     *
     * @return string  Chaîne HTML représentant le début de l'emploi du temps en tableau.
     *
     *
     * @version 1.0
     * @date 2024-10-15
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
     * Génère une chaîne HTML représentant la date dans un format localisé.
     *
     * Cette méthode retourne un titre HTML (`<h2>`) contenant la date complète (jour de la semaine, jour, mois)
     * formatée en fonction de la localisation de WordPress. La méthode ajoute un jour au jour fourni
     * pour calculer le jour de la semaine.
     *
     * @param int $day    Le jour du mois.
     * @param int $month  Le mois de l'année.
     * @param int $year   L'année.
     *
     * @return string  Chaîne HTML représentant la date formatée.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function giveDate($day, $month, $year) {
        $day_of_week = $day + 1;

        return '<h2>' . date_i18n('l j F', mktime(0, 0, 0, $month, $day_of_week, $year)) . '</h2>';
    }

    /**
     * Récupère le contenu d'un événement et génère une ligne de programme.
     *
     * Cette méthode vérifie si l'événement est actif pour le jour donné
     * et construit une représentation formatée de l'événement, incluant
     * sa durée, son emplacement et sa description. Si l'événement est en
     * cours, elle retourne les détails formatés. Sinon, elle retourne
     * `false`.
     *
     * @param array $event  Un tableau associatif représentant l'événement,
     *                      contenant les clés 'deb', 'fin', 'label', et 'description'.
     * @param int $day      Le jour du mois (optionnel). Par défaut, il s'agit du jour actuel.
     *
     * @return string|false  Une chaîne formatée représentant l'événement actif
     *                       ou `false` si l'événement n'est pas actif.
     *
     *
     * @version 1.0
     * @date 2024-10-15
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
            }        }

        if (substr($event['label'], -3) == "alt") {
            $label = substr($event['label'], 0, -3);
        } else {
            $label = $event['label'];
        }        $description = substr($event['description'], 0, -30);

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
     * Génère une ligne de tableau pour l'affichage d'un emploi du temps.
     *
     * Cette méthode crée une ligne dans un tableau HTML en fonction des données fournies.
     * Si l'événement est actif, elle applique une classe CSS spéciale pour le mettre en évidence.
     *
     * @param array $datas   Un tableau contenant les données à afficher dans les cellules du tableau.
     * @param bool $active   Un indicateur optionnel pour déterminer si la ligne doit être
     *                       marquée comme active (par défaut, `false`).
     *
     * @return string       Une chaîne HTML représentant la ligne du tableau.
     *
     *
     * @version 1.0
     * @date 2024-10-15
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
     * Génère le code HTML de fin pour l'affichage d'un emploi du temps.
     *
     * Cette méthode clôt le tableau HTML en fermant les balises `<tbody>` et `<table>`.
     *
     * @return string       Une chaîne HTML représentant la fin du tableau de l'emploi du temps.
     *
     *
     * @version 1.0
     * @date 2024-10-15
     */
    public function displayEndSchedule() {
        return '</tbody>
             </table>
          </div>';
    }

    /**
     * Affiche un message lorsque aucun emploi du temps n'est disponible pour l'utilisateur courant.
     *
     * Cette méthode vérifie les paramètres du thème pour déterminer si le message doit être affiché
     * pour les utilisateurs ayant le rôle 'television'. Si l'utilisateur ne possède pas de cours,
     * un message approprié est retourné.
     *
     * @param string $title        Le titre à afficher dans le message.
     * @param WP_User $current_user L'objet utilisateur courant.
     *
     * @return string|false       Une chaîne HTML contenant le message d'absence de cours,
     *                            ou false si aucun message ne doit être affiché.
     *
     *
     * @version 1.0
     * @date 2024-10-15
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
