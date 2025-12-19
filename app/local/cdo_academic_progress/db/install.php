<?php

use local_cdo_academic_progress\DTO\academic_progress_dto;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\models\cdo_config;

defined('MOODLE_INTERNAL') || die;

/**
 * @throws cdo_config_exception
 */
function xmldb_local_cdo_academic_progress_install(): bool {

    cdo_config::get_instance()->set_data(
        get_academic_progress_service()
    )->save();

    return true;
}

function get_academic_progress_service(): stdClass {
    $result = new stdClass();

    $result->name = "Электронная зачетная книга";
    $result->description = "Электронная зачетная книга";
    $result->method = "GET";
    $result->endpoint = "http://176.118.219.86/sgua_goryshkin/hs/cdo_eois_Campus/InsertGrade";
    $result->login = "useru";
    $result->password = "1";
    $result->code = "get_academic_progress";
    $result->dto = academic_progress_dto::class;
    $result->headers = "";

    return $result;
}