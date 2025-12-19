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
 * Страница отображения оценок пользователя с категориями
 *
 * @package   local_cdo_ag_tools
 * @copyright 2025
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/grade_category.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Приводит название категории к унифицированному ключу.
 *
 * @param string $categoryname
 * @return string
 */
function local_cdo_ag_tools_normalize_category_name(string $categoryname): string {
    $normalized = core_text::strtolower(trim($categoryname));
    return preg_replace('/\s+/u', ' ', $normalized);
}

global $PAGE, $OUTPUT, $DB, $USER;

// Получаем параметры
$userid = optional_param('userid', 0, PARAM_INT);
$useremail = optional_param('useremail', '', PARAM_EMAIL);
$courseid = optional_param('id', SITEID, PARAM_INT);

// Проверяем авторизацию
require_login(null, false);

// Определяем пользователя для просмотра оценок
if (!empty($useremail)) {
    // Поиск по email
    $user = $DB->get_record('user', ['email' => $useremail, 'deleted' => 0]);
    if (!$user) {
        throw new moodle_exception('usernotfound', 'error');
    }
    $userid = $user->id;
} else {
    // Если не указан userid, показываем текущего пользователя
    if ($userid == 0) {
        $userid = $USER->id;
    }
    // Проверяем существование пользователя
    $user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', MUST_EXIST);
}

// Получаем контексты
$systemcontext = context_system::instance();
$personalcontext = context_user::instance($userid);

// Проверка прав доступа
$canview = false;
if (has_capability('moodle/grade:viewall', $systemcontext)) {
    $canview = true;
} elseif ($userid == $USER->id) {
    $canview = true;
} elseif (has_capability('moodle/grade:viewall', $personalcontext)) {
    $canview = true;
} elseif (has_capability('moodle/user:viewuseractivitiesreport', $personalcontext)) {
    $canview = true;
}

if (!$canview) {
    throw new moodle_exception('nopermissiontoviewgrades', 'error', $CFG->wwwroot);
}

// Настройка страницы
$pluginname = 'local_cdo_ag_tools';
$title = get_string('user_grades_title', $pluginname);
$url = new moodle_url('/local/cdo_ag_tools/pages/user_grades.php', ['userid' => $userid]);

$PAGE->set_context($systemcontext);
$PAGE->set_url($url);
$PAGE->set_heading($title);
$PAGE->set_title($title . ' - ' . fullname($user));
$PAGE->set_pagelayout('standard');

// Навигация
$PAGE->navbar->add($title, $url);

echo $OUTPUT->header();

// Заголовок страницы
echo html_writer::tag('h2', $title);

// Форма выбора пользователя для администраторов/преподавателей

if (is_siteadmin()) {
    
    echo html_writer::start_div('user-selector-form', ['style' => 'margin: 20px 0; padding: 15px; background-color: #f5f5f5; border-radius: 5px;']);
    echo html_writer::tag('h4', get_string('select_user', $pluginname));
    
    // Форма выбора пользователя
    echo html_writer::start_tag('form', [
        'method' => 'get',
        'action' => $url->out_omit_querystring(),
        'class' => 'form-inline'
    ]);
    
    echo html_writer::start_div('form-group mr-3');
    echo html_writer::label(get_string('user') . ':', 'userid_input', false, ['class' => 'mr-2']);
    echo html_writer::empty_tag('input', [
        'type' => 'number',
        'name' => 'userid',
        'id' => 'userid_input',
        'value' => $userid,
        'class' => 'form-control',
        'placeholder' => 'User ID',
        'min' => '1'
    ]);
    echo html_writer::end_div();
    
    echo html_writer::start_div('form-group mr-3');
    echo html_writer::label(get_string('or', 'moodle') . ' ' . get_string('email') . ':', 'useremail_input', false, ['class' => 'mr-2']);
    echo html_writer::empty_tag('input', [
        'type' => 'text',
        'name' => 'useremail',
        'id' => 'useremail_input',
        'class' => 'form-control',
        'placeholder' => get_string('email')
    ]);
    echo html_writer::end_div();
    
    echo html_writer::empty_tag('input', [
        'type' => 'submit',
        'value' => get_string('show'),
        'class' => 'btn btn-primary'
    ]);
    
    echo html_writer::end_tag('form');
    
    // Показываем список недавних пользователей
    echo html_writer::start_div('recent-users', ['style' => 'margin-top: 15px;']);
    echo html_writer::tag('p', get_string('recent_users_hint', $pluginname), ['class' => 'text-muted small']);
    echo html_writer::end_div();
    
    echo html_writer::end_div();
}

