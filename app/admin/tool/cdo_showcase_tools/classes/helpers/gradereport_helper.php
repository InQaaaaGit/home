<?php

namespace tool_cdo_showcase_tools\helpers;

class gradereport_helper
{
    public static function get_grade_categories_name(int $id)
    {
        global $DB;
        $data = $DB->get_record("grade_categories", ['id' => $id]);
        return !is_bool($data) ? $data->fullname : "";
    }

    public static function get_average_grade($usergrades): array
    {
        // Группируем оценки по элементам
        $gradesByItem = [];
        $response = [];
        foreach ($usergrades as $usergrade) {
            if (!isset($usergrade['gradeitems'])) continue;

            foreach ($usergrade['gradeitems'] as $gradeitem) {
                $itemId = $gradeitem['id'];

                if (!isset($gradesByItem[$itemId])) {
                    $gradesByItem[$itemId] = [
                        'id' => $itemId,
                        'itemname' => $gradeitem['itemname'],
                        'itemtype' => $gradeitem['itemtype'],
                        'grademax' => $gradeitem['grademax'],
                        'grades' => []
                    ];
                }

                // Добавляем оценку, если она не null
                if ($gradeitem['graderaw'] !== null && is_numeric($gradeitem['graderaw'])) {
                    $gradesByItem[$itemId]['grades'][] = floatval($gradeitem['graderaw']);
                }
            }
        }

        // Вычисляем средние значения
        foreach ($gradesByItem as $itemId => $itemData) {
            if (count($itemData['grades']) > 0) {
                $average = array_sum($itemData['grades']) / count($itemData['grades']);

                $response['averages'][] = [
                    'item_id' => $itemId,
                    'item_name' => $itemData['itemname'],
                    'item_type' => $itemData['itemtype'],
                    'grade_max' => $itemData['grademax'],
                    'average' => round($average, 2),
                    'count' => count($itemData['grades']),
                    'percentage' => $itemData['grademax'] > 0 ? round(($average / $itemData['grademax']) * 100, 2) : 0
                ];
            }
        }

        return $response;
    }

    public static function get_category_name($id) {
        global $DB;

        return $DB->get_record("course_categories", ['id' => $id])->name;
    }
}

