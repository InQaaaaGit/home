<?php

namespace local_cdo_debts\external;

use external_single_structure;
use external_value;

class helper
{
    public static function directory_structure(): external_single_structure
    {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ""),
                'name' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ""),
                'code' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ""),
                'guid' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ""),
                'number' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ""),
                'value' => new external_value(PARAM_RAW, 'binary_string', VALUE_DEFAULT, ""),
            ],
            "",
            VALUE_DEFAULT,
            []
        );
    }
}