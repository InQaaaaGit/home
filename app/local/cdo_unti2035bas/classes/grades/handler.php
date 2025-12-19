<?php

namespace local_cdo_unti2035bas\grades;

use local_cdo_unti2035bas\infrastructure\xapi\schemas\grade_statement_schema;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\text_activity_statement_schema;
use local_cdo_unti2035bas\observer\dependencies;
use local_cdo_unti2035bas\infrastructure\xapi\client as xapi_client;

class handler
{
    private dependencies $dependencies;
    private xapi_client $xapiClient;

    public function __construct()
    {
        $this->dependencies = new dependencies();
        $this->xapiClient = $this->dependencies->get_xapi_client();
    }

    /**
     * Получить и отправить оценки для курса
     * 
     * @param int $course_id
     * @return array
     */
    public function get_grades($course_id): array {
        global $DB;
        
        try {
            // Получаем оценки для курса
            $grades = $this->get_course_grades($course_id);
            
            if (empty($grades)) {
                return [
                    'sent' => 0,
                    'skipped' => 0,
                    'errors' => 0,
                    'total' => 0
                ];
            }
            
            // Отправляем оценки через grade_activity_use_case
            return $this->send_grade_statements($grades);
            
        } catch (\Exception $e) {
            debugging("cdo_unti2035bas: Ошибка при обработке оценок: " . $e->getMessage(), DEBUG_DEVELOPER);
            return [
                'sent' => 0,
                'skipped' => 0,
                'errors' => 1,
                'total' => 0,
                'details' => [
                    'sent' => [],
                    'errors' => [[
                        'error_message' => $e->getMessage(),
                        'error_type' => 'system_error',
                        'timestamp' => time()
                    ]],
                    'skipped' => []
                ]
            ];
        }
    }

    /**
     * Получить и отправить оценки для конкретных пользователей в курсе
     *
     * @param int $course_id
     * @param array $userids
     * @return array
     */
    public function get_grades_for_users(int $course_id, array $userids, int $flowId): array {
        if (empty($userids)) {
            return ['sent' => 0, 'skipped' => 0, 'errors' => 0, 'total' => 0];
        }

        try {
            $grades = $this->get_course_grades($course_id, $userids);

            if (empty($grades)) {
                return ['sent' => 0, 'skipped' => 0, 'errors' => 0, 'total' => 0];
            }
            
            return $this->send_grade_statements($grades, $flowId);

        } catch (\Exception $e) {
            debugging("cdo_unti2035bas: Ошибка при обработке оценок для пользователей: " . $e->getMessage(), DEBUG_DEVELOPER);
            return [
                'sent' => 0,
                'skipped' => 0,
                'errors' => 1,
                'total' => 0,
                'details' => [
                    'errors' => [['error_message' => $e->getMessage()]]
                ]
            ];
        }
    }

    /**
     * Получить оценки курса из базы данных
     * 
     * @param int $course_id
     * @param array|null $userids
     * @return array
     */
    private function get_course_grades(int $course_id, ?array $userids = null): array {
        global $DB;

        // SQL и параметры
        $sql = "
            SELECT gg.*, gi.itemtype, gi.itemmodule, gi.iteminstance,
                   gi.courseid, gi.grademax, gi.grademin
            FROM {grade_grades} gg
            JOIN {grade_items} gi ON gi.id = gg.itemid
            WHERE gi.courseid = ?
            AND gg.finalgrade IS NOT NULL
            AND gi.itemtype IN ('mod', 'manual')
        ";
        $params = [$course_id];

        if (!empty($userids)) {
            list($user_sql, $user_params) = $DB->get_in_or_equal($userids);
            $sql .= " AND gg.userid $user_sql";
            $params = array_merge($params, $user_params);
        }

        return $DB->get_records_sql($sql, $params);
    }
    
