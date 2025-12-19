<?php

use block_cdo_seamless_transition\output\transitions\renderable;

class block_cdo_seamless_transition extends block_base {
	/**
	 * @return void
	 * @throws coding_exception
	 */
	public function init(): void {
		$this->title = get_string('pluginname', 'block_cdo_seamless_transition');
	}

	/**
	 * @return stdClass
	 * @throws coding_exception
	 */
	public function get_content(): stdClass {
		global $PAGE;
		if ($this->content !== null) {
			return $this->content;
		}
		$PAGE->requires->js_call_amd('block_cdo_seamless_transition/transition', 'init');
		$render = $PAGE->get_renderer('block_cdo_seamless_transition', 'transitions');
		$this->content = new stdClass;
		$this->content->text = $render->render(new renderable());

		return $this->content;
	}
}