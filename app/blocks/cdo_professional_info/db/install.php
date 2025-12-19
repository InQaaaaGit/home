<?php

use block_cdo_professional_info\DTO\professional_info_dto;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\models\cdo_config;

defined('MOODLE_INTERNAL') || die;

/**
 * @throws cdo_config_exception
 */
function xmldb_block_cdo_professional_info_install(): bool {

    cdo_config::get_instance()->set_data(
        get_cdo_get_professional_info_service()
    )->save();

    return true;
}

function get_cdo_get_professional_info_service(): stdClass {
    $result = new stdClass();

    $result->name = "Информация о сотруднике";
    $result->description = "Информация о сотруднике";
    $result->method = "GET";
    $result->endpoint = "{{host}}/hs/campus/teacher/employment/info";
    $result->login = "1";
    $result->password = "1";
    $result->code = "get_professional_info";
    $result->dto = professional_info_dto::class;
    $result->headers = "";

    return $result;
}