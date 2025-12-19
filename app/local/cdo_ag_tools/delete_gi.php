<?php
// delete_grade_item.php
define('CLI_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');
global $CFG, $DB;
require_once($CFG->libdir . '/gradelib.php');
//$gradeitemid = required_param('giid', PARAM_INT);
$gradeitemid = isset($argv[1]) ? (int)$argv[1] : 0;

if (!$gradeitemid) {
    die("Usage: php delete_grade_item.php <grade_item_id>\n");
}

// Получаем запись
$gradeitem = $DB->get_record('grade_items', ['id' => $gradeitemid]);
if (!$gradeitem) {
    die("Grade item with ID $gradeitemid not found.\n");
}

// Создаём объект grade_item
$gi = new grade_item($gradeitem, false);

echo "Deleting grade item: '{$gi->itemname}' (ID: $gradeitemid)...\n";

// Удаляем через API — это обработает все зависимости!
$gi->delete();

echo "✅ Deleted successfully.\n";