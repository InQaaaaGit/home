<?php
namespace local_cdo_unti2035bas\table;

use core_table\local\filter\filterset;
use core_table\local\filter\integer_filter;


class practice_diaries_filterset extends filterset {
    /**
     * @return array<string, mixed>
     */
    protected function get_optional_filters(): array {
        return [
            'streamid' => integer_filter::class,
        ];
    }
}
