<?php
namespace block_cdo_notification\output\notifications;

use renderable;
use templatable;
use renderer_base;

class list_renderable implements renderable, templatable {
    private array $notifications;
    private int $totalcount;
    private int $page;
    private int $perpage;
    private string $search;
    private string $sort;
    private string $order;
    private string $baseurl;

    public function __construct(array $notifications, int $totalcount, int $page, int $perpage, string $search, string $sort, string $order, string $baseurl) {
        $this->notifications = $notifications;
        $this->totalcount = $totalcount;
        $this->page = $page;
        $this->perpage = $perpage;
        $this->search = $search;
        $this->sort = $sort;
        $this->order = $order;
        $this->baseurl = $baseurl;
    }

    public function export_for_template(renderer_base $output): array {
        return [
            'notifications' => $this->notifications,
            'totalcount' => $this->totalcount,
            'page' => $this->page,
            'perpage' => $this->perpage,
            'search' => $this->search,
            'sort' => $this->sort,
            'order' => $this->order,
            'baseurl' => $this->baseurl,
        ];
    }
} 