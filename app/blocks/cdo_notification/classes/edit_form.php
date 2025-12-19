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
 * CDO Notification block edit form
 *
 * @package    block_cdo_notification
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/edit_form.php');

/**
 * CDO Notification block edit form class
 *
 * @package block_cdo_notification
 */
class block_cdo_notification_edit_form extends block_edit_form
{
    /**
     * Defines the form elements
     *
     * @param object $mform The form object
     */
    protected function specific_definition($mform)
    {
        // Section header
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Number of notifications to display
        $mform->addElement('advcheckbox', 'config_showcount', 
                          get_string('shownotificationscount', 'block_cdo_notification'),
                          get_string('shownotificationscount_desc', 'block_cdo_notification'));
        $mform->setDefault('config_showcount', 1);
        $mform->setType('config_showcount', PARAM_INT);

        // Maximum number of notifications to display
        $options = array();
        for ($i = 1; $i <= 20; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', 'config_maxnotifications', 
                          get_string('maxnotifications', 'block_cdo_notification'),
                          $options);
        $mform->setDefault('config_maxnotifications', 3);
        $mform->setType('config_maxnotifications', PARAM_INT);
        $mform->addHelpButton('config_maxnotifications', 'maxnotifications', 'block_cdo_notification');

        // Show notification date
        $mform->addElement('advcheckbox', 'config_showdate', 
                          get_string('shownotificationdate', 'block_cdo_notification'),
                          get_string('shownotificationdate_desc', 'block_cdo_notification'));
        $mform->setDefault('config_showdate', 1);
        $mform->setType('config_showdate', PARAM_INT);

        // Show "View all" link
        $mform->addElement('advcheckbox', 'config_showviewall', 
                          get_string('showviewalllink', 'block_cdo_notification'),
                          get_string('showviewalllink_desc', 'block_cdo_notification'));
        $mform->setDefault('config_showviewall', 1);
        $mform->setType('config_showviewall', PARAM_INT);

        // Auto-refresh interval (in seconds)
        $refreshoptions = array(
            0 => get_string('norefresh', 'block_cdo_notification'),
            30 => '30 ' . get_string('seconds', 'core'),
            60 => '1 ' . get_string('minute', 'core'),
            300 => '5 ' . get_string('minutes', 'core'),
            600 => '10 ' . get_string('minutes', 'core')
        );
        $mform->addElement('select', 'config_refreshinterval', 
                          get_string('refreshinterval', 'block_cdo_notification'),
                          $refreshoptions);
        $mform->setDefault('config_refreshinterval', 0);
        $mform->setType('config_refreshinterval', PARAM_INT);
        $mform->addHelpButton('config_refreshinterval', 'refreshinterval', 'block_cdo_notification');
    }

    /**
     * Validates the form data
     *
     * @param array $data The form data
     * @param array $files The uploaded files
     * @return array Array of errors
     */
    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        // Validate max notifications
        if (isset($data['config_maxnotifications']) && 
            ($data['config_maxnotifications'] < 1 || $data['config_maxnotifications'] > 20)) {
            $errors['config_maxnotifications'] = get_string('errormaxnotifications', 'block_cdo_notification');
        }

        // Validate refresh interval
        if (isset($data['config_refreshinterval']) && $data['config_refreshinterval'] < 0) {
            $errors['config_refreshinterval'] = get_string('errorrefreshinterval', 'block_cdo_notification');
        }

        return $errors;
    }
} 