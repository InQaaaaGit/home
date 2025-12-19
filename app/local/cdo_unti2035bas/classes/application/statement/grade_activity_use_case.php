<?php

namespace local_cdo_unti2035bas\application\statement;

use local_cdo_unti2035bas\infrastructure\xapi\client;
use local_cdo_unti2035bas\infrastructure\xapi\schemas\grade_statement_schema;
use local_cdo_unti2035bas\infrastructure\moodle\unti_mapping_service;
use local_cdo_unti2035bas\infrastructure\moodle\user_field_service;
use local_cdo_unti2035bas\infrastructure\moodle\xapi_sent_repository;
use stdClass;

/**
 * Use case для отправки цифровых следов выставления оценок
 */
class grade_activity_use_case
{
    private client $xapiClient;
    private unti_mapping_service $untiMappingService;
    private user_field_service $userFieldService;
    private xapi_sent_repository $xapiSentRepository;

    public function __construct(
        client               $xapiClient,
        unti_mapping_service $untiMappingService,
        user_field_service   $userFieldService,
        xapi_sent_repository $xapiSentRepository
    )
    {
        $this->xapiClient = $xapiClient;
        $this->untiMappingService = $untiMappingService;
        $this->userFieldService = $userFieldService;
        $this->xapiSentRepository = $xapiSentRepository;
    }

