<?php

require_once(__DIR__ . "/../../../../config.php");
defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE, $OUTPUT, $DB;

use local_cdo_unti2035bas\video_progress\handler;

// Получаем обязательные параметры
$flowId = required_param('flow_id', PARAM_INT);
$userId = required_param('user_id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$context = \context_system::instance();
require_login();
require_capability('moodle/site:config', $context);

// Проверяем, что пользователь существует
$user = get_complete_user_data('id', $userId);
if (!$user) {
    throw new \moodle_exception('invaliduser', 'error');
}

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
$PAGE->set_url(new \moodle_url('/local/cdo_unti2035bas/pages/video/bulk_send_user_statements.php', [
    'flow_id' => $flowId, 
    'user_id' => $userId
]));
$title = get_string('bulksendallstatements', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Подключение CSS стилей
$PAGE->requires->css('/local/cdo_unti2035bas/styles.css');

echo $OUTPUT->header();

// Контейнер для всего содержимого
echo \html_writer::start_div('bulk-send-statements-container');

// Получаем все следы прогресса пользователя в потоке
try {
    $sql = "SELECT lv.id, lv.cmid, lv.progress, lv.duration, lv.timecreated,
                   ua.lrid, ua.position, cm.course,
                   cs.name as sectionname, cs.section as sectionnumber,
                   a.name as activityname
            FROM {local_videoprogress} lv
            INNER JOIN {cdo_unti2035bas_activity} ua ON ua.modid = lv.cmid
            INNER JOIN {course_modules} cm ON cm.id = lv.cmid
            INNER JOIN {course_sections} cs ON cs.id = cm.section
            LEFT JOIN {cdo_unti2035bas_activity} a ON a.modid = lv.cmid
            INNER JOIN {cdo_unti2035bas_theme} t ON t.id = ua.themeid
            INNER JOIN {cdo_unti2035bas_module} m ON m.id = t.moduleid
            INNER JOIN {cdo_unti2035bas_block} b ON b.id = m.blockid
            INNER JOIN {cdo_unti2035bas_stream} s ON s.id = b.streamid
            WHERE lv.userid = ? AND s.untiflowid = ?
            ORDER BY cs.section, lv.cmid, lv.id";
    
    $videoProgressData = $DB->get_records_sql($sql, [$userId, $flowId]);
    
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
    
    // Отображение информации о пользователе
    echo \html_writer::start_div('user-info-section');
    echo $OUTPUT->heading(get_string('userinfo', 'local_cdo_unti2035bas'), 3);
    echo \html_writer::start_div('user-info-grid');
    
    echo \html_writer::div(
        \html_writer::div(get_string('userid', 'local_cdo_unti2035bas'), 'info-label') .
        \html_writer::div($userId, 'info-value'),
        'user-info-item'
    );
    
    echo \html_writer::div(
        \html_writer::div(get_string('fullname'), 'info-label') .
        \html_writer::div($fullname, 'info-value'),
        'user-info-item'
    );
    
    echo \html_writer::div(
        \html_writer::div(get_string('email'), 'info-label') .
        \html_writer::div($user->email, 'info-value'),
        'user-info-item'
    );
    
    echo \html_writer::div(
        \html_writer::div(get_string('flowid', 'local_cdo_unti2035bas'), 'info-label') .
        \html_writer::div($flowId, 'info-value'),
        'user-info-item'
    );
    
    echo \html_writer::end_div(); // user-info-grid
    echo \html_writer::end_div(); // user-info-section
    
    // Отображение списка следов прогресса для отправки
    echo \html_writer::start_div('progress-data-section');
    echo $OUTPUT->heading(get_string('progressdatatosend', 'local_cdo_unti2035bas'), 4);
    
    $table = new \html_table();
    $table->head = [
        get_string('id'),
        get_string('cmid', 'local_cdo_unti2035bas'),
        get_string('section'),
        get_string('name'),
        get_string('progress', 'local_cdo_unti2035bas'),
        get_string('duration', 'local_cdo_unti2035bas'),
        get_string('timecreated', 'local_cdo_unti2035bas')
    ];
    $table->attributes['class'] = 'generaltable progress-data-table';
    
    foreach ($videoProgressData as $progress) {
        $sectionDisplay = $progress->sectionnumber . '. ' . ($progress->sectionname ?: get_string('section') . ' ' . $progress->sectionnumber);
        $activityName = $progress->activityname ?: 'Activity ID: ' . $progress->cmid;
        
        $table->data[] = [
            $progress->id,
            $progress->cmid,
            $sectionDisplay,
            \html_writer::tag('strong', $activityName),
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
            $result = $handler->send_video_progress_for_user($userId, $flowId);
            
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
                get_string('errorbulksendingstatements', 'local_cdo_unti2035bas') . ': ' . $e->getMessage(), 
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
        echo $OUTPUT->heading(get_string('confirmbulksending', 'local_cdo_unti2035bas'), 4);
        
        $confirmMessage = get_string('confirmbulksendstatementsmessage', 'local_cdo_unti2035bas', [
            'count' => count($videoProgressData),
            'username' => $fullname
        ]);
        
        echo \html_writer::div($confirmMessage, 'confirmation-message');
        
        // Кнопки подтверждения
        echo \html_writer::start_div('confirmation-buttons');
        
        $confirmUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/bulk_send_user_statements.php', [
            'flow_id' => $flowId,
            'user_id' => $userId,
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
    
} catch (Exception $e) {
    echo $OUTPUT->notification('Ошибка при получении данных: ' . $e->getMessage(), 'error');
}

echo \html_writer::end_div(); // bulk-send-statements-container

echo $OUTPUT->footer(); 