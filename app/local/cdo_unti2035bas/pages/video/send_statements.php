<?php

require_once(__DIR__ . "/../../../../config.php");
defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE, $OUTPUT, $DB;

use local_cdo_unti2035bas\video_progress\handler;

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
$PAGE->set_url(new \moodle_url('/local/cdo_unti2035bas/pages/video/send_statements.php', [
    'flow_id' => $flowId, 
    'user_id' => $userId,
    'cmid' => $cmid
]));
$title = get_string('sendstatements', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Подключение CSS стилей
$PAGE->requires->css('/local/cdo_unti2035bas/styles.css');

echo $OUTPUT->header();

// Контейнер для всего содержимого
echo \html_writer::start_div('send-statements-container');

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
echo \html_writer::start_div('send-info-section');
echo $OUTPUT->heading(get_string('sendstatements', 'local_cdo_unti2035bas'), 3);
echo \html_writer::start_div('send-info-grid');

echo \html_writer::div(
    \html_writer::div(get_string('userid', 'local_cdo_unti2035bas'), 'info-label') .
    \html_writer::div($userId, 'info-value'),
    'send-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('fullname'), 'info-label') .
    \html_writer::div($fullname, 'info-value'),
    'send-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('email'), 'info-label') .
    \html_writer::div($user->email, 'info-value'),
    'send-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('cmid', 'local_cdo_unti2035bas'), 'info-label') .
    \html_writer::div($cmid, 'info-value'),
    'send-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('section'), 'info-label') .
    \html_writer::div($sectionDisplay, 'info-value'),
    'send-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('name'), 'info-label') .
    \html_writer::div($activityName, 'info-value'),
    'send-info-item'
);

echo \html_writer::end_div(); // send-info-grid
echo \html_writer::end_div(); // send-info-section

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

// Обработка подтверждения отправки
if ($confirm) {
    try {
        $handler = new handler();
        $result = $handler->send_video_progress_for_user_module($userId, $cmid);
        
        // Отображаем результаты
        $messageParts = [];
        
        if ($result['sent'] > 0) {
            $messageParts[] = get_string('sentcount', 'local_cdo_unti2035bas', $result['sent']);
        }
        
        if ($result['skipped'] > 0) {
            $messageParts[] = get_string('skippedcount', 'local_cdo_unti2035bas', $result['skipped']);
        }
        
        if ($result['errors'] > 0) {
            $messageParts[] = get_string('errorscount', 'local_cdo_unti2035bas', $result['errors']);
        }
        
        $messageParts[] = get_string('totalprocessed', 'local_cdo_unti2035bas', $result['total']);
        
        $resultMessage = implode(', ', $messageParts);
        
        if ($result['errors'] > 0) {
            echo $OUTPUT->notification($resultMessage, 'warning');
            
            if (!empty($result['error_details'])) {
                echo $OUTPUT->notification(
                    get_string('errordetails', 'local_cdo_unti2035bas') . '<br>' . implode('<br>', $result['error_details']), 
                    'error'
                );
            }
        } else {
            echo $OUTPUT->notification($resultMessage, 'success');
        }
        
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
        echo $OUTPUT->notification(
            get_string('errorsendingstatements', 'local_cdo_unti2035bas') . ': ' . $e->getMessage(), 
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
    // Показываем подтверждение отправки
    echo \html_writer::start_div('confirmation-section');
    echo $OUTPUT->heading(get_string('confirmsending', 'local_cdo_unti2035bas'), 4);
    
    $confirmMessage = get_string('confirmsendstatementsmessage', 'local_cdo_unti2035bas', [
        'count' => count($videoProgressData),
        'username' => $fullname,
        'activity' => $activityName
    ]);
    
    echo \html_writer::div($confirmMessage, 'confirmation-message');
    
    // Кнопки подтверждения
    echo \html_writer::start_div('confirmation-buttons');
    
    $confirmUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/send_statements.php', [
        'flow_id' => $flowId,
        'user_id' => $userId,
        'cmid' => $cmid,
        'confirm' => 1
    ]);
    
    echo \html_writer::link(
        $confirmUrl,
        get_string('confirmsend', 'local_cdo_unti2035bas'),
        ['class' => 'btn btn-primary']
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

echo \html_writer::end_div(); // send-statements-container

echo $OUTPUT->footer(); 