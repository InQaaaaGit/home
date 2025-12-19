<?php

namespace block_cdo_seamless_transition\transitions;

use coding_exception;
use moodle_url;
use stdClass;

class ibooks_transition extends transition
{

    public function get_code(): string
    {
        return 'ibooks';
    }

    public function to(): string
    {
        global $USER;
        $time =  date('YmdHis');
        $uri = "https://ibooks.ru/autosignon";
        $id = $this->get_data()->id;

        $url_params = [
            'domain' => $this->get_data()->domain,
            'id' => $id,
            'login' => $USER->username,
            'time' => $time,
            'name' => $USER->firstname,
            'lname' => $USER->lastname,
            'mname' => $USER->middlename,
            'email' => $USER->email,
            'sign' => md5($id.md5($this->get_data()->token).$time)
        ];
        return (new moodle_url($uri, $url_params))->out(false);
    }

    /**
     * @throws coding_exception
     */
    public function get_transition_name(): string
    {
        return get_string('ibooks_name', 'block_cdo_seamless_transition');
    }

    protected function get_other_external_param(): stdClass
    {
        return new stdClass();
    }
}