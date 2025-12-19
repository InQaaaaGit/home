<?php

namespace local_cdo_unti2035bas\infrastructure\moodle;

defined('MOODLE_INTERNAL') || die();

/**
 * Репозиторий для работы с результатами xAPI запросов
 */
class xapi_results_repository {
    
    private $db;
    private $table_name = 'cdo_unti2035bas_xapi_results';
    
    public function __construct() {
        global $DB;
        $this->db = $DB;
    }
    
    /**
     * Сохранить результат xAPI запроса
     * 
     * @param string $result JSON результат запроса
     * @param string $query JSON запрос
     * @return int|bool ID записи или false при ошибке
     */
    public function save_result(string $result, string $query) {
        $record = new \stdClass();
        $record->result = $result;
        $record->query = $query;
        
        try {
            return $this->db->insert_record($this->table_name, $record);
        } catch (\dml_exception $e) {
            debugging("Ошибка сохранения xAPI результата: " . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }
    
    /**
     * Получить результат по ID
     * 
     * @param int $id ID записи
     * @return object|null Запись или null если не найдена
     */
    public function get_by_id(int $id) {
        return $this->db->get_record($this->table_name, ['id' => $id]);
    }
    
    /**
     * Получить последние результаты
     * 
     * @param int $limit Количество записей
     * @return array Массив записей
     */
    public function get_recent_results(int $limit = 100): array {
        return $this->db->get_records(
            $this->table_name, 
            [], 
            'id DESC', 
            '*', 
            0, 
            $limit
        );
    }
    
    /**
     * Удалить старые записи
     * 
     * @param int $days_to_keep Количество дней для хранения
     * @return int Количество удаленных записей
     */
    public function cleanup_old_records(int $days_to_keep = 30): int {
        $cutoff_time = time() - ($days_to_keep * 24 * 60 * 60);
        
        // Получаем ID записей старше указанного времени
        $old_records = $this->db->get_records_select(
            $this->table_name,
            'id < ?',
            [$cutoff_time],
            'id',
            'id'
        );
        
        if (empty($old_records)) {
            return 0;
        }
        
        $ids = array_keys($old_records);
        return $this->db->delete_records_list($this->table_name, 'id', $ids);
    }
    
    /**
     * Получить статистику по результатам
     * 
     * @return array Статистика
     */
    public function get_statistics(): array {
        $total = $this->db->count_records($this->table_name);
        
        // Получаем последние 24 часа
        $last_24h = $this->db->count_records_select(
            $this->table_name,
            'id > ?',
            [time() - (24 * 60 * 60)]
        );
        
        return [
            'total' => $total,
            'last_24h' => $last_24h
        ];
    }
} 