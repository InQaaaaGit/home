<?php
namespace local_cdo_unti2035bas\domain\fd_extensions;


abstract class result_value_base {
    const ALLOWED_UNITS = null;
    const ALLOWED_RESULT_SELECTORS = null;

    /**
     * @readonly
     * @var mixed
     */
    public $score;
    /** @readonly */
    public ?string $unit;
    /** @readonly */
    public ?string $bestresultselector;
    /**
     * @readonly
     * @var mixed
     */
    public $min;
    /**
     * @readonly
     * @var mixed
     */
    public $max;

    /**
     * @param mixed $score
     * @param ?string $unit
     * @param ?string $bestresultselector
     * @param mixed $min
     * @param mixed $max
     */
    public function __construct($score, $unit, $bestresultselector, $min, $max) {
        $this->score = $score;
        $this->unit = $unit;
        $this->bestresultselector = $bestresultselector;
        $this->min = $min;
        $this->max = $max;
        if (!is_null(static::ALLOWED_UNITS) && !in_array($this->unit, static::ALLOWED_UNITS)) {
            throw new \InvalidArgumentException();
        }
        if (!is_null(static::ALLOWED_RESULT_SELECTORS) && !in_array($this->bestresultselector, static::ALLOWED_RESULT_SELECTORS)) {
            throw new \InvalidArgumentException();
        }
    }
}
