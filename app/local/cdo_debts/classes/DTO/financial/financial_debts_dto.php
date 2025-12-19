<?php

namespace local_cdo_debts\DTO\financial;

use local_cdo_debts\DTO\library_debts_data_dto;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class financial_debts_dto extends base_dto
{

    public $Dogovor;
    public $Sum;
    public $contract_number;
    public $contract_date;
    public $is_hostel;

    protected function get_object_name(): string
    {
        return 'financial_debts';
    }

    public function build(object $data): base_dto
    {
        $this->Dogovor = $data->Dogovor;
        $this->Sum = $data->Sum;
        $this->contract_number = $data->contract_number;
        $this->contract_date = $data->contract_date;
        $this->is_hostel = $data->is_hostel;
        return $this;
    }
}
