<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;


class context_activities_schema {
    /**
     * @var array<object_schema>
     * @readonly
     */
    public array $parent;

    /**
     * @param array<object_schema> $parent
     */
    public function __construct(
        array $parent
    ) {
        $this->parent = $parent;
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        return array_filter([
            'parent' => array_map(fn($o) => $o->dump(), $this->parent),
        ]);
    }
}
