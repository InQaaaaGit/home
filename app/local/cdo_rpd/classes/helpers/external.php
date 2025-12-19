<?php

namespace local_cdo_rpd\helpers;

use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

class external
{
    public static function get_external_return_literature(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, '3'),
                    'book' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, '3'),
                    'author' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, '3'),
                    'link' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, '3'),
                    'year' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, '3'),
                    'publishing' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, '3'),
                    'count' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, '3'),
                    'approval' => new external_value(PARAM_RAW, 'id', VALUE_DEFAULT, null),
                    'commentary' => new external_value(PARAM_RAW, 'id', VALUE_DEFAULT, null),
                ]
            ),
            '',
            VALUE_DEFAULT,
            []
        );
    }
}