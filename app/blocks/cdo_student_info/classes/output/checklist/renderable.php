<?php

namespace block_cdo_student_info\output\checklist;

use renderer_base;
use Throwable;
use tool_cdo_config\di;

class renderable implements \renderable, \templatable
{
    protected string $template = 'block_cdo_student_info/checklist';

    public function get_checklist(): array
    {
        global $USER;

        $options = di::get_instance()->get_request_options();
        $options->set_properties(["user_id" => $USER->id]);
        #$options->set_properties(["user_id" => 35971]);

        try {
            $request = di::get_instance()->get_request('get_checklist')->request($options);
            $data['checklist'] = $request->get_request_result()->to_array();

            return $data;
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    public function export_for_template(renderer_base $output): array
    {
        $array = $this->get_checklist();
        $array['template'] = $this->template;
        return $array;
    }
}