<?php

use block_cdo_files_learning_plan\DTO\discipline_program_file_dto;
use block_cdo_files_learning_plan\DTO\education_program_all_file_dto;
use block_cdo_files_learning_plan\DTO\education_program_dto;
use block_cdo_files_learning_plan\DTO\education_program_file_dto;
use block_cdo_files_learning_plan\DTO\education_program_link_dto;
use block_cdo_files_learning_plan\DTO\education_programs_dto;
use block_cdo_files_learning_plan\DTO\file_binary_dto;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\models\cdo_config;

defined('MOODLE_INTERNAL') || die;

/**
 * @throws cdo_config_exception
 */
function xmldb_block_cdo_files_learning_plan_install(): bool
{

    $service = [
        get_education_program_service(),
        get_education_programs_service(),
        put_discipline_program_file_service(),
        put_education_program_link_service(),
        put_education_program_file_service(),
        del_education_program_file_service(),
        get_file_binary_service(),
        get_education_program_all_file_service(),
    ];

    foreach ($service as $item) {
        cdo_config::get_instance()->set_data($item)->save();
    }

    return true;
}

function get_education_program_service(): stdClass
{
    $result = new stdClass();

    $result->name = "(OPOP) Образовательная программа студентов";
    $result->description = "Образовательная программа студентов - Прикрепление файлов к образовательной программе";
    $result->method = "GET";
    $result->endpoint = "http:///cdo_eois_Campus/GetEducationProgramInfo";
    $result->login = "";
    $result->password = "";
    $result->code = "files_learning_get_education_program";
    $result->dto = education_program_dto::class;
    $result->headers = "";

    return $result;
}

function get_education_programs_service(): stdClass
{
    $result = new stdClass();

    $result->name = "(OPOP) Список образовательных программ студентов";
    $result->description = "Образовательные программы студентов - Прикрепление файлов к образовательной программе";
    $result->method = "GET";
    $result->endpoint = "http:///cdo_eois_Campus/GetEducationProgramInfo";
    $result->login = "";
    $result->password = "";
    $result->code = "files_learning_get_education_programs";
    $result->dto = education_programs_dto::class;
    $result->headers = "";

    return $result;
}

function put_discipline_program_file_service(): stdClass
{
    $result = new stdClass();

    $result->name = "(OPOP) Редактирование описания к образовательной программе";
    $result->description = "Редактирование описания - Прикрепление файлов к образовательной программе";
    $result->method = "GET";
    $result->endpoint = "http:///cdo_eois_Campus/PutDisciplineProgramFile";
    $result->login = "";
    $result->password = "";
    $result->code = "files_learning_put_discipline_program_file";
    $result->dto = discipline_program_file_dto::class;
    $result->headers = "";

    return $result;
}

function put_education_program_link_service(): stdClass
{
    $result = new stdClass();

    $result->name = "(OPOP) Редактирование ссылки к образовательной программе";
    $result->description = "Редактирование ссылки - Прикрепление файлов к образовательной программе";
    $result->method = "GET";
    $result->endpoint = "http:///cdo_eois_Campus/PutEducationProgramLink";
    $result->login = "";
    $result->password = "";
    $result->code = "files_learning_put_education_program_link";
    $result->dto = education_program_link_dto::class;
    $result->headers = "";

    return $result;
}

function put_education_program_file_service(): stdClass
{
    $result = new stdClass();

    $result->name = "(OPOP) Редактирование файлов к образовательной программе";
    $result->description = "Редактирование файлов - Прикрепление файлов к образовательной программе";
    $result->method = "POST";
    $result->endpoint = "http:///cdo_eois_Campus/PutEducationProgramFile";
    $result->login = "";
    $result->password = "";
    $result->code = "files_learning_put_education_program_file";
    $result->dto = education_program_file_dto::class;
    $result->headers = "";

    return $result;
}


function del_education_program_file_service(): stdClass
{
    $result = new stdClass();

    $result->name = "(OPOP) Удаление файла образовательной программы";
    $result->description = "Удаление файла - Прикрепление файлов к образовательной программе";
    $result->method = "GET";
    $result->endpoint = "http:///cdo_eois_Campus/PutEducationProgramFile";
    $result->login = "";
    $result->password = "";
    $result->code = "files_learning_del_education_program_file";
    $result->dto = education_program_file_dto::class;
    $result->headers = "";

    return $result;
}

function get_file_binary_service(): stdClass
{
    $result = new stdClass();

    $result->name = "(OPOP) Получение бинарных файлов";
    $result->description = "Получение бинарных файлов - Прикрепление файлов к образовательной программе";
    $result->method = "GET";
    $result->endpoint = "http:///cdo_eois_Campus/GetFileBinary";
    $result->login = "";
    $result->password = "";
    $result->code = "files_learning_get_file_binary";
    $result->dto = file_binary_dto::class;
    $result->headers = "";

    return $result;
}

function get_education_program_all_file_service(): stdClass
{
    $result = new stdClass();

    $result->name = "(OPOP) Получение всех файлов образовательной программы";
    $result->description = "Получение всех файлов - Прикрепление файлов к образовательной программе";
    $result->method = "GET";
    $result->endpoint = "http:///cdo_eois_Campus/GetEducationProgramAllFiles";
    $result->login = "";
    $result->password = "";
    $result->code = "files_learning_get_education_program_all_file";
    $result->dto = education_program_all_file_dto::class;
    $result->headers = "";

    return $result;
}
