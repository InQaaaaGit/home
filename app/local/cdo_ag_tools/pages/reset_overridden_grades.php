<?php
require_once(__DIR__ . "/../../../config.php");
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/grade/grade_grade.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_login();
require_capability('moodle/grade:edit', context_system::instance());

$plugin_name = 'local_cdo_ag_tools';
global $PAGE, $OUTPUT, $DB;

$title = get_string('reset_overridden_grades_title', $plugin_name);
$url = new moodle_url('/local/cdo_ag_tools/pages/reset_overridden_grades.php');
$systemcontext = context_system::instance();

$PAGE->set_context($systemcontext);
$PAGE->set_url($url);
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->set_pagelayout('base');

$previewnode = $PAGE->navigation->add(
    $title,
    $url,
    navigation_node::TYPE_CONTAINER
);

// Обработка POST запроса для сброса статуса переопределения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_grades'])) {
    require_sesskey();
    
    $grade_ids = isset($_POST['grade_ids']) ? $_POST['grade_ids'] : [];
    $success_count = 0;
    $error_count = 0;
    $error_details = [];
    
    if (!empty($grade_ids)) {
        foreach ($grade_ids as $grade_id) {
            try {
                // Получаем объект grade_grade
                $grade_grade = $DB->get_record('grade_grades', ['id' => $grade_id]);
                if ($grade_grade) {
                    // Создаем объект grade_grade и сбрасываем статус переопределения
                    $grade_grade_obj = new grade_grade($grade_grade, false);
                    $grade_grade_obj->set_overridden(0);
                    $grade_grade_obj->update();
                    $success_count++;
                }
            } catch (Exception $e) {
                // Логируем ошибку и добавляем в отчет
                $error_count++;
                $error_details[] = "Оценка ID {$grade_id}: " . $e->getMessage();
                error_log("Ошибка при сбросе статуса переопределения для оценки ID {$grade_id}: " . $e->getMessage());
            }
        }
        
        // Формируем отчет о выполнении
        $report_message = "Обработано оценок: {$success_count} успешно, {$error_count} с ошибками";
        
        if ($success_count > 0) {
            \core\notification::success($report_message);
        } else {
            \core\notification::error($report_message);
        }
        
        // Если есть ошибки, показываем детали
        if ($error_count > 0) {
            $error_message = "Детали ошибок:\n" . implode("\n", array_slice($error_details, 0, 10)); // Показываем первые 10 ошибок
            if (count($error_details) > 10) {
                $error_message .= "\n... и еще " . (count($error_details) - 10) . " ошибок";
            }
            \core\notification::warning($error_message);
        }
    } else {
        \core\notification::error("Не выбраны оценки для сброса");
    }
    
    // Перенаправляем на ту же страницу для обновления данных
    redirect($url);
}

echo $OUTPUT->header();

echo '<div class="container-fluid">';
echo '<h2>' . get_string('reset_overridden_grades_heading', $plugin_name) . '</h2>';
echo '<p>' . get_string('reset_overridden_grades_description', $plugin_name) . '</p>';

