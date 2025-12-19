<?php

namespace local_cdo_education_plan\output\education_plan;

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
    public function render_school(school_renderable $widget)
    {
        $data = $widget->export_for_template($this);
        if (isset($data['error_message'])) {
            return bootstrap_renderer::early_notification($data['error_message'], "alert alert-danger");
        }
        return $this->render_from_template($data['template'], $data);

    }
}