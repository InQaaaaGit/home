<?php

namespace tool_cdo_config\external;

use assign;
use context_module;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use moodle_exception;

global $CFG;
require_once($CFG->dirroot . '/user/lib.php');
require_once $CFG->dirroot . '/mod/assign/locallib.php';


class download_file_submissions extends external_api
{
    public static function download_file_submission_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'course_module_id' => new external_value(PARAM_INT, '', VALUE_REQUIRED),
                'user_id' => new external_value(PARAM_INT, '', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * @throws moodle_exception
     */
    public static function download_file_submission($course_module_id, $user_id): array
    {

        $files_of_submissions = [];
        $params = self::validate_parameters(self::download_file_submission_parameters(),
            [
                'course_module_id' => $course_module_id,
                'user_id' => $user_id,
            ]
        );
        $user_array = (user_get_users_by_id([$params['user_id']]));
        $user = array_shift($user_array);
        list ($course, $cm) = get_course_and_cm_from_cmid($params['course_module_id'], 'assign');
        $context = context_module::instance($cm->id);
        $assign = new assign($context, $cm, $course);
        foreach ($assign->get_submission_plugins() as $plugin) {
            if (!$plugin->is_enabled() || !$plugin->is_visible()) {
                continue;
            }
            $submission = $assign->get_user_submission($params['user_id'], false);
            $pluginfiles = $plugin->get_files($submission, $user);
            foreach ($pluginfiles as $key => $pluginfile) {

                $file_info = [
                    'extension' => pathinfo($pluginfile->get_filename())['extension'],
                 //   'filename_without_extension' => pathinfo($pluginfile->get_filename())['extension'],
                    'filename' => $pluginfile->get_filename(),
                    'body64' => base64_encode($pluginfile->get_content())
                ];
                $files_of_submissions[] = $file_info;
            }
        }
        return $files_of_submissions;
    }

    public static function download_file_submission_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'body64' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                    'filename' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                    'extension' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                ]
            )
        );
    }

}