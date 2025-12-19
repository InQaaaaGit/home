<?php

namespace local_cdo_debts\output\debts;

use bootstrap_renderer;
use core\notification;
use moodle_exception;
use plugin_renderer_base;

class renderer extends plugin_renderer_base
{
    public string $template = 'local_cdo_debts/main';

    /**
     * @throws moodle_exception
     */
    public function render_renderable(renderable $widget): string
    {
        global $CFG;
        $main = $this->render_from_template($this->template,
            [
                'show_library' => (bool)$CFG->local_cdo_show_library_debts,
                'show_finance' => (bool)$CFG->local_cdo_show_finance_debts,
                'active_1' => $widget->type_render == "1" ? 'active' : '',
                'active_2' => $widget->type_render == "2" ? 'active' : '',
                'active_3' => $widget->type_render == "3" ? 'active' : '',
            ]
        );
        $data = $widget->export_for_template($this);
        if (!empty($data['error_message'])) {
            return $main . bootstrap_renderer::early_notification($data['error_message'], "alert alert-danger mt-2");
        }
        if (!empty($data['data_empty'])) {
            return $main . bootstrap_renderer::early_notification($data['data_empty'], "alert alert-info mt-2");
        }

        return $main . $this->render_from_template($data['template'], $data);

    }
}