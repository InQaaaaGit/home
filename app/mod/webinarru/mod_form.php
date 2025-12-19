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
 * The main mod_webinarru configuration form.
 *
 * @package     mod_webinarru
 * @copyright   2023 Maks Grishin (CDO Global@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_webinarru_mod_form extends moodleform_mod {

    public function definition() {
        global $PAGE;

        $PAGE->requires->css('/mod/webinarru/styles.css');

        if (get_config('mod_webinarru', 'show_help') === '1') { \core\notification::info('<a class="btn btn-secondary btn-sm" href="' . get_config('mod_webinarru', 'url_help') . '" target="_blank"><b>' . get_string('mod_form/help_button', 'mod_webinarru') . '</b></a>'); }

        $mform = $this->_form;

        $purpose = [
            'credit' => get_string('mod_form/purpose/credit', 'mod_webinarru'),
            'exam' => get_string('mod_form/purpose/exam', 'mod_webinarru'),
        ];

        $types = [
            'webinar' => get_string('mod_form/type/webinar', 'mod_webinarru'),
            'meeting' => get_string('mod_form/type/meeting', 'mod_webinarru'),
            'training' => get_string('mod_form/type/training', 'mod_webinarru')
        ];

        $webinar_duration = [
            '900' => get_string('mod_form/webinar_duration/900', 'mod_webinarru'),
            '1800' => get_string('mod_form/webinar_duration/1800', 'mod_webinarru'),
            '2700' => get_string('mod_form/webinar_duration/2700', 'mod_webinarru'),
            '3600' => get_string('mod_form/webinar_duration/3600', 'mod_webinarru'),
            '5400' => get_string('mod_form/webinar_duration/5400', 'mod_webinarru'),
            '7200' => get_string('mod_form/webinar_duration/7200', 'mod_webinarru'),
            '9000' => get_string('mod_form/webinar_duration/9000', 'mod_webinarru'),
            '10800' => get_string('mod_form/webinar_duration/10800', 'mod_webinarru'),
        ];

        #$mform->addElement('select', 'purpose', get_string('mod_form/purpose', 'mod_webinarru'), $purpose);
        $mform->addElement('textarea', 'purpose', get_string('mod_form/purpose', 'mod_webinarru'));
        $mform->addElement('select', 'type', get_string('mod_form/type', 'mod_webinarru'), $types);
        $mform->addElement('date_time_selector', 'webinar_date', get_string('mod_form/webinar_date', 'mod_webinarru'));
        $mform->addElement('select', 'webinar_duration', get_string('mod_form/webinar_duration', 'mod_webinarru'), $webinar_duration);

        $busy_time_information_desc = '<div id="fitem_id_busy_time_information_desc" class="form-group row  fitem femptylabel">
<div class="col-md-3"><span class="col-form-label d-inline-block "></span><span class="float-sm-right text-nowrap"></span></div>
<div class="col-md-9 form-inline felement" data-fieldtype="static"><div id="id_busy_time_information_desc" class="form-control-static"></div><div class="form-control-feedback invalid-feedback" id="id_error_busy_time_information_desc"></div></div></div>';
        $mform->addElement('html', $busy_time_information_desc);

        $mform->addElement('header', 'check_free_time' , get_string('mod_form/check_free_time', 'mod_webinarru'));
        $mform->addElement('html', '<div class="busy_time_information"></div>');

        $accounts = get_config('mod_webinarru', 'accounts');
        $accounts = json_decode($accounts);
        if ($accounts === null && json_last_error() !== JSON_ERROR_NONE) { $accounts_exist = false; \core\notification::error(get_string('notification/error_accounts', 'mod_webinarru')); } else { $accounts_exist = true; }
        if (get_config('mod_webinarru', 'show_calendar') === '0') { $show_calendar = false; } else { $show_calendar = true; }

        $params = [
            'accounts_exist' => $accounts_exist,
            'show_calendar' => $show_calendar
        ];

        if ($accounts_exist) {
            if (get_config('mod_webinarru', 'show_selected_date') === '0') { $show_selected_date_state = ''; } else { $show_selected_date_state = 'checked'; }
            $checkbox_show_selected_date = '<label id="show_selected_date"><input type="checkbox" class="form-check-input" ' . $show_selected_date_state . ' id="id_show_selected_date">' . get_string('mod_form/show_selected_date', 'mod_webinarru') . '</label>';
            $mform->addElement('html', $checkbox_show_selected_date);
        }

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();

        //$mform->addElement('html', '<p style="padding: 0px 20px 5px 0px;text-align:right; color:rgba(255, 255, 255, 1); font-size:14px;">' . get_string('mod_form/designed_by', 'mod_webinarru') . '</p>');

        if (get_config('mod_webinarru', 'change_submit_buttons') === '0') {
            $params['submitbutton1'] = get_string('savechangesanddisplay');
            $params['submitbutton2'] = get_string('savechangesandreturntocourse');
        } else {
            $params['submitbutton1'] = get_string('mod_form/submitbutton1', 'mod_webinarru');
            $params['submitbutton2'] = get_string('mod_form/submitbutton2', 'mod_webinarru');
        }

        if (get_config('mod_webinarru', 'disable_tags') === '0') { $params['disable_tags'] = false; } else { $params['disable_tags'] = true; }

        $params['update'] = optional_param('update', null, PARAM_RAW);

        //////////////////////////////////////////////////////////////////////////////////////////////////////
        $PAGE->requires->yui_module('moodle-mod_webinarru-check_settings', 'M.mod_webinarru.check_settings.init', array($params));
        $PAGE->requires->yui_module('moodle-mod_webinarru-get_busy_time_information', 'M.mod_webinarru.get_busy_time_information.init', array($params));
    }
}
