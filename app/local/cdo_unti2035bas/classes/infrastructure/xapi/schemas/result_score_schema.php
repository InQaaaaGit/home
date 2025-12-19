<?php
namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;


class result_score_schema {
    /** @readonly */
    public int $raw;
    /** @readonly */
    public int $min;
    /** @readonly */
    public int $max;
    /** @readonly */
    public float $scaled;

    public function __construct(
        int $raw,
        int $min,
        int $max,
        float $scaled
    ) {
        $this->raw = $raw;
        $this->min = $min;
        $this->max = $max;
        $this->scaled = $scaled;
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(): array {
        return [
            'raw' => $this->raw,
            'min' => $this->min,
            'max' => $this->max,
            'scaled' => $this->scaled,
        ];
    }
}
