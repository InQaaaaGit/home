<?php
namespace local_cdo_unti2035bas\domain;


class theme_unti_vo {
    /** @readonly */
    public ?int $themeid;

    public function __construct(?int $themeid) {
        $this->themeid = $themeid;
    }
}
