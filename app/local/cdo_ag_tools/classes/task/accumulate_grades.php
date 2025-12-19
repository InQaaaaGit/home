<?php

namespace local_cdo_ag_tools\task;

require_once($CFG->dirroot . '/local/cdo_ag_tools/classes/grades/grades_with_double_coefficient.php');

use core\task\adhoc_task;
use dml_exception;
use local_cdo_ag_tools\grades\grades_with_double_coefficient;
use moodle_exception;

class accumulate_grades extends adhoc_task
{
    use \core\task\logging_trait;

    public function get_name(): \lang_string|string
    {
        return get_string('accumulate_grades_task_name', 'local_cdo_ag_tools');
    }

    /**
     * @throws moodle_exception
     * @throws dml_exception
     */
    public function execute(): void
    {
        $data = $this->get_custom_data();
        $grades_with_double_coefficient = new grades_with_double_coefficient();
        $grades_with_double_coefficient->accumulate_coefficient_task(
            ['courseid' => $data->courseid],
        );
    }

    public static function queue(): void
    {
        $task = new self();
        \core\task\manager::queue_adhoc_task($task);
    }

    public static function instance(
        int $courseid
    ): self
    {
        $task = new self();
        $task->set_custom_data((object)[
            'courseid' => $courseid,
        ]);

        return $task;
    }
}
