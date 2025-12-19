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
 * Grade handler for processing letter grades data.
 *
 * @package     tool_cdo_showcase_tools
 * @copyright   2024 Your Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_cdo_showcase_tools\handlers;

use context_course;
use moodle_exception;

/**
 * Handler class for grade processing operations.
 */
class grade_handler {

    /**
     * Get letter grades data for a course.
     *
     * @param int $courseid Course ID
     * @param int $userid User ID (0 for all users)
     * @return array Processed letter grades data
     * @throws moodle_exception
     */
    public static function get_letter_grades_data(int $courseid, int $userid = 0): array {
        global $CFG, $DB;
        
        require_once($CFG->libdir . '/gradelib.php');
        
        // Validate course exists.
        if (!$DB->get_record('course', ['id' => $courseid])) {
            throw new moodle_exception('coursenotfound', 'tool_cdo_showcase_tools', '', $courseid);
        }
        
        $context = context_course::instance($courseid);
        $letters = grade_get_letters($context);
        
        $gradescales = [];
        $max = 100;
        
        foreach ($letters as $boundary => $letter) {
            $min = $boundary;
            
            $gradescales[] = [
                'minimum' => number_format($min, 2, ',', ' ') . ' %',
                'maximum' => number_format($max, 2, ',', ' ') . ' %',
                'gradename' => $letter,
                'lettercode' => self::convert_to_letter_code($letter),
                'gradevalue' => ($min + $max) / 2, // Average for calculations
            ];
            
            $max = $boundary - 0.01;
        }

        return $gradescales;
    }

    /**
     * Convert grade name to letter code.
     *
     * @param string $gradename Grade name
     * @return string Letter code
     */
    public static function convert_to_letter_code(string $gradename): string {
        // Map common Russian grade names to letter codes
        $mapping = [
            'Отлично' => 'A',
            'Хорошо' => 'B', 
            'Удовлетворительно' => 'C',
            'УДВ' => 'C',
            'Зачет' => 'P',
            'Незачет' => 'F',
            'Неудовлетворительно' => 'F',
        ];
        
        // Return mapped value or first character of grade name
        return $mapping[$gradename] ?? strtoupper(substr($gradename, 0, 1));
    }



    /**
     * Validate course access permissions.
     *
     * @param int $courseid Course ID
     * @param int $userid User ID (0 for current user)
     * @return bool True if user has access
     */
    public static function can_access_course_grades(int $courseid, int $userid = 0): bool {
        global $USER;
        
        $context = context_course::instance($courseid);
        
        // Check if user can view grades in this course
        if (has_capability('gradereport/grader:view', $context) || 
            has_capability('moodle/grade:viewall', $context)) {
            return true;
        }
        
        // If not a teacher/admin, can only view own grades
        if ($userid === 0 || $userid === $USER->id) {
            return has_capability('moodle/grade:view', $context);
        }
        
        return false;
    }
}