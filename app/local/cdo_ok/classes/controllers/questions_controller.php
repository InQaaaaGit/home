<?php

namespace local_cdo_ok\controllers;

use dml_exception;
use stdClass;

class questions_controller

{
    const TABLE_QUESTIONS = 'local_cdo_ok';

    /**
     * @throws dml_exception
     */
    public function get($params = []): array
    {
        global $DB;

        $records = $DB->get_records(self::TABLE_QUESTIONS, $params, 'sort');

        $data = [];
        foreach ($records as $record) {
            // Преобразуем int в boolean для поля visible
            $record->visible = (bool)$record->visible;
            $data[] = $record;
        }
        return $data;
    }

    /**
     * @throws dml_exception
     */
    public function get_with_answer($params = []): array
    {
        global $DB;
        $where_clause = '';
        if (!empty($params)) {
            $where_clause = 'WHERE ok.group_tab = :group_tab AND ok.visible = :visible ';
        }
        
        $records = $DB->get_records_sql("
            SELECT ok.*, IFNULL(a.answer, '') AS answer
            FROM {local_cdo_ok} ok
                    LEFT JOIN (SELECT question_id, MAX(answer) AS answer 
                               FROM {local_cdo_ok_answer} 
                               GROUP BY question_id) a ON a.question_id = ok.id
                    $where_clause
            ORDER BY ok.sort",
            $params
        );
        
        $data = [];
        foreach ($records as $record) {
            // Преобразуем int в boolean для поля visible
            $record->visible = (bool)$record->visible;
            $data[] = $record;
        }
        return $data;
    }

    /**
     * @throws dml_exception
     */
    public function create($group_tab, $sort): stdClass
    {
        global $DB, $USER;

        $data = new stdClass();
        $data->usermodified = $USER->id;
        $data->timecreated = time();
        $data->timemodified = time();
        $data->group_tab = $group_tab;
        $data->sort = $sort;
        $data->question = '';
        $data->type = 1;
        $data->parameters = '';
        $data->visible = false;
        $data->first_value_of_type = 1;
        $data->second_value = 100;
        $data->answer = '';
        $data->id = $DB->insert_record(self::TABLE_QUESTIONS, $data);
        return $data;
    }

    /**
     * @throws dml_exception
     */
    public function update($data): bool
    {
        global $DB, $USER;
        
        // Преобразуем массив в объект если нужно
        if (is_array($data)) {
            $data = (object)$data;
        }
        
        // Обновляем служебные поля
        $data->timemodified = time();
        $data->usermodified = $USER->id;
        
        // Преобразуем boolean в int для поля visible
        if (isset($data->visible)) {
            $data->visible = (int)$data->visible;
        }
        
        return $DB->update_record(self::TABLE_QUESTIONS, $data);
    }

    /**
     * @throws dml_exception
     */
    public function delete($id): bool
    {
        global $DB;
        return $DB->delete_records(self::TABLE_QUESTIONS, ['id' => $id]);
    }

}