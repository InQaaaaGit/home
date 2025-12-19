<?php
namespace block_cdo_notification\output\notifications;

use renderable;
use templatable;
use renderer_base;

class main_renderable implements renderable, templatable {
    private array $notifications;
    private int $totalcount;
    private bool $showmore;

    public function __construct(array $notifications, int $totalcount) {
        $this->notifications = $notifications;
        $this->totalcount = $totalcount;
        $this->showmore = $totalcount > 1;
    }

    public function export_for_template(renderer_base $output): array {
        return [
            'notifications' => $this->notifications,
            'totalcount' => $this->totalcount,
            'showmore' => $this->showmore,
        ];
    }
} 