<?php

namespace local_cdo_mto\DTO\room;

use tool_cdo_config\request\DTO\base_dto;

class create_dto extends base_dto {


  protected function get_object_name(): string
  {
    return "create_dto";
  }

  public function build(object $data): self
  {
    $this->error = $data->error;
    return $this;
  }
}
