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
 * Manager class for CDO Showcase Tools functionality.
 *
 * @package     tool_cdo_showcase_tools
 * @copyright   2024 Your Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_cdo_showcase_tools;

use tool_cdo_showcase_tools\handlers\grade_handler;

/**
 * Main manager class for the CDO Showcase Tools plugin.
 *
 * This class follows SOLID principles and provides a clean interface
 * for managing the plugin's core functionality.
 */
class manager {

    /** @var manager|null Singleton instance */
    private static ?manager $instance = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {
        // Initialize any required dependencies here.
    }

    /**
     * Get the singleton instance of the manager.
     *
     * @return manager
     */
    public static function get_instance(): manager {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check if user has permission to view the tool.
     *
     * @param \context $context The context to check permissions in.
     * @return bool True if user can view, false otherwise.
     */
    public function can_view(\context $context): bool {
        return has_capability('tool/cdo_showcase_tools:view', $context);
    }

    /**
     * Check if user has permission to manage the tool.
     *
     * @param \context $context The context to check permissions in.
     * @return bool True if user can manage, false otherwise.
     */
    public function can_manage(\context $context): bool {
        return has_capability('tool/cdo_showcase_tools:manage', $context);
    }

    /**
     * Get available tools configuration.
     *
     * @return array Array of available tools with their configurations.
     */
    public function get_available_tools(): array {
        return [
            'demo_tool' => [
                'name' => get_string('demo_tool', 'tool_cdo_showcase_tools', 'Demo Tool'),
                'description' => get_string('demo_tool_desc', 'tool_cdo_showcase_tools', 'A demonstration tool'),
                'enabled' => true,
            ],
            // Add more tools here as needed.
        ];
    }

    /**
     * Execute a specific tool action.
     *
     * @param string $toolname The name of the tool to execute.
     * @param array $params Parameters for the tool execution.
     * @return array Result of the tool execution.
     * @throws \moodle_exception If tool is not found or execution fails.
     */
    public function execute_tool(string $toolname, array $params = []): array {
        $tools = $this->get_available_tools();
        
        if (!isset($tools[$toolname])) {
            throw new \moodle_exception('toolnotfound', 'tool_cdo_showcase_tools', '', $toolname);
        }

        if (!$tools[$toolname]['enabled']) {
            throw new \moodle_exception('tooldisabled', 'tool_cdo_showcase_tools', '', $toolname);
        }

        // Implement tool-specific logic here.
        return [
            'success' => true,
            'message' => "Tool {$toolname} executed successfully",
            'data' => $params,
        ];
    }

    /**
     * Get letter grades for a course.
     *
     * @param int $courseid Course ID
     * @param int $userid User ID (0 for all users)
     * @return array Array of letter grades data
     * @throws \moodle_exception If course not found or permission denied
     */
    public function get_course_letter_grades(int $courseid, int $userid = 0): array {
        global $DB;

        // Validate course exists.
        if (!$course = $DB->get_record('course', ['id' => $courseid])) {
            throw new \moodle_exception('coursenotfound', 'tool_cdo_showcase_tools', '', $courseid);
        }

        // Check permissions.
        $context = \context_course::instance($courseid);
        if (!$this->can_view($context)) {
            throw new \moodle_exception('nopermissiontoviewgrades', 'tool_cdo_showcase_tools');
        }

        // TODO: Implement your custom letter grades retrieval logic here
        // This method should contain your business logic for:
        // 1. Accessing gradebook data
        // 2. Converting numeric grades to letter grades
        // 3. Filtering by user if specified
        // 4. Returning formatted data

        return grade_handler::get_letter_grades_data($courseid, $userid);
    }

    /**
     * Check if user can access course grades.
     *
     * @param int $courseid Course ID
     * @param int $userid User ID filter
     * @return bool True if user can access
     */
    public function can_access_course_grades(int $courseid, int $userid = 0): bool {
        return grade_handler::can_access_course_grades($courseid, $userid);
    }
} 