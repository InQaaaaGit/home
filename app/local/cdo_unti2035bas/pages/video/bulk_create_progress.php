<?php

require_once(__DIR__ . "/../../../../config.php");
defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE, $OUTPUT, $DB;

use local_cdo_unti2035bas\video\dependencies;

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
$PAGE->set_url(new \moodle_url('/local/cdo_unti2035bas/pages/video/bulk_create_progress.php', [
    'flow_id' => $flowId, 
    'user_id' => $userId
]));
$title = get_string('bulkcreateprogress', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Подключение CSS стилей
$PAGE->requires->css('/local/cdo_unti2035bas/styles.css');

echo $OUTPUT->header();

// Контейнер для всего содержимого
echo \html_writer::start_div('bulk-create-progress-container');

// Получаем все видео активности из курса
try {
    $sql = "SELECT cm.id as cmid, cm.module, cm.instance, cm.section, 
                   m.name as moduletype, cs.name as sectionname, cs.section as sectionnumber,
                   a.type_ as activitytype, a.name as activityname, a.description as activitydescription
            FROM {course_modules} cm
            JOIN {modules} m ON m.id = cm.module
            JOIN {course_sections} cs ON cs.id = cm.section
            JOIN {cdo_unti2035bas_activity} a ON a.modid = cm.id
            WHERE cm.course = :courseid 
            AND cm.deletioninprogress = 0
            AND a.type_ = 'video'
            AND a.deleted = 0
            ORDER BY cs.section, cm.id";
    
    $courseModules = $DB->get_records_sql($sql, ['courseid' => $stream->moodle->courseid]);
    $videoActivities = [];
    
    foreach ($courseModules as $cm) {
        // Проверяем, есть ли уже следы прогресса для этого пользователя и модуля
        $existingProgress = $DB->get_records('local_videoprogress', [
            'userid' => $userId,
            'cmid' => $cm->cmid
        ]);
        
        if (empty($existingProgress)) {
            $videoActivities[$cm->cmid] = [
                'cmid' => $cm->cmid,
                'name' => $cm->activityname ?: 'Activity ID: ' . $cm->cmid,
                'section' => $cm->sectionname,
                'sectionnumber' => $cm->sectionnumber
            ];
        }
    }
    
    if (empty($videoActivities)) {
        echo $OUTPUT->notification(get_string('noprogressneeded', 'local_cdo_unti2035bas'), 'info');
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
    
    echo \html_writer::end_div(); // user-info-grid
    echo \html_writer::end_div(); // user-info-section
    
    // Отображение списка активностей для создания следов
    echo \html_writer::start_div('activities-section');
    echo $OUTPUT->heading(get_string('activitiestocreate', 'local_cdo_unti2035bas'), 4);
    
    $table = new \html_table();
    $table->head = [
        get_string('cmid', 'local_cdo_unti2035bas'),
        get_string('section'),
        get_string('name')
    ];
    $table->attributes['class'] = 'generaltable activities-table';
    
    foreach ($videoActivities as $cmid => $activity) {
        $sectionDisplay = $activity['sectionnumber'] . '. ' . ($activity['section'] ?: get_string('section') . ' ' . $activity['sectionnumber']);
        
        $table->data[] = [
            $cmid,
            $sectionDisplay,
            \html_writer::tag('strong', $activity['name'])
        ];
    }
    
    echo \html_writer::table($table);
    echo \html_writer::end_div(); // activities-section
    
    // Обработка подтверждения создания
    if ($confirm) {
        try {
            $createUseCase = dependencies::getVideoProgressCreateUseCase();
            $createdCount = 0;
            $errors = [];
            
            foreach ($videoActivities as $cmid => $activity) {
                try {
                    $result = $createUseCase->execute($userId, $cmid);
                    $createdCount++;
                } catch (\Exception $e) {
                    $errors[] = "Activity {$cmid}: " . $e->getMessage();
                }
            }
            
            if ($createdCount > 0) {
                echo $OUTPUT->notification(
                    get_string('bulkprogresscreated', 'local_cdo_unti2035bas', ['count' => $createdCount]), 
                    'success'
                );
            }
            
            if (!empty($errors)) {
                echo $OUTPUT->notification(
                    get_string('bulkprogresserrors', 'local_cdo_unti2035bas') . '<br>' . implode('<br>', $errors), 
                    'warning'
                );
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
                get_string('errorbulkcreateprogress', 'local_cdo_unti2035bas') . ': ' . $e->getMessage(), 
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
        // Показываем подтверждение создания
        echo \html_writer::start_div('confirmation-section');
        echo $OUTPUT->heading(get_string('confirmbulkcreation', 'local_cdo_unti2035bas'), 4);
        
        $confirmMessage = get_string('confirmbulkcreateprogressmessage', 'local_cdo_unti2035bas', [
            'count' => count($videoActivities),
            'username' => $fullname
        ]);
        
        echo \html_writer::div($confirmMessage, 'confirmation-message');
        
        // Кнопки подтверждения
        echo \html_writer::start_div('confirmation-buttons');
        
        $confirmUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/bulk_create_progress.php', [
            'flow_id' => $flowId,
            'user_id' => $userId,
            'confirm' => 1
        ]);
        
        echo \html_writer::link(
            $confirmUrl,
            get_string('confirmcreate', 'local_cdo_unti2035bas'),
            ['class' => 'btn btn-success']
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
    echo $OUTPUT->notification('Ошибка при получении активностей: ' . $e->getMessage(), 'error');
}

echo \html_writer::end_div(); // bulk-create-progress-container

echo $OUTPUT->footer(); 