<?php

namespace tool_cdo_config\external;

use coding_exception;
use dml_exception;
use enrol_cohort_plugin;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use invalid_parameter_exception;
use moodle_exception;
use null_progress_trace;
use stdClass;

class cohorts extends external_api
{

    public static function enrol_cohort_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_TEXT, 'courseid', VALUE_REQUIRED),
                'cohortid' => new external_value(PARAM_TEXT, 'cohortid', VALUE_REQUIRED),
                'namecohort' => new external_value(PARAM_TEXT, 'namecohort', VALUE_DEFAULT, ""),
                'group_create' => new external_value(PARAM_BOOL, 'namecohort', VALUE_DEFAULT, false),

            )
        );
    }

    public static function enrol_cohort_returns(): external_value
    {
        return new external_value(PARAM_BOOL, 'result', VALUE_REQUIRED);
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function enrol_cohort($courseid, $cohortid, $name_cohort = "", $group_create = false): bool
    {
        global $DB, $CFG;

        $params = self::validate_parameters(self::enrol_cohort_parameters(),
            [
                'courseid' => $courseid,
                'cohortid' => $cohortid,
                'namecohort' => $name_cohort,
                'group_create' => $group_create,
            ]
        );
        $group_created = 0;
        // Get the course record.
        if ($name_cohort === 'cat') {
            $course = $DB->get_record('course_categories', ['id' => $params['courseid']]);
        } else
            $course = get_course($params['courseid']);
        if ($group_create) {
            $group = new stdClass();
            $group->courseid = $course->id;
            $group->name = $name_cohort;
            require_once $CFG->dirroot . '/group/lib.php';
            $all_groups = groups_get_all_groups($course->id);
            $group_exist = false;
            foreach ($all_groups as $group_in_course) {
                if ($group_in_course->name === $name_cohort) {
                    $group_exist = $group_in_course->id;
                }
            }
            if (empty($group_exist)) {
                $group_created = groups_create_group($group);
            } else {
                $group_created = $group_exist;
            }
        }
        // Get the cohort enrol plugin


        $enrol = enrol_get_plugin('cohort');
        $instance = [];
        $instance['status'] = ENROL_INSTANCE_ENABLED; // Enable it.
        $instance['customint1'] = $params['cohortid']; // Used to store the cohort id.
        $instance['roleid'] = $enrol->get_config('roleid'); // Default role for cohort enrol which is usually student.
        $instance['customint2'] = $group_created; // Optional group id.

        if (!enrol_is_enabled('cohort')) {
            // Not enabled.
            return false;
        }

        if ($DB->record_exists(
            'enrol',
            [
                'courseid' => $courseid,
                'enrol' => 'cohort',
                'customint1' => $cohortid
            ]
        )) {
            if ($group_create) {
                $update_group = new stdClass();
                $update_group->customint2 = $group_created;
                $inst = enrol_get_instances($courseid, true);
                foreach ($inst as $instance) {
                    if ($instance->customint1 === $cohortid) {
                        $enrol->update_instance($instance, $update_group);
                    }
                }
            }

            return false;
        }


        // Add a cohort instance to the course.

        $return = $enrol->add_instance($course, $instance);
        // Sync the existing cohort members.
        $trace = new null_progress_trace();
        $result = enrol_cohort_sync($trace, $course->id);
        $trace->finished();
        return !$result;
    }

    public static function unenrol_cohort_from_course_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_TEXT, 'courseid', VALUE_REQUIRED),
                'cohortid' => new external_value(PARAM_TEXT, 'cohortid', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * Удаляет метод записи "синхронизация с глобальной группой" с курса
     *
     * @param string $courseid ID курса
     * @param string $cohortid ID глобальной группы
     * @return bool true, если экземпляр записи был успешно удален, false в противном случае
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function unenrol_cohort_from_course($courseid, $cohortid): bool
    {
        global $DB;

        $params = self::validate_parameters(self::unenrol_cohort_from_course_parameters(),
            [
                'courseid' => $courseid,
                'cohortid' => $cohortid,
            ]
        );

        // Получаем курс для проверки существования
        $course = get_course($params['courseid']);

        // Получаем плагин записи cohort
        $enrol = enrol_get_plugin('cohort');
        if (!$enrol) {
            return false;
        }

        // Получаем все экземпляры записи cohort на курсе
        $instances = enrol_get_instances($params['courseid'], true);
        
        // Ищем экземпляр записи для данной глобальной группы
        $instance = null;
        foreach ($instances as $inst) {
            if ($inst->enrol === 'cohort' && $inst->customint1 == $params['cohortid']) {
                $instance = $inst;
                break;
            }
        }

        // Если экземпляр записи не найден, возвращаем false
        if (!$instance) {
            return false;
        }

        // Удаляем экземпляр записи
        $enrol->delete_instance($instance);
        return true;
    }

    public static function unenrol_cohort_from_course_returns(): external_value
    {
        return new external_value(PARAM_BOOL, 'result', VALUE_REQUIRED);
    }

}