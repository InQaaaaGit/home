<?php
namespace local_cdo_unti2035bas\domain;

use local_cdo_unti2035bas\domain\fd_extensions\result_value_base;


class factdef_result_extension_vo {
    /** @readonly */
    public string $name;
    /** @readonly */
    public string $schemaref;
    /** @readonly */
    public ?result_value_base $value;

    public function __construct(
        string $name,
        string $schemaref,
        ?result_value_base $value
    ) {
        $this->name = $name;
        $this->schemaref=$schemaref;
        $this->value = $value;
    }
}
