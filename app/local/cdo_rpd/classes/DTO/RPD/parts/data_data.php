<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class data_data extends base_dto
{

    public response_dto|array $credit_question;
    public response_dto|array $tests;

    protected function get_object_name(): string
    {
        return 'dd';
    }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
    {
        $this->credit_question = $data->credit_question ?
            response_dto::transform(
                fos::class,
                $data->credit_question
            ) : [];
        $this->tests = $data->tests ?
            response_dto::transform(
                fos::class,
                $data->tests
            ) : [];
        return $this;
    }
}