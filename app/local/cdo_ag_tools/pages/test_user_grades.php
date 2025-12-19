<?php
/**
 * Тестовая страница для проверки работы user_grades.php
 *
 * @package   local_cdo_ag_tools
 * @copyright 2025
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

global $PAGE, $OUTPUT, $DB, $USER;

require_login();

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url(new moodle_url('/local/cdo_ag_tools/pages/test_user_grades.php'));
$PAGE->set_heading('Тест страницы оценок пользователя');
$PAGE->set_title('Тест страницы оценок пользователя');
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

echo html_writer::tag('h2', 'Тест страницы отображения оценок пользователя');

echo html_writer::tag('p', 'Страница показывает оценки из всех курсов, на которых вы обучаетесь.');

// Ссылка на просмотр своих оценок
$url = new moodle_url('/local/cdo_ag_tools/pages/user_grades.php');
echo '<div class="alert alert-info" style="max-width: 800px;">';
echo '<h4>Просмотр своих оценок</h4>';
echo html_writer::link($url, 'Перейти к просмотру оценок', ['class' => 'btn btn-primary']);
echo '</div>';

// Получаем список курсов текущего пользователя
$courses = enrol_get_users_courses($USER->id, true, 'id, fullname, shortname');

if (empty($courses)) {
    echo $OUTPUT->notification('У вас нет доступных курсов', 'notifymessage');
} else {
    echo '<hr>';

    // Пример использования класса-помощника
    echo html_writer::tag('h3', 'Пример использования класса-помощника');

    $firstcourse = reset($courses);

    try {
        $helper = new \local_cdo_ag_tools\helpers\user_grades_helper();

        // Получаем оценки с категориями
        $gradesdata = $helper::getUserGradesWithCategories($firstcourse->id, $USER->id);

        echo '<div class="alert alert-info">';
        echo '<h4>Статистика для курса: ' . format_string($firstcourse->fullname) . '</h4>';
        echo '<p>Всего элементов оценок: ' . count($gradesdata['items']) . '</p>';
        echo '<p>Категорий с итогами: ' . count($gradesdata['category_totals']) . '</p>';

        if ($gradesdata['course_total']) {
            echo '<p>Итоговая оценка курса: ' . $gradesdata['course_total']['gradevalue_formatted'] . '</p>';
        }
        echo '</div>';

        // Получаем статистику
        $statistics = $helper::getUserGradesStatistics($firstcourse->id, $USER->id);

        echo '<div class="alert alert-success">';
        echo '<h4>Детальная статистика</h4>';
        echo '<ul>';
        echo '<li>Всего элементов: ' . $statistics['total_items'] . '</li>';
        echo '<li>Оценено элементов: ' . $statistics['graded_items'] . '</li>';
        echo '<li>Средний процент выполнения: ' . number_format($statistics['average_percentage'], 2) . '%</li>';

        if (!is_null($statistics['min_percentage'])) {
            echo '<li>Минимальный процент: ' . number_format($statistics['min_percentage'], 2) . '%</li>';
        }

        if (!is_null($statistics['max_percentage'])) {
            echo '<li>Максимальный процент: ' . number_format($statistics['max_percentage'], 2) . '%</li>';
        }

        echo '<li>Процент завершения: ' . number_format($statistics['completion_rate'], 2) . '%</li>';
        echo '</ul>';
        echo '</div>';

    } catch (Exception $e) {
        echo $OUTPUT->notification('Ошибка при получении данных: ' . $e->getMessage(), 'notifyproblem');
    }
}

// Добавляем информацию о правах доступа
echo '<hr>';
echo html_writer::tag('h3', 'Информация о правах доступа');
echo '<div class="alert alert-warning">';
echo '<p>Для просмотра оценок требуются следующие права:</p>';
echo '<ul>';
echo '<li><code>moodle/grade:view</code> - для просмотра своих оценок</li>';
echo '<li><code>moodle/grade:viewall</code> - для просмотра оценок всех пользователей (преподаватели, администраторы)</li>';
echo '</ul>';
echo '</div>';

echo $OUTPUT->footer();

