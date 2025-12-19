<?php
namespace local_cdo_unti2035bas\domain\fd_extensions;

class result_value_weight_vo extends result_value_base {
    const ALLOWED_UNITS = ['мкг', 'мг', 'г', 'кг'];
    const ALLOWED_RESULT_SELECTORS = ['min', 'max'];

    public function __construct(
        float $score,
        string $unit,
        string $bestresultselector,
        float $min,
        float $max
    ) {
        parent::__construct($score, $unit, $bestresultselector, $min, $max);
    }
}
