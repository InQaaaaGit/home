<?php

namespace local_cdo_variations\controllers;

use availability_profile\condition;
use coding_exception;
use dml_exception;
use stdClass;

class general
{
    private string $user_table = 'user';
    private string $course_modules_table = 'course_modules';

    /**
     * @throws dml_exception
     */
    public function get_user_variations(): array
    {
        global $DB;
        $result = $DB->get_records($this->user_table, null, 'institution', 'institution');
        $result_arr = [];
        foreach ($result as $element) {
            if (!empty($element->institution))
                $result_arr[] = $element->institution;
        }
        #$result = array_shift($result);
        return ($result_arr);
    }


    /**
     * @throws coding_exception
     * @throws dml_exception
     */
    public function update_module_info($courseid, $modules): bool
    {
        global $DB;
        $arr = [];
        $cms_id = [];
        $modules_in_user = [];
        $result = true;

        $cms = get_course_mods($courseid);
        foreach ($cms as $cm) {
            if (!empty($cm->availability))
                $cms_id[] = $cm->id;
        }

        foreach ($modules as $module) {
            $arr[$module['cmid']][] = $module;
            $modules_in_user[] = $module['cmid'];
        }
        // Для очистки удаленных условий используем
        // разницу текущих пришедших ID с теми,
        // которые уже есть в курсе
        $arr_to_clean = array_diff($cms_id, $modules_in_user);
        foreach ($arr_to_clean as $cm) {
            $clean_data_object = new stdClass();
            $clean_data_object->id = $cm;
            $clean_data_object->availability = null;
            $DB->update_record($this->course_modules_table, $clean_data_object);
        }

        // Собираем JSON условие доступности для элемента.
        foreach ($arr as $key => $value) {
            $c = [];
            $condition_on = [];
            foreach ($value as $child_value) {
                $c[] = condition::get_json(
                    false,
                    'institution',
                    condition::OP_DOES_NOT_CONTAIN,
                    $child_value['condition']
                );
                $condition_on[] = false;
            }
            $availability = json_encode(
                [
                    'op' => '&',
                    'c' => $c,
                    'showc' => $condition_on
                ],
                256
            );

            $data_object = new stdClass();
            $data_object->id = $key;
            $data_object->availability = $availability;
            $result = $DB->update_record($this->course_modules_table, $data_object);

        }
        rebuild_course_cache($courseid, true);
        return $result;
    }
}