<?php

namespace tool_cdo_config\tools;

use dml_exception;
use tool_cdo_config\exceptions\cdo_config_exception;

class helper
{
    /**
     * @param int $id
     * @return void
     * @throws cdo_config_exception
     */
    public static function get_current_gradebook(int $id): string
    {
        global $USER, $CFG;
        try {
            $user_object = get_complete_user_data("id", $id);
            $gradebook_parse = json_decode($user_object->profile['gradebook']) ?? [];
            if (!empty($gradebook_parse)) {
                foreach ($gradebook_parse as $key => $value) {
                    if ($value->priority === 1) {
                        return $value->name;
                    }
                }
            }
            throw new cdo_config_exception(2, get_string('exc_gradebook_notfound', 'tool_cdo_config'));
        } catch (dml_exception $e) {
            throw new cdo_config_exception($e->getMessage(), 1);
        }

    }
}
