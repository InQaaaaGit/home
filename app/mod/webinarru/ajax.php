<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

require_login();

$action = required_param('action', PARAM_RAW);
$selected_date = required_param_array('webinar_date', PARAM_RAW);
$selected_duration = required_param('webinar_duration', PARAM_RAW);
$update_moduleinstance_id = required_param('update', PARAM_RAW);

if ($update_moduleinstance_id != 'null') { $moduleinstance = get_moduleinstance($update_moduleinstance_id); }

$selected_date = sprintf('%02d-%02d-%04d %02d:%02d:00', $selected_date['day'], $selected_date['month'], $selected_date['year'], $selected_date['hour'], $selected_date['minute']);
$selected_date = DateTime::createFromFormat('d-m-Y H:i:s', $selected_date);

$responseData = array(
    'params' => $_REQUEST
);

switch ($action) {
    case 'get_busy_time_information':
        $response['desc'] = get_busy_time_information_desc($selected_date, $selected_duration, $moduleinstance);
        $response['timeline'] = get_busy_time_information_timeline($selected_date, $selected_duration, $moduleinstance, $response['desc']['status']);
        echo json_encode($response);
        break;
    default:
        header('HTTP/1.0 400 Bad Request');
        echo 'Invalid action :)';
        break;
}

function get_busy_time_information_desc($selected_date, $selected_duration, $moduleinstance) {
    global $DB;

    $accounts = get_config('mod_webinarru', 'accounts');
    $accounts = json_decode($accounts);
    if ($accounts === null && json_last_error() !== JSON_ERROR_NONE) {
        $response['status'] = false;
        $response['data'] = get_string('ajax/desc_error_accounts', 'mod_webinarru');
        return $response;
    }

    foreach ($accounts as $account) {
        $start_of_event = new DateTime($selected_date->format('d-m-Y H:i:s'));
        $start_of_event = $start_of_event->getTimestamp();
        $end_of_event = $start_of_event + $selected_duration;

        $sql = 'SELECT id, name, webinar_date, webinar_duration, webinar_token
        FROM {webinarru}
        WHERE (webinar_token = ? AND webinar_date + webinar_duration > ? AND webinar_date < ?)';

        if (!is_null($moduleinstance)) {
            $sql .= ' AND NOT (webinar_token = ? AND webinar_date + webinar_duration > ? AND webinar_date < ?)';
        }

        if (!($DB->get_records_sql($sql, array($account->token, $start_of_event, $end_of_event, $moduleinstance->webinar_token, $moduleinstance->webinar_date, $moduleinstance->webinar_date + $moduleinstance->webinar_duration)))) {
            $response['status'] = true;
            $response['data'] = '<p style="color: rgba(83,194,99, 1)">' . get_string('ajax/desc_free_range', 'mod_webinarru') . '</p>
<p style="font-size: 12px">' . get_string('ajax/legend_selected_desc', 'mod_webinarru') . '</p>';
            return $response;
        }
    }
    $response['status'] = false;
    $response['data'] = '<p style="color: rgba(220, 53, 69, 1)">' . get_string('ajax/desc_busy_range', 'mod_webinarru') . '</p>
<p style="font-size: 12px">' . get_string('ajax/legend_selected_desc', 'mod_webinarru') . '</p>';
    return $response;
}

