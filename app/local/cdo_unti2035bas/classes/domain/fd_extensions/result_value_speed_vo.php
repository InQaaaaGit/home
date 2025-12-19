<?php
namespace local_cdo_unti2035bas\domain\fd_extensions;

class result_value_speed_vo extends result_value_number_vo {
    const ALLOWED_UNITS = ['км/ч', 'м/с'];

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
