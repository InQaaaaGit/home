<?php

namespace block_cdo_notification\output\notifications;

use block_cdo_notification\notification_manager;
use coding_exception;
use core\output\renderer_base;
use core_reportbuilder\local\filters\date;
use DateMalformedStringException;
use DateTime;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

class renderable implements \renderable, \templatable
{
    private string $template = 'block_cdo_notification/notifications';
    private string $template_header = 'block_cdo_notification/notification_headers';

    /**
     * @param renderer_base $output
     * @return array
     * @throws DateMalformedStringException
     */
    public function export_for_template(renderer_base $output): array
    {
        global $CFG;
        
        $manager = new notification_manager();
        $notifications = [];
        try {
            $notifications = $manager->get_active_notifications();
        } catch (coding_exception|cdo_config_exception|cdo_type_response_exception $e) {
            $array['error_message'] = $e->getMessage();
        }

        // Сортировка по дате (по убыванию)
        usort($notifications, function($a, $b) {
            return strtotime($b['date']) <=> strtotime($a['date']);
        });

        // Получаем лимит из настроек
        $maxnotifications = (int) get_config('block_cdo_notification', 'maxnotifications');
        if ($maxnotifications < 1) {
            $maxnotifications = 3;
        }
        $notifications = array_slice($notifications, 0, $maxnotifications);

        foreach ($notifications as &$notification) {
            $notification['notification-idx'] = uniqid();
            $dateTime = new \DateTime($notification['date']); // Создаем объект DateTime из строки
            $notification['date'] = $dateTime->format('d-m-Y H:i:s');
        }
        if (!empty($notifications)) {
            $notifications[0]['notification-first'] = true;
        }
        $array['notifications'] = $notifications;
        $array['notifications_count'] = count($notifications);
        $array['template'] = $this->template_header;
        $array['wwwroot'] = $CFG->wwwroot;
        return $array;
    }
}
