<?php

namespace block_cdo_files_learning_plan\DTO;

use block_cdo_files_learning_plan\DTO\parts\academic_plans;
use block_cdo_files_learning_plan\DTO\parts\discipline_files;
use block_cdo_files_learning_plan\DTO\parts\files;
use block_cdo_files_learning_plan\DTO\parts\info;
use block_cdo_files_learning_plan\DTO\parts\structures;
use block_cdo_files_learning_plan\DTO\parts\web_links;
use coding_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

final class education_program_all_file_dto extends base_dto
{
    public response_dto|array $academic_plans;
    public response_dto|array $discipline_files;
    public response_dto|array $info;
    public response_dto|array $structures;
    public response_dto|array $files;
    public response_dto|array $web_links;

    protected function get_object_name(): string
  {
      return "education_program_all_file";
  }

    /**
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function build(object $data): base_dto
  {
    $this->academic_plans = $data->academic_plans
        ? response_dto::transform(academic_plans::class, $data->academic_plans)
        : [];
    $this->discipline_files = $data->discipline_files
        ? response_dto::transform(discipline_files::class, $data->discipline_files)
        : [];
    $this->files = $data->files
        ? response_dto::transform(files::class, $data->files)
        : [];
    $this->info = $data->info
        ? response_dto::transform(info::class, $data->info) : [];
    $this->structures = $data->structures
        ? response_dto::transform(structures::class, $data->structures)
        : [];
    $this->web_links = $data->web_links
        ? response_dto::transform(web_links::class, $data->web_links)
        : [];
    return $this;
  }
}
