<?php
/**
 * Скрипт для импорта оценок из JSON файла в таблицу local_cdo_ag_grade_notifications
 *
 * @package    local_cdo_ag_tools
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Требуется авторизация администратора
require_login();
admin_externalpage_setup('local_cdo_ag_tools_grade_digest_example');

$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Настройка страницы
$PAGE->set_url(new moodle_url('/local/cdo_ag_tools/import_grades_from_json.php'));
$PAGE->set_context($context);
$PAGE->set_title('Импорт оценок из JSON');
$PAGE->set_heading('Импорт оценок из JSON');

echo $OUTPUT->header();
echo $OUTPUT->heading('Импорт оценок из JSON файла', 2);

// Путь к JSON файлу
$jsonFile = __DIR__ . '/report_1760087573.json';

if (!file_exists($jsonFile)) {
    echo $OUTPUT->notification('JSON файл не найден: ' . $jsonFile, 'error');
    echo $OUTPUT->footer();
    exit;
}

echo html_writer::tag('p', 'Чтение JSON файла...', ['class' => 'alert alert-info']);

// Читаем JSON файл
$jsonContent = file_get_contents($jsonFile);

// Удаляем начальную запятую, если она есть
$jsonContent = ltrim($jsonContent, ',');

// Оборачиваем в массив, если это не массив
if (!str_starts_with(trim($jsonContent), '[')) {
    $jsonContent = '[' . $jsonContent . ']';
}

$grades = json_decode($jsonContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo $OUTPUT->notification('Ошибка парсинга JSON: ' . json_last_error_msg(), 'error');
    echo $OUTPUT->footer();
    exit;
}

if (empty($grades) || !is_array($grades)) {
    echo $OUTPUT->notification('JSON файл пуст или имеет неверный формат', 'error');
    echo $OUTPUT->footer();
    exit;
}

echo html_writer::tag('p', 'Найдено записей в JSON: ' . count($grades), ['class' => 'alert alert-info']);

// Ограничиваем 1000 записями
$limit = 1000;
$gradesToImport = array_slice($grades, 0, $limit);

echo html_writer::tag('p', 'Будет импортировано записей: ' . count($gradesToImport), ['class' => 'alert alert-warning']);

// Начинаем импорт
$imported = 0;
$skipped = 0;
$errors = 0;

echo html_writer::start_tag('div', ['class' => 'import-progress']);
echo html_writer::tag('h3', 'Процесс импорта:');
echo html_writer::start_tag('pre', ['style' => 'background: #f5f5f5; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto;']);

foreach ($gradesToImport as $index => $gradeData) {
    try {
        // Проверяем обязательные поля
        if (empty($gradeData['userid']) || empty($gradeData['courseid']) || !isset($gradeData['grade'])) {
            echo sprintf("[%d] Пропущено: отсутствуют обязательные поля (userid: %s, courseid: %s)\n",
                $index + 1,
                $gradeData['userid'] ?? 'N/A',
                $gradeData['courseid'] ?? 'N/A'
            );
            $skipped++;
            continue;
        }

        // Проверяем существование пользователя
        if (!$DB->record_exists('user', ['id' => $gradeData['userid']])) {
            echo sprintf("[%d] Пропущено: пользователь ID %d не существует\n",
                $index + 1,
                $gradeData['userid']
            );
            $skipped++;
            continue;
        }

        // Проверяем существование курса
        if (!$DB->record_exists('course', ['id' => $gradeData['courseid']])) {
            echo sprintf("[%d] Пропущено: курс ID %d не существует\n",
                $index + 1,
                $gradeData['courseid']
            );
            $skipped++;
            continue;
        }

        // Создаем запись
        $record = new stdClass();
        $record->userid = (int)$gradeData['userid'];
        $record->courseid = (int)$gradeData['courseid'];
        $record->grade = (float)$gradeData['grade'];
        $record->modulename = $gradeData['modulename'] ?? 'Неизвестный модуль';
        $record->moduletype = $gradeData['moduletype'] ?? 'unknown';
        $record->timecreated = !empty($gradeData['timecreated']) ? (int)$gradeData['timecreated'] : time();

        // Вставляем запись
        $id = $DB->insert_record('local_cdo_ag_grade_notifications', $record);

        if ($id) {
            echo sprintf("[%d] ✓ Импортировано: User %d, Course %d, Grade %.2f, Module: %s\n",
                $index + 1,
                $record->userid,
                $record->courseid,
                $record->grade,
                mb_substr($record->modulename, 0, 50)
            );
            $imported++;
        } else {
            echo sprintf("[%d] ✗ Ошибка вставки записи\n", $index + 1);
            $errors++;
        }

        // Flush вывод каждые 50 записей
        if (($index + 1) % 50 == 0) {
            flush();
            ob_flush();
        }

    } catch (Exception $e) {
        echo sprintf("[%d] ✗ Ошибка: %s\n", $index + 1, $e->getMessage());
        $errors++;
    }
}

echo html_writer::end_tag('pre');
echo html_writer::end_tag('div');

// Итоговая статистика
echo html_writer::start_div('import-summary', ['style' => 'margin-top: 20px;']);
echo html_writer::tag('h3', 'Результаты импорта:');

$stats = html_writer::start_tag('table', ['class' => 'generaltable']);
$stats .= html_writer::start_tag('tbody');

$stats .= html_writer::start_tag('tr');
$stats .= html_writer::tag('th', 'Всего записей в JSON:');
$stats .= html_writer::tag('td', count($grades));
$stats .= html_writer::end_tag('tr');

$stats .= html_writer::start_tag('tr');
$stats .= html_writer::tag('th', 'Обработано записей:');
$stats .= html_writer::tag('td', count($gradesToImport));
$stats .= html_writer::end_tag('tr');

$stats .= html_writer::start_tag('tr', ['class' => 'alert alert-success']);
$stats .= html_writer::tag('th', '✓ Успешно импортировано:');
$stats .= html_writer::tag('td', html_writer::tag('strong', $imported));
$stats .= html_writer::end_tag('tr');

$stats .= html_writer::start_tag('tr', ['class' => 'alert alert-warning']);
$stats .= html_writer::tag('th', '⊘ Пропущено:');
$stats .= html_writer::tag('td', html_writer::tag('strong', $skipped));
$stats .= html_writer::end_tag('tr');

$stats .= html_writer::start_tag('tr', ['class' => 'alert alert-danger']);
$stats .= html_writer::tag('th', '✗ Ошибок:');
$stats .= html_writer::tag('td', html_writer::tag('strong', $errors));
$stats .= html_writer::end_tag('tr');

$stats .= html_writer::end_tag('tbody');
$stats .= html_writer::end_tag('table');

echo $stats;
echo html_writer::end_div();

// Ссылка на пример дайджеста
if ($imported > 0) {
    // Находим пользователя с наибольшим количеством оценок
    $topUser = $DB->get_record_sql(
        "SELECT u.id, u.firstname, u.lastname, COUNT(lgn.id) as grades_count
         FROM {user} u
         INNER JOIN {local_cdo_ag_grade_notifications} lgn ON lgn.userid = u.id
         GROUP BY u.id, u.firstname, u.lastname
         ORDER BY grades_count DESC
         LIMIT 1"
    );
    
    echo html_writer::start_div('', ['style' => 'margin-top: 30px;']);
    
    if ($topUser) {
        echo html_writer::tag('h3', 'Просмотр дайджеста оценок');
        echo html_writer::tag('p', 
            'Пользователь с наибольшим количеством оценок: ' . fullname($topUser) . 
            ' (' . $topUser->grades_count . ' оценок)',
            ['class' => 'alert alert-info']
        );
        
        echo html_writer::tag('p',
            html_writer::link(
                new moodle_url('/local/cdo_ag_tools/grade_digest_example.php', ['userid' => $topUser->id]),
                'Посмотреть дайджест для ' . fullname($topUser) . ' →',
                ['class' => 'btn btn-success btn-lg', 'style' => 'margin-right: 10px;']
            ) .
            html_writer::link(
                new moodle_url('/local/cdo_ag_tools/grade_digest_example.php'),
                'Выбрать другого пользователя',
                ['class' => 'btn btn-primary btn-lg']
            )
        );
    } else {
        echo html_writer::tag('p',
            html_writer::link(
                new moodle_url('/local/cdo_ag_tools/grade_digest_example.php'),
                'Посмотреть примеры дайджестов оценок →',
                ['class' => 'btn btn-primary btn-lg']
            )
        );
    }
    
    echo html_writer::end_div();
}

echo $OUTPUT->footer();

