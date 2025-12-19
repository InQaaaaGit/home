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
 * Notification manager class
 *
 * @package    block_cdo_notification
 * @copyright  2024 Your Name <your.email@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_cdo_notification;

use coding_exception;
use context_system;
use dml_exception;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Class notification_manager
 *
 * @package block_cdo_notification
 */
class notification_manager
{
    private $db;
    private $context;
    /**
     * @var false|mixed|object|\stdClass
     */
    private mixed $user;

    /**
     * @throws dml_exception
     */
    public function __construct()
    {
        global $DB, $USER;
        $this->user = $USER;
        $this->db = $DB;
        $this->context = context_system::instance();
    }

    /**
     * @throws cdo_type_response_exception
     * @throws cdo_config_exception
     * @throws coding_exception
     */
    public function get_active_notifications(): array
    {

        $options = di::get_instance()->get_request_options();
        $options->set_properties(
            [
                'user_id' => $this->user->id,
            ]
        );

        $request = di::get_instance()->get_request('get_user_notification')->request($options);
        $data = $request->get_request_result()->to_array();
        return $data;

    }

    /**
     * @throws cdo_type_response_exception
     * @throws cdo_config_exception
     */
    public function get_concrete_notifications(string $id): array
    {

        $options = di::get_instance()->get_request_options();
        $options->set_properties(
            [
                'user_id' => $this->user->id,
                'id' => $id
            ]
        );

        $request = di::get_instance()->get_request('get_user_notification')->request($options);
        $data = $request->get_request_result()->to_array();
        return $data;

    }
} 