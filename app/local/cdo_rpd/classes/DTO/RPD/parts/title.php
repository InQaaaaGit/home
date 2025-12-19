<?php

namespace local_cdo_rpd\DTO\RPD\parts;

use tool_cdo_config\request\DTO\base_dto;

class title extends base_dto
{

    public string $rpd_id;
    public string $discipline;
    public string $validaton_passed;
    public string $council_date;
    public string $council_number;
    public string $council_structer;

    protected function get_object_name(): string
    {
        return '';
    }

    public function build(object $data): base_dto
    {
        $this->rpd_id = $data->rpd_id;
        $this->discipline = $data->discipline;
        $this->validaton_passed = $data->validaton_passed;
        $this->council_date = $data->council_date;
        $this->council_number = $data->council_number;
        $this->council_structer = $data->council_structer;

        return $this;
    }
}