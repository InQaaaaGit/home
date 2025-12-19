<?php
namespace local_cdo_unti2035bas\infrastructure;

use DateTime;


class timedate_service {
    public function now(): int {
        return time();
    }

    public static function iso8061_duration(int $seconds): string {
        $interval = (new DateTime('@0'))->diff(new DateTime("@{$seconds}"));
        $datepart = '';
        if ($interval->y !== 0) {
            $datepart .= $interval->y . 'Y';
        }
        if ($interval->m !== 0) {
            $datepart .= $interval->m . 'M';
        }
        if ($interval->d !== 0) {
            $datepart .= $interval->d . 'D';
        }
        $timepart = '';
        if ($interval->h !== 0) {
            $timepart .= $interval->h . 'H';
        }
        if ($interval->i !== 0) {
            $timepart .= $interval->i . 'M';
        }
        if ($interval->s !== 0) {
            $timepart .= $interval->s . 'S';
        }
        if ($timepart === '') {
            if ($datepart === '') {
                return 'P0D';
            }
            return 'P' . $datepart;
        }
        return 'P' . $datepart . 'T' . $timepart;
    }
}
