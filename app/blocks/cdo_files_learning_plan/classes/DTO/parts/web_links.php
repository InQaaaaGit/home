<?php

namespace block_cdo_files_learning_plan\DTO\parts;

use tool_cdo_config\request\DTO\base_dto;

class web_links extends base_dto
{
    public string $discipline_id;
    public string $discipline_index;
    public string $link_URL;
    public string $link_guid;
    public string $link_name;
    public string $module_id;
    public string $type;
    /**
     * @inheritDoc
     */
    protected function get_object_name(): string
    {
        return 'web_links';
    }

    /**
     * @inheritDoc
     */
    public function build(object $data): base_dto
    {
        $this->discipline_id = $data->discipline_id;
        $this->discipline_index = $data->discipline_index;
        $this->link_URL = $data->link_URL;
        $this->link_guid = $data->link_guid;
        $this->link_name = $data->link_name;
        $this->module_id = $data->module_id;
        $this->type = $data->type;
        return $this;
    }
}