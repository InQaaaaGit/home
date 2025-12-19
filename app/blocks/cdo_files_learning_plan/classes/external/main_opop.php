<?php

namespace block_cdo_files_learning_plan\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use moodle_exception;

class main_opop extends external_api
{
  /**
   * Параметры метода set_agreed
   */
  public static function set_agreed_parameters(): external_function_parameters
  {
    return new external_function_parameters([
      'data' => new external_single_structure([
        'rpd_id' => new external_value(PARAM_TEXT, 'rpd_id', VALUE_REQUIRED),
        'date' => new external_value(PARAM_TEXT, 'date', VALUE_REQUIRED),
        'number' => new external_value(PARAM_TEXT, 'number', VALUE_REQUIRED),
        'structure' => new external_value(PARAM_TEXT, 'structure', VALUE_REQUIRED),
      ])
    ]);
  }

  /**
   * Отправка данных во внешний сервис
   *
   * @param array $data
   * @return array
   * @throws moodle_exception
   */
  public static function set_agreed($data)
  {
    global $CFG;
    require_once($CFG->dirroot . "/lib/filelib.php");

    // Валидация параметров
    $params = self::validate_parameters(self::set_agreed_parameters(), ['data' => $data]);

    // Получаем настройки блока
    $url = get_config('cdo_block_files_learning_plan', 'url');
    $scenario = get_config('cdo_block_files_learning_plan', 'scenario');
    $login = get_config('cdo_block_files_learning_plan', 'hslogin');
    $pass = get_config('cdo_block_files_learning_plan', 'hspass');

    // Инициализация cURL и настройка авторизации
    $curl = new \curl();
    $curl->setopt([
      'CURLOPT_USERPWD' => "$login:$pass",
      'CURLOPT_RETURNTRANSFER' => true
    ]);
    $curl->setHeader(['Content-Type: application/json']);

    // Кодирование данных в JSON и отправка POST-запроса
    $jsonData = json_encode($params['data']);
    $response = $curl->post($url . $scenario, $jsonData);

    // Проверка кода ответа
    $http_code = $curl->get_info('http_code');
    if ($http_code !== 200) {
      throw new moodle_exception("Ошибка: $http_code. Ответ сервера: $response");
    }

    // Возвращаем ответ как массив
    return ['response' => $response];
  }

  /**
   * Определение структуры возвращаемого значения
   */
  public static function set_agreed_returns(): external_single_structure
  {
    return new external_single_structure([
      'response' => new external_value(PARAM_RAW, 'Ответ от сервера')
    ]);
  }
}
