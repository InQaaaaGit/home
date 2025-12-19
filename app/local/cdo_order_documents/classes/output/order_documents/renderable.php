<?php

namespace local_cdo_order_documents\output\order_documents;

use coding_exception;
use context_system;
use dml_exception;
use renderer_base;
use Throwable;
use tool_cdo_config\di;

class renderable implements \renderable, \templatable
{
    private string $template = 'local_cdo_order_documents/main';

    public function get_current_documents()
    {

    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_document_structure(string $document_type_id): array
    {
        global $USER;
        $options = di::get_instance()->get_request_options();
        $type = '';
        if (has_capability('local/cdo_order_documents:student_view', context_system::instance())) {
            $type = 'student';
        }
        if (has_capability('local/cdo_order_documents:aspirant_view', context_system::instance())) {
            $type = 'aspirant';
        }
        $options->set_properties(
            [
                'document_type_id' => $document_type_id,
                'type' => $type,
                'user_id' => $USER->id
               # 'user_id' => 34143
            ]
        );
        try {
            return di::get_instance()
                ->get_request('get_document_structure')
                ->request($options)
                ->get_request_result()
                ->to_array();

        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    public function get_types_references(): array
    {
        global $USER;
        $options = di::get_instance()->get_request_options();

        //TODO change on production $USER->id,
        $options->set_properties(['user_id' => $USER->id]);
        #$options->set_properties(['user_id' => 34143, 'record_book' => '']);

        try {
            return di::get_instance()
                ->get_request('get_types_references')
                ->request($options)
                ->get_request_result()
                ->to_array();

        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    public function get_certificates(): array
    {
        global $USER;

        $options = di::get_instance()->get_request_options();
        $type = '';
        if (has_capability('block/cdo_schedule:viewstudentschedule', context_system::instance())) {
            $type = 'student';
        }

        $options->set_properties(['user_id' => $USER->id, 'profile_type' => $type]);
       # $options->set_properties(['user_id' => 34143, 'profile_type' => $type]);

        try {
            return di::get_instance()
                ->get_request('get_certificates')
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
        /*$array = $this->get_academic_progress();*/
        $array['template'] = $this->template;
        return $array;
    }
}
