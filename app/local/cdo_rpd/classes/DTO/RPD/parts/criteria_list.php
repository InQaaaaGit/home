<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use local_cdo_rpd\DTO\RPD\parts\fos\first_template;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class criteria_list extends base_dto
{

    public string|response_dto $credit_question;
    public string|response_dto $exam_question;
    public string|response_dto $lab_work;
    public string|response_dto $credit_assign;
    public string|response_dto $exam_assign;
    public string|response_dto $tests;
    public string|response_dto $referat;
    public string|response_dto $esse;
    public string|response_dto $questions;
    public string|response_dto $doklad;
    public string|response_dto $domzad;

    protected function get_object_name(): string
    {
        return 'criteria_list';
    }

    public function build(object $data): base_dto
    {
        $this->credit_question = $data->credit_question ?? '';
        /*$this->credit_question = $data->credit_question ?
        response_dto::transform(
            first_template::class,
            $data->credit_question
        ) : '';*/
        $this->exam_question = $data->credit_question ?? '';
        $this->lab_work = $data->lab_work ?? '';
        $this->credit_assign = $data->credit_assign ?? '';
        $this->exam_assign = $data->exam_assign ?? '';
        $this->tests = $data->tests ?? '';
        $this->referat = $data->referat ?? '';
        $this->esse = $data->esse ?? '';
        $this->questions = $data->questions ?? '';
        $this->doklad = $data->doklad ?? '';
        $this->domzad = $data->domzad ?? '';

        return $this;
    }
}