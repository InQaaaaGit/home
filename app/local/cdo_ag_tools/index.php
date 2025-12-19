<?php
use local_cdo_ag_tools\grades\grades_with_double_coefficient;

require_once(__DIR__ . "/../../config.php");

$grades_with_double_coefficient = new grades_with_double_coefficient();
#$grades_with_double_coefficient->update_grades_with_coefficient();
try {
    $grades_with_double_coefficient->accumulate_coefficient_task();
} catch (dml_exception|moodle_exception $e) {
    var_dump($e->getMessage());
}