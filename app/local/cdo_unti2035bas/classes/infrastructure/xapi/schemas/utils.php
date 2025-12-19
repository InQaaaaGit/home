<?php

namespace local_cdo_unti2035bas\infrastructure\xapi\schemas;


class utils {
    /**
     * @param float $value
     * @return float|int
     */
    public static function format_duration(float $value) {
        $rounded = round($value, 2);
        if (floor($rounded) == $rounded) {
            return (int)$rounded;
        }
        return $rounded;
    }

    public static function validate_activity_type(string $value): void {
        if (!in_array($value, ['article', 'video', 'practice'])) {
            throw new \InvalidArgumentException();
        }
    }

    public static function validate_assessment_level(string $value): void {
        if (!in_array($value, ['final', 'block', 'module', 'theme'])) {
            throw new \InvalidArgumentException();
        }
    }
}
