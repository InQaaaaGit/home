<?php

require_once(__DIR__ . "/../../../config.php");
defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE, $OUTPUT, $DB;

// Require group lib functions
require_once($CFG->dirroot . '/group/lib.php');

// Получаем обязательный параметр flow_id
$flowId = required_param('flow_id', PARAM_INT);

$context = \context_system::instance();
require_login();
require_capability('moodle/site:config', $context);

// Настройка страницы
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new \moodle_url('/local/cdo_unti2035bas/pages/video_statement_management.php', ['flow_id' => $flowId]));
$title = get_string('videostatementsmanagement', 'local_cdo_unti2035bas') . ': ' . $flowId;
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Подключение CSS стилей
$PAGE->requires->css('/local/cdo_unti2035bas/styles.css');

// Инициализация сервисов
$streamRepository = new \local_cdo_unti2035bas\infrastructure\persistence\stream_repository();
$moodleService = new \local_cdo_unti2035bas\infrastructure\moodle\moodle_service();

// Поиск потока по flow_id
try {
    $stream = $streamRepository->find_by_flow_id($flowId);
    if (!$stream) {
        // Если поток не найден, покажем полезную информацию
        echo $OUTPUT->header();
        echo $OUTPUT->notification('Поток с Flow ID ' . $flowId . ' не найден в базе данных.', 'error');
        
        // Показываем доступные Flow ID для удобства
        $availableFlowIds = $DB->get_fieldset_select('cdo_unti2035bas_stream', 'untiflowid', 'untiflowid IS NOT NULL AND untiflowid != 0');
        if (!empty($availableFlowIds)) {
            echo \html_writer::tag('p', 'Доступные Flow IDs: ' . implode(', ', array_unique($availableFlowIds)));
            echo \html_writer::tag('p', 'Попробуйте использовать один из них в URL.');
        }
        
        echo \html_writer::link(
            new \moodle_url('/local/cdo_unti2035bas/streams.php'),
            'Вернуться к списку потоков',
            ['class' => 'btn btn-secondary']
        );
        echo $OUTPUT->footer();
        exit;
    }
} catch (Exception $e) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification('Ошибка при поиске потока: ' . $e->getMessage(), 'error');
    echo $OUTPUT->footer();
    exit;
}

// Получение информации о группе и курсе
$groupId = $stream->moodle->groupid;
$courseId = $stream->moodle->courseid;

