<?php
namespace local_cdo_unti2035bas\domain\fd_extensions;


class result_value_score_vo extends result_value_base {
    const ALLOWED_UNITS = ['проценты'];
    const ALLOWED_RESULT_SELECTORS = ['min', 'max'];

    public function __construct(
        int $score,
        string $unit,
        string $bestresultselector,
        int $min,
        int $max
    ) {
        parent::__construct($score, $unit, $bestresultselector, $min, $max);
        if (array_filter(fn($v) => $v < 0 || $v > 100, [$this->score, $this->min, $this->max])) {
            throw new InvalidArgumentException('value out of 0..100');
        }
        if ($this->score < $this->min || $this->score > $this->max) {
            throw new \InvalidArgumentException('score out of min-max');
        }
    }
}
