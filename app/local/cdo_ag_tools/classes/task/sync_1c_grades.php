<?php

namespace local_cdo_ag_tools\task;

use core\task\scheduled_task;
use local_cdo_ag_tools\factories\grade_strategy_factory;
use local_cdo_ag_tools\handlers\grade_saver;
use local_cdo_ag_tools\helpers\grade_data_helper;
use local_cdo_ag_tools\integrations\onec_integration;
use local_cdo_ag_tools\config\grade_interceptor_config;

/**
 * Scheduled task для отправки оценок в 1С
 *
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class sync_1c_grades extends scheduled_task
{
    /**
     * Получить имя задачи
     *
     * @return string
     */
    public function get_name(): string
    {
        return 'Синхронизация оценок с 1С';
    }

    /**
     * Выполнить задачу
     *
     * @return bool
     */
    public function execute(): bool
    {

        try {
            //1756742400 1/09/2025
            $grades = grade_saver::get_grades_since(1756742400);
            $strategy = grade_strategy_factory::create_by_name('direct_send');
            foreach ($grades as $grade) {
                $result = $strategy->handle_grade($grade);
                if ($result) {
                    grade_saver::delete_grade($grade->id);
                }
            }

            return true;
        } catch (\Exception $e) {
            mtrace('Error during 1C sync: ' . $e->getMessage());

            if (grade_interceptor_config::$onec_log_enabled) {
                debugging('1C sync task error: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }

            return false;
        }
    }

    /**
     * Очистить старые записи из очереди
     *
     * @return void
     */
    private function cleanup_old_records(): void
    {
        global $DB;

        try {
            // Удаляем успешно отправленные записи старше 7 дней
            $cutoff_time = time() - (7 * 24 * 60 * 60); // 7 дней назад

            $deleted_sent = $DB->delete_records_select(
                'local_cdo_ag_tools_1c_queue',
                'status = ? AND sent_at < ?',
                ['sent', $cutoff_time]
            );

            // Удаляем окончательно неудачные записи старше 30 дней
            $old_cutoff_time = time() - (30 * 24 * 60 * 60); // 30 дней назад

            $deleted_failed = $DB->delete_records_select(
                'local_cdo_ag_tools_1c_queue',
                'status = ? AND created_at < ?',
                ['failed', $old_cutoff_time]
            );

            if ($deleted_sent > 0 || $deleted_failed > 0) {
                mtrace("Cleanup completed: deleted $deleted_sent sent records and $deleted_failed failed records");
            }

        } catch (\Exception $e) {
            mtrace('Error during cleanup: ' . $e->getMessage());
        }
    }
} 