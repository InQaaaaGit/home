<?php

namespace block_cdo_files_learning_plan\DTO;

use tool_cdo_config\request\DTO\base_dto;

final class education_program_dto extends base_dto
{
  public int $user_id;
  public int $doc_number;
  public string $mode;

  protected function get_object_name(): string
  {
      return "education_program";
  }

  public function build(object $data): base_dto
  {
    $this->user_id = $data->user_id ?? 0;
    $this->doc_number = $data->doc_number ?? 0;
    $this->mode = $data->mode ?? '';
    return $this;
  }
}
