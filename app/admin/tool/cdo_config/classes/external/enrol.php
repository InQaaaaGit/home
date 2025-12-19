<?php

namespace tool_cdo_config\external;

use core_enrol_external;
use core_external\external_api;
use core_external\external_description;
use core_external\external_files;
use core_external\external_format_value;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use dml_exception;

require_once($CFG->dirroot . '/enrol/externallib.php');

class enrol extends external_api
{
    public static function enrol_get_users_courses_parameters(): external_function_parameters
    {
        return core_enrol_external::get_users_courses_parameters();
    }

    /**
     * @throws dml_exception
     */
    public static function enrol_get_users_courses($userid, $returnusercount = true): array
    {

        $enrol_data = core_enrol_external::get_users_courses($userid, $returnusercount);
        foreach ($enrol_data as &$enrol) {
            $enrol_timings = \tool_cdo_config\helpers\enrol::get_enrol_timings($userid, $enrol['id']);
            $enrol['enrol_start'] = $enrol_timings['timestart'];
            $enrol['enrol_end'] = $enrol_timings['timeend'];
        }
        return $enrol_data;
    }

    public static function enrol_get_users_courses_returns(): external_multiple_structure|external_description
    {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'id of course'),
                    'shortname' => new external_value(PARAM_RAW, 'short name of course'),
                    'fullname' => new external_value(PARAM_RAW, 'long name of course'),
                    'displayname' => new external_value(PARAM_RAW, 'course display name for lists.', VALUE_OPTIONAL),
                    'enrolledusercount' => new external_value(PARAM_INT, 'Number of enrolled users in this course',
                        VALUE_OPTIONAL),
                    'idnumber' => new external_value(PARAM_RAW, 'id number of course'),
                    'visible' => new external_value(PARAM_INT, '1 means visible, 0 means not yet visible course'),
                    'summary' => new external_value(PARAM_RAW, 'summary', VALUE_OPTIONAL),
                    'summaryformat' => new external_format_value('summary', VALUE_OPTIONAL),
                    'format' => new external_value(PARAM_PLUGIN, 'course format: weeks, topics, social, site', VALUE_OPTIONAL),
                    'courseimage' => new external_value(PARAM_URL, 'The course image URL', VALUE_OPTIONAL),
                    'showgrades' => new external_value(PARAM_BOOL, 'true if grades are shown, otherwise false', VALUE_OPTIONAL),
                    'lang' => new external_value(PARAM_LANG, 'forced course language', VALUE_OPTIONAL),
                    'enablecompletion' => new external_value(PARAM_BOOL, 'true if completion is enabled, otherwise false',
                        VALUE_OPTIONAL),
                    'completionhascriteria' => new external_value(PARAM_BOOL, 'If completion criteria is set.', VALUE_OPTIONAL),
                    'completionusertracked' => new external_value(PARAM_BOOL, 'If the user is completion tracked.', VALUE_OPTIONAL),
                    'category' => new external_value(PARAM_INT, 'course category id', VALUE_OPTIONAL),
                    'progress' => new external_value(PARAM_FLOAT, 'Progress percentage', VALUE_OPTIONAL),
                    'completed' => new external_value(PARAM_BOOL, 'Whether the course is completed.', VALUE_OPTIONAL),
                    'startdate' => new external_value(PARAM_INT, 'Timestamp when the course start', VALUE_OPTIONAL),
                    'enddate' => new external_value(PARAM_INT, 'Timestamp when the course end', VALUE_OPTIONAL),
                    'marker' => new external_value(PARAM_INT, 'Course section marker.', VALUE_OPTIONAL),
                    'lastaccess' => new external_value(PARAM_INT, 'Last access to the course (timestamp).', VALUE_OPTIONAL),
                    'isfavourite' => new external_value(PARAM_BOOL, 'If the user marked this course a favourite.', VALUE_OPTIONAL),
                    'hidden' => new external_value(PARAM_BOOL, 'If the user hide the course from the dashboard.', VALUE_OPTIONAL),
                    'overviewfiles' => new external_files('Overview files attached to this course.', VALUE_OPTIONAL),
                    'showactivitydates' => new external_value(PARAM_BOOL, 'Whether the activity dates are shown or not'),
                    'showcompletionconditions' => new external_value(PARAM_BOOL, 'Whether the activity completion conditions are shown or not'),
                    'timemodified' => new external_value(PARAM_INT, 'Last time course settings were updated (timestamp).',
                        VALUE_OPTIONAL),
                    'enrol_start' => new external_value(PARAM_INT, ''),
                    'enrol_end' => new external_value(PARAM_INT, ''),
                )
            )
        );
    }
}