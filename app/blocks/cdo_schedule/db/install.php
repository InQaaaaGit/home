<?php

use block_cdo_professional_info\DTO\professional_info_dto;
use block_cdo_schedule\dto\schedule_dto;
use block_cdo_schedule\dto\subgroup_dto;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\models\cdo_config;

defined('MOODLE_INTERNAL') || die;

/**
 * @throws cdo_config_exception
 */
function xmldb_block_cdo_schedule_install(): bool {

    cdo_config::get_instance()->set_data(
        get_subgroup_service()
    )->save();

    cdo_config::get_instance()->set_data(
        get_schedule_dto_service()
    )->save();

    return true;
}

function get_subgroup_service(): stdClass {
    $result = new stdClass();

    $result->name = "(НЕ используется) Информация о подгруппах";
    $result->description = "Информация о подгруппах";
    $result->method = "GET";
    $result->endpoint = "{{host}}";
    $result->login = "1";
    $result->password = "1";
    $result->code = "get_subgroup";
    $result->dto = subgroup_dto::class;
    $result->headers = "";

    return $result;
}

function get_schedule_dto_service(): stdClass {
    $result = new stdClass();

    $result->name = "Расписание (1C Университет)";
    $result->description = "Расписание";
    $result->method = "GET";
    $result->endpoint = "{{host}}/hs/univer_schedule/get_schedule_by_period";
    $result->login = "1";
    $result->password = "1";
    $result->code = "get_schedule";
    $result->dto = schedule_dto::class;
    $result->headers = "";

    return $result;
}