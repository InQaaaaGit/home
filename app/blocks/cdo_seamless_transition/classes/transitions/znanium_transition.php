<?php

namespace block_cdo_seamless_transition\transitions;

use coding_exception;
use moodle_exception;
use moodle_url;
use stdClass;

class znanium_transition extends transition
{

    /**
     * @return string
     * @throws moodle_exception
     */
    public function to(): string
    {
        global $USER;

        $time = gmdate('YmdHis');

        $uri_params = [
            "domain" => $this->get_data()->domain,
            'username' => $USER->username,
            'email' => $USER->email,
            'lname' => $USER->lastname,
            'fname' => $USER->firstname,
            'mname' => $USER->middlename,
            'gmt' => $time,
            'token' => md5("{$this->get_data()->domain}{$USER->username}{$time}{$this->get_data()->token}")
        ];

        return (new moodle_url("https://znanium.com/sso", $uri_params))->out(false);
    }

    /**
     * @return string
     */
    public function get_code(): string
    {
        return "znanium";
    }

    /**
     * @return string
     * @throws coding_exception
     */
    public function get_transition_name(): string
    {
        return get_string('znanium_name', 'block_cdo_seamless_transition');
    }

    /**
     * @return stdClass
     */
    public function get_other_external_param(): stdClass
    {
        return new stdClass();
    }
}