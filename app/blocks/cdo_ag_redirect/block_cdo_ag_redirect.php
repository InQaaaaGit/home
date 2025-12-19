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
 * AG Redirect block.
 *
 * @package    block_cdo_ag_redirect
 * @copyright  2022 InQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * AG Redirect block class.
 *
 * @package    block_cdo_ag_redirect
 * @copyright  2022 InQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_cdo_ag_redirect extends block_base
{
    /**
     * Component name constant.
     */
    const COMPONENT_NAME = 'block_cdo_ag_redirect';

    /**
     * Initialize the block.
     *
     * @throws coding_exception
     */
    public function init(): void
    {
        $this->title = get_string('pluginname', self::COMPONENT_NAME);
    }

    /**
     * Check if the block has configuration.
     *
     * @return bool
     */
    public function has_config(): bool
    {
        return false;
    }

    /**
     * Get the content of the block.
     *
     * @return stdClass
     */
    public function get_content(): stdClass
    {
        global $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Check if user needs registration using lib function.
        if (!block_cdo_ag_redirect_needs_registration($USER->id)) {
            return $this->content;
        }

        // Get redirect URL using lib function.
        $redirect_url = block_cdo_ag_redirect_get_url($USER->id);
        if (empty($redirect_url)) {
            return $this->content;
        }

        // Create redirect link.
        $html = html_writer::link(
            $redirect_url,
            get_string('redirect_name', self::COMPONENT_NAME),
            [
                'class' => 'btn btn-primary w-100',
                'target' => '_blank',
                'rel' => 'noopener noreferrer'
            ]
        );

        $this->content->text = $html;
        return $this->content;
    }

    /**
     * Get applicable formats for this block.
     *
     * @return array
     */
    public function applicable_formats(): array
    {
        return [
            'site' => true,
            'course' => true,
            'my' => true
        ];
    }

    /**
     * Check if the block can be added to the page.
     *
     * @return bool
     */
    public function user_can_addto($page): bool
    {
        return parent::user_can_addto($page) && has_capability('block/cdo_ag_redirect:addinstance', $page->context);
    }

    /**
     * Check if the block can be added to My Moodle page.
     *
     * @return bool
     */
    public function user_can_edit(): bool
    {
        return parent::user_can_edit() && has_capability('block/cdo_ag_redirect:myaddinstance', context_user::instance($this->page->context->instanceid));
    }
}