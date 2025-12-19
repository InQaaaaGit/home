<?php

namespace local_cdo_vkr\output\vuetify;

defined('MOODLE_INTERNAL') || die();

use local_cdo_vkr\output\vuetify\renderable as widget;

class renderer extends \plugin_renderer_base {
	public function render_renderable(widget $widget) {
		// TODO получаем данные из renderable
		$context = $widget->export_for_template($this);
		// TODO говори через какой шаблон отрисовать
		return $this->render_from_template('local_cdo_vkr/mini_app', $context);
	}
}

