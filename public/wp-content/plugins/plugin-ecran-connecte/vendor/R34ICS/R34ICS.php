<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 16/04/2019
 * Time: 15:08
 */

use controllers\Controller;
use models\CodeAde;
use views\ICSView;

if (!defined('ABSPATH')) {
    exit;
}


class R34ICS extends Controller
{

    var $ical_path = '/vendor/johngrogg/ics-parser/src/ICal/ICal.php';
    var $event_path = '/vendor/johngrogg/ics-parser/src/ICal/Event.php';
    var $parser_loaded = false;
    var $limit_days = 365;

    /**
     * Vue de R34ICS
     * @var ICSView
     */
    private $_view;

    public function __construct()
    {
        // Set property values
        $this->ical_path = dirname(__FILE__) . $this->ical_path;
        $this->event_path = dirname(__FILE__) . $this->event_path;
        $this->_view = new ICSView();
    }

    public function days_of_week($format = null)
    {
        $days_of_week = array();
        switch ($format) {
            case 'min':
                $days_of_week = array(
                    0 => __('Su', 'r34ics'),
                    1 => __('M', 'r34ics'),
                    2 => __('Tu', 'r34ics'),
                    3 => __('W', 'r34ics'),
                    4 => __('Th', 'r34ics'),
                    5 => __('F', 'r34ics'),
                    6 => __('Sa', 'r34ics'),
                );
                break;
            case 'short':
                $days_of_week = array(
                    0 => __('Sun', 'r34ics'),
                    1 => __('Mon', 'r34ics'),
                    2 => __('Tue', 'r34ics'),
                    3 => __('Wed', 'r34ics'),
                    4 => __('Thu', 'r34ics'),
                    5 => __('Fri', 'r34ics'),
                    6 => __('Sat', 'r34ics'),
                );
                break;
            default:
                $days_of_week = array(
                    0 => __('Sunday', 'r34ics'),
                    1 => __('Monday', 'r34ics'),
                    2 => __('Tuesday', 'r34ics'),
                    3 => __('Wednesday', 'r34ics'),
                    4 => __('Thursday', 'r34ics'),
                    5 => __('Friday', 'r34ics'),
                    6 => __('Saturday', 'r34ics'),
                );
                break;
        }
        return $days_of_week;
    }

