<?php

use local_cdo_academic_progress\DTO\academic_progress_dto;
use local_cdo_debts\DTO\academic\academic_debts_dto;
use local_cdo_debts\DTO\library_debts_dto;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\models\cdo_config;

defined('MOODLE_INTERNAL') || die;

/**
 * @throws cdo_config_exception
 */
function xmldb_local_cdo_debts_install(): bool {

    cdo_config::get_instance()->set_data(
        get_cdo_library_debts_service()
    )->save();

    cdo_config::get_instance()->set_data(
        get_cdo_academic_debts_service()
    )->save();

    return true;
}

function get_cdo_library_debts_service(): stdClass {
    $result = new stdClass();

    $result->name = "Задолженности студентов библиотечные";
    $result->description = "Задолженности студентов";
    $result->method = "GET";
    $result->endpoint = "http://176.118.219.86/sgua_goryshkin/hs/cdo_eois_Campus/InsertGrade";
    $result->login = "useru";
    $result->password = "1";
    $result->code = "library_debts";
    $result->dto = library_debts_dto::class;
    $result->headers = "";

    return $result;
}

function get_cdo_academic_debts_service(): stdClass {
    $result = new stdClass();

    $result->name = "Задолженности студентов академические";
    $result->description = "Задолженности студентов академические";
    $result->method = "GET";
    $result->endpoint = "http://176.118.219.86/sgua_goryshkin/hs/cdo_eois_Campus/InsertGrade";
    $result->login = "useru";
    $result->password = "1";
    $result->code = "academic_debts";
    $result->dto = academic_debts_dto::class;
    $result->headers = "";

    return $result;
}