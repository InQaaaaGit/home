<?php

namespace local_cdo_debts\output\vuetify;

defined('MOODLE_INTERNAL') || die();

use local_cdo_debts\output\vuetify\renderable as widget;
use moodle_exception;
use plugin_renderer_base;

class renderer extends plugin_renderer_base {
    /**
     * @throws moodle_exception
     */
    public function render_renderable(widget $widget) {
		// TODO получаем данные из renderable
		$context = $widget->export_for_template($this);
		// TODO говори через какой шаблон отрисовать
		return $this->render_from_template('local_cdo_debts/mini_app', $context);
	}
}

