<?php

namespace tool_cdo_config\external;

use coding_exception;
use dml_exception;
use Exception;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use invalid_parameter_exception;
use stdClass;
use tool_cdo_config\tools\dumper;

class add_to_course extends external_api
{
    private const TYPE_MOD = 'page';

    public static function add_page_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'courseid', VALUE_REQUIRED),
                'sectionid' => new external_value(PARAM_TEXT, 'sectionid', VALUE_REQUIRED),
                'name' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                'desc' => new external_value(PARAM_RAW, 'namemethod', VALUE_REQUIRED),
                'intro' => new external_value(PARAM_RAW, 'intro', VALUE_DEFAULT, ""),

            ]
        );
    }

    public static function add_page_returns(): external_single_structure
    {

        return new external_single_structure(
            [
                "error" => new external_value(PARAM_BOOL, 'Ошибка'),
                "id" => new external_value(PARAM_TEXT, 'Текст ответа'),
                "section" => new external_value(PARAM_TEXT, 'Текст ответа', VALUE_REQUIRED)
            ],
        );
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws \moodle_exception
     * @throws Exception
     */
    public static function add_page($courseid, $sectionid, $name, $desc, $intro=""): array
    {
        global $DB;

        self::validate_parameters(self::add_page_parameters(),
            [
                'courseid' => $courseid,
                'sectionid' => $sectionid,
                'name' => $name,
                'desc' => $desc,
                'intro' => $intro,
            ]
        );

        $course = get_course($courseid);
        $section = $DB->get_record('course_sections', ['course' => $course->id, 'id' => $sectionid]);
        if (!$section) {
            throw new Exception("Section with $sectionid does`t exist {$course->id}");
        }

        $data = new stdClass();
        $data->section = $section->section;
        $data->course = $course->id;
        $data->name = $name;
        $data->content = $desc;
        $data->intro = $intro;
        $data->introformat = 1; //HTML
        $data->display = 5;
        $data->printheading = 0;
        $data->printintro = 0;
        $data->printlastmodified = 0;

        $data = self::prepare_new_module_data($data);
        $return = add_moduleinfo($data, $course);

        return [
            'error' => false,
            'id' => $return->id,
            'section' => $return->section
        ];
    }

    private static function prepare_new_module_data(object $data): object
    {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/course/modlib.php');

        $module = $DB->get_record('modules', ['name' => self::TYPE_MOD], '*', MUST_EXIST);
        $data->module = $module->id;
        $data->modulename = $module->name;
        $data->visibleoncoursepage = 1;
        $data->visible = 1;
        $data->showdescription = 1;
        $data->contentformat = 1;

        return $data;

    }

}
