<?php

require_once(__DIR__ . "/../../../../config.php");
defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE, $OUTPUT, $DB;

// Получаем обязательные параметры
$flowId = required_param('flow_id', PARAM_INT);
$userId = required_param('user_id', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$context = \context_system::instance();
require_login();
require_capability('moodle/site:config', $context);

// Проверяем, что пользователь и модуль существуют
$user = get_complete_user_data('id', $userId);
if (!$user) {
    throw new \moodle_exception('invaliduser', 'error');
}
$courseModule = get_coursemodule_from_id('', $cmid, 0, false, MUST_EXIST);

// Поиск потока
$streamRepository = new \local_cdo_unti2035bas\infrastructure\persistence\stream_repository();
$stream = $streamRepository->find_by_flow_id($flowId);

if (!$stream) {
    throw new \moodle_exception('streamnotfound', 'local_cdo_unti2035bas', '', $flowId);
}

$fullname = trim($user->firstname . ' ' . $user->lastname);

// Настройка страницы
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new \moodle_url('/local/cdo_unti2035bas/pages/video/delete_progress.php', [
    'flow_id' => $flowId, 
    'user_id' => $userId,
    'cmid' => $cmid
]));
$title = get_string('deleteprogress', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Подключение CSS стилей
$PAGE->requires->css('/local/cdo_unti2035bas/styles.css');

echo $OUTPUT->header();

// Контейнер для всего содержимого
echo \html_writer::start_div('delete-progress-container');

// Получаем информацию о следах прогресса
$videoProgressData = $DB->get_records('local_videoprogress', [
    'userid' => $userId,
    'cmid' => $cmid
], 'id DESC');

if (empty($videoProgressData)) {
    echo $OUTPUT->notification(get_string('noprogressdata', 'local_cdo_unti2035bas'), 'info');
    echo \html_writer::link(
        new \moodle_url('/local/cdo_unti2035bas/pages/video/video_statement_for_user.php', [
            'flow_id' => $flowId,
            'user_id' => $userId
        ]),
        get_string('back'),
        ['class' => 'btn btn-secondary']
    );
    echo $OUTPUT->footer();
    exit;
}

// Получаем информацию о модуле
$moduleInfo = $DB->get_record_sql(
    "SELECT cm.id as cmid, cm.module, cm.instance, cs.name as sectionname, cs.section as sectionnumber,
            m.name as moduletype, a.name as activityname
     FROM {course_modules} cm
     JOIN {modules} m ON m.id = cm.module
     JOIN {course_sections} cs ON cs.id = cm.section
     LEFT JOIN {cdo_unti2035bas_activity} a ON a.modid = cm.id
     WHERE cm.id = :cmid",
    ['cmid' => $cmid]
);

$activityName = $moduleInfo->activityname ?: 'Activity ID: ' . $cmid;
$sectionDisplay = $moduleInfo->sectionnumber . '. ' . ($moduleInfo->sectionname ?: get_string('section') . ' ' . $moduleInfo->sectionnumber);

// Отображение информации о пользователе и активности
echo \html_writer::start_div('delete-info-section');
echo $OUTPUT->heading(get_string('deleteprogress', 'local_cdo_unti2035bas'), 3);
echo \html_writer::start_div('delete-info-grid');

echo \html_writer::div(
    \html_writer::div(get_string('userid', 'local_cdo_unti2035bas'), 'info-label') .
    \html_writer::div($userId, 'info-value'),
    'delete-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('fullname'), 'info-label') .
    \html_writer::div($fullname, 'info-value'),
    'delete-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('email'), 'info-label') .
    \html_writer::div($user->email, 'info-value'),
    'delete-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('cmid', 'local_cdo_unti2035bas'), 'info-label') .
    \html_writer::div($cmid, 'info-value'),
    'delete-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('section'), 'info-label') .
    \html_writer::div($sectionDisplay, 'info-value'),
    'delete-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('name'), 'info-label') .
    \html_writer::div($activityName, 'info-value'),
    'delete-info-item'
);

echo \html_writer::end_div(); // delete-info-grid
echo \html_writer::end_div(); // delete-info-section

// Отображение информации о следах прогресса
echo \html_writer::start_div('progress-data-section');
echo $OUTPUT->heading(get_string('progressdata', 'local_cdo_unti2035bas'), 4);

$table = new \html_table();
$table->head = [
    get_string('id'),
    get_string('progress', 'local_cdo_unti2035bas'),
    get_string('duration', 'local_cdo_unti2035bas'),
    get_string('timecreated', 'local_cdo_unti2035bas')
];
$table->attributes['class'] = 'generaltable progress-data-table';

foreach ($videoProgressData as $progress) {
    $table->data[] = [
        $progress->id,
        round($progress->progress, 1) . '%',
        gmdate('H:i:s', $progress->duration),
        userdate($progress->timecreated, get_string('strftimedatetime'))
    ];
}

echo \html_writer::table($table);
echo \html_writer::end_div(); // progress-data-section

// Обработка подтверждения удаления
if ($confirm) {
    try {
        // Начинаем транзакцию
        $transaction = $DB->start_delegated_transaction();
        
        // Удаляем все следы прогресса для данного пользователя и модуля
        $deletedCount = $DB->delete_records('local_videoprogress', [
            'userid' => $userId,
            'cmid' => $cmid
        ]);
        
        // Подтверждаем транзакцию
        $transaction->allow_commit();
        
        // Логируем действие
        debugging("Deleted {$deletedCount} video progress records for user {$userId}, cmid {$cmid}", DEBUG_DEVELOPER);
        
        echo $OUTPUT->notification(
            get_string('progressdeletedsuccessfully', 'local_cdo_unti2035bas', [
                'progresscount' => $deletedCount,
                'statementscount' => 0
            ]), 
            'success'
        );
        
        // Кнопка возврата
        echo \html_writer::start_div('navigation-back');
        echo \html_writer::link(
            new \moodle_url('/local/cdo_unti2035bas/pages/video/video_statement_for_user.php', [
                'flow_id' => $flowId,
                'user_id' => $userId
            ]),
            get_string('back'),
            ['class' => 'btn btn-primary']
        );
        echo \html_writer::end_div();
        
    } catch (\Exception $e) {
        // Откатываем транзакцию в случае ошибки
        $transaction->rollback($e);
        
        echo $OUTPUT->notification(
            get_string('errorprogressdeletion', 'local_cdo_unti2035bas') . ': ' . $e->getMessage(), 
            'error'
        );
        
        // Кнопка возврата
        echo \html_writer::start_div('navigation-back');
        echo \html_writer::link(
            new \moodle_url('/local/cdo_unti2035bas/pages/video/video_statement_for_user.php', [
                'flow_id' => $flowId,
                'user_id' => $userId
            ]),
            get_string('back'),
            ['class' => 'btn btn-secondary']
        );
        echo \html_writer::end_div();
    }
} else {
    // Показываем подтверждение удаления
    echo \html_writer::start_div('confirmation-section');
    echo $OUTPUT->heading(get_string('confirmdeletion', 'local_cdo_unti2035bas'), 4);
    
    $confirmMessage = get_string('confirmdeleteprogressmessage', 'local_cdo_unti2035bas', [
        'count' => count($videoProgressData),
        'username' => $fullname,
        'activity' => $activityName
    ]);
    
    echo \html_writer::div($confirmMessage, 'confirmation-message');
    
    // Кнопки подтверждения
    echo \html_writer::start_div('confirmation-buttons');
    
    $confirmUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/delete_progress.php', [
        'flow_id' => $flowId,
        'user_id' => $userId,
        'cmid' => $cmid,
        'confirm' => 1
    ]);
    
    echo \html_writer::link(
        $confirmUrl,
        get_string('confirmdelete', 'local_cdo_unti2035bas'),
        ['class' => 'btn btn-danger']
    );
    
    echo \html_writer::link(
        new \moodle_url('/local/cdo_unti2035bas/pages/video/video_statement_for_user.php', [
            'flow_id' => $flowId,
            'user_id' => $userId
        ]),
        get_string('cancel'),
        ['class' => 'btn btn-secondary']
    );
    
    echo \html_writer::end_div(); // confirmation-buttons
    echo \html_writer::end_div(); // confirmation-section
}

echo \html_writer::end_div(); // delete-progress-container

echo $OUTPUT->footer(); 