<?php
namespace local_cdo_unti2035bas\domain;


class module_unti_vo {
    /** @readonly */
    public ?int $moduleid;

    public function __construct(?int $moduleid) {
        $this->moduleid = $moduleid;
    }
}
