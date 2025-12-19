<?php

namespace local_cdo_unti2035bas\infrastructure\moodle;

class xapi_sent_repository {
    private $db;
    private $table_name = 'cdo_unti2035bas_xapi_sent';

    public function __construct() {
        global $DB;
        $this->db = $DB;
    }

    /**
     * Проверить, был ли уже отправлен statement для данного пользователя и контента
     */
    public function is_statement_sent($userid, $cmid, $lrid) {
        return $this->db->record_exists($this->table_name, [
            'userid' => $userid,
            'cmid' => $cmid,
            'lrid' => $lrid
        ]);
    }

    /**
     * Сохранить информацию об успешно отправленном statement
     */
    public function save_sent_statement($userid, $cmid, $lrid, $statement_id, $progress_value, $duration_seconds) {
        $record = new \stdClass();
        $record->userid = $userid;
        $record->cmid = $cmid;
        $record->lrid = $lrid;
        $record->statement_id = $statement_id;
        $record->statement_type = 'video_watched';
        $record->progress_value = $progress_value;
        $record->duration_seconds = $duration_seconds;
        $record->timecreated = time();

        try {
            return $this->db->insert_record($this->table_name, $record);
        } catch (\dml_exception $e) {
            // Если запись уже существует (дубликат), это нормально
            if (stripos($e->getMessage(), 'duplicate') !== false) {
                return true;
            }
            throw $e;
        }
    }

    /**
     * Получить все отправленные statements для пользователя
     */
    public function get_user_sent_statements($userid) {
        return $this->db->get_records($this->table_name, ['userid' => $userid], 'timecreated DESC');
    }

    /**
     * Получить отправленный statement по ID
     */
    public function get_sent_statement_by_id($statement_id) {
        return $this->db->get_record($this->table_name, ['statement_id' => $statement_id]);
    }

    /**
     * Удалить старые записи (старше определенного количества дней)
     */
    public function cleanup_old_records($days_to_keep = 90) {
        $cutoff_time = time() - ($days_to_keep * 24 * 60 * 60);
        return $this->db->delete_records_select($this->table_name, 'timecreated < ?', [$cutoff_time]);
    }

    /**
     * Сохранить информацию об успешно отправленном statement для оценки
     */
    public function save_grade_statement($userid, $itemid, $lrid, $statement_id, $grade_value, $min_grade, $max_grade, $action) {
        $record = new \stdClass();
        $record->userid = $userid;
        $record->cmid = 0; // Для оценок cmid может быть 0, так как это не course module
        $record->lrid = $lrid;
        $record->statement_id = $statement_id;
        $record->statement_type = 'grade_' . $action;
        $record->progress_value = $max_grade > $min_grade ? (($grade_value - $min_grade) / ($max_grade - $min_grade)) * 100 : 0;
        $record->duration_seconds = 0; // Для оценок duration не применим
        $record->timecreated = time();

        try {
            return $this->db->insert_record($this->table_name, $record);
        } catch (\dml_exception $e) {
            // Если запись уже существует (дубликат), это нормально
            if (stripos($e->getMessage(), 'duplicate') !== false) {
                return true;
            }
            throw $e;
        }
    }

    /**
     * Проверить, был ли уже отправлен statement для данной оценки
     */
    public function is_grade_statement_sent($userid, $itemid, $lrid, $action) {
        return $this->db->record_exists($this->table_name, [
            'userid' => $userid,
            'lrid' => $lrid,
            'statement_type' => 'grade_' . $action
        ]);
    }
} 