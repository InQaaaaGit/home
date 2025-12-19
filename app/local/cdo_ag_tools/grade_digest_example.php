<?php
/**
 * Пример использования класса grade_digest
 *
 * @package    local_cdo_ag_tools
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_cdo_ag_tools\digest\grade_digest;

// Требуется авторизация
require_login();

$context = context_system::instance();

// Получаем ID пользователя (обязательный параметр)
$userid = optional_param('userid', 0, PARAM_INT);

// Настройка страницы
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/cdo_ag_tools/grade_digest_example.php', ['userid' => $userid]));
$PAGE->set_title(get_string('grade_digest', 'local_cdo_ag_tools'));
$PAGE->set_heading(get_string('grade_digest', 'local_cdo_ag_tools'));

// Добавляем breadcrumbs (навигационную цепочку)
$PAGE->navbar->add(get_string('pluginname', 'local_cdo_ag_tools'), new moodle_url('/local/cdo_ag_tools/'));
if (!empty($userid)) {
    $PAGE->navbar->add(get_string('grade_digest_example_link', 'local_cdo_ag_tools'), 
        new moodle_url('/local/cdo_ag_tools/grade_digest_example.php'));
    
    // Добавляем имя пользователя в breadcrumbs
    $user = $DB->get_record('user', ['id' => $userid], 'id, firstname, lastname');
    if ($user) {
        $PAGE->navbar->add(fullname($user));
    }
} else {
    $PAGE->navbar->add(get_string('grade_digest_example_link', 'local_cdo_ag_tools'));
}

// Подключаем CSS стили
$PAGE->requires->css('/local/cdo_ag_tools/styles/grade_digest.css');

// Вывод заголовка страницы
echo $OUTPUT->header();

// Если userid не указан, показываем форму выбора пользователя
if (empty($userid)) {
    echo $OUTPUT->heading('Выберите пользователя для просмотра дайджеста оценок', 2);
    
    // Получаем список пользователей с оценками
    $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, COUNT(lgn.id) as grades_count
            FROM {user} u
            INNER JOIN {local_cdo_ag_grade_notifications} lgn ON lgn.userid = u.id
            GROUP BY u.id, u.firstname, u.lastname, u.email
            ORDER BY grades_count DESC, u.lastname, u.firstname
            LIMIT 50";
    
    $users = $DB->get_records_sql($sql);
    
    if (empty($users)) {
        echo $OUTPUT->notification('Нет пользователей с оценками в системе. Сначала импортируйте данные.', 'warning');
        echo html_writer::tag('p', 
            html_writer::link(
                new moodle_url('/local/cdo_ag_tools/import_grades_from_json.php'),
                'Перейти к импорту оценок →',
                ['class' => 'btn btn-primary']
            )
        );
    } else {
        echo html_writer::tag('p', 'Список пользователей с оценками (топ-50 по количеству оценок):', 
            ['class' => 'alert alert-info']);
        
        // Таблица с пользователями
        $table = new html_table();
        $table->head = ['#', 'Пользователь', 'Email', 'Количество оценок', 'Действие'];
        $table->attributes['class'] = 'generaltable';
        $table->data = [];
        
        $counter = 1;
        foreach ($users as $user) {
            $fullname = fullname($user);
            $viewurl = new moodle_url('/local/cdo_ag_tools/grade_digest_example.php', ['userid' => $user->id]);
            
            $row = [];
            $row[] = $counter++;
            $row[] = $fullname;
            $row[] = $user->email;
            $row[] = html_writer::tag('span', $user->grades_count, ['class' => 'badge badge-info']);
            $row[] = html_writer::link($viewurl, 'Посмотреть дайджест →', ['class' => 'btn btn-sm btn-primary']);
            
            $table->data[] = $row;
        }
        
        echo html_writer::table($table);
    }
    
    echo $OUTPUT->footer();
    exit;
}

// Проверяем существование пользователя
if (!$DB->record_exists('user', ['id' => $userid])) {
    echo $OUTPUT->notification('Пользователь не найден', 'error');
    echo html_writer::tag('p', 
        html_writer::link(
            new moodle_url('/local/cdo_ag_tools/grade_digest_example.php'),
            '← Вернуться к списку пользователей',
            ['class' => 'btn btn-secondary']
        )
    );
    echo $OUTPUT->footer();
    exit;
}

// Проверяем права доступа (пользователь может просматривать только свои оценки, если не является администратором)
if ($userid != $USER->id && !has_capability('moodle/site:config', $context)) {
    echo $OUTPUT->notification('У вас нет прав для просмотра оценок других пользователей', 'error');
    echo html_writer::tag('p', 
        html_writer::link(
            new moodle_url('/local/cdo_ag_tools/grade_digest_example.php'),
            '← Вернуться к списку пользователей',
            ['class' => 'btn btn-secondary']
        )
    );
    echo $OUTPUT->footer();
    exit;
}

// Добавляем навигацию
$backurl = new moodle_url('/local/cdo_ag_tools/grade_digest_example.php');
echo html_writer::tag('p', 
    html_writer::link($backurl, '← Вернуться к списку пользователей', ['class' => 'btn btn-secondary']),
    ['style' => 'margin-bottom: 20px;']
);

// Пример 1: Дайджест за все время
echo $OUTPUT->heading(get_string('grade_digest', 'local_cdo_ag_tools'), 2);
$digest = new grade_digest($userid);
echo $digest->generate_html_digest();

echo html_writer::empty_tag('hr', ['style' => 'margin: 40px 0;']);

// Пример 2: Дайджест за последние 30 дней
echo $OUTPUT->heading('Дайджест за последние 30 дней', 2);
$digest30 = new grade_digest($userid);
$digest30->set_last_days(30);
echo $digest30->generate_html_digest();

echo html_writer::empty_tag('hr', ['style' => 'margin: 40px 0;']);

// Пример 3: Дайджест за текущий месяц
echo $OUTPUT->heading('Дайджест за текущий месяц', 2);
$digestMonth = new grade_digest($userid);
$digestMonth->set_current_month();
echo $digestMonth->generate_html_digest();

echo html_writer::empty_tag('hr', ['style' => 'margin: 40px 0;']);

// Пример 4: Дайджест за текущий год
echo $OUTPUT->heading('Дайджест за текущий год', 2);
$digestYear = new grade_digest($userid);
$digestYear->set_current_year();
echo $digestYear->generate_html_digest();

echo html_writer::empty_tag('hr', ['style' => 'margin: 40px 0;']);

// Пример 5: Дайджест за произвольный период
$dateFrom = strtotime('2024-01-01');
$dateTo = strtotime('2024-12-31');
echo $OUTPUT->heading('Дайджест за 2024 год', 2);
$digestCustom = new grade_digest($userid, $dateFrom, $dateTo);
echo $digestCustom->generate_html_digest();

// Вывод футера
echo $OUTPUT->footer();