// Получаем параметры фильтрации
$course_filter = optional_param('course', 0, PARAM_INT);
$user_filter = optional_param('user', '', PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = 50; // Количество записей на странице

// Формируем базовый SQL запрос с оптимизацией
$sql = "
    SELECT
        c.fullname AS course_name, 
        c.id AS cid, 
        gi.id AS gid,
        gg.id AS grade_id,
        u.firstname,
        u.lastname,
        u.email,
        CASE
            WHEN gi.itemtype = 'course' THEN 'Итог курса'
            WHEN gi.itemtype = 'category' THEN CONCAT('Категория: ', gc.fullname)
            ELSE gi.itemname
        END AS grade_item_name,
        gg.finalgrade,
        gg.overridden,
        CASE
            WHEN gg.overridden = 1 THEN 'Да'
            ELSE 'Нет'
        END AS overridden_text
    FROM
        {grade_items} gi
    JOIN
        {grade_grades} gg ON gg.itemid = gi.id
    JOIN
        {user} u ON u.id = gg.userid
    JOIN
        {course} c ON c.id = gi.courseid
    LEFT JOIN
        {grade_categories} gc ON gc.id = gi.iteminstance AND gi.itemtype = 'category'
    WHERE
        gi.itemtype IN ('course', 'category')
        AND gg.finalgrade IS NOT NULL
        AND u.deleted = 0 
        AND gg.overridden > 0
";

$params = array();

// Добавляем фильтры
if ($course_filter > 0) {
    $sql .= " AND c.id = :courseid";
    $params['courseid'] = $course_filter;
}

if (!empty($user_filter)) {
    $sql .= " AND (u.firstname LIKE :userfilter OR u.lastname LIKE :userfilter2 OR u.email LIKE :userfilter3)";
    $params['userfilter'] = '%' . $user_filter . '%';
    $params['userfilter2'] = '%' . $user_filter . '%';
    $params['userfilter3'] = '%' . $user_filter . '%';
}

$sql .= " ORDER BY c.fullname, u.lastname, u.firstname, gi.itemtype DESC";

// Получаем общее количество записей для пагинации (с оптимизацией)
$count_sql = "SELECT COUNT(*) FROM ($sql) AS count_query";
$total_count = $DB->count_records_sql($count_sql, $params);

// Добавляем лимит для пагинации
$overridden_grades = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

// Получаем список курсов для фильтра (только с переопределенными оценками)
$courses_sql = "
    SELECT DISTINCT c.id, c.fullname
    FROM {course} c
    JOIN {grade_items} gi ON gi.courseid = c.id
    JOIN {grade_grades} gg ON gg.itemid = gi.id
    WHERE gi.itemtype IN ('course', 'category')
    AND gg.finalgrade IS NOT NULL
    AND gg.overridden > 0
    ORDER BY c.fullname
";
$available_courses = $DB->get_records_sql($courses_sql);

// Получаем список всех курсов для пересчета оценок
$all_courses_sql = "
    SELECT DISTINCT c.id, c.fullname
    FROM {course} c
    WHERE c.id != 1  -- исключаем системный курс
    ORDER BY c.fullname
";
$all_courses = $DB->get_records_sql($all_courses_sql);

// Обработка массового сброса всех оценок
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_all_grades'])) {
    require_sesskey();
    
    // Получаем все ID оценок по текущим фильтрам
    $all_grades_sql = str_replace("SELECT", "SELECT gg.id AS grade_id", $sql);
    $all_grade_ids = $DB->get_fieldset_sql($all_grades_sql, $params);
    
    $success_count = 0;
    $error_count = 0;
    $error_details = [];
    
    if (!empty($all_grade_ids)) {
        foreach ($all_grade_ids as $grade_id) {
            try {
                $grade_grade = $DB->get_record('grade_grades', ['id' => $grade_id]);
                if ($grade_grade) {
                    $grade_grade_obj = new grade_grade($grade_grade, false);
                    $grade_grade_obj->set_overridden(0);
                    $grade_grade_obj->update();
                    $success_count++;
                }
            } catch (Exception $e) {
                $error_count++;
                $error_details[] = "Оценка ID {$grade_id}: " . $e->getMessage();
                error_log("Ошибка при сбросе статуса переопределения для оценки ID {$grade_id}: " . $e->getMessage());
            }
        }
        
        // Формируем отчет о выполнении
        $report_message = "Массовый сброс: {$success_count} успешно, {$error_count} с ошибками";
        
        if ($success_count > 0) {
            \core\notification::success($report_message);
        } else {
            \core\notification::error($report_message);
        }
        
        // Если есть ошибки, показываем детали
        if ($error_count > 0) {
            $error_message = "Детали ошибок массового сброса:\n" . implode("\n", array_slice($error_details, 0, 10)); // Показываем первые 10 ошибок
            if (count($error_details) > 10) {
                $error_message .= "\n... и еще " . (count($error_details) - 10) . " ошибок";
            }
            \core\notification::warning($error_message);
        }
    } else {
        \core\notification::error("Не найдено оценок для сброса по текущим фильтрам");
    }
    
    redirect($url);
}

