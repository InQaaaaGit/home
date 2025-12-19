<?php

use local_cdo_ag_tools\files\controller_work_archive;
use local_cdo_ag_tools\task\create_archive;

require_once(__DIR__ . "/../../config.php");

require_login();
global $USER;

$cwa = new controller_work_archive();
$cwa->delete_file(['userid' => $USER->id]);
$task = new create_archive();
$task->set_custom_data(['userid' => $USER->id]);
$task->set_component('local_cdo_ag_tools');
\core\task\manager::queue_adhoc_task($task, true);
redirect('/my', get_string('yourpersonalqrcode', 'local_cdo_ag_tools'));