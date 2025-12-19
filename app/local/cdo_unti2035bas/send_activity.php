<?php
namespace local_cdo_unti2035bas;

use context_system;
use moodle_url;

require_once(__DIR__ . '/../../config.php');
defined('MOODLE_INTERNAL') || die();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Параметры
$lrid = required_param('lrid', PARAM_RAW);

// Настройка страницы
/** @var \moodle_page $PAGE */
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('/local/cdo_unti2035bas/send_activity.php', ['lrid' => $lrid]));
$title = get_string('sendactivitytitle', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Подключение CSS
$PAGE->requires->css('/local/cdo_unti2035bas/styles.css');

// Увеличиваем время выполнения для больших групп
set_time_limit(300); // 5 минут

// Получение данных о потоке
$activity_repository = new \local_cdo_unti2035bas\infrastructure\persistence\activity_repository();
$stream_params = $activity_repository->get_stream_params_by_activity_lrid($lrid);

/** @var \core_renderer $output */
$output = $PAGE->get_renderer('core');

echo $output->header();

// Проверка наличия потока
if (!$stream_params) {
    echo $output->notification(get_string('noactivityfound', 'local_cdo_unti2035bas'), 'error');
    
    // Навигация при ошибке
    $template_data = [
        'has_users' => false,
        'streams_url' => new moodle_url('/local/cdo_unti2035bas/streams.php')
    ];
    
    echo $output->render_from_template('local_cdo_unti2035bas/send_activity', $template_data);
    echo $output->footer();
    exit;
}

// Извлекаем параметры потока
$parent_course_id = $stream_params['parent_course_id'];
$flow_id = $stream_params['flow_id'];
$course_id = $stream_params['course_id'];
$group_id = $stream_params['group_id'];

// Получение пользователей группы
$group_members = groups_get_members($group_id, 'u.id, u.firstname, u.lastname, u.email');
$users = array_keys($group_members);

if (empty($users)) {
    echo $output->notification(get_string('nousersfound', 'local_cdo_unti2035bas'), 'warning');
    
    // Навигация при отсутствии пользователей
    $template_data = [
        'has_users' => false,
        'streams_url' => new moodle_url('/local/cdo_unti2035bas/streams.php')
    ];
    
    echo $output->render_from_template('local_cdo_unti2035bas/send_activity', $template_data);
    echo $output->footer();
    exit;
}

// Обработка пользователей и отправка данных
$users_data = [];
$users_count = count($users);
$successful_sends = [];
$failed_sends = [];

// Создаем handler для отправки активности
$handler = new \local_cdo_unti2035bas\grades\handler();

foreach ($users as $userid) {
    $user = $group_members[$userid];
    $unti_id = \local_cdo_unti2035bas\infrastructure\moodle\user_field_service::get_unti_id($userid);
    
    $user_data = [
        'id' => $userid,
        'fullname' => fullname($user),
        'email' => $user->email,
        'unti_id' => $unti_id ?: '',
        'has_unti_id' => !empty($unti_id),
        'send_status_success' => false,
        'send_status_error' => false,
        'send_status_no_unti_id' => false
    ];
    
    // Если есть UNTI ID - отправляем
    if (!empty($unti_id)) {
        try {
            $result = $handler->send_activity_successful_text_usage(
                $unti_id,
                $lrid,
                $flow_id,
                $parent_course_id
            );
            
            if ($result['success']) {
                $user_data['send_status'] = 'success';
                $user_data['send_status_success'] = true;
                $user_data['send_message'] = 'Активность успешно отправлена';
                $successful_sends[] = $result;
            } else {
                $user_data['send_status'] = 'error';
                $user_data['send_status_error'] = true;
                $user_data['send_message'] = $result['error'] ?? 'Неизвестная ошибка';
                $failed_sends[] = $result;
            }
        } catch (\Exception $e) {
            $user_data['send_status'] = 'error';
            $user_data['send_status_error'] = true;
            $user_data['send_message'] = 'Ошибка: ' . $e->getMessage();
            $failed_sends[] = [
                'success' => false,
                'error' => $e->getMessage(),
                'unti_id' => $unti_id,
                'object_id' => $lrid
            ];
        }
    } else {
        $user_data['send_status'] = 'no_unti_id';
        $user_data['send_status_no_unti_id'] = true;
        $user_data['send_message'] = 'UNTI ID не найден';
    }
    
    $users_data[] = $user_data;
}

// Подготовка данных для шаблона
$template_data = [
    'lrid' => $lrid,
    'stream_info' => [
        'lrid' => $lrid,
        'parent_course_id' => $parent_course_id,
        'flow_id' => $flow_id,
        'course_id' => $course_id,
        'group_id' => $group_id
    ],
    'has_users' => !empty($users_data),
    'users' => $users_data,
    'users_count' => $users_count,
    'processing_completed' => true,
    'successful_count' => count($successful_sends),
    'failed_count' => count($failed_sends),
    'total_with_unti_id' => count(array_filter($users_data, function($u) { return $u['has_unti_id']; })),
    'streams_url' => new moodle_url('/local/cdo_unti2035bas/streams.php'),
    'failed_details' => $failed_sends
];

// Рендеринг шаблона
echo $output->render_from_template('local_cdo_unti2035bas/send_activity', $template_data);

echo $output->footer();
