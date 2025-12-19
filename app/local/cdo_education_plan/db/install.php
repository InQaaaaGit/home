<?php

use local_cdo_academic_progress\DTO\academic_progress_dto;
use local_cdo_debts\DTO\academic\academic_debts_dto;
use local_cdo_debts\DTO\library_debts_dto;
use local_cdo_education_plan\DTO\education_plan_dto;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\models\cdo_config;

defined('MOODLE_INTERNAL') || die;

/**
 * @throws cdo_config_exception
 */
function xmldb_local_cdo_education_plan_install(): bool {

    cdo_config::get_instance()->set_data(
        get_cdo_education_plan_service()
    )->save();

    return true;
}

function get_cdo_education_plan_service(): stdClass {
    $result = new stdClass();

    $result->name = "Учебный план студентов";
    $result->description = "Учебный план студентов";
    $result->method = "GET";
    $result->endpoint = "http://176.118.219.86/sgua_goryshkin/hs/cdo_eois_Campus/InsertGrade";
    $result->login = "useru";
    $result->password = "1";
    $result->code = "get_academic_plan";
    $result->dto = education_plan_dto::class;
    $result->headers = "";

    return $result;
}