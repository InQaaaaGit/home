<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;


class context_schema {
    /** @readonly */
    public ?string $registration;
    /** @readonly */
    public ?context_activities_schema $contextactivities;
    /**
     * @readonly
     * @var array<string, mixed>
     * */
    public array $extensions;
    /** @readonly */
    public ?agent_schema $instructor;

    /**
     * @param array<string, mixed> $extensions
     */
    public function __construct(
        ?context_activities_schema $contextactivities = null,
        array $extensions = [],
        ?string $registration = null,
        ?agent_schema $instructor = null,
    ) {
        $this->contextactivities = $contextactivities;
        $this->extensions = $extensions;
        $this->registration = $registration;
        $this->instructor = $instructor;
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        return array_filter([
            'registration' => $this->registration,
            'contextActivities' => $this->contextactivities ? $this->contextactivities->dump() : null,
            'extensions' => array_filter($this->extensions, fn($v) => !is_null($v)),
            'instructor' => $this->instructor ? $this->instructor->dump() : null,
        ]);
    }
}
