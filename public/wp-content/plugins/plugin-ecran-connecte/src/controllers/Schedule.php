<?php

namespace controllers;

/**
 * TODO : Ajouter les tags @author, @category, @license, @link et @package
 * Manage schedules,
 * For display a schedule we use R34ICS
 * Interface Schedule
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
