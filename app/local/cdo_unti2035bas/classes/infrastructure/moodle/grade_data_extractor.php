<?php

namespace local_cdo_unti2035bas\infrastructure\moodle;

/**
 * Класс для извлечения данных оценки из событий
 * Отвечает только за извлечение и форматирование данных
 */
class grade_data_extractor {
    
    /**
     * Извлечение данных оценки из события
     * 
     * @param array $event_data
     * @return array
     */
    public static function extract(array $event_data): array {
        global $DB;
        
        $grade_data = [
            'userid' => $event_data['relateduserid'] ?? 0,
            'itemid' => $event_data['objectid'] ?? 0,
            'courseid' => $event_data['courseid'] ?? 0,
            'contextid' => $event_data['contextid'] ?? 0,
            'timecreated' => $event_data['timecreated'] ?? time(),
            'grade_info' => $event_data['other'] ?? [],
            'grade' => null,
            'maxgrade' => null,
            'itemname' => null,
            'itemtype' => null,
            'itemmodule' => null
        ];
        
        // Получаем дополнительную информацию о grade item
        if ($grade_data['itemid']) {
            $grade_data = self::enrich_with_grade_item_data($grade_data, $DB);
        }
        
        return $grade_data;
    }
    
    /**
     * Обогащает данные информацией о grade item
     * 
     * @param array $grade_data
     * @param object $DB
     * @return array
     */
    private static function enrich_with_grade_item_data(array $grade_data, $DB): array {
        try {
            $grade_item = $DB->get_record('grade_items', ['id' => $grade_data['itemid']], '*', MUST_EXIST);
            $grade_data['itemname'] = $grade_item->itemname;
            $grade_data['itemtype'] = $grade_item->itemtype;
            $grade_data['itemmodule'] = $grade_item->itemmodule;
            $grade_data['maxgrade'] = $grade_item->grademax;
            
            // Получаем значение оценки
            if ($grade_data['userid']) {
                $grade_data = self::enrich_with_grade_value($grade_data, $DB);
            }
        } catch (\Exception $e) {
            debugging("cdo_unti2035bas: Ошибка получения данных grade item: " . $e->getMessage(), DEBUG_DEVELOPER);
        }
        
        return $grade_data;
    }
    
    /**
     * Обогащает данные значением оценки
     * 
     * @param array $grade_data
     * @param object $DB
     * @return array
     */
    private static function enrich_with_grade_value(array $grade_data, $DB): array {
        try {
            $grade_record = $DB->get_record('grade_grades', [
                'itemid' => $grade_data['itemid'],
                'userid' => $grade_data['userid']
            ], 'finalgrade, rawgrade');
            
            if ($grade_record) {
                $grade_data['grade'] = $grade_record->finalgrade ?? $grade_record->rawgrade;
            }
        } catch (\Exception $e) {
            debugging("cdo_unti2035bas: Ошибка получения значения оценки: " . $e->getMessage(), DEBUG_DEVELOPER);
        }
        
        return $grade_data;
    }
    
    /**
     * Получение информации о курсе и модуле для оценки
     * 
     * @param int $courseid
     * @param int $itemid
     * @return array
     */
    public static function get_course_module_info(int $courseid, int $itemid): array {
        global $DB;
        
        $info = [
            'course_id' => $courseid,
            'flow_id' => 0,
            'module_number' => 0,
            'cmid' => null
        ];
        
        try {
            // Получаем информацию о course module если это элемент модуля
            $sql = "SELECT cm.id as cmid, c.customfield_flow_id, c.customfield_module_number
                    FROM {grade_items} gi
                    JOIN {course_modules} cm ON cm.id = gi.iteminstance AND gi.itemmodule = cm.module
                    JOIN {course} c ON c.id = cm.course
                    WHERE gi.id = :itemid AND cm.course = :courseid";
            
            $record = $DB->get_record_sql($sql, [
                'itemid' => $itemid,
                'courseid' => $courseid
            ]);
            
            if ($record) {
                $info['cmid'] = $record->cmid;
                $info['flow_id'] = (int)$record->customfield_flow_id;
                $info['module_number'] = (int)$record->customfield_module_number;
            }
            
        } catch (\Exception $e) {
            debugging("cdo_unti2035bas: Ошибка получения информации о модуле: " . $e->getMessage(), DEBUG_DEVELOPER);
        }
        
        return $info;
    }
} 