<?php
namespace block_cdo_notification\event;

defined('MOODLE_INTERNAL') || die();

class notification_viewed extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'r'; // read
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'block_cdo_notification'; // или имя таблицы, где хранятся уведомления
        $this->data['other'] = [
            'source' => 'userpanel'
        ]; // или имя таблицы, где хранятся уведомления

    }

    public static function get_name() {
        return get_string('eventnotificationviewed', 'block_cdo_notification');
    }

    public function get_description() {
        return "Пользователь с id {$this->userid} просмотрел уведомление с id {$this->objectid}.";
    }

    public function get_url() {
        return new \moodle_url('/blocks/cdo_notification/index.php', ['id' => $this->objectid]);
    }
} 