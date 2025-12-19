<?php

namespace block_cdo_student_info\output\diplomas;

use renderer_base;
use Throwable;
use tool_cdo_config\di;

class renderable implements \renderable, \templatable
{
    private string $template = 'block_cdo_student_info/diplomas';

    public function get_diplomas(): array
    {
        global $USER;

        $options = di::get_instance()->get_request_options();
        $options->set_properties(["student_id" => $USER->id]);
        $options->set_properties(["student_id" => 11734]);

        try {
            $request = di::get_instance()->get_request('get_diplomas')->request($options);
            $data = $request->get_request_result()->to_array();
            $data['gradebooks'][0]['active'] = 'active';
            $data['gradebooks'][0]['show'] = 'show';
            return $data;
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    public function export_for_template(renderer_base $output): array
    {
        $array = $this->get_diplomas();
        $array['template'] = $this->template;
        return $array;
    }
}