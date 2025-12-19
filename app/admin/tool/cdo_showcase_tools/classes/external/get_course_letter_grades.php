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
 * External API for retrieving course letter grades.
 *
 * @package     tool_cdo_showcase_tools
 * @copyright   2024 Your Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_cdo_showcase_tools\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_value;
use core_external\external_warnings;
use context_course;
use moodle_exception;
use tool_cdo_showcase_tools\handlers\grade_handler;

/**
 * External function to get course letter grades by course ID.
 */
class get_course_letter_grades extends external_api {

    /**
     * Describes the parameters for get_course_letter_grades.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(
                PARAM_INT,
                'Course ID to retrieve letter grades for',
                VALUE_REQUIRED
            ),
            'userid' => new external_value(
                PARAM_INT,
                'User ID to filter grades for (optional, 0 for all users)',
                VALUE_DEFAULT,
                0
            ),
        ]);
    }

    /**
     * Get course letter grades by course ID.
     *
     * @param int $courseid The course ID
     * @param int $userid Optional user ID filter
     * @return array Array containing letter grades data and warnings
     * @throws moodle_exception
     */
    public static function execute(int $courseid, int $userid = 0): array {
        global $DB, $USER;

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'userid' => $userid,
        ]);

        $courseid = $params['courseid'];
        $userid = $params['userid'];
        $warnings = [];

        // Validate course exists.
        if (!$course = $DB->get_record('course', ['id' => $courseid])) {
            throw new moodle_exception('coursenotfound', 'tool_cdo_showcase_tools', '', $courseid);
        }

        // Check permissions.
        $context = context_course::instance($courseid);
        self::validate_context($context);

        // Check if user can view grades in this course.
        if (!has_capability('gradereport/grader:view', $context) && 
            !has_capability('moodle/grade:viewall', $context)) {
            
            // If not a teacher/admin, can only view own grades.
            if ($userid === 0 || $userid !== $USER->id) {
                throw new moodle_exception('nopermissiontoviewgrades', 'tool_cdo_showcase_tools');
            }
        }

        $lettergrades = [];

        try {
            $lettergrades = grade_handler::get_letter_grades_data($courseid);

        } catch (\Exception $e) {
            $warnings[] = [
                'item' => 'courseid',
                'itemid' => $courseid,
                'warningcode' => 'errorprocessingdata',
                'message' => get_string('errorprocessingdata', 'tool_cdo_showcase_tools', $e->getMessage()),
            ];
        }

        return [
            'gradescales' => $lettergrades,
            'courseid' => $courseid,
            'userid' => $userid,
            'warnings' => $warnings,
        ];
    }

    /**
     * Describes the return value for get_course_letter_grades.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'gradescales' => new external_multiple_structure(
                new external_single_structure([
                    'minimum' => new external_value(
                        PARAM_TEXT,
                        'Minimum percentage for this grade (e.g., "90,00 %")'
                    ),
                    'maximum' => new external_value(
                        PARAM_TEXT,
                        'Maximum percentage for this grade (e.g., "100,00 %")'
                    ),
                    'gradename' => new external_value(
                        PARAM_TEXT,
                        'Name of the grade (e.g., "Отлично", "Хорошо")'
                    ),
                  /*  'lettercode' => new external_value(
                        PARAM_TEXT,
                        'Letter code (A, B, C, D, F, etc.)',
                        VALUE_OPTIONAL
                    ),*/
                    'gradevalue' => new external_value(
                        PARAM_FLOAT,
                        'Numeric grade value for calculations',
                        VALUE_OPTIONAL
                    ),
                ])
            ),
            'courseid' => new external_value(
                PARAM_INT,
                'Course ID that was processed'
            ),
            'userid' => new external_value(
                PARAM_INT,
                'User ID filter (0 if all users)'
            ),
            'warnings' => new external_warnings(),
        ]);
    }
} 