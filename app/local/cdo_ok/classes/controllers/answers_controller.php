<?php

namespace local_cdo_ok\controllers;

use dml_exception;

class answers_controller extends database_controller
{
    public function __construct()
    {
        $this->table = 'local_cdo_ok_answer';
    }

    /**
     * Создает или обновляет ответ пользователя
     * 
     * @param array $data Данные ответа
     * @return null
     * @throws dml_exception
     */
    public function create_update($data)
    {
        global $DB;

        $exist_record = $DB->get_record($this->table,
            [
                'question_id' => $data['question_id'],
                'user_id' => $data['user_id'],
                'integration' => $data['integration']
            ]
        );

        if (empty($exist_record)) {
            $DB->insert_record($this->table, $data);
        } else {
            $data['id'] = $exist_record->id;
            $DB->update_record($this->table, (object)$data);
        }
        
        // Инвалидация кеша для этого пользователя
        $this->invalidate_cache_for_user($data['user_id']);

        return null;
    }
    
    /**
     * Инвалидирует кеш вопросов с ответами для пользователя
     * 
     * @param int $userId ID пользователя
     */
    private function invalidate_cache_for_user(int $userId): void
    {
        try {
            $cache = \cache::make('local_cdo_ok', 'questions_answers');
            // Очищаем весь кеш сессии для этого пользователя
            $cache->purge();
            
            debugging(
                sprintf('Cache invalidated for user %d', $userId),
                DEBUG_DEVELOPER
            );
        } catch (\Throwable $e) {
            // Логируем ошибку, но не прерываем выполнение
            debugging(
                sprintf('Error invalidating cache: %s', $e->getMessage()),
                DEBUG_DEVELOPER
            );
        }
    }

    /**
     * Получает вопросы с ответами для текущего пользователя
     * 
     * @param array $params Параметры запроса (group_tab, integration)
     * @return array Массив вопросов с ответами
     * @throws dml_exception
     */
    public function get_question_with_answers($params): array
    {
        global $DB, $USER;
        
        // Увеличиваем лимиты выполнения
        @set_time_limit(120);
        
        $start_time = microtime(true);
        $params['user_id'] = $USER->id;
        
        // Формируем ключ кеша
        $cache_key = 'questions_answers_' . md5(json_encode($params));
        $cache = \cache::make('local_cdo_ok', 'questions_answers');
        
        // Пытаемся получить из кеша
        $cached_data = $cache->get($cache_key);
        if ($cached_data !== false) {
            debugging(
                sprintf('Questions loaded from cache for user %d', $USER->id),
                DEBUG_DEVELOPER
            );
            return $cached_data;
        }
        
        try {
            // Оптимизированный SQL запрос
            $sql = 'SELECT ok.id, ok.question, ok.type, ok.parameters, ok.sort, 
                           ok.group_tab, ok.visible, ok.usermodified, 
                           ok.timecreated, ok.timemodified,
                           ok.first_value_of_type, ok.second_value,
                           COALESCE(a.answer, \'\') AS answer
                    FROM {local_cdo_ok} ok 
                    INNER JOIN {local_cdo_ok_active_group} okag 
                        ON okag.group_tab = ok.group_tab AND okag.active = 1
                    LEFT JOIN {local_cdo_ok_answer} a 
                        ON a.question_id = ok.id 
                        AND a.user_id = :user_id 
                        AND a.integration = :integration
                    WHERE ok.group_tab = :group_tab 
                        AND ok.visible = 1 
                    ORDER BY ok.sort';
            
            $records = $DB->get_records_sql($sql, $params);
            
            $data = [];
            foreach ($records as $record) {
                $data[] = $record;
            }
            
            $execution_time = microtime(true) - $start_time;
            
            // Логируем время выполнения
            debugging(
                sprintf(
                    'Questions loaded in %.2f seconds for user %d (count: %d)',
                    $execution_time,
                    $USER->id,
                    count($data)
                ),
                DEBUG_DEVELOPER
            );
            
            // Сохраняем в кеш на 5 минут
            $cache->set($cache_key, $data);
            
            return $data;
            
        } catch (\dml_exception $e) {
            $execution_time = microtime(true) - $start_time;
            
            debugging(
                sprintf(
                    'Error loading questions after %.2f seconds: %s',
                    $execution_time,
                    $e->getMessage()
                ),
                DEBUG_DEVELOPER
            );
            
            throw $e;
        }
    }

    /**
     * @throws dml_exception
     */
    public function get_answer_with_sort($where=''): array
    {
        global $DB;
        /*$where = '';
        if ($by_type) {
            // group_tab = 0 - disciplines
            $where = 'WHERE ok.type = ? AND ok.group_tab=?';
        }*/
        $records = $DB->get_records_sql("SELECT oka.id id, oka.answer, oka.user_id, ok.question, ok.sort, oka.discipline 
                                                FROM {local_cdo_ok_answer} oka 
                                                INNER JOIN {local_cdo_ok} ok ON ok.id = oka.question_id 
                                                                             $where ORDER BY ok.sort");
        $data = [];
        foreach ($records as $record) {
            $data[] = $record;
        }
        return $data;
    }

    /**
     * @throws dml_exception
     */
    public function get_all_user_ids_from_answers(): array
    {
        global $DB;
        return $DB->get_records_sql(
            'SELECT user_id FROM {local_cdo_ok_answer} GROUP BY user_id'
        );
    }
}