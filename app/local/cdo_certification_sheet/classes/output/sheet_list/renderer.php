<?php

namespace local_cdo_certification_sheet\output\sheet_list;

defined('MOODLE_INTERNAL') || die();

use bootstrap_renderer;
use local_cdo_certification_sheet\output\sheet_list\renderable as widget;
use moodle_exception;

class renderer extends \plugin_renderer_base {
	/**
	 * @param renderable $widget
	 * @return bool|string
	 * @throws moodle_exception
	 */
	public function render_renderable(widget $widget) {
		$_widget = $widget->export_for_template($this);

		if ($_widget['error']){
			return bootstrap_renderer::early_notification($_widget['error_message'], 'alert alert-danger');
		}
		return $this->render_from_template('local_cdo_certification_sheet/sheet_list', $_widget);
	}
}
