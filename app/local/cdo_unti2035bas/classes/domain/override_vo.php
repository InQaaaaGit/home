<?php
namespace local_cdo_unti2035bas\domain;

class override_vo {
    /** @readonly */
    public bool $ismanual;
    /** @readonly */
    public ?string $name;
    /** @readonly */
    public ?string $description;

    public function __construct(
        bool $ismanual = false,
        ?string $name = null,
        ?string $description = null
    ) {
        $this->ismanual = $ismanual;
        $this->name = $name;
        $this->description = $description;
    }
}
