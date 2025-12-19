<?php

namespace tool_cdo_showcase_tools\external;


use core\exception\moodle_exception;
use core_external\external_format_value;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core_user;
use gradereport_user\external\user;
use tool_cdo_showcase_tools\helpers\gradereport_helper;

class gradereport extends user
{
    public static function get_grade_items_parameters(): external_function_parameters
    {
        return new external_function_parameters (
            [
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED),
                'userid'   => new external_value(PARAM_TEXT, 'Return grades only for this user (optional)', VALUE_DEFAULT, ''),
                'groupid'  => new external_value(PARAM_INT, 'Get users from this group only', VALUE_DEFAULT, 0)
            ]
        );
    }

    public static function get_grade_items(int $courseid, $userid = '', int $groupid = 0): array
    {
        $user = get_complete_user_data('email', $userid);
        try {
            $data = parent::get_grade_items($courseid, $user->id, $groupid);
            $data_full_grades = parent::get_grade_items($courseid);
            $average = gradereport_helper::get_average_grade($data_full_grades['usergrades']);
            
            // Создаем индексированный массив средних значений для быстрого поиска по ID
            $averageByItemId = [];
            if (isset($average['averages'])) {
                foreach ($average['averages'] as $avgItem) {
                    $averageByItemId[$avgItem['item_id']] = $avgItem;
                }
            }
            
            foreach ($data['usergrades'] as &$usergrades) {
                foreach ($usergrades['gradeitems'] as &$usergrade) {
                    $usergrade['itemname'] = empty($usergrade['itemname']) ?
                        gradereport_helper::get_grade_categories_name($usergrade['iteminstance']) : $usergrade['itemname'];
                    
                    // Добавляем средние значения к каждой оценке пользователя по ID
                    if (isset($averageByItemId[$usergrade['id']])) {
                        $avgData = $averageByItemId[$usergrade['id']];
                        $usergrade['average_grade'] = $avgData['average'];
                        $usergrade['average_percentage'] = $avgData['percentage'];
                        $usergrade['average_count'] = $avgData['count'];
                    } else {
                        // Если среднее значение не найдено, устанавливаем значения по умолчанию
                        $usergrade['average_grade'] = null;
                        $usergrade['average_percentage'] = null;
                        $usergrade['average_count'] = 0;
                    }
                }
            }
        } catch (moodle_exception $e) {
            throw new moodle_exception('cannotgetgrades', 'cdo_showcase', '', 'params:' . $userid . $e->getMessage());
        }
        return $data;
    }

    public static function get_grade_items_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'usergrades' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'courseid' => new external_value(PARAM_INT, 'course id'),
                            'courseidnumber' => new external_value(PARAM_TEXT, 'course idnumber'),
                            'userid'   => new external_value(PARAM_INT, 'user id'),
                            'userfullname' => new external_value(PARAM_TEXT, 'user fullname'),
                            'useridnumber' => new external_value(
                                core_user::get_property_type('idnumber'), 'user idnumber'),
                            'maxdepth'   => new external_value(PARAM_INT, 'table max depth (needed for printing it)'),
                            'gradeitems' => new external_multiple_structure(
                                new external_single_structure(
                                    [
                                        'id' => new external_value(PARAM_INT, 'Grade item id'),
                                        'itemname' => new external_value(PARAM_RAW, 'Grade item name'),
                                        'itemtype' => new external_value(PARAM_ALPHA, 'Grade item type'),
                                        'itemmodule' => new external_value(PARAM_PLUGIN, 'Grade item module'),
                                        'iteminstance' => new external_value(PARAM_INT, 'Grade item instance'),
                                        'itemnumber' => new external_value(PARAM_INT, 'Grade item item number'),
                                        'idnumber' => new external_value(PARAM_TEXT, 'Grade item idnumber'),
                                        'categoryid' => new external_value(PARAM_INT, 'Grade item category id'),
                                        'outcomeid' => new external_value(PARAM_INT, 'Outcome id'),
                                        'scaleid' => new external_value(PARAM_INT, 'Scale id'),
                                        'locked' => new external_value(PARAM_BOOL, 'Grade item for user locked?', VALUE_OPTIONAL),
                                        'cmid' => new external_value(PARAM_INT, 'Course module id (if type mod)', VALUE_OPTIONAL),
                                        'weightraw' => new external_value(PARAM_FLOAT, 'Weight raw', VALUE_OPTIONAL),
                                        'weightformatted' => new external_value(PARAM_NOTAGS, 'Weight', VALUE_OPTIONAL),
                                        'status' => new external_value(PARAM_ALPHA, 'Status', VALUE_OPTIONAL),
                                        'graderaw' => new external_value(PARAM_FLOAT, 'Grade raw', VALUE_OPTIONAL),
                                        'gradedatesubmitted' => new external_value(PARAM_INT, 'Grade submit date', VALUE_OPTIONAL),
                                        'gradedategraded' => new external_value(PARAM_INT, 'Grade graded date', VALUE_OPTIONAL),
                                        'gradehiddenbydate' => new external_value(PARAM_BOOL, 'Grade hidden by date?', VALUE_OPTIONAL),
                                        'gradeneedsupdate' => new external_value(PARAM_BOOL, 'Grade needs update?', VALUE_OPTIONAL),
                                        'gradeishidden' => new external_value(PARAM_BOOL, 'Grade is hidden?', VALUE_OPTIONAL),
                                        'gradeislocked' => new external_value(PARAM_BOOL, 'Grade is locked?', VALUE_OPTIONAL),
                                        'gradeisoverridden' => new external_value(PARAM_BOOL, 'Grade overridden?', VALUE_OPTIONAL),
                                        'gradeformatted' => new external_value(PARAM_RAW, 'The grade formatted', VALUE_OPTIONAL),
                                        'grademin' => new external_value(PARAM_FLOAT, 'Grade min', VALUE_OPTIONAL),
                                        'grademax' => new external_value(PARAM_FLOAT, 'Grade max', VALUE_OPTIONAL),
                                        'rangeformatted' => new external_value(PARAM_NOTAGS, 'Range formatted', VALUE_OPTIONAL),
                                        'percentageformatted' => new external_value(PARAM_NOTAGS, 'Percentage', VALUE_OPTIONAL),
                                        'lettergradeformatted' => new external_value(PARAM_NOTAGS, 'Letter grade', VALUE_OPTIONAL),
                                        'rank' => new external_value(PARAM_INT, 'Rank in the course', VALUE_OPTIONAL),
                                        'numusers' => new external_value(PARAM_INT, 'Num users in course', VALUE_OPTIONAL),
                                        'averageformatted' => new external_value(PARAM_NOTAGS, 'Grade average', VALUE_OPTIONAL),
                                        'feedback' => new external_value(PARAM_RAW, 'Grade feedback', VALUE_OPTIONAL),
                                        'feedbackformat' => new external_format_value('feedback', VALUE_OPTIONAL),
                                        'average_grade' => new external_value(PARAM_RAW,'average_grade', VALUE_OPTIONAL),
                                        'average_percentage' => new external_value(PARAM_RAW, 'average_percentage', VALUE_OPTIONAL),
                                        'average_count' => new external_value(PARAM_RAW, 'average_count', VALUE_OPTIONAL),
                                    ], 'Grade items'
                                )
                            )
                        ]
                    )
                ),
                'warnings' => new external_warnings()
            ]
        );
    }
}