// Обработка запуска пересчета оценок
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['regrade_grades'])) {
    require_sesskey();
    
    $course_id = optional_param('regrade_course', 0, PARAM_INT);
    $regrade_count = 0;
    $error_count = 0;
    $error_details = [];
    
    if ($course_id > 0) {
        // Пересчет оценок для конкретного курса
        try {
            $grade_items = grade_item::fetch_all(['courseid' => $course_id]);
            if ($grade_items) {
                $processed_items = 0;
                foreach ($grade_items as $grade_item) {
                    try {
                        // Применяем force_regrading() только для категорий и итоговых оценок
                        if ($grade_item->is_category_item() || $grade_item->is_course_item()) {
                            $grade_item->force_regrading();
                            $processed_items++;
                        }
                    } catch (Exception $e) {
                        $error_count++;
                        $error_details[] = "Элемент оценки ID {$grade_item->id}: " . $e->getMessage();
                        error_log("Ошибка при force_regrading() для элемента оценки ID {$grade_item->id}: " . $e->getMessage());
                    }
                }
                
                // Запускаем полный пересчет оценок для курса
                try {
                    $regrade_result = grade_regrade_final_grades($course_id);
                    if ($regrade_result === true) {
                        \core\notification::success("Пересчет оценок для курса ID {$course_id} успешно запущен. Обработано {$processed_items} элементов оценок.");
                    } else {
                        $error_count++;
                        $error_details[] = "Ошибка при пересчете оценок курса: " . json_encode($regrade_result);
                        \core\notification::error("Ошибка при запуске пересчета оценок для курса ID {$course_id}");
                    }
                } catch (Exception $e) {
                    $error_count++;
                    $error_details[] = "Ошибка при grade_regrade_final_grades() для курса ID {$course_id}: " . $e->getMessage();
                    \core\notification::error("Ошибка при запуске пересчета оценок для курса ID {$course_id}");
                }
            } else {
                \core\notification::error("Не найдены элементы оценок для курса ID {$course_id}");
            }
        } catch (Exception $e) {
            $error_count++;
            $error_details[] = "Ошибка при получении элементов оценок для курса ID {$course_id}: " . $e->getMessage();
            \core\notification::error("Ошибка при обработке курса ID {$course_id}");
        }
    } else {
        // Пересчет оценок для всех курсов
        $total_processed = 0;
        $successful_courses = 0;
        
        foreach ($all_courses as $course) {
            try {
                $grade_items = grade_item::fetch_all(['courseid' => $course->id]);
                if ($grade_items) {
                    $course_processed = 0;
                    $course_errors = [];
                    
                    foreach ($grade_items as $grade_item) {
                        try {
                            // Применяем force_regrading() только для категорий и итоговых оценок
                            if ($grade_item->is_category_item() || $grade_item->is_course_item()) {
                                $grade_item->force_regrading();
                                $course_processed++;
                                $total_processed++;
                            }
                        } catch (Exception $e) {
                            $course_errors[] = "Элемент оценки ID {$grade_item->id}: " . $e->getMessage();
                            error_log("Ошибка при force_regrading() для элемента оценки ID {$grade_item->id} в курсе {$course->id}: " . $e->getMessage());
                        }
                    }
                    
                    // Запускаем полный пересчет оценок для каждого курса
                    try {
                        $regrade_result = grade_regrade_final_grades($course->id);
                        if ($regrade_result === true) {
                            $successful_courses++;
                        } else {
                            $course_errors[] = "Ошибка при пересчете оценок: " . json_encode($regrade_result);
                        }
                    } catch (Exception $e) {
                        $course_errors[] = "Ошибка при grade_regrade_final_grades(): " . $e->getMessage();
                    }
                    
                    // Добавляем ошибки курса в общий список
                    if (!empty($course_errors)) {
                        $error_count += count($course_errors);
                        $error_details[] = "Курс {$course->fullname} (ID: {$course->id}):";
                        $error_details = array_merge($error_details, $course_errors);
                    }
                }
            } catch (Exception $e) {
                $error_count++;
                $error_details[] = "Ошибка при обработке курса {$course->fullname} (ID: {$course->id}): " . $e->getMessage();
            }
        }
        
        // Формируем отчет о выполнении
        $report_message = "Пересчет оценок: {$successful_courses} курсов успешно, {$error_count} ошибок. Обработано {$total_processed} элементов оценок.";
        
        if ($successful_courses > 0) {
            \core\notification::success($report_message);
        } else {
            \core\notification::error($report_message);
        }
        
        // Если есть ошибки, показываем детали
        if ($error_count > 0) {
            $error_message = "Детали ошибок пересчета:\n" . implode("\n", array_slice($error_details, 0, 10)); // Показываем первые 10 ошибок
            if (count($error_details) > 10) {
                $error_message .= "\n... и еще " . (count($error_details) - 10) . " ошибок";
            }
            \core\notification::warning($error_message);
        }
    }
    
    redirect($url);
}

