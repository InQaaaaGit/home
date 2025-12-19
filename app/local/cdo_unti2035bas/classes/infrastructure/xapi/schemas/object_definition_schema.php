<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;

use DateTime;


class object_definition_schema {
    /** @readonly */
    public ?string $name;
    /** @readonly */
    public ?string $description;
    /** @readonly */
    public ?string $type;
    /**
     * @readonly
     * @var array<string, mixed>
     * */
    public array $extensions;

    /**
     * @param array<string, mixed> $extensions
     */
    public function __construct(
        ?string $name,
        ?string $description,
        ?string $type,
        array $extensions
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->extensions = $extensions;
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        $extensions = [];
        foreach ($this->extensions as $key => $value) {
            if (is_null($value)) {
                continue;
            }
            if ($value instanceof DateTime) {
                $value = $value->format(DateTime::ATOM);
            }
            $extensions[$key] = $value;
        }
        return array_filter(
            [
                'name' => is_null($this->name) ? null : ['ru-RU' => $this->name],
                'description' => is_null($this->description) ? null : ['ru-RU' => $this->description],
                'type' => $this->type,
                'extensions' => $extensions,
            ],
            fn($v) => !is_null($v) && [] != $v,
        );
    }
}
