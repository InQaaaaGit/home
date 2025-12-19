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

defined('MOODLE_INTERNAL') || die();

/**
 * Get the renderer for this plugin
 *
 * @param moodle_page $page
 * @return \local_videoprogress\output\renderer
 */
function local_videoprogress_get_renderer($page) {
    return $page->get_renderer('local_videoprogress');
}

/**
 * Extend the navigation
 *
 * @param global_navigation $navigation
 * @return void
 */
function local_videoprogress_extend_navigation($navigation): void
{
    global $PAGE, $COURSE;

    /*if ($PAGE->context->contextlevel == CONTEXT_MODULE) {
        $cm = get_coursemodule_from_id(null, $PAGE->context->instanceid, 0, false, MUST_EXIST);
        
        // Добавляем ссылку на просмотр прогресса, если у пользователя есть права
        if (has_capability('local/videoprogress:view', $PAGE->context)) {
            $url = new moodle_url('/local/videoprogress/view.php', ['cmid' => $cm->id]);
            $navigation->add(
                get_string('viewprogress', 'local_videoprogress'),
                $url,
                navigation_node::TYPE_SETTING
            );
        }
    }*/
} 