<?php

namespace local_cdo_ok\controllers;

use dml_exception;

class database_controller
{
    public $table;

    /**
     * @throws dml_exception
     */
    public function create($data): bool
    {
        global $DB;
        $DB->insert_record($this->table, $data);

        return true;
    }

    /**
     * @param $data
     * @param bool $create_on_update
     * @param array $search_params
     * @throws dml_exception
     */
    public function update($data, $create_on_update = false, $search_params = [])
    {
        global $DB;

        if ($create_on_update) {
            $exist_record = $DB->get_record($this->table,
                $search_params
            );

            if (empty($exist_record))
                $DB->insert_record($this->table, $data);
            else {
                $data['id'] = $exist_record->id;
                $DB->update_record($this->table, (object)$data);
            }
        } else {
            $DB->update_record($this->table, $data);
        }
    }

    /**
     * @throws dml_exception
     */
    public function delete($id): bool
    {
        global $DB;
        return $DB->delete_records($this->table, ['id' => $id]);
    }

    /**
     * @throws dml_exception
     */
    public function get($params = []): array
    {
        global $DB;
        $records = $DB->get_records($this->table, $params);
        $data = [];
        foreach ($records as $record) {
            $data[] = $record;
        }
        return $data;
    }


}