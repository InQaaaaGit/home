<?php

namespace local_cdo_mto\DTO\edu_program;

use tool_cdo_config\request\DTO\base_dto;

class structures_info_dto extends base_dto {

  public string $EducationalProgram;
  public string $Specialty;

  public string $Profile;
  public string $YearSet;
  public string $Realize;
  public string $Guid_Specialty;
  public string $Guid_Profile;

    protected function get_object_name(): string
    {
        return "structures_info_dto";
    }

  /**
   * @param object $data
   * @return $this
   */
    public function build(object $data): self
    {
      $this->EducationalProgram = $data->EducationalProgram ?? '';
      $this->Specialty          = $data->Specialty ?? '';
      $this->Profile            = $data->Profile ?? '';
      $this->YearSet            = $data->YearSet ?? '';
      $this->Realize            = $data->Realize ?? '';
      $this->Guid_Specialty     = $data->Guid_Specialty ?? '';
      $this->Guid_Profile       = $data->Guid_Profile ?? '';

      return $this;
    }
}