echo html_writer::tag('h3', get_string('grades_for_user', $pluginname) . ': ' . fullname($user), 
    ['style' => 'margin-top: 20px; color: #0066cc;']);

// Получаем все курсы пользователя
$onlyactive = ($userid === $USER->id);
$courses = enrol_get_users_courses($userid, $onlyactive, 'id, shortname, fullname, showgrades');

if (empty($courses)) {
    echo $OUTPUT->notification(get_string('notenrolled', 'grades'), 'notifymessage');
    echo $OUTPUT->footer();
    exit;
}

// Собираем данные по всем курсам
$coursesdata = [];
$allcategories = []; // Все уникальные категории ['normalizedkey' => 'display name']

foreach ($courses as $course) {
    if (!$course->showgrades) {
        continue;
    }

    $coursecontext = context_course::instance($course->id);

    // Проверяем видимость курса для текущего пользователя
    if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
        continue;
    }

    // Пропускаем курс, если целевой пользователь не имеет к нему доступа
    if (!can_access_course($course, $userid)) {
        continue;
    }

    // Проверяем права на просмотр оценок в курсе
    if (!has_capability('moodle/user:viewuseractivitiesreport', $personalcontext) &&
        !has_capability('moodle/grade:view', $coursecontext) &&
        !has_capability('moodle/grade:viewall', $coursecontext)) {
        continue;
    }

    $coursedata = [
        'course' => $course,
        'context' => $coursecontext,
        'categories' => [],
        'coursetotal' => null
    ];

    try {
        // Получаем все элементы оценок курса
        $gradeitems = grade_item::fetch_all(['courseid' => $course->id]);
        
        // Получаем категории курса
        $categories = grade_category::fetch_all(['courseid' => $course->id]);
        $categorymap = [];
        
        if ($categories) {
            foreach ($categories as $category) {
                if (method_exists($category, 'is_course_category') && $category->is_course_category()) {
                    continue;
                }
                $categoryname = trim($category->get_name());
                if ($categoryname === '') {
                    continue;
                }
                $categorymap[$category->id] = $categoryname;
            }
        }
        
        // Сначала пробуем получить итоговые оценки по категориям (если они есть)
        if ($gradeitems) {
            foreach ($gradeitems as $gradeitem) {
                // Обрабатываем только элементы типа 'category'
                if ($gradeitem->itemtype == 'category') {
                    // Используем прямой запрос к БД
                    $graderecord = $DB->get_record('grade_grades', [
                        'itemid' => $gradeitem->id,
                        'userid' => $userid
                    ]);
                    
                    $finalgrade = null;
                    if ($graderecord && !is_null($graderecord->finalgrade)) {
                        $finalgrade = $graderecord->finalgrade;
                    }
                    
                    if (!isset($categorymap[$gradeitem->iteminstance])) {
                        continue;
                    }

                    $categoryname = $categorymap[$gradeitem->iteminstance];
                    $categorykey = local_cdo_ag_tools_normalize_category_name($categoryname);
                    if ($categorykey === '') {
                        continue;
                    }

                    if (!isset($coursedata['categories'][$categorykey])) {
                        $coursedata['categories'][$categorykey] = [
                            'grade' => null,
                            'finalgrade' => null,
                            'grademin' => $gradeitem->grademin,
                            'grademax' => $gradeitem->grademax,
                            'label' => $categoryname
                        ];
                    } else {
                        $coursedata['categories'][$categorykey]['label'] = $categoryname;
                        $coursedata['categories'][$categorykey]['grademin'] = $gradeitem->grademin;
                        $coursedata['categories'][$categorykey]['grademax'] = $gradeitem->grademax;
                    }

                    if (!is_null($finalgrade)) {
                        $gradevalue = grade_format_gradevalue($finalgrade, $gradeitem, true, GRADE_DISPLAY_TYPE_REAL);
                        $coursedata['categories'][$categorykey]['grade'] = $gradevalue;
                        $coursedata['categories'][$categorykey]['finalgrade'] = $finalgrade;
                    }

                    if (!array_key_exists($categorykey, $allcategories)) {
                        $allcategories[$categorykey] = $categoryname;
                    }
                }
            }
        }
        
        // Дополняем данные категориями на основе обычных элементов, если прямых итоговых оценок нет
        if ($gradeitems) {
            $categorygrades = [];
            
            foreach ($gradeitems as $gradeitem) {
                // Пропускаем служебные элементы
                if ($gradeitem->itemtype == 'course' || $gradeitem->itemtype == 'category') {
                    continue;
                }
                
                // Получаем оценку пользователя
                $graderecord = $DB->get_record('grade_grades', [
                    'itemid' => $gradeitem->id,
                    'userid' => $userid
                ]);
                
                $finalgrade = null;
                if ($graderecord && !is_null($graderecord->finalgrade)) {
                    $finalgrade = $graderecord->finalgrade;
                }
                
                if (!is_null($finalgrade) && isset($categorymap[$gradeitem->categoryid])) {
                    $categoryname = $categorymap[$gradeitem->categoryid];
                    $categorykey = local_cdo_ag_tools_normalize_category_name($categoryname);
                    if ($categorykey === '') {
                        continue;
                    }
                    
                    if (!isset($categorygrades[$categorykey])) {
                        $categorygrades[$categorykey] = [
                            'sum' => 0,
                            'count' => 0,
                            'max' => 0,
                            'label' => $categoryname
                        ];
                    }

                    if (!isset($coursedata['categories'][$categorykey])) {
                        $coursedata['categories'][$categorykey] = [
                            'grade' => null,
                            'finalgrade' => null,
                            'grademin' => 0,
                            'grademax' => 100,
                            'label' => $categoryname
                        ];
                    }
                    
                    // Нормализуем оценку к процентам
                    if ($gradeitem->grademax > $gradeitem->grademin) {
                        $percentage = (($finalgrade - $gradeitem->grademin) / 
                                      ($gradeitem->grademax - $gradeitem->grademin)) * 100;
                        $categorygrades[$categorykey]['sum'] += $percentage;
                        $categorygrades[$categorykey]['count']++;
                        $categorygrades[$categorykey]['max'] = 100;
                    }
                }
            }
            
            // Вычисляем средние оценки по категориям
            foreach ($categorygrades as $categorykey => $data) {
                if (!isset($coursedata['categories'][$categorykey])) {
                    $coursedata['categories'][$categorykey] = [
                        'grade' => null,
                        'finalgrade' => null,
                        'grademin' => 0,
                        'grademax' => 100,
                        'label' => $data['label']
                    ];
                }

                if ($data['count'] > 0 && is_null($coursedata['categories'][$categorykey']['finalgrade'])) {
                    $avggrade = $data['sum'] / $data['count'];
                    
                    $coursedata['categories'][$categorykey] = [
                        'grade' => number_format($avggrade, 2),
                        'finalgrade' => $avggrade,
                        'grademin' => 0,
                        'grademax' => 100,
                        'label' => $data['label']
                    ];
                    
                    // Добавляем в список всех категорий
                    if (!array_key_exists($categorykey, $allcategories)) {
                        $allcategories[$categorykey] = $data['label'];
                    }
                } else {
                    if (!array_key_exists($categorykey, $allcategories)) {
                        $allcategories[$categorykey] = $data['label'];
                    }
                }
            }
        }

        // Добавляем отсутствующие категории без оценок
        foreach ($categorymap as $categoryid => $categoryname) {
            $categorykey = local_cdo_ag_tools_normalize_category_name($categoryname);
            if ($categorykey === '') {
                continue;
            }
            if (!isset($coursedata['categories'][$categorykey])) {
                $coursedata['categories'][$categorykey] = [
                    'grade' => null,
                    'finalgrade' => null,
                    'grademin' => 0,
                    'grademax' => 0,
                    'label' => $categoryname
                ];
            }
            if (!array_key_exists($categorykey, $allcategories)) {
                $allcategories[$categorykey] = $categoryname;
            }
        }
        
        // Получаем итоговую оценку курса
        $courseitem = grade_item::fetch_course_item($course->id);
        if ($courseitem) {
            // Используем прямой запрос к БД
            $graderecord = $DB->get_record('grade_grades', [
                'itemid' => $courseitem->id,
                'userid' => $userid
            ]);
            
            $finalgrade = null;
            if ($graderecord && !is_null($graderecord->finalgrade)) {
                $finalgrade = $graderecord->finalgrade;
            }
            
            if (!is_null($finalgrade)) {
                $gradevalue = grade_format_gradevalue($finalgrade, $courseitem, true, GRADE_DISPLAY_TYPE_REAL);
                $coursedata['coursetotal'] = [
                    'grade' => $gradevalue,
                    'finalgrade' => $finalgrade,
                    'grademin' => $courseitem->grademin,
                    'grademax' => $courseitem->grademax
                ];
            }
        }
        
        // Добавляем курс только если есть хоть какие-то данные
        if (!empty($coursedata['categories']) || !is_null($coursedata['coursetotal'])) {
            $coursesdata[] = $coursedata;
        }
        
    } catch (Exception $e) {
        // Пропускаем курс с ошибкой
        continue;
    }
}

if (empty($coursesdata)) {
    echo $OUTPUT->notification(get_string('nogrades', $pluginname), 'notifymessage');
    echo html_writer::tag('p', get_string('no_grades_explanation', $pluginname), ['class' => 'alert alert-info']);
    echo $OUTPUT->footer();
    exit;
}

// Создаем сводную таблицу
$table = new html_table();
$table->attributes['class'] = 'generaltable boxaligncenter';

// Формируем заголовки таблицы
$headers = [get_string('course', 'grades')];
foreach ($allcategories as $categorylabel) {
    $headers[] = $categorylabel;
}
$headers[] = get_string('coursetotal', 'grades');

$table->head = $headers;

// Формируем выравнивание
$align = ['left'];
for ($i = 0; $i < count($allcategories) + 1; $i++) {
    $align[] = 'center';
}
$table->align = $align;

$table->data = [];

// Заполняем данные
foreach ($coursesdata as $coursedata) {
    $course = $coursedata['course'];
    $coursecontext = $coursedata['context'];
    
    $coursenamelink = format_string($course->fullname, true, ['context' => $coursecontext]);
    
    // Ссылка на детальный отчет курса
    if (has_capability('gradereport/user:view', $coursecontext) || 
        has_capability('moodle/grade:viewall', $coursecontext)) {
        $courseurl = new moodle_url('/grade/report/user/index.php', [
            'id' => $course->id,
            'userid' => $userid
        ]);
        $coursenamelink = html_writer::link($courseurl, $coursenamelink);
    }
    
    $row = [$coursenamelink];
    
    // Добавляем оценки по категориям
    foreach ($allcategories as $categorykey => $categorylabel) {
        if (isset($coursedata['categories'][$categorykey])) {
            $catdata = $coursedata['categories'][$categorykey];
            if (!is_null($catdata['finalgrade'])) {
                $gradetext = $catdata['grade'];
                
                // Добавляем процент если нужно
                if ($catdata['grademax'] > $catdata['grademin']) {
                    $percentage = (($catdata['finalgrade'] - $catdata['grademin']) / 
                                  ($catdata['grademax'] - $catdata['grademin'])) * 100;
                    $gradetext .= ' (' . number_format($percentage, 1) . '%)';
                }
                
                $row[] = $gradetext;
            } else {
                $row[] = '-';
            }
        } else {
            $row[] = '-';
        }
    }
    
    // Добавляем итоговую оценку курса
    if ($coursedata['coursetotal']) {
        $totaldata = $coursedata['coursetotal'];
        $totaltext = html_writer::tag('strong', $totaldata['grade']);
        
        if ($totaldata['grademax'] > $totaldata['grademin']) {
            $percentage = (($totaldata['finalgrade'] - $totaldata['grademin']) / 
                          ($totaldata['grademax'] - $totaldata['grademin'])) * 100;
            $totaltext .= ' ' . html_writer::tag('span', '(' . number_format($percentage, 1) . '%)', 
                                                  ['style' => 'font-weight: normal;']);
        }
        
        $row[] = $totaltext;
    } else {
        $row[] = '-';
    }
    
    $table->data[] = $row;
}

echo html_writer::tag('p', get_string('courses_enrolled', $pluginname) . ': ' . count($coursesdata));
echo html_writer::table($table);

echo $OUTPUT->footer();