function get_busy_time_information_timeline($selected_date, $selected_duration, $moduleinstance, $selected_range_status) {
    global $DB;

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    $accounts = get_config('mod_webinarru', 'accounts');
    $accounts = json_decode($accounts);
    if ($accounts === null && json_last_error() !== JSON_ERROR_NONE) { return '<div class="error_message">' . get_string('ajax/error_accounts', 'mod_webinarru') . '</div>'; }
    //////////////////////////////////////////////////////////////////////////////////////////////////////

    $start_time = 0;
    $end_time = 24;

    $timeline = '<div class="timeline">';

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    $timeline .= '<div class="scale">';

    $timeline .= '<div class="hours">';
    for ($hour = $start_time; $hour <= $end_time; $hour++) {
        $percent = (100/$end_time)*$hour;
        $timeline .= '<div class="hour" style="left: ' . $percent . '%;"></div>';
    } $timeline .= '</div>';

    $timeline .= '<div class="minutes">';
    for ($hour = $start_time; $hour < $end_time; $hour++) {
        $percent = (100/$end_time)*($hour+0.5);
        $timeline .= '<div class="minute" style="left: ' . $percent . '%;"></div>';
    } $timeline .= '</div>';

    $timeline .= '<div class="markers">';
    for ($hour = $start_time; $hour <= $end_time; $hour++) {
        $percent = (100/$end_time)*$hour-2;
        $hour_padded = sprintf("%02d", $hour);
        $timeline .= '<div class="marker" style="left: ' . $percent . '%;">' . $hour_padded . ':00</div>';
    } $timeline .= '</div>';

    $timeline .= '</div>';

    //////////////////////////////////////////////////////////////////////////////////////////////////////

    $selected_day = new DateTime($selected_date->format('d-m-Y H:i:s'));
    $selected_day->modify('tomorrow -1 second'); $end_of_day = $selected_day->getTimestamp();
    $selected_day->modify('midnight'); $start_of_day = $selected_day->getTimestamp();
    $selected_day = $selected_day->getTimestamp();
    $one_day = $end_of_day - $start_of_day + 1;

    $sql = 'SELECT id, name, webinar_date, webinar_duration, webinar_token
        FROM {webinarru}
        WHERE webinar_date + webinar_duration > ? AND webinar_date < ?';

    $results = $DB->get_records_sql($sql, array($start_of_day, $end_of_day));

    $busy_ranges = array();

    foreach ($results as $result) {
        $token = $result->webinar_token;

        preg_match('/(.+)\s\[(.+)\]\s\[(.+)\]/u', $result->name, $matches);

        $event = array(
            'id' => $result->id,
            'name' => $result->name,
            'short_name' => $matches[1],
            'teacher_name' => $matches[2],
            'start_date_sec' => $result->webinar_date,
            'end_date_sec' => $result->webinar_date + $result->webinar_duration,
            'duration' => $result->webinar_duration
        );
        if (!isset($busy_ranges[$token])) {
            $busy_ranges[$token] = array();
        }
        $busy_ranges[$token][] = $event;
    }

    $timeline .= '<div class="busy_time">';
    foreach ($accounts as $account) {
        $accounts_num++;
        $timeline .= '<div class="webinar_token">';
        foreach ($busy_ranges as $webinar_token_value => $webinar_token){
            if ($account->token === $webinar_token_value) {
                foreach ($webinar_token as $webinar_event){
                    if (!is_null($moduleinstance)
                        && $moduleinstance->webinar_token === $webinar_token_value
                        && $moduleinstance->webinar_date === $webinar_event['start_date_sec']
                        && $moduleinstance->webinar_duration === $webinar_event['duration']) {
                        continue;
                    }

                    $left = ($webinar_event['start_date_sec'] - $selected_day)/$one_day * 100;
                    $width = ($webinar_event['end_date_sec'] - $webinar_event['start_date_sec'])/$one_day*100;
                    $start_date = date('d.m.Y H:i',$webinar_event['start_date_sec']);
                    $end_date = date('d.m.Y H:i',$webinar_event['end_date_sec']);
                    $timeline .= '<div class="webinar_event" style="left: ' . $left . '%; width: ' . $width . '%;"
                    data-info="' . $webinar_event['short_name'] . '&#10;&#10;' . get_string('ajax/label_teacher', 'mod_webinarru') . ': ' . $webinar_event['teacher_name'] . '&#10;' . get_string('ajax/label_start_of', 'mod_webinarru') . ': ' . $start_date . '&#10;' . get_string('ajax/label_end_of', 'mod_webinarru') . ': ' . $end_date . '"></div>';
                }
            }
        } $timeline .= '</div>';
    } $timeline .= '</div>';

    //////////////////////////////////////////////////////////////////////////////////////////////////////

    $selected_date = $selected_date->getTimestamp();
    $left = ($selected_date - $selected_day)/$one_day * 100;
    $width = $selected_duration/$one_day * 100;

    if ($selected_range_status) { $timeline .= '<div class="webinar_selected_range" id="webinar_selected_range_free" style="left: ' . $left . '%; width: ' . $width . '%;"></div>'; }
    else { $timeline .= '<div class="webinar_selected_range" id="webinar_selected_range_busy" style="left: ' . $left . '%; width: ' . $width . '%;"></div>'; }

    $timeline .= '</div>';
    $timeline .= '<div class="legends">
    <hr>
    <div class="legend_busy"><span class="marker"></span><span class="marker" style="background-color: rgba(103, 58, 183, 0.65);"></span><span class="legend_label">' . get_string('ajax/legend_busy', 'mod_webinarru') . $accounts_num . ' шт.)</span></div>
    <div class="legend_selected"><span class="marker" style="background-color: rgba(83,194,99, 0.65);"></span><span class="marker" style="background-color: rgba(220, 53, 69, 0.65);"></span><span class="legend_label">' . get_string('ajax/legend_selected', 'mod_webinarru') . '</span></div>';
//    $timeline .= '<div class="legend_selected_desc"><span class="legend_label_selected_desc">' . get_string('ajax/legend_selected_desc', 'mod_webinarru') . '</span></div>';
    $timeline .= '</div>';

    return $timeline;
}

function get_moduleinstance($id) {
    global $DB;

    $cource_module = get_coursemodule_from_id('webinarru', $id);
    return $DB->get_record('webinarru', array('id' => $cource_module->instance));
}
