<?php

namespace local_cdo_variations\external;

use coding_exception;
use core_course_external;
use dml_exception;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use local_cdo_variations\controllers\general as generalController;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/course/externallib.php');

class general extends core_course_external
{
    public static function update_module_availability_info_parameters(): external_function_parameters
    {
        return
            new external_function_parameters(
                [
                    'courseid' => new external_value(PARAM_INT, 'courseid', VALUE_REQUIRED),
                    'modules' => new external_multiple_structure(
                       // new external_value(PARAM_TEXT, 'TEXT', VALUE_REQUIRED)
                        new external_single_structure(
                            [
                                //new external_value(PARAM_TEXT, 'TEXT', VALUE_REQUIRED)
                                'cmid' => new external_value(PARAM_INT, 'course module id', VALUE_REQUIRED),
                                'condition' => new external_value(PARAM_TEXT, 'new condition', VALUE_REQUIRED),

                            ]
                        )
                    ),
                ]
            );
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function update_module_availability_info($courseid, $modules): bool
    {
        $general = new generalController();
        # var_dump($modules); die();
        return $general->update_module_info($courseid, $modules);
    }

    public static function update_module_availability_info_returns(): external_value
    {
        return new external_value(PARAM_BOOL, 'result', VALUE_REQUIRED);
    }

    public static function get_user_variations_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [

            ]
        );
    }

    /**
     * @throws dml_exception
     */
    public static function get_user_variations(): array
    {
        $general = new generalController();
        return $general->get_user_variations();
    }

    public static function get_user_variations_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_value(PARAM_TEXT, 'variation', VALUE_REQUIRED)
        );

    }

}