// Форма фильтрации
echo '<div class="card mb-4">';
echo '<div class="card-header"><strong>Фильтры</strong></div>';
echo '<div class="card-body">';
echo '<form method="get" class="form-inline">';
echo '<input type="hidden" name="page" value="0">'; // Сбрасываем страницу при фильтрации

echo '<div class="form-group mr-3 mb-2">';
echo '<label for="course_filter" class="mr-2">Курс:</label>';
echo '<select name="course" id="course_filter" class="form-control">';
echo '<option value="0">Все курсы</option>';
foreach ($available_courses as $course) {
    $selected = $course->id == $course_filter ? 'selected' : '';
    echo '<option value="' . $course->id . '" ' . $selected . '>' . format_string($course->fullname) . '</option>';
}
echo '</select>';
echo '</div>';

echo '<div class="form-group mr-3 mb-2">';
echo '<label for="user_filter" class="mr-2">Пользователь:</label>';
echo '<input type="text" name="user" id="user_filter" class="form-control" value="' . s($user_filter) . '" placeholder="ФИО или email">';
echo '</div>';

echo '<button type="submit" class="btn btn-primary mb-2">Применить фильтры</button>';
echo '<a href="' . $url . '" class="btn btn-secondary mb-2 ml-2">Сбросить</a>';
echo '</form>';
echo '</div>';
echo '</div>';

// Статистика и массовые действия
echo '<div class="card mb-4">';
echo '<div class="card-header"><strong>Статистика и действия</strong></div>';
echo '<div class="card-body">';
echo '<p>Найдено переопределенных оценок: <strong>' . $total_count . '</strong></p>';

if ($total_count > 0) {
    echo '<div class="mb-3">';
    echo '<form method="post" class="d-inline">';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
    echo '<button type="submit" name="reset_all_grades" class="btn btn-warning" onclick="return confirm(\'Вы уверены, что хотите сбросить статус переопределения для ВСЕХ ' . $total_count . ' оценок? Эта операция может занять некоторое время.\')">';
    echo 'Сбросить ВСЕ оценки (' . $total_count . ')';
    echo '</button>';
    echo '</form>';
    
    echo '<span class="ml-3 text-muted">Будут обработаны все оценки, соответствующие текущим фильтрам</span>';
    echo '</div>';
}

// Форма для запуска пересчета оценок
echo '<div class="mt-4">';
echo '<h5>Пересчет оценок категорий и итоговых оценок</h5>';
echo '<p class="text-muted">После сброса статуса переопределения рекомендуется запустить пересчет оценок для обновления итоговых значений. Можно выбрать конкретный курс или все курсы.</p>';

echo '<form method="post" class="form-inline">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';

echo '<div class="form-group mr-3 mb-2">';
echo '<label for="regrade_course" class="mr-2">Курс для пересчета:</label>';
echo '<select name="regrade_course" id="regrade_course" class="form-control">';
echo '<option value="0">Все курсы</option>';
foreach ($all_courses as $course) {
    echo '<option value="' . $course->id . '">' . format_string($course->fullname) . ' (ID: ' . $course->id . ')</option>';
}
echo '</select>';
echo '</div>';

echo '<button type="submit" name="regrade_grades" class="btn btn-info mb-2" onclick="return confirm(\'Вы уверены, что хотите запустить пересчет оценок? Эта операция может занять некоторое время.\')">';
echo 'Запустить пересчет оценок';
echo '</button>';
echo '</form>';
echo '</div>';

echo '</div>';
echo '</div>';

