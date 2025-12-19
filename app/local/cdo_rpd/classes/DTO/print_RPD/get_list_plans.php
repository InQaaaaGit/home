<?php

namespace local_cdo_rpd\DTO\print_RPD;

use tool_cdo_config\request\DTO\base_dto;

class get_list_plans extends base_dto
{
    public string $plan_id;
    public string $form;

    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return "get_list_plans";
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->plan_id = $data->plan_id;
        $this->form = $data->form;
        return $this;
    }
}