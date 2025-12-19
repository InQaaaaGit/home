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
 * Debug script to test grade letter retrieval from Moodle.
 *
 * @package     tool_cdo_showcase_tools
 * @copyright   2024 Your Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/gradelib.php');

use tool_cdo_showcase_tools\handlers\grade_handler;

// Ensure user is logged in and has proper permissions.
require_login();
$context = context_system::instance();
require_capability('tool/cdo_showcase_tools:view', $context);

// Set up the page.
$PAGE->set_url('/admin/tool/cdo_showcase_tools/debug_grades.php');
$PAGE->set_context($context);
$PAGE->set_title('Debug Grade Letters');
$PAGE->set_heading('Debug Grade Letters');
$PAGE->set_pagelayout('admin');

// Get course ID from parameter.
$courseid = optional_param('courseid', 1, PARAM_INT);

// Output page header.
echo $OUTPUT->header();
echo $OUTPUT->heading('Debug Grade Letters for Course ID: ' . $courseid);

try {
    $course_context = context_course::instance($courseid);
    $letters = grade_get_letters($course_context);
    
    echo html_writer::tag('h3', 'Raw grade_get_letters() output:');
    echo html_writer::start_tag('pre');
    echo htmlspecialchars(print_r($letters, true));
    echo html_writer::end_tag('pre');
    
    echo html_writer::tag('h3', 'Processed format (via grade_handler):');
    
    $gradescales = grade_handler::get_letter_grades_data($courseid);
    
    echo html_writer::start_tag('table', ['class' => 'table table-striped']);
    echo html_writer::start_tag('thead');
    echo html_writer::start_tag('tr');
    echo html_writer::tag('th', 'Minimum');
    echo html_writer::tag('th', 'Maximum');
    echo html_writer::tag('th', 'Grade Name');
    echo html_writer::tag('th', 'Letter Code');
    echo html_writer::tag('th', 'Grade Value');
    echo html_writer::end_tag('tr');
    echo html_writer::end_tag('thead');
    echo html_writer::start_tag('tbody');
    
    foreach ($gradescales as $scale) {
        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', $scale['minimum']);
        echo html_writer::tag('td', $scale['maximum']);
        echo html_writer::tag('td', $scale['gradename']);
        echo html_writer::tag('td', $scale['lettercode']);
        echo html_writer::tag('td', number_format($scale['gradevalue'], 2));
        echo html_writer::end_tag('tr');
    }
    
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
    
    echo html_writer::tag('h3', 'JSON output:');
    echo html_writer::start_tag('pre');
    echo htmlspecialchars(json_encode([
        'gradescales' => $gradescales,
        'courseid' => $courseid,
        'userid' => 0,
        'warnings' => []
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo html_writer::end_tag('pre');
    
} catch (Exception $e) {
    echo html_writer::div('Error: ' . $e->getMessage(), 'alert alert-danger');
}

// Add test for existing external functions
echo html_writer::tag('h3', 'Test Other External Functions:');

echo html_writer::tag('h4', 'Available External Functions:');
echo html_writer::start_tag('ul', ['class' => 'list-group']);

$functions = [
    'tool_cdo_showcase_tools_get_course_letter_grades' => 'Get course letter grades',
    'tool_cdo_showcase_tools_get_users_courses' => 'Get user courses with teachers and enrolled users',
    'tool_cdo_showcase_tools_get_courses' => 'Get courses with extended information',
    'tool_cdo_showcase_tools_get_grade_items' => 'Get grade items for course',
];

foreach ($functions as $func => $desc) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item']);
    echo html_writer::tag('strong', $func);
    echo html_writer::tag('p', $desc, ['class' => 'text-muted mb-0']);
    echo html_writer::end_tag('li');
}

echo html_writer::end_tag('ul');

// Test different course IDs.
echo html_writer::tag('h3', 'Test with different course IDs:');
echo html_writer::start_tag('form', ['method' => 'get']);
echo html_writer::tag('label', 'Course ID: ');
echo html_writer::empty_tag('input', ['type' => 'number', 'name' => 'courseid', 'value' => $courseid, 'min' => 1]);
echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => 'Test', 'class' => 'btn btn-primary']);
echo html_writer::end_tag('form');

// Output page footer.
echo $OUTPUT->footer(); 