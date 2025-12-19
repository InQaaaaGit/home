<?php

namespace local_cdo_mto\DTO\building;

use tool_cdo_config\request\DTO\base_dto;

class patch_dto extends base_dto {


  protected function get_object_name(): string
  {
    return "patch_dto";
  }

  public function build(object $data): self
  {
    $this->error = $data->error;
    return $this;
  }
}
