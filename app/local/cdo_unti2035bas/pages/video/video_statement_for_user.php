<?php

require_once(__DIR__ . "/../../../../config.php");
defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE, $OUTPUT, $DB;

// Require video progress lib
require_once($CFG->dirroot . '/group/lib.php');

// Получаем обязательные параметры
$flowId = required_param('flow_id', PARAM_INT);
$userId = required_param('user_id', PARAM_INT);

$context = \context_system::instance();
require_login();
require_capability('moodle/site:config', $context);

// Получаем информацию о пользователе
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

$moodleService = new \local_cdo_unti2035bas\infrastructure\moodle\moodle_service();

// Получение информации о курсе и группе
$courseId = $stream->moodle->courseid;
$groupId = $stream->moodle->groupid;
$courses = $moodleService->get_courses([$courseId]);
$groups = $moodleService->get_groups([$groupId]);

$courseName = $courses[$courseId]['fullname'] ?? '';
$groupName = $groups[$groupId] ?? '';
$fullname = trim($user->firstname . ' ' . $user->lastname);

// Настройка страницы
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new \moodle_url('/local/cdo_unti2035bas/pages/video/video_statement_for_user.php', [
    'flow_id' => $flowId, 
    'user_id' => $userId
]));
$title = get_string('videostatements', 'local_cdo_unti2035bas') . ': ' . $fullname . ' (ID: ' . $userId . ')';
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Подключение CSS стилей
#$PAGE->requires->css('/local/cdo_unti2035bas/styles.css');

echo $OUTPUT->header();

// Контейнер для всего содержимого
echo \html_writer::start_div('video-statements-container');

// Отображение информации о пользователе и потоке
echo \html_writer::start_div('flow-info-section');
echo $OUTPUT->heading(get_string('userinfo', 'local_cdo_unti2035bas'), 3);
echo \html_writer::start_div('flow-info-grid');

echo \html_writer::div(
    \html_writer::div(get_string('userid', 'local_cdo_unti2035bas'), 'info-label') .
    \html_writer::div($userId, 'info-value'),
    'flow-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('fullname'), 'info-label') .
    \html_writer::div($fullname, 'info-value'),
    'flow-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('email'), 'info-label') .
    \html_writer::div($user->email, 'info-value'),
    'flow-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('flowid', 'local_cdo_unti2035bas'), 'info-label') .
    \html_writer::div($flowId, 'info-value'),
    'flow-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('course'), 'info-label') .
    \html_writer::div(
        \html_writer::link(
            new \moodle_url('/course/view.php', ['id' => $courseId]),
            $courseName
        ),
        'info-value'
    ),
    'flow-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('group'), 'info-label') .
    \html_writer::div($groupName, 'info-value'),
    'flow-info-item'
);

echo \html_writer::end_div(); // flow-info-grid
echo \html_writer::end_div(); // flow-info-section

// Секция видео statements
echo \html_writer::start_div('users-table-section');
echo $OUTPUT->heading(get_string('videostatements', 'local_cdo_unti2035bas'), 3);

