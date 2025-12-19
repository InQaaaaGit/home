<?php

namespace tool_cdo_config\external;

use core_external\external_function_parameters;
use core_external\external_single_structure;
use dml_exception;
use gradereport_user\external\user;
use tool_cdo_config\helpers\gradereports as gradereports_helper;

class gradereports extends user
{
    public static function get_grade_items_parameters(): external_function_parameters
    {
        return user::get_grade_items_parameters();
    }

    /**
     * @throws dml_exception
     */
    public static function get_grade_items(int $courseid, int $userid = 0, int $groupid = 0): array
    {

        $data = parent::get_grade_items($courseid, $userid, $groupid);
        foreach ($data['usergrades'] as &$usergrades) {
            foreach ($usergrades['gradeitems'] as &$usergrade) {

               # $usergrade['itemname'] = empty($usergrade['itemname']) ?? gradereports_helper::get_grade_categories_name($usergrade['id']);
                $usergrade['itemname'] = empty($usergrade['itemname']) ?  gradereports_helper::get_grade_categories_name($usergrade['iteminstance']) : $usergrade['itemname'];
            }
        }
        return $data;
    }
    public static function get_grade_items_returns(): external_single_structure {
        return user::get_grade_items_returns();
    }
}