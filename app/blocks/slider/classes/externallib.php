<?php


namespace block_slider;

use external_api;
use external_function_parameters;
use external_value;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

class externallib extends external_api
{
    public static function get_course_module_parameters()
    {
        return new external_function_parameters(
            [
                "courseid" => new external_value(PARAM_TEXT, "course id", true)
            ]
        );
    }

    public static function get_course_module($course_id)
    {
        $params = self::validate_parameters(self::get_course_module_parameters(),
            [
                "courseid" => $course_id
            ]
        );
        $cms = get_fast_modinfo($params['courseid']);
        $preparedCms = [];
        $preparedCms[] = 0;
        foreach ($cms->get_cms() as $cm) {
            /*$preparedCmsItem=[
                "name" => $cm->name,
                "id" => $cm->id
            ];
            $preparedCms[] = $preparedCmsItem;*/
            $preparedCms[] =  $cm->id;
        }

        return $preparedCms;
    }

    public static function get_course_module_returns()
    {
        #return null;

    }

}