// Получаем все видео активности из курса
try {
    // Получаем все course modules из курса, которые содержат видео согласно таблице активностей
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
    
    $courseModules = $DB->get_records_sql($sql, ['courseid' => $courseId]);
    $videoActivities = [];
    
    foreach ($courseModules as $cm) {
        // Получаем детальную информацию о модуле
        $moduleData = null;
        
        if ($cm->moduletype === 'resource') {
            $resource = $DB->get_record('resource', ['id' => $cm->instance]);
            if ($resource) {
                $moduleData = [
                    'cmid' => $cm->cmid,
                    'name' => $cm->activityname ?: $resource->name,
                    'intro' => $cm->activitydescription ?: strip_tags($resource->intro),
                    'modulename' => 'resource',
                    'section' => $cm->sectionname,
                    'sectionnumber' => $cm->sectionnumber,
                    'activitytype' => $cm->activitytype
                ];
            }
        } elseif ($cm->moduletype === 'page') {
            $page = $DB->get_record('page', ['id' => $cm->instance]);
            if ($page) {
                $moduleData = [
                    'cmid' => $cm->cmid,
                    'name' => $cm->activityname ?: $page->name,
                    'intro' => $cm->activitydescription ?: strip_tags($page->intro),
                    'modulename' => 'page',
                    'section' => $cm->sectionname,
                    'sectionnumber' => $cm->sectionnumber,
                    'activitytype' => $cm->activitytype
                ];
            }
        } elseif ($cm->moduletype === 'url') {
            $url = $DB->get_record('url', ['id' => $cm->instance]);
            if ($url) {
                $moduleData = [
                    'cmid' => $cm->cmid,
                    'name' => $cm->activityname ?: $url->name,
                    'intro' => $cm->activitydescription ?: strip_tags($url->intro),
                    'modulename' => 'url',
                    'section' => $cm->sectionname,
                    'sectionnumber' => $cm->sectionnumber,
                    'activitytype' => $cm->activitytype
                ];
            }
        }
        
        if ($moduleData) {
            $videoActivities[$cm->cmid] = $moduleData;
        }
    }
    
    if (empty($videoActivities)) {
        echo $OUTPUT->notification(get_string('novideoactivities', 'local_cdo_unti2035bas'), 'info');
    } else {
        // Создание таблицы видео активностей
        $table = new \html_table();
        $table->head = [
            get_string('cmid', 'local_cdo_unti2035bas'),
            get_string('section'),
            get_string('name'),
            get_string('type'),
            get_string('statements', 'local_cdo_unti2035bas'),
            get_string('actions', 'local_cdo_unti2035bas')
        ];
        $table->attributes['class'] = 'generaltable video-activities-table';
        
        foreach ($videoActivities as $cmid => $activity) {
            // Получаем данные о прогрессе видео из таблицы local_videoprogress
            $videoProgressData = $DB->get_records('local_videoprogress', [
                'userid' => $userId,
                'cmid' => $cmid
            ], 'id DESC'); // Сортируем по ID по убыванию, чтобы получить последние записи
            
            $actions = [];
            
            // Если нет данных прогресса, добавляем кнопку создания следа
            if (empty($videoProgressData)) {
                $createUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/create_progress.php', [
                    'flow_id' => $flowId,
                    'user_id' => $userId,
                    'cmid' => $cmid
                ]);
                $actions[] = \html_writer::link(
                    $createUrl,
                    get_string('createprogress', 'local_cdo_unti2035bas'),
                    ['class' => 'btn btn-sm btn-success']
                );
            } else {
                // Если есть данные прогресса, добавляем кнопку удаления следов
                $deleteUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/delete_progress.php', [
                    'flow_id' => $flowId,
                    'user_id' => $userId,
                    'cmid' => $cmid
                ]);
                $actions[] = \html_writer::link(
                    $deleteUrl,
                    get_string('deleteprogress', 'local_cdo_unti2035bas'),
                    [
                        'class' => 'btn btn-sm btn-danger',
                        'onclick' => 'return confirm("' . get_string('confirmdeleteprogress', 'local_cdo_unti2035bas') . '");'
                    ]
                );
            }
            
            // Кнопка отправки statements (только если есть данные прогресса)
            if (!empty($videoProgressData)) {
                $sendUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/send_statements.php', [
                    'flow_id' => $flowId,
                    'user_id' => $userId,
                    'cmid' => $cmid
                ]);
                $actions[] = \html_writer::link(
                    $sendUrl,
                    get_string('send', 'local_cdo_unti2035bas'),
                    ['class' => 'btn btn-sm btn-primary']
                );
            }
            
            $actionsHtml = implode(' ', $actions);
            
            // Формируем отображение следов на основе данных из local_videoprogress
            if (!empty($videoProgressData)) {
                $progressInfo = [];
                $maxProgress = 0;
                $maxDuration = 0;
                
                foreach ($videoProgressData as $progress) {
                    if ($progress->progress > $maxProgress) {
                        $maxProgress = $progress->progress;
                    }
                    if ($progress->duration > $maxDuration) {
                        $maxDuration = $progress->duration;
                    }
                }
                
                $progressInfo[] = get_string('records', 'local_cdo_unti2035bas') . ': ' . count($videoProgressData);
                if ($maxProgress > 0) {
                    $progressInfo[] = get_string('maxprogress', 'local_cdo_unti2035bas') . ': ' . round($maxProgress, 1) . '%';
                }
                if ($maxDuration > 0) {
                    $progressInfo[] = get_string('duration', 'local_cdo_unti2035bas') . ': ' . gmdate('H:i:s', $maxDuration);
                }
                
                $statementsDisplay = \html_writer::tag('div', implode('<br>', $progressInfo), ['class' => 'video-progress-info']);
            } else {
                $statementsDisplay = \html_writer::tag('span', get_string('nodata', 'local_cdo_unti2035bas'), ['class' => 'badge badge-secondary']);
            }
            
            // Форматируем название секции
            $sectionDisplay = $activity['sectionnumber'] . '. ' . $activity['section'];
            if (empty($activity['section'])) {
                $sectionDisplay = get_string('section') . ' ' . $activity['sectionnumber'];
            }
            
            $table->data[] = [
                $cmid,
                $sectionDisplay,
                \html_writer::tag('strong', $activity['name']),
                ucfirst($activity['modulename']),
                $statementsDisplay,
                $actionsHtml
            ];
        }
        
        echo \html_writer::table($table);
    }
} catch (Exception $e) {
    echo $OUTPUT->notification('Ошибка при получении активностей: ' . $e->getMessage(), 'error');
}

