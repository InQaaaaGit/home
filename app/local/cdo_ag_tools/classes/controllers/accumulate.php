<?php

namespace local_cdo_ag_tools\controllers;

use dml_exception;

class accumulate
{
    const TABLE_NAME = 'cdo_ag_tools_accumulate';


    public static function set_accumulate_row($course_id): void
    {
        global $DB;
        try {
            $condition = [
                'courseid' => $course_id
            ];
            $exist = self::get_accumulate_row($condition);
            if (count($exist) === 0)
                $result = $DB->insert_record(
                    self::TABLE_NAME,
                    $condition
                );
        } catch (dml_exception $e) {
            // Log the error.  For now, we'll use error_log, but a more robust
            // logging solution might be better in a production environment.
            error_log('Error inserting into ' . self::TABLE_NAME . ': ' . $e->getMessage());
        }
    }

    /**
     * @throws dml_exception
     */
    public static function get_accumulate_row($conditions = []): array
    {
        global $DB;
        return $DB->get_records(self::TABLE_NAME, $conditions);
    }

    /**
     * @throws dml_exception
     */
    public static function delete_accumulate_row($course_id): void
    {
        global $DB;
        $DB->delete_records(self::TABLE_NAME, ['courseid' => $course_id]);
    }
}
