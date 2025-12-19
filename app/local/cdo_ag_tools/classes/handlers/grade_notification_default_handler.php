<?php

namespace local_cdo_ag_tools\handlers;

use local_cdo_ag_tools\interfaces\grade_notification_handler;

/**
 * Базовый обработчик уведомлений
 */
class grade_notification_default_handler implements grade_notification_handler {
    public function get_subject(): string {
        return get_string('messageprovider:grade_update_subject', 'local_cdo_ag_tools');
    }

    public function get_message(array $params): string {
        return get_string('messageprovider:grade_update_message', 'local_cdo_ag_tools', $params);
    }
} 