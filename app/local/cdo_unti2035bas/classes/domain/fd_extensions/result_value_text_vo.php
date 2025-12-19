<?php
namespace local_cdo_unti2035bas\domain\fd_extensions;


class result_value_text_vo extends result_value_base {
    const ALLOWED_UNITS = ['текст'];
    const ALLOWED_RESULT_SELECTORS = ['none'];

    /**
     * @param null $min
     * @param null $max
     */
    public function __construct(
        string $score,
        string $unit,
        string $bestresultselector,
        $min,
        $max
    ) {
        parent::__construct($score, $unit, $bestresultselector, $min, $max);
        if (!is_null($this->min) || !is_null($this->max)) {
            throw new \InvalidArgumentException();
        }
    }
}
