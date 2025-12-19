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
 * Main page for CDO Showcase Tools.
 *
 * @package     tool_cdo_showcase_tools
 * @copyright   2024 Your Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Ensure user is logged in and has proper permissions.
require_login();
$context = context_system::instance();
require_capability('tool/cdo_showcase_tools:view', $context);

// Set up the page.
$PAGE->set_url('/admin/tool/cdo_showcase_tools/index.php');
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'tool_cdo_showcase_tools'));
$PAGE->set_heading(get_string('heading', 'tool_cdo_showcase_tools'));
$PAGE->set_pagelayout('admin');

// Output page header.
echo $OUTPUT->header();

// Page heading.
echo $OUTPUT->heading(get_string('heading', 'tool_cdo_showcase_tools'));

// Main content.
echo html_writer::start_div('alert alert-info');
echo html_writer::tag('p', get_string('welcome', 'tool_cdo_showcase_tools'));
echo html_writer::tag('p', get_string('description', 'tool_cdo_showcase_tools'));
echo html_writer::end_div();

// Add your main functionality here.
echo html_writer::start_div('mt-3');
echo html_writer::tag('h3', 'Available Tools');
echo html_writer::start_tag('ul', ['class' => 'list-group']);

// Main tools.
$tools = [
    [
        'name' => 'Letter Grades API Test',
        'description' => 'Тестирование external API функции получения буквенных оценок курса',
        'url' => new moodle_url('/admin/tool/cdo_showcase_tools/test.php'),
        'icon' => 'fa-check-circle'
    ],
    [
        'name' => 'Debug Grade Letters',
        'description' => 'Отладка получения буквенных оценок из Moodle gradebook',
        'url' => new moodle_url('/admin/tool/cdo_showcase_tools/debug_grades.php'),
        'icon' => 'fa-bug'
    ],
    [
        'name' => 'API Usage Examples',
        'description' => 'Примеры использования external API функций',
        'url' => new moodle_url('/admin/tool/cdo_showcase_tools/example_usage.php'),
        'icon' => 'fa-code'
    ]
];

foreach ($tools as $tool) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-start']);
    echo html_writer::start_div('ms-2 me-auto');
    echo html_writer::start_div('fw-bold');
    echo html_writer::tag('i', '', ['class' => 'fa ' . $tool['icon'] . ' me-2']);
    echo html_writer::link($tool['url'], $tool['name']);
    echo html_writer::end_div();
    echo html_writer::tag('p', $tool['description'], ['class' => 'text-muted mb-1']);
    echo html_writer::end_div();
    echo html_writer::link($tool['url'], 'Открыть', ['class' => 'btn btn-primary btn-sm']);
    echo html_writer::end_tag('li');
}

echo html_writer::end_tag('ul');
echo html_writer::end_div();

// Output page footer.
echo $OUTPUT->footer(); 