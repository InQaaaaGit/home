<?php

namespace tool_cdo_config\exceptions;

final class code_errors {

    //100 - основные ошибки работы CURL
    private static function mistakes_100() {
        return [
            1001    => 'Не удалось составить корректный URL адрес для обращения',
            1002    => 'Ошибка при вызове curl',
            1003    => 'Ошибка при составлении дополнительных options',
            100400  => 'Некорректный запрос',
            100401  => 'Ошибка авторизации',
            100402  => 'Необходима оплата',
            100403  => get_string('exc_access_denied', 'tool_cdo_config'),
            100404  => get_string('exc_resource_not_found', 'tool_cdo_config'),
            100406  => 'Ответ не соответствует списку доступных значений',
            100408  => get_string('exc_timed_out', 'tool_cdo_config'),
            100500  => 'Внутренняя ошибка сервера',
            100502  => 'Ошибочный шлюз',
            100503  => 'Сервис недоступен',
            100504  => 'Шлюз не отвечает',
        ];
    }

    //101 - ошибки Json JsonException
    private static function mistakes_101() {
        return [
            1011 => 'Ошибка во входящей JSON строке',
            1012 => 'Ошибка обработки JSON',
        ];
    }

    //102 - ошибки Curl Auth
    private static function mistakes_102() {
        return [
            1021 => get_string('exc_token_notfound', 'tool_cdo_config'),
            1022 => get_string('exc_login_notfound', 'tool_cdo_config'),
            1023 => get_string('exc_password_notfound', 'tool_cdo_config'),
            1024 => get_string('exc_empty_token', 'tool_cdo_config'),
        ];
    }

    //2xx - системные ошибки + moodle
    private static function mistakes_200() {
        return [
            2001 => 'Системная ошибка базы данных.',  //dml_exception
            2002 => 'Ошибка получения контекста системы.', // context exception
            2003 => 'Возникла непредвиденная ошибка кодирования.', // coding_exception
            2004 => 'Возникла непредвиденная ошибка рефлексии.', // ReflectionException
            2005 => 'Возникла непредвиденная ошибка системы.', // moodle_exception
            2006 => 'Не является файлом. ', // is_file()
        ];
    }

    //3xx - Персонализированные ошибки
    private static function mistakes_300() {
        return [
            3001 => get_string('exc_record_not_found_by_id', 'tool_cdo_config'),
            3002 => get_string('exc_request_not_found_by_code', 'tool_cdo_config'),
            3003 => get_string('exc_byte_formation', 'tool_cdo_config'),
            3004 => get_string('exc_wrong_string_for_decode', 'tool_cdo_config'),
            3005 => get_string('exc_service_not_found', 'tool_cdo_config'),
            3006 => get_string('exc_not_filled_required_fields', 'tool_cdo_config'),
            3007 => get_string('exc_close_statement', 'tool_cdo_config'),
            3008 => get_string('exc_db_record_not_found', 'tool_cdo_config'),
            3009 => 'Формат курса не является flexsections',
            3010 => 'Секция не может быть удалена',
            3011 => 'Формат курса не является sections/topics/weekly',
            3012 => 'Невозможно удалить общую секцию (секция 0)',

        ];
    }

    public static function get_error_by_first_tree_num(int $code): array
    {
        $key = 'mistakes_' .mb_substr((string)$code, 0, 3);
        if (!method_exists(__CLASS__, $key)) {
            return [];
        }
        return self::{$key}();
    }

    public static function get_msg_by_code(int $code): string
    {
        $result = self::get_error_by_first_tree_num($code);
        return $result[$code]?? '';
    }

}
