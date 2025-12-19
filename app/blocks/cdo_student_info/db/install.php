<?php

use block_cdo_student_info\dto\checklist\checklist_dto;
use block_cdo_student_info\dto\student_info_dto;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\models\cdo_config;

defined('MOODLE_INTERNAL') || die;

/**
 * @throws cdo_config_exception
 */
function xmldb_block_cdo_student_info_install(): bool {

    cdo_config::get_instance()->set_data(
        get_cdo_student_info_service()
    )->save();

    return true;
}

function get_cdo_student_info_service(): stdClass {
    $result = new stdClass();

    $result->name = "Информация о студенте";
    $result->description = "Информация о студенте";
    $result->method = "GET";
    $result->endpoint = "{{host}}/hs/campus/student/personal/info";
    $result->login = "CDO_HTTP_USER";
    $result->password = "1";
    $result->code = "get_student_info";
    $result->dto = student_info_dto::class;
    $result->headers = "";

    return $result;
}

function get_cdo_student_info_checklist_service(): stdClass {
    $result = new stdClass();

    $result->name = "Обходной лист";
    $result->description = "Обходной лист";
    $result->method = "GET";
    $result->endpoint = "{host}/hs/cdo_eois_Campus/InsertGrade";
    $result->login = "CDO_HTTP_USER";
    $result->password = "1";
    $result->code = "get_checklist";
    $result->dto = checklist_dto::class;
    $result->headers = "";

    return $result;
}