// Получение участников группы
try {
    $groupMembers = groups_get_members($groupId, 'u.id, u.firstname, u.lastname, u.email, u.username');
    
    if (empty($groupMembers)) {
        $users = [];
    } else {
        $users = array_values($groupMembers);
    }
} catch (Exception $e) {
    // В случае ошибки инициализируем пустой массив
    $users = [];
    debugging('Error getting group members for group ' . $groupId . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
}

// Получение информации о курсе и группе для отображения
$courses = $moodleService->get_courses([$courseId]);
$groups = $moodleService->get_groups([$groupId]);

$courseName = $courses[$courseId]['fullname'] ?? '';
$groupName = $groups[$groupId] ?? '';

echo $OUTPUT->header();

// Контейнер для всего содержимого
echo \html_writer::start_div('video-statements-container');

// Отображение информации о потоке
echo \html_writer::start_div('flow-info-section');
echo $OUTPUT->heading(get_string('flowinfo', 'local_cdo_unti2035bas'), 3);
echo \html_writer::start_div('flow-info-grid');

echo \html_writer::div(
    \html_writer::div(get_string('flowid', 'local_cdo_unti2035bas'), 'info-label') .
    \html_writer::div($flowId, 'info-value'),
    'flow-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('course'), 'info-label') .
    \html_writer::div($courseName, 'info-value'),
    'flow-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('group'), 'info-label') .
    \html_writer::div($groupName, 'info-value'),
    'flow-info-item'
);

echo \html_writer::end_div(); // flow-info-grid
echo \html_writer::end_div(); // flow-info-section

// Отображение таблицы пользователей
echo \html_writer::start_div('users-table-section');
echo $OUTPUT->heading(get_string('streamusers', 'local_cdo_unti2035bas'), 3);

if (empty($users)) {
    echo $OUTPUT->notification(get_string('nousersinstream', 'local_cdo_unti2035bas'), 'warning');
} else {
    // Создание таблицы пользователей
    $table = new \html_table();
    $table->head = [
        get_string('userid', 'local_cdo_unti2035bas'),
        get_string('fullname'),
        get_string('email'),
        get_string('username'),
        get_string('actions', 'local_cdo_unti2035bas')
    ];
    $table->attributes['class'] = 'generaltable users-table';
    
    foreach ($users as $user) {
        // Создаем полное имя безопасным способом
        $fullname = trim($user->firstname . ' ' . $user->lastname);
        $profileUrl = new \moodle_url('/user/profile.php', ['id' => $user->id]);
        $profileLink = \html_writer::link($profileUrl, $fullname);
        
        // Кнопки действий для пользователя
        $actions = [];
        
        // Кнопка работы со следами по видео пользователя
        $videoStatementsUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/video_statement_for_user.php', [
            'flow_id' => $flowId,
            'user_id' => $user->id
        ]);
        $actions[] = \html_writer::link(
            $videoStatementsUrl,
            get_string('videostatements', 'local_cdo_unti2035bas'),
            ['class' => 'btn btn-sm btn-secondary']
        );
        
        // Кнопка отправки video statements для пользователя
        $sendStatementsUrl = new \moodle_url('/local/cdo_unti2035bas/pages/send_user_video_statements.php', [
            'flow_id' => $flowId,
            'user_id' => $user->id
        ]);
        $actions[] = \html_writer::link(
            $sendStatementsUrl,
            get_string('sendstatements', 'local_cdo_unti2035bas'),
            ['class' => 'btn btn-sm btn-primary']
        );
        
        $actionsHtml = implode(' ', $actions);
        
        $table->data[] = [
            $user->id,
            $profileLink,
            $user->email,
            $user->username,
            $actionsHtml
        ];
    }
    
    echo \html_writer::table($table);
    echo \html_writer::end_div(); // users-table-section
    
    // Кнопки массовых действий
    echo \html_writer::start_div('bulk-actions-section');
    echo $OUTPUT->heading(get_string('bulkactions', 'local_cdo_unti2035bas'), 4);
    
    $bulkActions = [];
    
    // Массовая отправка statements для всех пользователей потока
    $bulkSendUrl = new \moodle_url('/local/cdo_unti2035bas/pages/bulk_send_video_statements.php', [
        'flow_id' => $flowId
    ]);
    $bulkActions[] = \html_writer::link(
        $bulkSendUrl,
        get_string('bulksendstatements', 'local_cdo_unti2035bas'),
        ['class' => 'btn btn-primary']
    );
    
    // Массовый просмотр отчетов
    $bulkReportUrl = new \moodle_url('/local/cdo_unti2035bas/pages/bulk_video_statements_report.php', [
        'flow_id' => $flowId
    ]);
    $bulkActions[] = \html_writer::link(
        $bulkReportUrl,
        get_string('bulkreport', 'local_cdo_unti2035bas'),
        ['class' => 'btn btn-secondary']
    );
    
    echo \html_writer::div(implode(' ', $bulkActions), 'bulk-actions-buttons');
    echo \html_writer::end_div(); // bulk-actions-section
}

echo \html_writer::end_div(); // users-table-section

// Навигация назад
echo \html_writer::start_div('navigation-back');
echo \html_writer::link(
    new \moodle_url('/local/cdo_unti2035bas/streams.php'),
    get_string('backtostreams', 'local_cdo_unti2035bas'),
    ['class' => 'btn btn-secondary']
);
echo \html_writer::end_div(); // navigation-back

echo \html_writer::end_div(); // video-statements-container

echo $OUTPUT->footer();
