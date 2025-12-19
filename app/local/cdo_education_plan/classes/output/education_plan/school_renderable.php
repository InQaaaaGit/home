<?php

namespace local_cdo_education_plan\output\education_plan;

use renderer_base;
use Throwable;
use tool_cdo_config\di;
use tool_cdo_config\tools\dumper;
use tool_cdo_config\tools\helper;
use tool_cdo_config\tools\tool;

class school_renderable implements \renderable, \templatable
{
    private string $template = 'local_cdo_education_plan/school';

    /**
     * @return array
     */
    public function get_education_plan(): array
    {
        global $USER;
        $options = di::get_instance()->get_request_options();
        $options->set_properties(["user_id" => $USER->id]);
        $options->set_properties(["user_id" => 23]);


        try {
            $request = di::get_instance()->get_request('get_academic_plan')->request($options);

            $data['data'] = $request->get_request_result()->to_array();

            return $data;
        } catch (Throwable $e) {

            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    public function export_for_template(renderer_base $output): array
    {
        $array = $this->get_education_plan();
        $array['template'] = $this->template;
        return $array;
    }
}