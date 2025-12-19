<?php

namespace block_cdo_seamless_transition\output\transitions;

use block_cdo_seamless_transition\transitions\factory_transitions;
use renderer_base;
use stdClass;

class renderable implements \renderable, \templatable {

	private string $template = 'block_cdo_seamless_transition/main';
	private bool $is_error = false;
	private string $error_html = "";

	private function get_error_html(string $message): string {
		return \bootstrap_renderer::early_notification($message, "alert alert-danger");
	}

	private function get_items(): array {
		$result = [];

		foreach (factory_transitions::get_instance()->get_transitions() as $item) {
			if (!$item->is_active()) {
				continue;
			}
			$content = new stdClass();
			$content->name = $item->get_transition_name();
			$content->external_data = $item->get_external_data()->to_object();
			$result[] = $content;
		}

		if (!count($result)) {
			$this->is_error = true;
            $this->error_html = $this->get_error_html(get_string(
                'no_active_services', 'block_cdo_seamless_transition',
            ));
		}

		return $result;
	}

	public function export_for_template(renderer_base $output): stdClass {
		$content = new stdClass();
		$content->template = $this->template;
		$content->items = $this->get_items();
		$content->is_error = $this->is_error;
		$content->error_html = $this->error_html;
		return $content;
	}
}
