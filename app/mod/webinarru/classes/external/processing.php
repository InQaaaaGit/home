<?php

require_once(dirname(__FILE__) . '/webinar.php');

class processing
{
    private static function get_exist_moduleinstance($id)
    {
        global $DB;

        return $DB->get_record('webinarru', array('id' => $id));
    }

    private static function get_token($moduleinstance, $exist_moduleinstance)
    {
        global $DB;

        $accounts = get_config('mod_webinarru', 'accounts');
        $accounts = json_decode($accounts);
        if ($accounts === null && json_last_error() !== JSON_ERROR_NONE) {
            return 'error_accounts';
        }

        foreach ($accounts as $account) {
            $start_of_event = $moduleinstance->webinar_date;
            $end_of_event = $start_of_event + $moduleinstance->webinar_duration;

            $sql = 'SELECT id, name, webinar_date, webinar_duration, webinar_token
        FROM {webinarru}
        WHERE (webinar_token = ? AND webinar_date + webinar_duration > ? AND webinar_date < ?)';

            if (!is_null($exist_moduleinstance)) {
                $sql .= ' AND NOT (webinar_token = ? AND webinar_date + webinar_duration > ? AND webinar_date < ?)';
            }

            if (!($DB->get_records_sql($sql, array($account->token, $start_of_event, $end_of_event, $exist_moduleinstance->webinar_token, $exist_moduleinstance->webinar_date, $exist_moduleinstance->webinar_date + $exist_moduleinstance->webinar_duration)))) {
                return $account->token;
            }
        }
        return 'error_tokens';
    }

    public static function create_webinar_event($moduleinstance, $course, $user)
    {
        $moduleinstance->name = self::get_webinar_event_name($moduleinstance, $course, $user);
        $moduleinstance->webinar_token = self::get_token($moduleinstance, null);

        if ($moduleinstance->webinar_token === 'error_accounts' || $moduleinstance->webinar_token === 'error_tokens') {
            return $moduleinstance->webinar_token;
        }
        $webinar_date = new DateTime();
        $webinar_date->setTimestamp($moduleinstance->webinar_date);
        $webinar_duration = self::get_webinar_duration($moduleinstance->webinar_duration);

        if (get_config('mod_webinarru', 'free_access') === '0') {
            $access = '10';
        } else {
            $access = '1';
        }
        $webinar_event = json_decode(
            webinar::createEvent(
                $moduleinstance->webinar_token,
                $moduleinstance->purpose,
                $webinar_date,
                $webinar_duration,
                $access,
                $moduleinstance->type
            )
        );

        if (is_null($webinar_event->id)) {
            return 'error_create_event';
        }

        $moduleinstance->webinar_id = $webinar_event->id;
        $moduleinstance->webinar_link = $webinar_event->link;

        $webinar_participants = self::get_webinar_participants($course, $user);
        $webinar_registered_participants = json_decode(webinar::registerParticipants($moduleinstance->webinar_token, $webinar_event->id, $webinar_participants), true);

        $moduleinstance->webinar_participants = json_encode(self::get_merge_participants_arrays($webinar_participants, $webinar_registered_participants));

        return $moduleinstance;
    }

    public static function update_webinar_event($moduleinstance, $course, $user)
    {
        $moduleinstance->name = self::get_webinar_event_name($moduleinstance, $course, $user);
        $exist_moduleinstance = self::get_exist_moduleinstance($moduleinstance->instance);
        $moduleinstance->webinar_token = self::get_token($moduleinstance, $exist_moduleinstance);

        if ($moduleinstance->webinar_token === 'error_accounts' || $moduleinstance->webinar_token === 'error_tokens') {
            return $moduleinstance->webinar_token;
        }
        $webinar_date = new DateTime();
        $webinar_date->setTimestamp($moduleinstance->webinar_date);
        $webinar_duration = self::get_webinar_duration($moduleinstance->webinar_duration);

        $changed = json_decode(webinar::changeEvent($moduleinstance->webinar_token, $moduleinstance->webinar_id, $moduleinstance->name, $webinar_date, $webinar_duration));
        if (!$changed) {
            return 'error_change_event';
        }

        $webinar_participants = self::get_webinar_participants($course, $user);
        $webinar_registered_participants = json_decode(webinar::registerParticipants($moduleinstance->webinar_token, $moduleinstance->webinar_id, $webinar_participants), true);

        $moduleinstance->webinar_participants = json_encode(self::get_merge_participants_arrays($webinar_participants, $webinar_registered_participants));

        return $moduleinstance;
    }

