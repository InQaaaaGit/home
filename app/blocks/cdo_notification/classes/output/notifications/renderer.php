<?php

namespace block_cdo_notification\output\notifications;

use coding_exception;
use core\output\bootstrap_renderer;
use core\output\plugin_renderer_base;

class renderer extends plugin_renderer_base
{

    /**
     * @throws \tool_cdo_config\exceptions\cdo_type_response_exception
     * @throws \core\exception\moodle_exception
     * @throws coding_exception
     * @throws \tool_cdo_config\exceptions\cdo_config_exception
     */
    public function render_renderable(renderable $widget): bool|string
    {
        $data = $widget->export_for_template($this);

        if (isset($data['error_message'])) {
            return bootstrap_renderer::early_notification($data['error_message'], "alert alert-danger");
        }
        return $this->render_from_template($data['template'], $data);

    }

    public function render_list_renderable(list_renderable $renderable): string {
        return $this->render_from_template('block_cdo_notification/notifications_list', $renderable->export_for_template($this));
    }
}