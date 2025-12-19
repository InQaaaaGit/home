<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;



class object_schema {
    /** @readonly */
    public string $id;
    /** @readonly */
    public string $objecttype;
    /** @readonly */
    public ?object_definition_schema $definition;

    public function __construct(
        string $id,
        string $objecttype,
        ?object_definition_schema $definition
    ) {
        $this->id = $id;
        $this->objecttype = $objecttype;
        $this->definition = $definition;
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        return array_filter([
            'id' => $this->id,
            'objectType' => $this->objecttype,
            'definition' => $this->definition ? $this->definition->dump() : null,
        ]);
    }
}
