<?php

namespace block_cdo_files_learning_plan\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class file_binary_dto extends base_dto
{
  public array $urlParams;

  protected function get_object_name(): string
  {
      return "file_binary";
  }

  public function build(object $data): base_dto
  {
    $this->urlParams = $data->urlParams ?? [];
    return $this;
  }
}
