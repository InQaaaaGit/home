<?php

use block_cdo_survey\DTO\citizenship_dto;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\models\cdo_config;

/**
 * @throws cdo_config_exception
 */
function xmldb_block_cdo_survey_install(): bool
{
    $result = new stdClass();

    $result->name = "Получение списка типов гражданств";
    $result->description = "get_citizenship_data";
    $result->method = "GET";
    $result->endpoint = "{{host}}/hs/?";
    $result->login = "1";
    $result->password = "1";
    $result->code = "get_citizenship_data";
    $result->dto = citizenship_dto::class;
    $result->headers = "";
    cdo_config::get_instance()->set_data($result)->save();
    
    return true;
}