    /**
     * Lit un fichier ICS et renvoie des données parsées
     *
     * @param $ics_url      string url du fichier ICS
     * @param $code         int Code ADE relié à l'url
     * @param array $args Array d'option
     *
     * @return array
     */
    public function get_event_data($ics_url, $code, $allDay, $args = array()) {
        // Get ICS file, from transient if possible
        $transient_name = __METHOD__ . '_' . sha1($ics_url);
        $ics_contents = null;

        // Some servers (e.g. Airbnb) will require a user_agent string or return 403 Forbidden
        ini_set('user_agent', 'ICS Calendar for WordPress');
        if (file_exists($ics_url) && file_get_contents($ics_url) != '') {
            $ics_contents = file_get_contents($ics_url);
        } elseif (strlen($ics_contents) > 150) {
            set_transient($transient_name, $ics_contents, 600);
        } else {
            $this->addFile($code);
            if (!file_exists($ics_url) || filesize($ics_url) <= 0) {
                $this->addLogEvent("Le fichier n'a pas réussit à être lu url: " . $ics_url);
            }
        }

        // Parse ICS contents
        $ics_data = array();
        if (!$this->parser_loaded) {
            $this->parser_loaded = $this->_load_parser();
        }
        $ICal = new ICal\ICal;
        $ICal->initString($ics_contents);
        $ics_data['title'] = $args['title'] ?? $ICal->calendarName();
        $ics_data['description'] = $args['description'] ?? $ICal->calendarDescription();

        // Process events
        if ($ics_events = $ICal->events()) {
            // Assemble events
            foreach ($ics_events as $event) {
                // Get the start date and time
                // All-day events
                if (strlen($event->dtstart) == 8) {
                    $dtstart_date = substr($event->dtstart, 0, 8);
                    $dtend_date = substr($event->dtend, 0, 8);
                    $all_day = true;
                } else {
                    // Workaround for time zone data breaking the _tz values returned by ICS Parser
                    // @todo This workaround may need to be removed if a future update of ICS Parser fixes this bug
                    // If event's time zone appears in $event->dtstart_array[0]; the start and end times are correct, and $event->dtstart_tz overcompensates
                    if (isset($event->dtstart_array[0])) {
                        $dtstart_date = substr($event->dtstart, 0, 8);
                        $dtstart_time = substr($event->dtstart, 9, 6);
                        $dtend_date = substr($event->dtend, 0, 8);
                        $dtend_time = substr($event->dtend, 9, 6);
                    } // No time zone in $event->dtstart_array[0]; ICS Parser treats as GMT and $event->dtstart_tz is the correct value
                    else {
                        // $event->dtstart_tz matches $event->dtstart_array[1]; assume time zone is completely absent and adjust for local time
                        if ($event->dtstart_array[1] == $event->dtstart_tz . 'Z') {
                            $dtstart_gmt = strtotime($event->dtstart_tz) + (get_option('gmt_offset') * 3600);
                            $dtstart_date = date_i18n('Ymd', $dtstart_gmt);
                            $dtstart_time = date_i18n('His', $dtstart_gmt);
                            $dtend_gmt = strtotime($event->dtend_tz) + (get_option('gmt_offset') * 3600);
                            $dtend_date = date_i18n('Ymd', $dtend_gmt);
                            $dtend_time = date_i18n('His', $dtend_gmt);
                        } // ICS Parser adjusts, and $event->dtstart_tz is the correct local value
                        else {
                            $dtstart_date = substr($event->dtstart_tz, 0, 8);
                            $dtstart_time = substr($event->dtstart_tz, 9, 6);
                            $dtend_date = substr($event->dtend_tz, 0, 8);
                            $dtend_time = substr($event->dtend_tz, 9, 6);
                        }
                    }
                    $all_day = false;
                }

                // Add event to output array if start day less than 30 days old
                $thirty_days_ago = date_i18n('Ymd', strtotime('-30 days'));
                if ($dtstart_date >= $thirty_days_ago) {
                    // Events with different start and end dates
                    if ($dtend_date != $dtstart_date) {
                        $loop_date = $dtstart_date;
                        while ($loop_date <= $dtend_date) {
                            // Classified as an all-day event and we've hit the end date -- don't display
                            if ($all_day && $loop_date == $dtend_date) {
                                break;
                            }
                            // Classified as an all-day event, or we're in the middle of the range -- treat as regular all-day event
                            if ($all_day || ($loop_date != $dtstart_date && $loop_date != $dtend_date)) {
                                $ics_data['events'][$loop_date]['all-day'][] = array(
                                    'label' => @$event->summary,
                                    'description' => @$event->description,
                                    'location' => @$event->location,
                                    'deb' => @$event->dtstart,
                                    'fin' => @$event->dtend,
                                );
                            } // First date in range -- treat as all-day but also show start time
                            elseif ($loop_date == $dtstart_date) {
                                $ics_data['events'][$loop_date]['t' . $dtstart_time][] = array(
                                    'label' => @$event->summary,
                                    'description' => @$event->description,
                                    'location' => @$event->location,
                                    'deb' => @$event->dtstart,
                                    'fin' => @$event->dtend,
                                    'start' => date_i18n(
                                        get_option('time_format'),
                                        strtotime($dtstart_date . ' ' . $dtstart_time)
                                    ),
                                );
                            }
                        }
                    } // All-day events
                    elseif ($all_day) {
                        $ics_data['events'][$dtstart_date]['all-day'][] = array(
                            'label' => @$event->summary,
                            'description' => @$event->description,
                            'location' => @$event->location,
                            'deb' => @$event->dtstart,
                            'fin' => @$event->dtend,
                        );
                    } // Events with start/end times
                    else {
                        $ics_data['events'][$dtstart_date]['t' . $dtstart_time][] = array(
                            'label' => @$event->summary,
                            'description' => @$event->description,
                            'location' => @$event->location,
                            'deb' => @$event->dtstart,
                            'fin' => @$event->dtend,
                            'start' => date_i18n(
                                get_option('time_format'),
                                strtotime($dtstart_date . ' ' . $dtstart_time)
                            ),
                            'end' => date_i18n(
                                get_option('time_format'),
                                strtotime($dtend_date . ' ' . $dtend_date)
                            ),
                        );
                    }
                }
            }
        }


        if(isset($ics_data['events'])) {
            // Sort events and remove out-of-range dates
            foreach (array_keys((array)$ics_data['events']) as $date) {
                $first_date = $args['first_date'] ?? date_i18n('Ymd');
                $limit_date = date_i18n('Ymd', mktime(0, 0, 0, date_i18n('n'), date_i18n('j') + $this->limit_days, date_i18n('Y')));
                if ($date < $first_date || $date > $limit_date) {
                    unset($ics_data['events'][$date]);
                } else {
                    ksort($ics_data['events'][$date]);
                }
            }
            if (isset($ics_data['events'])) {
                ksort($ics_data['events']);
            }

            // Split events into year/month/day groupings and determine earliest and latest dates along the way
            foreach ((array)$ics_data['events'] as $date => $events) {
                $year = substr($date, 0, 4);
                $month = substr($date, 4, 2);
                $day = substr($date, 6, 2);
                $ym = substr($date, 0, 6);
                $ics_data['events'][$year][$month][$day] = $events;
                unset($ics_data['events'][$date]);
                if (empty($ics_data['earliest']) || $ym < $ics_data['earliest']) {
                    $ics_data['earliest'] = $ym;
                }
                if (empty($ics_data['latest']) || $ym > $ics_data['latest']) {
                    $ics_data['latest'] = $ym;
                }
            }
        }


        // Override defaults with inputs
        if (isset($args['title'])) {
            $ics_data['title'] = ($args['title'] == 'none') ? false : $args['title'];
        }
        if (isset($args['description'])) {
            $ics_data['description'] = ($args['description'] == 'none') ? false : $args['description'];
        }


        return array($ics_data, $allDay);
    }

    public function first_dow($date = null)
    {
        if (empty($date)) {
            $date = current_time();
        }
        return date_i18n('w', mktime(0, 0, 0, date_i18n('n', $date), 1, date_i18n('Y', $date)));
    }

    public function get_days_of_week($format = null)
    {
        $days_of_week = $this->days_of_week($format);
        // Shift sequence of days based on site configuration
        $start_of_week = get_option('start_of_week', 0);
        for ($i = 0; $i < $start_of_week; $i++) {
            $day = $days_of_week[$i];
            unset($days_of_week[$i]);
            $days_of_week[$i] = $day;
        }
        return $days_of_week;
    }

    private function _load_parser()
    {
        include_once($this->ical_path);
        include_once($this->event_path);
        return true;
    }

}
