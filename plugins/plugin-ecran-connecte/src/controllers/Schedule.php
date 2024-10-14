<?php

namespace Controllers;

/**
 * Interface Schedule
 *
 * Cette interface définit les méthodes nécessaires pour gérer les horaires.
 * Pour afficher un horaire, nous utilisons R34ICS.
 */
interface Schedule
{
    /**
     * Affiche l'horaire du code spécifié.
     *
     * @param int $code Code ADE pour lequel afficher l'horaire.
     * @param bool $allDay Indique si l'horaire doit être affiché sur toute la journée.
     * @return void
     *
     * Exemple d'utilisation :
     * $schedule->displaySchedule(12345, true);
     */
    public function displaySchedule($code, $allDay);

    /**
     * Affiche l'horaire de l'utilisateur courant.
     *
     * @return mixed Retourne l'horaire de l'utilisateur courant.
     *
     * Exemple d'utilisation :
     * $mySchedule = $schedule->displayMySchedule();
     */
    public function displayMySchedule();
}