echo \html_writer::end_div(); // users-table-section

// Массовые действия
echo \html_writer::start_div('bulk-actions-section');
echo $OUTPUT->heading(get_string('bulkactions', 'local_cdo_unti2035bas'), 4);

$bulkActions = [];

// Массовое создание следов для всех видео пользователя
$bulkCreateUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/bulk_create_progress.php', [
    'flow_id' => $flowId,
    'user_id' => $userId
]);
$bulkActions[] = \html_writer::link(
    $bulkCreateUrl,
    get_string('bulkcreateprogress', 'local_cdo_unti2035bas'),
    ['class' => 'btn btn-success']
);

// Массовая отправка statements для всех видео пользователя
$bulkSendUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/bulk_send_user_statements.php', [
    'flow_id' => $flowId,
    'user_id' => $userId
]);
$bulkActions[] = \html_writer::link(
    $bulkSendUrl,
    get_string('bulksendallstatements', 'local_cdo_unti2035bas'),
    ['class' => 'btn btn-primary']
);

// Отчет по всем statements пользователя
$reportUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/user_statements_report.php', [
    'flow_id' => $flowId,
    'user_id' => $userId
]);
$bulkActions[] = \html_writer::link(
    $reportUrl,
    get_string('userreport', 'local_cdo_unti2035bas'),
    ['class' => 'btn btn-info']
);

echo \html_writer::div(implode(' ', $bulkActions), 'bulk-actions-buttons');
echo \html_writer::end_div(); // bulk-actions-section

// Навигация назад
echo \html_writer::start_div('navigation-back');
echo \html_writer::link(
    new \moodle_url('/local/cdo_unti2035bas/pages/video_statement_management.php', ['flow_id' => $flowId]),
    get_string('backtoflow', 'local_cdo_unti2035bas'),
    ['class' => 'btn btn-secondary']
);
echo \html_writer::end_div(); // navigation-back

echo \html_writer::end_div(); // video-statements-container

echo $OUTPUT->footer();