    public static function delete_webinar_event($moduleinstance)
    {
        return json_decode(webinar::deleteEvent($moduleinstance->webinar_token, $moduleinstance->webinar_id));
    }

    public static function get_webinar_link($moduleinstance, $user)
    {
        global $DB;

        $webinar_participants = json_decode($moduleinstance->webinar_participants);

        return $webinar_participants->{$user->id}->link;
    }

    private static function get_webinar_event_name($moduleinstance, $course, $user)
    {
        return $moduleinstance->purpose;
        //self::get_purpose_name($moduleinstance)
        /*self::get_discipline_name($course) .
        self::get_user_name($user) .
        self::get_date($moduleinstance)*/;
    }

    private static function get_purpose_name($moduleinstance)
    {
        if ($moduleinstance->purpose === 'credit') {
            return 'Зачет. ';
        } else if ($moduleinstance->purpose === 'exam') {
            return 'Экзамен. ';
        }
    }

    private static function get_discipline_name($course)
    {
        $pattern = '/(.*) (.*-.*-.*\/.*) \((.*),(.*)\)/u';
        preg_match($pattern, $course->fullname, $matches);

        if (!$matches) {
            return $course->fullname . ' ';
        } else {
            return $matches[1] . ' ';
        }
    }

    private static function get_user_name($user)
    {
        return '[' . $user->lastname . ' ' . mb_substr($user->firstname, 0, 1) . '.' . mb_substr($user->middlename, 0, 1) . '.] ';
    }

    private static function get_date($moduleinstance)
    {
        return '[' . date('d.m.Y H:i', $moduleinstance->webinar_date) . ']';
    }

    private static function get_webinar_duration($duration)
    {
        $hours = floor($duration / 3600);
        $duration = $duration % 3600;
        $minutes = floor($duration / 60);
        $secs = $duration % 60;

        return 'PT' . $hours . 'H' . $minutes . 'M' . $secs . 'S';
    }

    private static function get_webinar_participants($course, $user)
    {
        $context = context_course::instance($course->id);
        $participants = get_enrolled_users($context);
        $result = [];

        foreach ($participants as $participant) {
            if ($participant->id == $user->id) {
                $role = 'ADMIN';
            } else {
                $role = 'GUEST';
            }

            $result = $result + [
                    'users[' . $participant->id . '][email]' => $participant->email,
                    'users[' . $participant->id . '][secondName]' => $participant->lastname,
                    'users[' . $participant->id . '][name]' => $participant->firstname,
                    'users[' . $participant->id . '][pattrName]' => $participant->middlename,
                    'users[' . $participant->id . '][role]' => $role,
                    'users[' . $participant->id . '][isAutoEnter]' => true,
                    'users[' . $participant->id . '][isAccepted]' => true,
                ];
        }

        return $result;
    }

    private static function get_merge_participants_arrays($webinar_participants, $webinar_registered_participants)
    {
        $result = array();
        foreach ($webinar_participants as $key => $value) {
            preg_match('/users\[(\d+)\]\[(\w+)\]/', $key, $matches);
            $index = $matches[1];
            $field = $matches[2];
            $result[$index][$field] = $value;
        }

        $webinar_participants = $result;

        foreach ($webinar_registered_participants as $webinar_registered_participant) {
            foreach ($webinar_participants as $id => &$webinar_participant) {
                if ($webinar_registered_participant['email'] == $webinar_participant['email']) {
                    $webinar_participant = array_merge($webinar_participant, $webinar_registered_participant);
                    break;
                }
            }
        }

        return $webinar_participants;
    }
}
