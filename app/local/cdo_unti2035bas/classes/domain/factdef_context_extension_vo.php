<?php
namespace local_cdo_unti2035bas\domain;


class factdef_context_extension_vo {
    /** @readonly */
    public string $name;
    /**
        @readonly
        @var mixed $value
     */
    public $value;

    /**
     * @param mixed $value
     */
    public function __construct(
        string $name,
        $value,
    ) {
        $this->name = $name;
        $this->value = $value;
    }
}
