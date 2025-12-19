<?php
namespace local_cdo_unti2035bas\domain\fd_extensions;

use local_cdo_unti2035bas\domain\validator;


class result_value_duration_vo extends result_value_base {
    const ALLOWED_UNITS = null;
    const ALLOWED_RESULT_SELECTORS = ['min', 'max'];

    /**
     * @param null $unit
     */
    public function __construct(
        string $score,
        $unit,
        string $bestresultselector,
        string $min,
        string $max
    ) {
        parent::__construct($score, $unit, $bestresultselector, $min, $max);
        /** @var string $value */
        foreach ([$this->score, $this->min, $this->max] as $value) {
            if (!validator::validate_duration($value)) {
                throw new \InvalidArgumentException("Wrong duration value: {$value}");
            }
        }
    }
}
