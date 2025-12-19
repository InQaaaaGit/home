<?php

namespace local_cdo_ag_tools\helpers;

use dml_exception;
use moodle_exception;

class helper
{
    /**
     * @throws dml_exception
     */
    public static function get_category_name_helper($category)
    {
        global $DB;
        return $DB->get_record('course_categories', ['id' => $category])->name;
    }

    /**
     * @throws dml_exception
     */
    public static function get_file_system_instances(): array
    {
        global $DB;
        $fs = $DB->get_records('repository_instances', ['typeid' => 9]);
        $select = [];
        foreach ($fs as $f) {
            $select[$f->id] = $f->name;
        }
        return $select;
    }

    /**
     * @throws dml_exception
     */
    public static function get_customfields_value($id)
    {
        global $DB;
        $SQL = "SELECT 
                    c.id AS course_id,
                    c.fullname AS course_name,
                    cf.name AS customfield_name,
                    cd.value AS customfield_value
                FROM 
                    {course} c
                LEFT JOIN 
                    {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50 /* contextlevel 50 соответствует курсу */
                LEFT JOIN 
                    {customfield_data} cd ON cd.contextid = ctx.id
                LEFT JOIN 
                    {customfield_field} cf ON cf.id = cd.fieldid
                    WHERE cd.value <> '' AND c.id = ? AND cf.shortname = 'disciplines'
                ORDER BY 
                    c.id, cf.name";
        return $DB->get_record_sql($SQL, [$id]);
    }

    /**
     * @param $course_id
     * @return array
     * @throws moodle_exception
     */
    public static function get_section_names_for_work($course_id): array
    {
        $modinfo = get_fast_modinfo($course_id);
        // Перебор секций и модулей в каждом разделе
        $sections_name = [];
        foreach ($modinfo->get_section_info_all() as $sectionnum => $sectioninfo) {
            if (!empty($sectioninfo->name))
                $sections_name[$sectioninfo->id] = $sectioninfo->name;
        }
        return $sections_name;
    }
    public static function longestCommonSubstring($str1, $str2): string
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);

        // Матрица для хранения длин общих подстрок
        $matrix = array_fill(0, $len1 + 1, array_fill(0, $len2 + 1, 0));

        $longestLength = 0;
        $endPosStr1 = 0;

        for ($i = 1; $i <= $len1; $i++) {
            for ($j = 1; $j <= $len2; $j++) {
                if ($str1[$i - 1] == $str2[$j - 1]) {
                    $matrix[$i][$j] = $matrix[$i - 1][$j - 1] + 1;
                    if ($matrix[$i][$j] > $longestLength) {
                        $longestLength = $matrix[$i][$j];
                        $endPosStr1 = $i;
                    }
                }
            }
        }

        // Извлечение наибольшей общей подстроки
        $longestCommonSubstring = substr($str1, $endPosStr1 - $longestLength, $longestLength);

        return $longestCommonSubstring;
    }

}