    /**
     * Отправляет цифровой след выставления оценки
     *
     * @param array $grade_data Данные оценки
     * @param string $action Действие (graded, deleted, item_created, item_updated, item_deleted)
     * @return array Результат отправки
     */
    public function send_grade_statement(array $grade_data,
                                         string $action,
                                         int $itemid,
                                         int $attempts = 1,
                                         float $grade_value = 0,
                                         int $user_attempts = 1,

    ): array
    {
        try {
            $assessment_info = $this->untiMappingService->get_assessment_info($itemid, $grade_data['flowId']);
            if (!$assessment_info) {
                debugging("Не найдено соответствие для оценки", DEBUG_DEVELOPER);
                return ['success' => false, 'error' => 'Assessment info not found'];
            }
            // Получаем UNTI ID пользователя
            $untiId = $this->userFieldService->get_unti_id($grade_data['userid']);
            if (!$untiId) {
                debugging("cdo_unti2035bas: Не найден UNTI ID для пользователя {$grade_data['userid']}", DEBUG_DEVELOPER);
                return ['success' => false, 'error' => 'UNTI ID not found'];
            }

            // Получаем UNTI ID курса
            $untiCourseId = $this->untiMappingService->get_unti_course_id($grade_data['courseid']);
            // Если UNTI course ID равен course ID, значит соответствие не найдено
             if ($untiCourseId == $grade_data['courseid']) {
                debugging("cdo_unti2035bas: Не найден UNTI ID для курса {$grade_data['courseid']}", DEBUG_DEVELOPER);
                return ['success' => false, 'error' => 'UNTI course ID not found'];
            }
            debugging("flowId = " . $grade_data['flowId']);
            if (empty($grade_data['flowId']))
                $untiFlowId = $this->untiMappingService->get_unti_flow_id($grade_data['courseid']);
            else
                $untiFlowId = $grade_data['flowId'];
            $moduleNumber = $assessment_info[0]->position;

            // Получаем object_id и данные оценки из assessment_info
            $object_id = $assessment_info[0]->lrid;
            debugging("object_id = " . $object_id);
            $min_grade      = $assessment_info[0]->grademin;
            $max_grade      = $assessment_info[0]->grademax;
            $cmid           = $assessment_info[0]->cmid;
            $module_type    = $assessment_info[0]->module;
            $passing_grade  = $assessment_info[0]->gradepass;

            // Получаем время прохождения теста
            $test_duration = null;
            if ($cmid && $module_type) {
                $test_duration = $this->untiMappingService->get_test_duration(
                    $cmid,
                    $module_type,
                    $grade_data['userid']
                );
            }

            // Конвертируем время в ISO 8601 duration формат
            $duration_iso = $test_duration ? $this->seconds_to_duration($test_duration) : null;

            // Проверяем, не был ли уже отправлен statement для данной оценки
//            if ($this->xapiSentRepository->is_grade_statement_sent($grade_data['userid'], $grade_data['itemid'], $object_id, $action)) {
//                debugging("cdo_unti2035bas: Statement для оценки уже был отправлен. Пропускаем.", DEBUG_DEVELOPER);
//                return ['success' => true, 'result' => 'Already sent'];
//            }

            // Создаем statement в зависимости от действия
            $statement = $this->create_grade_statement(
                $grade_data,
                $action,
                $untiId,
                $untiCourseId,
                $untiFlowId,
                $moduleNumber,
                $object_id,
                $grade_value,
                $min_grade,
                $max_grade,
                $duration_iso,
                $passing_grade,
                $attempts,
                $user_attempts
            );

            if (!$statement) {
                debugging("cdo_unti2035bas: Не удалось создать statement для действия {$action}", DEBUG_DEVELOPER);
                return ['success' => false, 'error' => 'Failed to create statement'];
            }

            // Логируем информацию о success
            $statement_data = $statement->dump();
            $success = $statement_data['result']['success'] ?? null;
            $scaled_score = $statement_data['result']['score']['scaled'] ?? 0;
            debugging("cdo_unti2035bas: Результат оценки - Scaled: " . round($scaled_score * 100, 2) . "%, Threshold: {$passing_grade}%, Success: " . ($success ? 'true' : 'false'), DEBUG_DEVELOPER);

            // Отправляем statement на xAPI сервер
            $result = $this->xapiClient->send([$statement]);

            // Если отправка успешна, сохраняем информацию в базу данных
            if ($result && !isset($result['error'])) {
                // Сохраняем информацию об отправке
               /* $this->xapiSentRepository->save_grade_statement(
                    $grade_data['userid'],
                    $grade_data['itemid'],
                    $object_id,
                    $statement->getStatementId(),
                    $grade_value,
                    $min_grade,
                    $max_grade,
                    $action
                );*/

                debugging(
                    "cdo_unti2035bas: Данные об отправке оценки сохранены в БД. Statement ID: {$statement->getStatementId()}",
                    DEBUG_DEVELOPER
                );
            }

            // Логируем успешную отправку
            debugging(
                "Grade {$action} xAPI statement sent successfully. User: {$untiId}, Item: {$grade_data['itemid']}",
                DEBUG_DEVELOPER
            );

            return ['success' => true, 'result' => $result];

        } catch (\Exception $e) {
            // Логируем ошибку
            debugging(
                "Failed to send grade {$action} xAPI statement. User: {$grade_data['userid']}, Item: {$grade_data['itemid']}, Error: " . $e->getMessage(),
                DEBUG_DEVELOPER
            );

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Создает xAPI statement в зависимости от действия
     *
     * @param array $grade_data
     * @param string $action
     * @param string $untiId
     * @param int $untiCourseId
     * @param int $untiFlowId
     * @param int $moduleNumber
     * @param string $object_id
     * @param float $grade_value
     * @param float $min_grade
     * @param float $max_grade
     * @param string|null $duration_iso
     * @param float $passing_grade
     * @return grade_statement_schema|null
     */
    public function create_grade_statement(
        array   $grade_data,
        string  $action,
        string  $untiId,
        int     $untiCourseId,
        int     $untiFlowId,
        int     $moduleNumber,
        string  $object_id,
        float   $grade_value,
        float   $min_grade,
        float   $max_grade,
        ?string $duration_iso,
        float   $passing_grade,
        int     $attempts,
        int     $user_attempts
    ): ?grade_statement_schema
    {
        // Генерируем registration UUID для попытки
        $registration = $this->generate_registration_uuid();

        switch ($action) {
            case 'quiz_submitted':
            case 'graded':
                return grade_statement_schema::create_grade_received_statement(
                    $untiId,
                    $object_id,
                    $grade_value,
                    $min_grade,
                    $max_grade,
                    $untiCourseId,
                    $untiFlowId,
                    $moduleNumber,
                    time(),
                    $registration,
                    $user_attempts, // attempts_index
                    $attempts, // attempts_max
                    $passing_grade,
                    $duration_iso
                );

            case 'deleted':
                return grade_statement_schema::create_grade_deleted_statement(
                    $untiId,
                    $grade_data['itemid'],
                    $grade_data['itemname'] ?? 'Unknown Item',
                    $untiCourseId,
                    $untiFlowId,
                    $moduleNumber,
                    $grade_data['timecreated']
                );

            case 'item_created':
                return grade_statement_schema::create_grade_item_created_statement(
                    $grade_data['itemid'],
                    $grade_data['itemname'] ?? 'Unknown Item',
                    $grade_data['itemtype'] ?? 'unknown',
                    $grade_data['itemmodule'] ?? 'unknown',
                    $untiCourseId,
                    $untiFlowId,
                    $moduleNumber,
                    $grade_data['timecreated']
                );

            case 'item_updated':
                return grade_statement_schema::create_grade_item_updated_statement(
                    $grade_data['itemid'],
                    $grade_data['itemname'] ?? 'Unknown Item',
                    $grade_data['itemtype'] ?? 'unknown',
                    $grade_data['itemmodule'] ?? 'unknown',
                    $untiCourseId,
                    $untiFlowId,
                    $moduleNumber,
                    $grade_data['timecreated']
                );

            case 'item_deleted':
                return grade_statement_schema::create_grade_item_deleted_statement(
                    $grade_data['itemid'],
                    $grade_data['itemname'] ?? 'Unknown Item',
                    $grade_data['itemtype'] ?? 'unknown',
                    $grade_data['itemmodule'] ?? 'unknown',
                    $untiCourseId,
                    $untiFlowId,
                    $moduleNumber,
                    $grade_data['timecreated']
                );

            default:
                debugging("cdo_unti2035bas: Неизвестное действие: {$action}", DEBUG_DEVELOPER);
                return null;
        }
    }

    /**
     * Генерирует UUID для registration
     *
     * @return string
     */
    private function generate_registration_uuid(): string
    {
        if (function_exists('random_bytes')) {
            $data = random_bytes(16);
        } else {
            $data = openssl_random_pseudo_bytes(16);
        }

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Конвертирует секунды в ISO 8601 duration формат
     *
     * @param int $seconds
     * @return string|null
     */
    private function seconds_to_duration(int $seconds): ?string
    {
        if ($seconds < 0) {
            return null;
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remaining_seconds = $seconds % 60;

        return "PT{$hours}H{$minutes}M{$remaining_seconds}S";
    }
} 