if (empty($overridden_grades)) {
    echo '<div class="alert alert-info">' . get_string('no_overridden_grades', $plugin_name) . '</div>';
} else {
    // Пагинация
    if ($total_count > $perpage) {
        echo '<div class="d-flex justify-content-between align-items-center mb-3">';
        echo '<div>';
        $start = $page * $perpage + 1;
        $end = min(($page + 1) * $perpage, $total_count);
        echo 'Показаны записи ' . $start . '-' . $end . ' из ' . $total_count;
        echo '</div>';
        
        echo '<nav>';
        echo '<ul class="pagination">';
        
        // Предыдущая страница
        if ($page > 0) {
            $prev_url = new moodle_url($url, ['page' => $page - 1, 'course' => $course_filter, 'user' => $user_filter]);
            echo '<li class="page-item"><a class="page-link" href="' . $prev_url . '">Предыдущая</a></li>';
        }
        
        // Номера страниц
        $total_pages = ceil($total_count / $perpage);
        $start_page = max(0, $page - 2);
        $end_page = min($total_pages - 1, $page + 2);
        
        for ($i = $start_page; $i <= $end_page; $i++) {
            $page_url = new moodle_url($url, ['page' => $i, 'course' => $course_filter, 'user' => $user_filter]);
            $active = $i == $page ? 'active' : '';
            echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . $page_url . '">' . ($i + 1) . '</a></li>';
        }
        
        // Следующая страница
        if ($page < $total_pages - 1) {
            $next_url = new moodle_url($url, ['page' => $page + 1, 'course' => $course_filter, 'user' => $user_filter]);
            echo '<li class="page-item"><a class="page-link" href="' . $next_url . '">Следующая</a></li>';
        }
        
        echo '</ul>';
        echo '</nav>';
        echo '</div>';
    }
    
    echo '<form method="post" id="reset-form">';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
    
    echo '<div class="mb-3">';
    echo '<button type="button" class="btn btn-secondary" id="select-all">' . get_string('select_all', $plugin_name) . '</button>';
    echo '<button type="button" class="btn btn-secondary" id="deselect-all">' . get_string('deselect_all', $plugin_name) . '</button>';
    echo '<button type="submit" name="reset_grades" class="btn btn-danger" onclick="return confirm(\'' . get_string('confirm_reset', $plugin_name) . '\')">' . get_string('reset_selected_grades', $plugin_name) . '</button>';
    echo '</div>';
    
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-bordered">';
    echo '<thead class="thead-dark">';
    echo '<tr>';
    echo '<th><input type="checkbox" id="select-all-checkbox"></th>';
    echo '<th>' . get_string('course_name', $plugin_name) . '</th>';
    echo '<th>' . get_string('student_name', $plugin_name) . '</th>';
    echo '<th>' . get_string('email', $plugin_name) . '</th>';
    echo '<th>' . get_string('grade_item_name', $plugin_name) . '</th>';
    echo '<th>' . get_string('final_grade', $plugin_name) . '</th>';
    echo '<th>' . get_string('overridden', $plugin_name) . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($overridden_grades as $grade) {
        echo '<tr>';
        echo '<td><input type="checkbox" name="grade_ids[]" value="' . $grade->grade_id . '" class="grade-checkbox"></td>';
        echo '<td>' . format_string($grade->course_name) . '</td>';
        echo '<td>' . fullname((object)['firstname' => $grade->firstname, 'lastname' => $grade->lastname]) . '</td>';
        echo '<td>' . $grade->email . '</td>';
        echo '<td>' . format_string($grade->grade_item_name) . '</td>';
        echo '<td>' . format_float($grade->finalgrade, 2) . '</td>';
        echo '<td>' . $grade->overridden_text . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    echo '</form>';
    
    // JavaScript для управления чекбоксами
    echo '
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectAllCheckbox = document.getElementById("select-all-checkbox");
        const gradeCheckboxes = document.querySelectorAll(".grade-checkbox");
        const selectAllBtn = document.getElementById("select-all");
        const deselectAllBtn = document.getElementById("deselect-all");
        
        selectAllCheckbox.addEventListener("change", function() {
            gradeCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        selectAllBtn.addEventListener("click", function() {
            gradeCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            selectAllCheckbox.checked = true;
        });
        
        deselectAllBtn.addEventListener("click", function() {
            gradeCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            selectAllCheckbox.checked = false;
        });
        
        // Обновляем состояние главного чекбокса при изменении отдельных чекбоксов
        gradeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener("change", function() {
                const allChecked = Array.from(gradeCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            });
        });
    });
    </script>
    ';
}

echo '</div>';

echo $OUTPUT->footer();