    /**
     * Отправить statements для оценок
     * 
     * @param array $grades
     * @return array
     */
    private function send_grade_statements(array $grades, $flowId): array {
        $sent_count = 0;
        $skipped_count = 0;
        $error_count = 0;
        
        // Детальная информация о результатах
        $details = [
            'sent' => [],
            'errors' => [],
            'skipped' => []
        ];
        
        $grade_use_case = $this->dependencies->get_grade_activity_use_case();
        
        foreach ($grades as $grade) {
            try {
                // Получаем информацию о пользователе и элементе оценки
                global $DB;
                $user = $DB->get_record('user', ['id' => $grade->userid], 'id, firstname, lastname, email');
                $grade_item = $DB->get_record('grade_items', ['id' => $grade->itemid], 'id, itemname, itemtype, itemmodule');
                debugging("grade_item = " . $grade->itemid);
                // Формируем данные оценки
                $grade_data = [
                    'flowId' => $flowId,
                    'userid' => $grade->userid,
                    'itemid' => $grade->itemid,
                    'courseid' => $grade->courseid,
                    'grade_info' => [
                        'itemid' => $grade->itemid,
                        'finalgrade' => $grade->finalgrade,
                        'grademax' => $grade->grademax,
                        'grademin' => $grade->grademin,
                        'itemtype' => $grade->itemtype,
                        'itemmodule' => $grade->itemmodule
                    ]
                ];
                
                // Отправляем statement
                $result = $grade_use_case->send_grade_statement(
                    $grade_data, 
                    'graded', 
                    $grade->itemid,
                    1, // attempts
                    $grade->finalgrade,
                    1  // user_attempts
                );
                
                // Формируем детальную информацию
                $detail_record = [
                    'user_id' => $grade->userid,
                    'user_name' => $user ? trim($user->firstname . ' ' . $user->lastname) : "ID: {$grade->userid}",
                    'user_email' => $user->email ?? '',
                    'item_id' => $grade->itemid,
                    'item_name' => $grade_item->itemname ?? "ID: {$grade->itemid}",
                    'item_type' => $grade_item->itemtype ?? '',
                    'grade_value' => $grade->finalgrade,
                    'grade_max' => $grade->grademax,
                    'timestamp' => time()
                ];
                
                if ($result['success']) {
                    $sent_count++;
                    $details['sent'][] = $detail_record;
                    debugging("cdo_unti2035bas: Успешно отправлен statement для оценки user:{$grade->userid}, item:{$grade->itemid}", DEBUG_DEVELOPER);
                } else {
                    $error_count++;
                    $detail_record['error_message'] = $result['error'] ?? 'Неизвестная ошибка';
                    $detail_record['error_type'] = $this->classify_error($result['error'] ?? '');
                    $details['errors'][] = $detail_record;
                    debugging("cdo_unti2035bas: Ошибка отправки statement для оценки user:{$grade->userid}, item:{$grade->itemid}: " . ($result['error'] ?? ''), DEBUG_DEVELOPER);
                }
                
            } catch (\Exception $e) {
                $error_count++;
                
                // Получаем базовую информацию даже при исключении
                $user = null;
                $grade_item = null;
                try {
                    global $DB;
                    $user = $DB->get_record('user', ['id' => $grade->userid], 'id, firstname, lastname, email');
                    $grade_item = $DB->get_record('grade_items', ['id' => $grade->itemid], 'id, itemname, itemtype');
                } catch (\Exception $db_e) {
                    // Игнорируем ошибки получения дополнительной информации
                }
                
                $detail_record = [
                    'user_id' => $grade->userid,
                    'user_name' => $user ? trim($user->firstname . ' ' . $user->lastname) : "ID: {$grade->userid}",
                    'user_email' => $user->email ?? '',
                    'item_id' => $grade->itemid,
                    'item_name' => $grade_item->itemname ?? "ID: {$grade->itemid}",
                    'item_type' => $grade_item->itemtype ?? '',
                    'grade_value' => $grade->finalgrade,
                    'grade_max' => $grade->grademax,
                    'error_message' => $e->getMessage(),
                    'error_type' => $this->classify_error($e->getMessage()),
                    'timestamp' => time()
                ];
                
                $details['errors'][] = $detail_record;
                debugging("cdo_unti2035bas: Exception при отправке оценки user:{$grade->userid}, item:{$grade->itemid}: " . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }
        
        return [
            'sent' => $sent_count,
            'skipped' => $skipped_count,
            'errors' => $error_count,
            'total' => count($grades),
            'details' => $details
        ];
    }
    
    /**
     * Классифицирует ошибки по типам для лучшего отображения
     */
    private function classify_error(string $error_message): string {
        $error_message = mb_strtolower($error_message);
        
        if (strpos($error_message, 'assessment info not found') !== false || 
            strpos($error_message, 'не найдено соответствие') !== false) {
            return 'mapping_error';
        }
        
        if (strpos($error_message, 'unti id not found') !== false || 
            strpos($error_message, 'не найден unti id') !== false) {
            return 'user_mapping_error';
        }
        
        if (strpos($error_message, 'unti course id not found') !== false || 
            strpos($error_message, 'не найден unti id для курса') !== false) {
            return 'course_mapping_error';
        }
        
        if (strpos($error_message, 'connection') !== false || 
            strpos($error_message, 'timeout') !== false || 
            strpos($error_message, 'network') !== false) {
            return 'network_error';
        }
        
        if (strpos($error_message, 'permission') !== false || 
            strpos($error_message, 'access') !== false || 
            strpos($error_message, 'auth') !== false) {
            return 'permission_error';
        }
        
        return 'unknown_error';
    }

    /**
     * Отправляет данные о просмотре текстовой активности в систему УНТИ
     * 
     * @param string $untiId UNTI ID пользователя
     * @param string $object_id LRID объекта
     * @param int $untiFlowId ID потока в УНТИ
     * @param int $untiCourseId ID курса в УНТИ
     * @param int $moduleNumber Номер модуля (опционально)
     * @return array Результат отправки ['success' => bool, 'error' => string|null, 'response' => array|null]
     */
    public function send_activity_successful_text_usage(
        $untiId, $object_id, $untiFlowId, $untiCourseId, $moduleNumber = null): array
    {
        try {
            // Создаем statement для завершения текстовой активности
            $statement = text_activity_statement_schema::create_text_completed_statement(
                $untiId,
                $object_id,
                $untiCourseId,
                $untiFlowId,
                $moduleNumber
            );

            // Отправляем statement через xAPI клиент
            $response = $this->xapiClient->send([$statement]);
            
            debugging("cdo_unti2035bas: Успешно отправлен statement текстовой активности для UNTI ID {$untiId}, объект {$object_id}", DEBUG_DEVELOPER);
            
            return [
                'success' => true,
                'error' => null,
                'response' => $response,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
            
        } catch (\local_cdo_unti2035bas\exceptions\curl_error $e) {
            $error_msg = "CURL ошибка при отправке данных текстовой активности для UNTI ID {$untiId}: " . $e->getMessage();
            debugging("cdo_unti2035bas: " . $error_msg, DEBUG_DEVELOPER);
            
            return [
                'success' => false,
                'error' => $error_msg,
                'error_type' => 'curl_error',
                'error_code' => $e->getCode(),
                'response' => null,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
            
        } catch (\local_cdo_unti2035bas\exceptions\xapi_error $e) {
            $error_msg = "xAPI ошибка при отправке данных текстовой активности для UNTI ID {$untiId}: " . $e->getMessage();
            debugging("cdo_unti2035bas: " . $error_msg, DEBUG_DEVELOPER);
            
            return [
                'success' => false,
                'error' => $error_msg,
                'error_type' => 'xapi_error',
                'error_code' => $e->getCode(),
                'response' => null,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
            
        } catch (\Exception $e) {
            $error_msg = "Общая ошибка при отправке данных текстовой активности для UNTI ID {$untiId}: " . $e->getMessage();
            debugging("cdo_unti2035bas: " . $error_msg, DEBUG_DEVELOPER);
            
            return [
                'success' => false,
                'error' => $error_msg,
                'error_type' => 'general_error',
                'error_code' => $e->getCode(),
                'response' => null,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
        }
    }

    /**
     * Отправляет данные о прохождении практики в систему УНТИ
     * 
     * @param string $untiId UNTI ID пользователя
     * @param string $object_id LRID объекта
     * @param int $untiCourseId ID курса в УНТИ
     * @param int $untiFlowId ID потока в УНТИ
     * @return array Результат отправки ['success' => bool, 'error' => string|null, 'response' => array|null]
     */
    public function send_grade_to_practice_with_success_status(
        $untiId, $object_id, $untiFlowId, $untiCourseId): array
    {
        try {
            // Создаем statement для практики
            $statement = grade_statement_schema::create_grade_received_statement(
                $untiId,
                $object_id,
                5,
                0,
                5,
                $untiCourseId,
                $untiFlowId
            );

            // Отправляем statement через xAPI клиент
            $response = $this->xapiClient->send([$statement]);
            
            debugging("cdo_unti2035bas: Успешно отправлен statement практики для UNTI ID {$untiId}, объект {$object_id}", DEBUG_DEVELOPER);
            
            return [
                'success' => true,
                'error' => null,
                'response' => $response,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
            
        } catch (\local_cdo_unti2035bas\exceptions\curl_error $e) {
            $error_msg = "CURL ошибка при отправке данных практики для UNTI ID {$untiId}: " . $e->getMessage();
            debugging("cdo_unti2035bas: " . $error_msg, DEBUG_DEVELOPER);
            
            return [
                'success' => false,
                'error' => $error_msg,
                'error_type' => 'curl_error',
                'error_code' => $e->getCode(),
                'response' => null,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
            
        } catch (\local_cdo_unti2035bas\exceptions\xapi_error $e) {
            $error_msg = "xAPI ошибка при отправке данных практики для UNTI ID {$untiId}: " . $e->getMessage();
            debugging("cdo_unti2035bas: " . $error_msg, DEBUG_DEVELOPER);
            
            return [
                'success' => false,
                'error' => $error_msg,
                'error_type' => 'xapi_error',
                'error_code' => $e->getCode(),
                'response' => null,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
            
        } catch (\Exception $e) {
            $error_msg = "Общая ошибка при отправке данных практики для UNTI ID {$untiId}: " . $e->getMessage();
            debugging("cdo_unti2035bas: " . $error_msg, DEBUG_DEVELOPER);
            
            return [
                'success' => false,
                'error' => $error_msg,
                'error_type' => 'general_error',
                'error_code' => $e->getCode(),
                'response' => null,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
        }
    }

    /**
     * Отправляет данные о просмотре текстового контента в систему УНТИ
     * 
     * @param string $untiId UNTI ID пользователя
     * @param string $object_id LRID объекта
     * @param int $untiFlowId ID потока в УНТИ
     * @param int $untiCourseId ID курса в УНТИ
     * @param int $moduleNumber Номер модуля (опционально)
     * @return array Результат отправки
     */
    public function send_text_viewed_activity(
        $untiId, $object_id, $untiFlowId, $untiCourseId, $moduleNumber = null): array
    {
        try {
            // Создаем statement для просмотра текстового контента
            $statement = text_activity_statement_schema::create_text_viewed_statement(
                $untiId,
                $object_id,
                $untiCourseId,
                $untiFlowId,
                $moduleNumber
            );

            // Отправляем statement через xAPI клиент
            $response = $this->xapiClient->send([$statement]);
            
            debugging("cdo_unti2035bas: Успешно отправлен statement просмотра текста для UNTI ID {$untiId}, объект {$object_id}", DEBUG_DEVELOPER);
            
            return [
                'success' => true,
                'error' => null,
                'response' => $response,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
            
        } catch (\Exception $e) {
            $error_msg = "Ошибка при отправке данных просмотра текста для UNTI ID {$untiId}: " . $e->getMessage();
            debugging("cdo_unti2035bas: " . $error_msg, DEBUG_DEVELOPER);
            
            return [
                'success' => false,
                'error' => $error_msg,
                'error_type' => 'general_error',
                'error_code' => $e->getCode(),
                'response' => null,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
        }
    }

    /**
     * Отправляет данные о скачивании текстового контента в систему УНТИ
     * 
     * @param string $untiId UNTI ID пользователя
     * @param string $object_id LRID объекта
     * @param int $untiFlowId ID потока в УНТИ
     * @param int $untiCourseId ID курса в УНТИ
     * @param int $moduleNumber Номер модуля (опционально)
     * @return array Результат отправки
     */
    public function send_text_downloaded_activity(
        $untiId, $object_id, $untiFlowId, $untiCourseId, $moduleNumber = null): array
    {
        try {
            // Создаем statement для скачивания текстового контента
            $statement = text_activity_statement_schema::create_text_downloaded_statement(
                $untiId,
                $object_id,
                $untiCourseId,
                $untiFlowId,
                $moduleNumber
            );

            // Отправляем statement через xAPI клиент
            $response = $this->xapiClient->send([$statement]);
            
            debugging("cdo_unti2035bas: Успешно отправлен statement скачивания текста для UNTI ID {$untiId}, объект {$object_id}", DEBUG_DEVELOPER);
            
            return [
                'success' => true,
                'error' => null,
                'response' => $response,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
            
        } catch (\Exception $e) {
            $error_msg = "Ошибка при отправке данных скачивания текста для UNTI ID {$untiId}: " . $e->getMessage();
            debugging("cdo_unti2035bas: " . $error_msg, DEBUG_DEVELOPER);
            
            return [
                'success' => false,
                'error' => $error_msg,
                'error_type' => 'general_error',
                'error_code' => $e->getCode(),
                'response' => null,
                'unti_id' => $untiId,
                'object_id' => $object_id,
                'timestamp' => time()
            ];
        }
    }
}