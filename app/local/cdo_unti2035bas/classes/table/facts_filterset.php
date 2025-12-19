<?php
namespace local_cdo_unti2035bas\table;

use core_table\local\filter\filterset;
use core_table\local\filter\integer_filter;
use core_table\local\filter\string_filter;


class facts_filterset extends filterset {
    /**
     * @return array<string, mixed>
     */
    protected function get_optional_filters(): array {
        return [
            'factdefid' => integer_filter::class,
            'actoruntiid' => integer_filter::class,
        ];
    }
}
