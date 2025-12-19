<?php

namespace local_cdo_mto\DTO;

use tool_cdo_config\request\DTO\base_dto;

class element_characteristics_dto extends base_dto {
    public ?string $building_address;
    public ?string $building_docsanit;
    public ?string $building_docfire;
    public ?string $building_owner;
    public ?string $building_cadastre;
    public ?string $building_usagedoc;
    public ?string $building_usagetype;
    public ?string $building_registry;
    public ?string $building_purpose;
    public ?string $building_4disabled;

    protected function get_object_name(): string
    {
        return "element_characteristics_dto";
    }

    public function build(object $data): base_dto
    {
        $this->building_address     = $data->building_address;
        $this->building_docsanit    = $data->building_docsanit;
        $this->building_docfire     = $data->building_docfire;
        $this->building_owner       = $data->building_owner;
        $this->building_cadastre    = $data->building_cadastre;
        $this->building_usagedoc    = $data->building_usagedoc;
        $this->building_usagetype   = $data->building_usagetype;
        $this->building_registry    = $data->building_registry;
        $this->building_purpose     = $data->building_purpose;
        $this->building_4disabled   = $data->building_4disabled;
        return $this;
    }
}