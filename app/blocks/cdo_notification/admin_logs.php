<?php
require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

require_login();n
$context = context_system::instance();
require_capability('block/cdo_notification:manage', $context);

$PAGE->set_context($context);
$PAGE->set_url('/blocks/cdo_notification/admin_logs.php');
$PAGE->set_title(get_string('pluginname', 'block_cdo_notification') . ' - Логи действий');
$PAGE->set_heading(get_string('pluginname', 'block_cdo_notification'));
$PAGE->set_pagelayout('admin');

// Фильтры
$userid = optional_param('userid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_TEXT);
$download = optional_param('download', '', PARAM_ALPHA);

$table = new flexible_table('block_cdo_notification_log');
$table->define_columns(['id', 'userid', 'ip', 'mac', 'action', 'timecreated']);
$table->define_headers(['ID', 'Пользователь', 'IP', 'MAC', 'Действие', 'Время']);
$table->define_baseurl($PAGE->url);
$table->set_attribute('class', 'generaltable generalbox');
$table->sortable(true, 'timecreated', SORT_DESC);
$table->no_sorting('mac');
$table->set_control_variables(array(
    TABLE_VAR_SORT => 'tsort',
    TABLE_VAR_HIDE => 'thide',
    TABLE_VAR_SHOW => 'tshow',
    TABLE_VAR_IFIRST => 'tifirst',
    TABLE_VAR_ILAST => 'tilast',
    TABLE_VAR_PAGE => 'page',
));
$table->setup();

$params = [];
$where = '1=1';
if ($userid) {
    $where .= ' AND userid = :userid';
    $params['userid'] = $userid;
}
if ($action) {
    $where .= ' AND action LIKE :action';
    $params['action'] = "%$action%";
}

$count = $DB->count_records_select('block_cdo_notification_log', $where, $params);
$perpage = 30;
$page = optional_param('page', 0, PARAM_INT);
$logs = $DB->get_records_select('block_cdo_notification_log', $where, $params, 'timecreated DESC', '*', $page * $perpage, $perpage);

if ($download === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=notification_logs.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'UserID', 'IP', 'MAC', 'Action', 'Time']);
    foreach ($logs as $log) {
        fputcsv($out, [$log->id, $log->userid, $log->ip, $log->mac, $log->action, userdate($log->timecreated)]);
    }
    fclose($out);
    exit;
}

echo $OUTPUT->header();
echo $OUTPUT->heading('Логи действий пользователей');

// Форма фильтра
echo '<form method="get" action="">';
echo 'Пользователь (ID): <input type="text" name="userid" value="'.s($userid).'" size="5"> ';
echo 'Действие: <input type="text" name="action" value="'.s($action).'" size="20"> ';
echo '<input type="submit" value="Фильтровать" class="btn btn-secondary"> ';
echo '<a href="?download=csv" class="btn btn-primary">Экспорт в CSV</a>';
echo '</form>';

$table->initialbars(false);
$table->pagesize($perpage, $count);
foreach ($logs as $log) {
    $row = [
        $log->id,
        $log->userid,
        $log->ip,
        $log->mac,
        $log->action,
        userdate($log->timecreated)
    ];
    $table->add_data($row);
}
$table->print_html();

echo $OUTPUT->footer(); 