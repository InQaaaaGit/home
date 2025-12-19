<?php

require_once(__DIR__ . "/../../config.php");

global $DB;
$DB->delete_records_select('grade_grades', 'rawgrade < 1');
