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
 * CDO Notification block
 *
 * @package    block_cdo_notification
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_cdo_notification extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_cdo_notification');
        // Include our custom CSS.
        global $PAGE;

    }

    /**
     * @return stdClass|null
     * @throws \core\exception\coding_exception
     */
    public function get_content(): ?stdClass
    {
        global $PAGE;
        if ($this->content !== null) {
            return $this->content;
        }
        $PAGE->requires->css(new moodle_url('/blocks/cdo_notification/styles.css'));
        $render = $PAGE->get_renderer('block_cdo_notification', 'notifications');
        $html = $render->render(new \block_cdo_notification\output\notifications\renderable());
        $this->content = new stdClass;
        $this->content->text = $html;
        $this->content->footer = '';

        return $this->content;
    }

    public function instance_allow_multiple(): bool
    {
        return false;
    }

    public function has_config(): bool
    {
        return true;
    }

    public function applicable_formats(): array
    {
        return array('all' => true);
    }
} 