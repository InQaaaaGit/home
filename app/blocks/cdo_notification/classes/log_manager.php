<?php
namespace block_cdo_notification;

global $CFG;
require_once($CFG->libdir.'/dml/moodle_database.php');

class log_manager {
    public static function log_action($userid, $action, $ip = null, $mac = null) {
        global $DB;
        if (!$userid) return;
        $record = new \stdClass();
        $record->userid = $userid;
        $record->ip = $ip ?? self::get_user_ip();
        $record->mac = $mac;
        $record->action = $action;
        $record->timecreated = time();
        $DB->insert_record('block_cdo_notification_log', $record);
    }

    public static function get_user_ip() {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    // MAC-адрес получить с сервера невозможно, только если клиент сам передаст (например, через JS и WebRTC, но это не стандартно и не безопасно)
} 