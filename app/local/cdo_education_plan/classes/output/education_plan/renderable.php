<?php

namespace local_cdo_education_plan\output\education_plan;

use renderer_base;
use Throwable;
use tool_cdo_config\di;

class renderable implements \renderable, \templatable
{
    private string $template = 'local_cdo_education_plan/main';

    /**
     * @return array
     */
    public function get_education_plan(): array
    {
        global $USER;
        try {
            $options = di::get_instance()->get_request_options();
            $options->set_properties(["id" => $USER->id]);
            #$options->set_properties(["id" => 32390]);
            $request = di::get_instance()->get_request('get_academic_plan')->request($options);
            $eduplan = $request->get_request_result()->to_array();

            foreach ($eduplan as &$data) {

                $data['active_gb'] = (int) $data['order'] === 1 ? 'active' : '';
                $data['show_gb'] = (int) $data['order'] === 1 ? 'show' : '';
                $ij = 1;
                foreach ($data['eduplan']['CurriculumEntries'] as &$CurriculumEntry) {
                    if ($ij === 1) {
                        $CurriculumEntry['active'] = 'active';
                        $CurriculumEntry['show'] = 'display: block';
                    }
                    $ij++;

                    $i = 1;
                    foreach ($CurriculumEntry['disciplines'] as &$Curriculum) {
                        if ($i === 1) {
                            $Curriculum['active'] = 'active';
                            $Curriculum['show'] = 'show';
                        }

                        $Curriculum['order'] = $i++;
                    }
                }

            }
            return ['data' => $eduplan];
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
        #var_dump($array); die();
        return $array;
    }
}