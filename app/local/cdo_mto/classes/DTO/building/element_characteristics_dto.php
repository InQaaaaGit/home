<?php

namespace local_cdo_mto\DTO\building;

use tool_cdo_config\request\DTO\base_dto;
use tool_cdo_config\request\DTO\response_dto;

class element_characteristics_dto extends base_dto {
    public $building_address;
    public $building_docsanit;
    public $building_docfire;
    public $building_owner;
    public $building_cadastre;
    public $building_usagedoc;
    public $building_usagetype;
    public $building_registry;
    public $building_purpose;
    public $building_4disabled;

    protected function get_object_name(): string
    {
        return "element_characteristics_dto";
    }

    public function build(object $data): self
    {
        $this->building_address = $data->building_address
          ? response_dto::transform(building_characteristics_dto::class, $data->building_address)
          : ['value', 'quantity'];
        $this->building_docsanit = $data->building_docsanit
          ? response_dto::transform(building_characteristics_dto::class, $data->building_docsanit)
          : ['value', 'quantity'];
        $this->building_docfire = $data->building_docfire
          ? response_dto::transform(building_characteristics_dto::class, $data->building_docfire)
          : ['value', 'quantity'];
        $this->building_owner = $data->building_owner
          ? response_dto::transform(building_characteristics_dto::class, $data->building_owner)
          : ['value', 'quantity'];
        $this->building_usagedoc = $data->building_usagedoc
          ? response_dto::transform(building_characteristics_dto::class, $data->building_usagedoc)
          : ['value', 'quantity'];
        $this->building_usagetype = $data->building_usagetype
          ? response_dto::transform(building_characteristics_dto::class, $data->building_usagetype)
          : ['value', 'quantity'];
        $this->building_cadastre = $data->building_cadastre
          ? response_dto::transform(building_characteristics_dto::class, $data->building_cadastre)
          : ['value', 'quantity'];
        $this->building_registry = $data->building_registry
          ? response_dto::transform(building_characteristics_dto::class, $data->building_registry)
          : ['value', 'quantity'];
        $this->building_purpose = $data->building_purpose
          ? response_dto::transform(building_characteristics_dto::class, $data->building_purpose)
          : ['value', 'quantity'];
        $this->building_4disabled = $data->building_4disabled
          ? response_dto::transform(building_characteristics_dto::class, $data->building_4disabled)
          : ['value', 'quantity'];
        return $this;
    }
}
