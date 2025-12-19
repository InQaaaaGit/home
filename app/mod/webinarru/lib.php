<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants.
 *
 * @package     mod_webinarru
 * @copyright   2023 Maks Grishin (CDO Global@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/classes/external/processing.php');

function webinarru_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

function webinarru_add_instance($moduleinstance, $mform = null) {
    global $DB, $PAGE, $USER;

    $course = $DB->get_record('course', array('id' => $moduleinstance->course));

    $moduleinstance->timecreated = time();

    /* Создать видеоконференцию и изменить данные в экземпляре модуля,
       если создать видеоконференцию невозможно (выбранная дата не свободна),
       то метод возращает null и, при дальнейшей проверке, данные либо записываются в базу данных,
       либо пользователь перенаправляется обратно на страницу создания экземпляра модуля с предупреждением. */

    $moduleinstance = processing::create_webinar_event($moduleinstance, $course, $USER);

    if ($moduleinstance === 'error_accounts') {
        $warningmsg = get_string('notification/error_accounts', 'mod_webinarru');
        redirect($PAGE->url, $warningmsg, null, \core\output\notification::NOTIFY_ERROR);
    }
    else if ($moduleinstance === 'error_tokens') {
        $warningmsg = get_string('notification/error_tokens', 'mod_webinarru');
        redirect($PAGE->url, $warningmsg, null, \core\output\notification::NOTIFY_WARNING);
    }
    else if ($moduleinstance === 'error_create_event') {
        $warningmsg = get_string('notification/error_create_event', 'mod_webinarru');
        redirect($PAGE->url, $warningmsg, null, \core\output\notification::NOTIFY_WARNING);
    }

    $id = $DB->insert_record('webinarru', $moduleinstance);

    return $id;
}

function webinarru_update_instance($moduleinstance, $mform = null) {
    global $DB, $PAGE, $USER;

    $course = $DB->get_record('course', array('id' => $moduleinstance->course));

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    $moduleinstance->webinar_token = $DB->get_record('webinarru', array('id' => $moduleinstance->id))->webinar_token;
    $moduleinstance->webinar_id = $DB->get_record('webinarru', array('id' => $moduleinstance->id))->webinar_id;

    $moduleinstance = processing::update_webinar_event($moduleinstance, $course, $USER);

    if ($moduleinstance === 'error_accounts') {
        $warningmsg = get_string('notification/error_accounts', 'mod_webinarru');
        redirect($PAGE->url, $warningmsg, null, \core\output\notification::NOTIFY_ERROR);
    }
    else if ($moduleinstance === 'error_tokens') {
        $warningmsg = get_string('notification/error_tokens', 'mod_webinarru');
        redirect($PAGE->url, $warningmsg, null, \core\output\notification::NOTIFY_WARNING);
    }
    else if ($moduleinstance === 'error_change_event') {
        $warningmsg = get_string('notification/error_change_event', 'mod_webinarru');
        redirect($PAGE->url, $warningmsg, null, \core\output\notification::NOTIFY_WARNING);
    }

    return $DB->update_record('webinarru', $moduleinstance);
}

function webinarru_delete_instance($id) {
    global $DB;

    $moduleinstance = $DB->get_record('webinarru', array('id' => $id));

    if (!$moduleinstance) { return false; }

    processing::delete_webinar_event($moduleinstance); // Стоит ли проверять была ли удалена видеоконференция на mts-link.ru?

    $DB->delete_records('webinarru', array('id' => $id));

    return true;
}
