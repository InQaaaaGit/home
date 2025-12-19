<?php

namespace local_cdo_order_documents\services;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use Throwable;
use tool_cdo_config\di;

class main_service extends external_api
{
    public static function send_document_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'document_type_id' => new external_value(PARAM_TEXT, 'код справки из справочника ВидыСправок', true),
                'person_code' => new external_value(PARAM_TEXT, 'Код физического лица из справочника ФизическиеЛица', true),
                'fields' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'name' => new external_value(PARAM_TEXT, 'наименование поля', true),
                            'value' => new external_value(PARAM_TEXT, 'значение поля', true)
                        ]
                    )
                )
            ]
        );
    }

    public static function send_document(
        string $person_code,
        string $document_type_id,
        array $fields,
        string $record_book = ''
    ): array
    {
        $options = di::get_instance()->get_request_options();
        $options->set_parameters_in_json();
        $options->set_properties([
            'fields' => $fields,
            'person_code' => $person_code,
         #   'user_id' => $person_code,
            'user_id' => 5,
            'document_type_id' => $document_type_id,
            'record_book' => $record_book
        ]);

        try {
            return di::get_instance()
                ->get_request('send_document')
                ->request($options)
                ->get_request_result()
                ->to_array();

        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    public static function send_document_returns()
    {
        return null;

    }

}
