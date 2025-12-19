<?php

namespace local_cdo_ag_tools\tasks;

use local_cdo_ag_tools\grades\grades_with_double_coefficient;
use moodle_exception;

class update_grade_for_double_coefficient extends \core\task\scheduled_task
{

    public function get_name(): string
    {
        return 'update_grade_for_double_coefficient';
    }

    /**
     * @throws moodle_exception
     */
    public function execute(): void
    {
        $grades_with_double_coefficient = new grades_with_double_coefficient();
        $grades_with_double_coefficient->accumulate_coefficient_task();
        // php scheduled_task.php --execute=\\local_cdo_ag_tools\\tasks\\update_grade_for_double_coefficient
    }
}