<?php

namespace tool_cdo_config\exceptions;

use Exception;

class cdo_config_exception extends Exception {

    public static array $prefix_errors_num_keys = [
        'system_curl' => 100,
    ];

	public function __construct(int $code = 0, string $message ='', bool $only_msg = false) {
        if ($only_msg){
            parent::__construct($message);
            return;
        }
        $text_error = $this->get_text_error_cdo_by_code($code);
        if (!empty($message)){
            $text_error = $text_error . " $message";
        }
		parent::__construct($text_error);
	}

    private function get_text_error_cdo_by_code(int $code): string
    {
        $default_text = get_string('wrong_code_in_exc', 'tool_cdo_config', $code);
        $arr_errors = code_errors::get_error_by_first_tree_num($code);
        return $arr_errors[$code]?? $default_text;
    }
}
