<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;


class result_schema2 {
    /** @readonly */
    public ?result_score_schema $score;
    /** @readonly */
    public ?bool $success;
    /** @readonly */
    public ?string $duration;
    /**
     * @readonly
     * @var array<string, mixed>
     */
    public array $extensions;

    /**
     * @param array<string, mixed> $extensions
     */
    public function __construct(
        ?result_score_schema $score,
        ?bool $success,
        ?string $duration,
        array $extensions
    ) {
        $this->score = $score;
        $this->success = $success;
        $this->duration = $duration;
        $this->extensions = $extensions;
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        return array_filter(
            [
                'score' => !is_null($this->score) ? $this->score->dump() : null,
                'success' => $this->success,
                'duration' => $this->duration,
                'extensions' => $this->extensions,
            ],
            fn($v) => !is_null($v) && [] != $v,
        );
    }
}
