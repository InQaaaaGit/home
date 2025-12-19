<?php

namespace local_cdo_ag_tools\tasks;

use coding_exception;
use dml_exception;
use local_cdo_ag_tools\services\work_notification_service;
use moodle_exception;

/**
 * Scheduled task для еженедельной рассылки отчетов о пройденных тестах
 *
 * Выполняется каждое воскресенье и отправляет отчеты всем пользователям,
 * которые прошли хотя бы один тест за текущую неделю (с понедельника по воскресенье)
 *
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class weekly_quiz_report_task extends \core\task\scheduled_task
{
    /**
     * Возвращает название задачи
     *
     * @return string
     * @throws coding_exception
     */
    public function get_name(): string
    {
        return get_string('weekly_quiz_report_task', 'local_cdo_ag_tools');
    }

    /**
     * Выполняет задачу отправки еженедельных отчетов
     *
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function execute(): void
    {
        global $DB;

        mtrace('Начало отправки еженедельных отчетов о пройденных тестах...');

        // Определяем период: текущая неделя (понедельник - воскресенье)
        $currentTimestamp = time();
        $dayOfWeek = date('N', $currentTimestamp); // 1 (понедельник) - 7 (воскресенье)
        
        // Начало недели (понедельник)
        $startOfWeek = strtotime('-' . ($dayOfWeek - 1) . ' days', $currentTimestamp);
        $dateFrom = date('Y-m-d', $startOfWeek);
        
        // Конец недели (воскресенье)
        $endOfWeek = strtotime('+' . (7 - $dayOfWeek) . ' days', $currentTimestamp);
        $dateTo = date('Y-m-d', $endOfWeek);

        mtrace("Период отчета: с {$dateFrom} по {$dateTo} (текущая неделя)");

        // Получаем список пользователей, у которых есть оценки за тесты за этот период
        $usersWithQuizGrades = $this->get_users_with_quiz_grades($dateFrom, $dateTo);

        if (empty($usersWithQuizGrades)) {
            mtrace('Нет пользователей с оценками за тесты в указанном периоде.');
            return;
        }

        mtrace('Найдено пользователей с оценками: ' . count($usersWithQuizGrades));

        $successCount = 0;
        $failCount = 0;

        foreach ($usersWithQuizGrades as $userId) {
            try {
                $sent = work_notification_service::send_weekly_quiz_report(
                    $userId,
                    $dateFrom,
                    $dateTo
                );

                if ($sent) {
                    $successCount++;
                    mtrace("  ✓ Отчет отправлен пользователю ID: {$userId}");
                } else {
                    $failCount++;
                    mtrace("  ✗ Не удалось отправить отчет пользователю ID: {$userId}");
                }
            } catch (\Exception $e) {
                $failCount++;
                mtrace("  ✗ Ошибка при отправке отчета пользователю ID: {$userId}: " . $e->getMessage());
            }
        }

        mtrace("Завершено. Успешно: {$successCount}, Ошибок: {$failCount}");
    }

    /**
     * Получает список пользователей, у которых есть оценки за тесты за указанный период
     *
     * @param string $dateFrom Дата начала периода
     * @param string $dateTo Дата окончания периода
     * @return array Массив ID пользователей
     * @throws dml_exception
     */
    private function get_users_with_quiz_grades(string $dateFrom, string $dateTo): array
    {
        global $DB;

        $timeFrom = strtotime($dateFrom);
        $timeTo = strtotime($dateTo . ' 23:59:59');

        $sql = "SELECT DISTINCT gg.userid
                FROM {grade_grades} gg
                JOIN {grade_items} gi ON gi.id = gg.itemid
                WHERE gi.itemtype = 'mod'
                  AND gi.itemmodule = 'quiz'
                  AND gg.finalgrade IS NOT NULL
                  AND gg.timemodified >= :timefrom
                  AND gg.timemodified <= :timeto
                ORDER BY gg.userid";

        $params = [
            'timefrom' => $timeFrom,
            'timeto' => $timeTo,
        ];

        $records = $DB->get_records_sql($sql, $params);

        return array_keys($records);
    }
}

