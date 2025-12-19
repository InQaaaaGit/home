<?php

namespace block_cdo_professional_info\output\professional_info;

use renderer_base;
use templatable;
use Throwable;
use tool_cdo_config\di;

class renderable implements \renderable, templatable
{
    private string $template = 'block_cdo_professional_info/main';

    public function get_professional_info(): array
    {
        global $USER;
        $options = di::get_instance()->get_request_options();
        $options->set_properties(["id" => $USER->id]);

        try {
			return di::get_instance()
				->get_request('get_professional_info')
				->request($options)
				->get_request_result()
				->to_array();
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }


    }

    public function export_for_template(renderer_base $output): array
    {
        $array = $this->get_professional_info();
        $array['template'] = $this->template;
        return $array;
    }
}