<?php

namespace block_cdo_seamless_transition\output\transitions;

use moodle_exception;
use plugin_renderer_base;

class renderer extends plugin_renderer_base {
	/**
	 * @param renderable $widget
	 * @return bool|string
	 * @throws moodle_exception
	 */
	public function render_renderable(renderable $widget) {
		$data = $widget->export_for_template($this);
		return $this->render_from_template($data->template, $data);

	}
}