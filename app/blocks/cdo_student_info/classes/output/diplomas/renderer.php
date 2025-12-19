<?php

namespace block_cdo_student_info\output\diplomas;

use block_cdo_student_info\output\student_info\orders\renderable as renderable_orders;

use bootstrap_renderer;
use moodle_exception;
use plugin_renderer_base;

class renderer extends plugin_renderer_base
{
	/**
	 * @param renderable $widget
	 * @return bool|string
	 * @throws moodle_exception
	 */
    public function render_renderable(renderable $widget)
    {
        $data = $widget->export_for_template($this);
        if (isset($data['error_message'])) {
            return bootstrap_renderer::early_notification($data['error_message'], "alert alert-danger");
        }
        return $this->render_from_template($data['template'], $data);

    }
}