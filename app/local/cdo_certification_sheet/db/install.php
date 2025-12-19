<?php

use local_cdo_certification_sheet\DTO\certification_sheet_dto;
use local_cdo_certification_sheet\DTO\close_sheet_dto;
use local_cdo_certification_sheet\DTO\default_request;
use tool_cdo_config\models\cdo_config;

defined('MOODLE_INTERNAL') || die;

function xmldb_local_cdo_certification_sheet_install(): bool
{

    $service = [
        get_update_grade_service(),
        get_commission_agreed_service(),
        get_close_sheet_service(),
        get_list_sheets_service()
    ];

    foreach ($service as $item) {
        cdo_config::get_instance()->set_data($item)->save();
    }

    return true;
}

function get_list_sheets_service(): stdClass
{
    $result = new stdClass();

    $result->name = "Получение списка ведомостей";
    $result->description = "GetListSheet";
    $result->method = "GET";
    $result->endpoint = "http://176.118.219.86/univer_vet_kolesnikov/hs/cdo_eois_Campus/GetListSheet";
    $result->login = "AdminHS";
    $result->password = "yXQddL";
    $result->code = "get_list_sheet";
    $result->dto = certification_sheet_dto::class;
    $result->headers = "";

    return $result;
}

function get_update_grade_service(): stdClass
{
    $result = new stdClass();

    $result->name = "Установка оценок";
    $result->description = "Установка оценок";
    $result->method = "GET";
    $result->endpoint = "http://176.118.219.86/sgua_goryshkin/hs/cdo_eois_Campus/InsertGrade";
    $result->login = "useru";
    $result->password = "1";
    $result->code = "insert_grade";
    $result->dto = default_request::class;
    $result->headers = "";

    return $result;
}

function get_commission_agreed_service(): stdClass
{
    $result = new stdClass();

    $result->name = "Изменение согласования";
    $result->description = "Изменение согласования";
    $result->method = "GET";
    $result->endpoint = "http://176.118.219.86/sgua_goryshkin/hs/cdo_eois_Campus/InsertAgreed";
    $result->login = "useru";
    $result->password = "1";
    $result->code = "change_agreed";
    $result->dto = default_request::class;
    $result->headers = "";

    return $result;
}

function get_close_sheet_service(): stdClass
{
    $result = new stdClass();

    $result->name = "Закрытие ведомости";
    $result->description = "Закрытие ведомости";
    $result->method = "GET";
    $result->endpoint = "http://176.118.219.86/sgua_goryshkin/hs/cdo_eois_Campus/EndSheet";
    $result->login = "useru";
    $result->password = "1";
    $result->code = "close_sheet";
    $result->dto = close_sheet_dto::class;
    $result->headers = "";

    return $result;
}
