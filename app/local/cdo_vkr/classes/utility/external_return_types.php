<?php

namespace local_cdo_vkr\utility;

use external_description;
use external_single_structure;
use external_value;

class external_return_types extends external_description
{
    public function type_of_vkr_file(): external_single_structure
    {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, '', VALUE_REQUIRED),
                'name' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                'url' => new external_value(PARAM_RAW, '', VALUE_REQUIRED),
                'reason' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
                'user_status' => new external_single_structure(
                    [
                        'id' => new external_value(PARAM_INT, '', VALUE_REQUIRED),
                        'date' => new external_value(PARAM_TEXT, '', VALUE_REQUIRED),
                    ]
                )
            ],
            $this->desc,
            $this->required,
            $this->default
        );
    }
    public static function type_of_result_return(){
        return new external_single_structure([
            'message' => new external_value(PARAM_TEXT, 'result', VALUE_REQUIRED),
            'status' => new external_value(PARAM_BOOL, 'result', VALUE_REQUIRED)
        ]);
    }
}