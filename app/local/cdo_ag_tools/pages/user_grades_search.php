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
 * Страница поиска пользователей для просмотра оценок (для администраторов)
 *
 * @package   local_cdo_ag_tools
 * @copyright 2025
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

global $PAGE, $OUTPUT, $DB, $USER;

// Проверяем авторизацию
require_login();

$systemcontext = context_system::instance();

// Проверяем права доступа (только для администраторов и преподавателей)
if (!has_capability('moodle/grade:viewall', $systemcontext) && 
    !has_capability('moodle/site:accessallgroups', $systemcontext)) {
    throw new moodle_exception('nopermissions', 'error', '', 'view user grades');
}

$pluginname = 'local_cdo_ag_tools';
$title = get_string('user_grades_search_title', $pluginname);
$url = new moodle_url('/local/cdo_ag_tools/pages/user_grades_search.php');

$PAGE->set_context($systemcontext);
$PAGE->set_url($url);
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

echo html_writer::tag('h2', $title);
echo html_writer::tag('p', get_string('user_grades_search_description', $pluginname));

// Поиск пользователя
$searchterm = optional_param('search', '', PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = 20;

echo html_writer::start_div('user-search-form', ['style' => 'margin: 20px 0;']);

// Форма поиска
echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => $url->out_omit_querystring(),
    'class' => 'form-inline'
]);

echo html_writer::start_div('form-group mr-3');
echo html_writer::label(get_string('search') . ':', 'search_input', false, ['class' => 'mr-2']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'id' => 'search_input',
    'value' => $searchterm,
    'class' => 'form-control',
    'placeholder' => get_string('search_user_placeholder', $pluginname),
    'size' => '40'
]);
echo html_writer::end_div();

echo html_writer::empty_tag('input', [
    'type' => 'submit',
    'value' => get_string('search'),
    'class' => 'btn btn-primary'
]);

echo html_writer::end_tag('form');
echo html_writer::end_div();

// Если есть поисковый запрос, ищем пользователей
if (!empty($searchterm)) {
    $searchterm = trim($searchterm);
    
    // Поиск по имени, фамилии или email
    $sql = "SELECT id, firstname, lastname, email, username
            FROM {user}
            WHERE deleted = 0
            AND (
                " . $DB->sql_like('firstname', ':search1', false) . "
                OR " . $DB->sql_like('lastname', ':search2', false) . "
                OR " . $DB->sql_like('email', ':search3', false) . "
                OR " . $DB->sql_like('username', ':search4', false) . "
            )
            ORDER BY lastname, firstname";
    
    $params = [
        'search1' => '%' . $DB->sql_like_escape($searchterm) . '%',
        'search2' => '%' . $DB->sql_like_escape($searchterm) . '%',
        'search3' => '%' . $DB->sql_like_escape($searchterm) . '%',
        'search4' => '%' . $DB->sql_like_escape($searchterm) . '%'
    ];
    
    $totalcount = $DB->count_records_sql("SELECT COUNT(*) FROM ($sql) AS cnt", $params);
    $users = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);
    
    if ($users) {
        echo html_writer::tag('p', get_string('found_users', $pluginname, $totalcount));
        
        // Таблица с результатами
        $table = new html_table();
        $table->attributes['class'] = 'generaltable';
        $table->head = [
            'ID',
            get_string('firstname'),
            get_string('lastname'),
            get_string('email'),
            get_string('username'),
            get_string('actions')
        ];
        
        foreach ($users as $user) {
            $viewurl = new moodle_url('/local/cdo_ag_tools/pages/user_grades.php', ['userid' => $user->id]);
            $viewlink = html_writer::link($viewurl, get_string('view_grades', $pluginname), 
                ['class' => 'btn btn-sm btn-primary']);
            
            $table->data[] = [
                $user->id,
                s($user->firstname),
                s($user->lastname),
                s($user->email),
                s($user->username),
                $viewlink
            ];
        }
        
        echo html_writer::table($table);
        
        // Пагинация
        if ($totalcount > $perpage) {
            $pagingbar = new paging_bar($totalcount, $page, $perpage, $url);
            $pagingbar->pagevar = 'page';
            echo $OUTPUT->render($pagingbar);
        }
    } else {
        echo $OUTPUT->notification(get_string('no_users_found', $pluginname), 'notifymessage');
    }
} else {
    // Показываем последних активных пользователей
    echo html_writer::tag('h4', get_string('recent_active_users', $pluginname));
    
    $sql = "SELECT id, firstname, lastname, email, username, lastaccess
            FROM {user}
            WHERE deleted = 0 AND lastaccess > 0
            ORDER BY lastaccess DESC";
    
    $recentusers = $DB->get_records_sql($sql, [], 0, 10);
    
    if ($recentusers) {
        $table = new html_table();
        $table->attributes['class'] = 'generaltable';
        $table->head = [
            'ID',
            get_string('firstname'),
            get_string('lastname'),
            get_string('email'),
            get_string('lastaccess'),
            get_string('actions')
        ];
        
        foreach ($recentusers as $user) {
            $viewurl = new moodle_url('/local/cdo_ag_tools/pages/user_grades.php', ['userid' => $user->id]);
            $viewlink = html_writer::link($viewurl, get_string('view_grades', $pluginname), 
                ['class' => 'btn btn-sm btn-primary']);
            
            $lastaccess = $user->lastaccess > 0 ? userdate($user->lastaccess) : get_string('never');
            
            $table->data[] = [
                $user->id,
                s($user->firstname),
                s($user->lastname),
                s($user->email),
                $lastaccess,
                $viewlink
            ];
        }
        
        echo html_writer::table($table);
    }
}

// Быстрый доступ по ID
echo html_writer::start_div('quick-access', ['style' => 'margin-top: 30px; padding: 15px; background-color: #e8f4f8; border-radius: 5px;']);
echo html_writer::tag('h4', get_string('quick_access_by_id', $pluginname));
echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => new moodle_url('/local/cdo_ag_tools/pages/user_grades.php'),
    'class' => 'form-inline'
]);

echo html_writer::start_div('form-group mr-3');
echo html_writer::label('User ID:', 'quick_userid', false, ['class' => 'mr-2']);
echo html_writer::empty_tag('input', [
    'type' => 'number',
    'name' => 'userid',
    'id' => 'quick_userid',
    'class' => 'form-control',
    'min' => '1',
    'placeholder' => 'User ID'
]);
echo html_writer::end_div();

echo html_writer::empty_tag('input', [
    'type' => 'submit',
    'value' => get_string('view_grades', $pluginname),
    'class' => 'btn btn-success'
]);

echo html_writer::end_tag('form');
echo html_writer::end_div();

echo $OUTPUT->footer();

