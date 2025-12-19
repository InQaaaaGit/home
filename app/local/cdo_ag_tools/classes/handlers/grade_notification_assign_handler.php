<?php

namespace local_cdo_ag_tools\handlers;

use coding_exception;
use local_cdo_ag_tools\interfaces\grade_notification_handler;

/**
 * Обработчик уведомлений для заданий
 */
class grade_notification_assign_handler implements grade_notification_handler {
    /**
     * @throws coding_exception
     */
    public function get_subject(): string {
        return get_string('messageprovider:grade_update_assign_subject', 'local_cdo_ag_tools');
    }

    /**
     * @throws coding_exception
     */
    public function get_message(array $params): string {
        return get_string('messageprovider:grade_update_assign_message', 'local_cdo_ag_tools', $params);
    }
} 