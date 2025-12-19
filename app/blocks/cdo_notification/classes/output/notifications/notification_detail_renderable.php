<?php
namespace block_cdo_notification\output\notifications;

use renderable;
use templatable;
use renderer_base;

class notification_detail_renderable implements renderable, templatable {
    protected $notification;

    public function __construct(array $notification) {
        $this->notification = $notification;
    }

    public function export_for_template(renderer_base $output) {
        return [
            'date' => $this->notification['date'],
            'notificationidx' => $this->notification['notification-idx'],
            'header' => $this->notification['header'],
            'body_message' => $this->notification['body_message'],
            'backurl' => $this->notification['backurl'],
        ];
    }
} 