<?php

require_once(__DIR__ . "/../../../../config.php");
defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE, $OUTPUT, $DB;

use local_cdo_unti2035bas\video\dependencies;

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
$PAGE->set_url(new \moodle_url('/local/cdo_unti2035bas/pages/video/create_progress.php', [
    'flow_id' => $flowId, 
    'user_id' => $userId,
    'cmid' => $cmid
]));
$title = get_string('createprogress', 'local_cdo_unti2035bas');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Подключение CSS стилей
$PAGE->requires->css('/local/cdo_unti2035bas/styles.css');

// Обработка создания следа
if ($confirm && confirm_sesskey()) {
    try {
        $createUseCase = dependencies::getVideoProgressCreateUseCase();
        $result = $createUseCase->execute($userId, $cmid);
        
        if ($result->success) {
            redirect(
                new \moodle_url('/local/cdo_unti2035bas/pages/video/video_statement_for_user.php', [
                    'flow_id' => $flowId,
                    'user_id' => $userId
                ]),
                get_string('progresscreated', 'local_cdo_unti2035bas', [
                    'progress' => $result->getFormattedProgress(),
                    'duration' => $result->getFormattedDuration()
                ]),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        }
        
    } catch (\InvalidArgumentException $e) {
        redirect(
            new \moodle_url('/local/cdo_unti2035bas/pages/video/video_statement_for_user.php', [
                'flow_id' => $flowId,
                'user_id' => $userId
            ]),
            get_string('progressalreadyexists', 'local_cdo_unti2035bas'),
            null,
            \core\output\notification::NOTIFY_WARNING
        );
    } catch (Exception $e) {
        echo $OUTPUT->header();
        echo $OUTPUT->notification('Ошибка при создании следа: ' . $e->getMessage(), 'error');
        echo \html_writer::link(
            new \moodle_url('/local/cdo_unti2035bas/pages/video/video_statement_for_user.php', [
                'flow_id' => $flowId,
                'user_id' => $userId
            ]),
            get_string('backtouser', 'local_cdo_unti2035bas'),
            ['class' => 'btn btn-secondary']
        );
        echo $OUTPUT->footer();
        exit;
    }
}

echo $OUTPUT->header();

// Контейнер для всего содержимого
echo \html_writer::start_div('video-statements-container');

// Информация о создании следа
echo \html_writer::start_div('flow-info-section');
echo $OUTPUT->heading(get_string('createprogress', 'local_cdo_unti2035bas'), 3);

echo \html_writer::tag('p', get_string('createprogressdesc', 'local_cdo_unti2035bas'));

echo \html_writer::start_div('flow-info-grid');

echo \html_writer::div(
    \html_writer::div(get_string('fullname'), 'info-label') .
    \html_writer::div($fullname, 'info-value'),
    'flow-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('cmid', 'local_cdo_unti2035bas'), 'info-label') .
    \html_writer::div($cmid, 'info-value'),
    'flow-info-item'
);

echo \html_writer::div(
    \html_writer::div(get_string('progressrange', 'local_cdo_unti2035bas'), 'info-label') .
    \html_writer::div('85% - 100%', 'info-value'),
    'flow-info-item'
);

echo \html_writer::end_div(); // flow-info-grid
echo \html_writer::end_div(); // flow-info-section

// Форма подтверждения
echo \html_writer::start_div('users-table-section');
echo $OUTPUT->heading(get_string('confirmation'), 4);

echo \html_writer::tag('p', get_string('confirmatecreate', 'local_cdo_unti2035bas'));

$confirmUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/create_progress.php', [
    'flow_id' => $flowId,
    'user_id' => $userId,
    'cmid' => $cmid,
    'confirm' => 1,
    'sesskey' => sesskey()
]);

$cancelUrl = new \moodle_url('/local/cdo_unti2035bas/pages/video/video_statement_for_user.php', [
    'flow_id' => $flowId,
    'user_id' => $userId
]);

echo \html_writer::start_div('bulk-actions-buttons');
echo \html_writer::link(
    $confirmUrl,
    get_string('createprogress', 'local_cdo_unti2035bas'),
    ['class' => 'btn btn-primary']
);
echo ' ';
echo \html_writer::link(
    $cancelUrl,
    get_string('cancel'),
    ['class' => 'btn btn-secondary']
);
echo \html_writer::end_div();

echo \html_writer::end_div(); // users-table-section

echo \html_writer::end_div(); // video-statements-container

echo $OUTPUT->footer(); 