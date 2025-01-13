<?php
/**
 * Fichier ScheduleManager.php
 *
 * Ce fichier contient la classe `ScheduleManager`, qui gère les horaires.
 * Pour afficher un emploi du temps, elle utilise R34ICS, une interface pour
 * récupérer et afficher les informations de l'emploi du temps.
 *
 * PHP version 7.4 or later
 *
 * @category API
 * @package  Controllers
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/ScheduleManager
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace controllers;

/**
 * Class ScheduleManager
 *
 * Gère les horaires. Pour afficher un emploi du temps, elle utilise R34ICS.
 * L'interface Schedule permet de récupérer et d'afficher les informations
 * de l'emploi du temps.
 *
 * @category API
 * @package  Controllers
 * @author   John Doe <johndoe@example.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/ScheduleManager Documentation de
 * la classe
 * @since    2025-01-07
 */
interface Schedule
{
    /**
     * Display the schedule of the code
     *
     * @param $code   int Code ADE
     * @param $allDay bool
     *
     * @return void
     */
    public function displaySchedule($code, $allDay);

    /**
     * Display the schedule of the current user
     *
     * @return mixed
     */
    public function displayMySchedule();
}
