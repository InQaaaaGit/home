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
 * Web services definition for the CDO Showcase Tools plugin.
 *
 * @package     tool_cdo_showcase_tools
 * @copyright   2024 Your Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Define the web service functions.
$functions = [
    'tool_cdo_showcase_tools_get_course_letter_grades' => [
        'classname' => 'tool_cdo_showcase_tools\external\get_course_letter_grades',
        'methodname' => 'execute',
        'description' => 'Get letter grades for a specific course',
        'type' => 'read',
        'ajax' => true,
      //  'capabilities' => 'tool/cdo_showcase_tools:view',
    ],
    'tool_cdo_showcase_tools_get_users_courses' => [
        'classname' => 'tool_cdo_showcase_tools\external\courses',
        'methodname' => 'get_users_courses',
        'description' => 'Get user courses with teachers and enrolled users',
        'type' => 'read',
        'ajax' => true,
     //   'capabilities' => 'tool/cdo_showcase_tools:view',
    ],
    'tool_cdo_showcase_tools_get_courses' => [
        'classname' => 'tool_cdo_showcase_tools\external\courses',
        'methodname' => 'get_courses',
        'description' => 'Get courses with extended information (teachers, users, grades)',
        'type' => 'read',
        'ajax' => true,
    //    'capabilities' => 'tool/cdo_showcase_tools:view',
    ],
    'tool_cdo_showcase_tools_get_grade_items' => [
        'classname' => 'tool_cdo_showcase_tools\external\gradereport',
        'methodname' => 'get_grade_items',
        'description' => 'Get grade items for course with enhanced information',
        'type' => 'read',
        'ajax' => true,
    //    'capabilities' => 'tool/cdo_showcase_tools:view',
    ],
    'tool_cdo_showcase_tools_get_courses_by_category' => [
        'classname' => 'tool_cdo_showcase_tools\external\courses',
        'methodname' => 'get_courses_by_category',
        'description' => 'Get courses from specific category with extended information (teachers, users, grades)',
        'type' => 'read',
        'ajax' => true,
    //    'capabilities' => 'tool/cdo_showcase_tools:view',
    ],
];

// Define external services.
$services = [
    'CDO Showcase Tools Service' => [
        'functions' => [
            'core_webservice_get_site_info',
            'core_course_get_courses',
            'tool_cdo_showcase_tools_get_course_letter_grades',
            'tool_cdo_showcase_tools_get_users_courses',
            'tool_cdo_showcase_tools_get_courses',
            'tool_cdo_showcase_tools_get_grade_items',
            'tool_cdo_showcase_tools_get_courses_by_category',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'cdo_showcase_tools_service',
        'downloadfiles' => 0,
        'uploadfiles' => 0,
    ],
]; 