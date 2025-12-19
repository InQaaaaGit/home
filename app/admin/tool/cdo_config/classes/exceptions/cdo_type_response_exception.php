<?php

namespace tool_cdo_config\exceptions;


class cdo_type_response_exception extends \Exception {

    private string $msg;
	public function __construct(\TypeError $error) {

        $this->msg = $error->getMessage();
        $this->check_error_msg();

        parent::__construct($this->msg);
	}

    private function check_error_msg(): void
    {
        if (stripos($this->msg, 'typed property') !== false){
            $this->get_param_name_for_render();
        }
    }

    private function get_param_name_for_render():void
    {
        preg_match('@(DTO\W)(.+?)(::\$)(\w+\b)@im', $this->msg, $matches);

        if (isset($matches[4])){
            $result = 'Параметр: <b>' . $matches[4] . '</b> в классе: ' . $matches[2] . ' ';
            $result .= $this->get_endings_of_msg();
            $this->msg = $result;
        }

    }
    private function get_endings_of_msg(): string
    {
        if (stripos($this->msg, 'null used') !== false){
            return 'из запроса в 1с вернулся пустым. Повторите попытку 
            позже или обратитесь к разработчикам';
        }
        return 'указан неверно. ';

    }
}
