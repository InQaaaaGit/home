<?php
/**
 * Scheduled task for notification
 *
 * @package    local_cdo_ok
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cdo_ok\task;

use core\task\scheduled_task;

/**
 * Task для отправки уведомлений о необходимости пройти опрос
 */
class notify extends scheduled_task {

    /**
     * Получить имя задачи
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasknotify', 'local_cdo_ok');
    }

    /**
     * Выполнить задачу
     *
     * @return void
     */
    public function execute() {
        // TODO: Реализовать логику отправки уведомлений
        mtrace('Executing notify task for local_cdo_ok');
    }
}









