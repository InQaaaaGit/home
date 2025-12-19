<?php
namespace local_cdo_unti2035bas\domain\fd_extensions;


class result_value_count_vo extends result_value_base {
    const ALLOWED_UNITS = ['число'];
    const ALLOWED_RESULT_SELECTORS = ['min', 'max'];

    public function __construct(
        int $score,
        string $unit,
        string $bestresultselector,
        int $min,
        int $max
    ) {
        parent::__construct($score, $unit, $bestresultselector, $min, $max);
    }
}
