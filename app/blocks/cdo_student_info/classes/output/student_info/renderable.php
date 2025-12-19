<?php

namespace block_cdo_student_info\output\student_info;

use renderer_base;
use Throwable;
use tool_cdo_config\di;

class renderable implements \renderable, \templatable
{
    private string $template = 'block_cdo_student_info/main';

    public function get_student_info(): array
    {
        global $USER;

        $options = di::get_instance()->get_request_options();
        #$options->set_properties(["id" => 39365]);
        $options->set_properties(["id" => $USER->id]);
        #$options->set_properties(["id" => 3]);
        try {
            $request = di::get_instance()->get_request('get_student_info')->request($options);
            $data = $request->get_request_result()->to_array();

            $data['student_record_books'][0]['active'] = 'active';
            $data['student_record_books'][0]['show'] = 'show';

            return $data;
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    public function export_for_template(renderer_base $output): array
    {
        $array = $this->get_student_info();
        $array['template'] = $this->template;
        return $array;
    }
}