<?php

namespace tool_cdo_config\helpers;

use dml_exception;

class gradereports
{
    /**
     * @throws dml_exception
     */
    public static function get_grade_categories_name(int $id)
    {
        global $DB;
        $data = $DB->get_record("grade_categories", ['id' => $id]);
        return !is_bool($data) ? $data->fullname : "";
    }
}