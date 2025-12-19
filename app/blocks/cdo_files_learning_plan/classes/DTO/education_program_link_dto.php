<?php

namespace block_cdo_files_learning_plan\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class education_program_link_dto extends base_dto
{


    public string $doc_id;
    public string $link_name;
    public string $link_URL;
    public string $link_guid;
    public string $discipline_id;
    public string $module_id;
    public string $discipline_index;
    public string $type;

    protected function get_object_name(): string
  {
      return "education_program_link";
  }

  public function build(object $data): base_dto
  {
    $this->doc_id = $data->doc_id;
    $this->link_name = $data->link_name;
    $this->link_URL = $data->link_URL;
    $this->link_guid = $data->link_guid;
    $this->discipline_id = $data->discipline_id;
    $this->module_id = $data->module_id;
    $this->discipline_index = $data->discipline_index;
    $this->type = $data->type;
    return $this;
  }
}
