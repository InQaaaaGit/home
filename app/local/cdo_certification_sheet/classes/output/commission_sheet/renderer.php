<?php

namespace local_cdo_certification_sheet\output\commission_sheet;

defined('MOODLE_INTERNAL') || die();

use local_cdo_certification_sheet\output\commission_sheet\renderable as widget;
use tool_cdo_config\exceptions\cdo_config_exception;

class renderer extends \plugin_renderer_base {
	/**
	 * @param renderable $widget
	 * @return bool|string
	 * @throws cdo_config_exception
	 */
	public function render_renderable(widget $widget) {
		try {
			return $this->render_from_template(
				'local_cdo_certification_sheet/components/commission-sheet',
				$widget->export_for_template($this)
			);
		} catch (\moodle_exception $e) {
			throw new cdo_config_exception(2005, $e->getMessage());
		}
	}
}
