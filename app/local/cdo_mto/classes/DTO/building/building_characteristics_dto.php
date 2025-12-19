<?php

namespace local_cdo_mto\DTO\building;

use tool_cdo_config\request\DTO\base_dto;

class building_characteristics_dto  extends base_dto
{
  public string $value;
  public string $quantity;

  protected function get_object_name(): string
  {
    return "building_characteristics_dto";
  }
  public function build(object $data): self
  {
    $this->value = $data->value ?? '';
    $this->quantity = $data->quantity ?? '';

    return $this;
  }
}
