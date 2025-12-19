<?php

namespace block_cdo_student_info\output\health;

use coding_exception;
use curl;
use dml_exception;
use moodle_exception;
use renderer_base;
use Throwable;
use tool_cdo_config\di;

class renderable implements \renderable, \templatable
{
    private string $template = 'block_cdo_student_info/health';

    /**
     * @throws dml_exception
     */
    public function get_list_files_for_health_info(): array
    {
        global $USER, $CFG;

        $options = di::get_instance()->get_request_options();
        #$options->set_properties(["user_id" => $USER->id]);
        //TODO
        $options->set_properties(["mask" => $CFG->block_cdo_student_info_find_mask, "user_id" => 4]);

        try {
            $request = di::get_instance()->get_request('get_list_files_for_health_info')->request($options);
            $data['data'] = $request->get_request_result()->to_array();
            $data['haveFiles'] = count($data['data']) > 0;
            return $data;
        } catch (Throwable $e) {

            return [
                'error_message' => $e->getMessage() . $e->getTraceAsString()
            ];
        }
    }

    /**
     * @throws moodle_exception
     * @throws coding_exception
     */
    public function get_file_to_print($file_id)
    {
        $options = di::get_instance()->get_request_options();
        $options->set_properties(["file_id" => $file_id]);
        try {
            $request = di::get_instance()->get_request('GetFileBinary')->request($options);
            $data = $request->get_request_result(true);
            return $data;
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage() . $e->getTraceAsString()
            ];
        }
        /*$curl = new curl();

        $auth = base64_encode("AdminHS:C4xQaK");
        $curl->setopt(['CURLOPT_USERPWD' => $auth]);
        $curl->setHeader([
            "Authorization: Basic {$auth}"
        ]);
        $body = $curl->get(
            'http://10.99.99.205/UniDB/hs/cdo_eois_Campus/GetFileBinary',
            ['file_id' => $file_id]
        );
        if (isset($curl) && array_key_exists('http_code', (array)$curl->get_info())) {
            $http_code = (int)$curl->get_info()['http_code'];
            if ($http_code !== 200) {
                throw new moodle_exception($http_code);
            }
        }
        return $body;*/
    }

    public function export_for_template(renderer_base $output): array
    {
        $array = $this->get_list_files_for_health_info();
        $array['template'] = $this->template;
        return $array;
    }
}