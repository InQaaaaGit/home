<?php
require_once(__DIR__ . "/../../config.php");

// Отключаем требование авторизации для публичного доступа
define('NO_MOODLE_COOKIES', true); // Не использовать куки Moodle
define('NO_UPGRADE_CHECK', true);  // Отключить проверку обновлений

global $PAGE, $OUTPUT;
$title = get_string('pluginname', 'block_cdo_schedule');
$url = new moodle_url('/blocks/cdo_schedule/schedule_for_all.php');

// Устанавливаем контекст системы для публичного доступа
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url($url);
$PAGE->set_heading($title);
$PAGE->set_title($title);

// Добавляем в навигацию
$previewnode = $PAGE->navigation->add(
    $title,
    $url,
    navigation_node::TYPE_CONTAINER
);

// Загружаем JavaScript приложение
$PAGE->requires->js_call_amd(
    'block_cdo_schedule/full-schedule-for-all-app-lazy',
    'init',
);

echo $OUTPUT->header();
echo "<style>.v-select__selections input {
  display: none;
}</style>";
echo $OUTPUT->render_from_template('block_cdo_schedule/mini_app', []);
echo $OUTPUT->footer();
