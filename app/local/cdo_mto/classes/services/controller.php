<?php

namespace local_cdo_mto\services;

use coding_exception;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\request\DTO\response_dto;

class controller
{
  /**
   * Универсальный метод для выполнения запросов.
   *
   * @param array  $params          Параметры запроса.
   * @param string $structure_type  Тип структуры.
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   */
  private function execute_request(array $params, string $structure_type): response_dto
  {
    $options = di::get_instance()->get_request_options();
    $options->set_properties($params);

    return di::get_instance()
      ->get_request($structure_type)
      ->request($options)
      ->get_request_result();
  }

  /**
   * Получить информацию о структуре.
   *
   * @param array  $params          Параметры запроса.
   * @param string $structure_type  Тип структуры.
   * @return response_dto
   * @throws cdo_type_response_exception
   * @throws cdo_config_exception
   * @throws coding_exception
   */
  public function get_structure_info_api(array $params, string $structure_type): response_dto
  {
    return $this->execute_request($params, $structure_type);
  }

  /**
   * Создать здание.
   *
   * @param array  $data            Данные для создания.
   * @param string $structure_type  Тип структуры.
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   */
  public function create_building_api(array $data, string $structure_type): response_dto
  {
    $characteristics = [
      "data" => $this->map_building_data($data),
    ];

    $params = [
      "object_name" => $data["object_name"],
      "user_id"     => $data["user_id"],
      "parameters"  => json_encode($characteristics, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
    ];

    return $this->execute_request($params, $structure_type);
  }

  /**
   * Создать помещение.
   *
   * @param array  $data            Данные для создания.
   * @param string $structure_type  Тип структуры.
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   */
  public function create_room_api(array $data, string $structure_type): response_dto
  {
    $characteristics = [
      "data" => $this->map_room_data($data),
    ];

    $params = [
      "allow_repeated_names" => "true",
      "object_name"          => $data["object_name"],
      "building_uid"         => $data["building_uid"],
      "user_id"              => $data["user_id"],
      "parameters"           => json_encode($characteristics, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
    ];

    return $this->execute_request($params, $structure_type);
  }

  /**
   * Обновить информацию о помещении.
   *
   * @param array  $data            Данные для обновления.
   * @param string $structure_type  Тип структуры.
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   */
  public function patch_room_api(array $data, string $structure_type): response_dto
  {
    $characteristics = [
      "room" => [
        [
          "uid"  => $data["object_uid"],
          "name" => $data["object_name"],
          "data" => $this->map_room_data($data),
        ],
      ],
    ];

    $params = [
      "allow_repeated_names" => "true",
      "user_id"              => $data["user_id"],
      "parameters"           => json_encode($characteristics, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
    ];

    return $this->execute_request($params, $structure_type);
  }

  /**
   * Обновить информацию о здании.
   *
   * @param array  $data            Данные для обновления.
   * @param string $structure_type  Тип структуры.
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   */
  public function patch_building_api(array $data, string $structure_type): response_dto
  {
    $characteristics = [
      "building" => [
        [
          "uid"  => $data["object_uid"],
          "name" => $data["object_name"],
          "data" => $this->map_building_data($data),
        ],
      ],
    ];

    $params = [
      "user_id"    => $data["user_id"],
      "parameters" => json_encode($characteristics, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
    ];

    return $this->execute_request($params, $structure_type);
  }

  /**
   * Обновить информацию о дисциплине.
   *
   * @param array  $data            Данные для обновления.
   * @param string $structure_type  Тип структуры.
   * @return response_dto
   * @throws cdo_config_exception
   * @throws cdo_type_response_exception
   * @throws coding_exception
   */
  public function patch_discipline_api(array $data, string $structure_type): response_dto
  {
    $parameters = [
      "data" => [
        "discipline" => [
          "uid"  => $data["uid"],
          "name" => $data["name"],
        ],
      ],
    ];

    $params = [
      "user_id"    => $data["user_id"],
      "parameters" => json_encode($parameters, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
    ];

    return $this->execute_request($params, $structure_type);
  }

  /**
   * Маппинг данных здания.
   *
   * @param array $data Данные.
   * @return array
   */
  private function map_building_data(array $data): array
  {
    return [
      "assmgr_building_address"   => $data["building_address"] ?? null,
      "assmgr_building_docsanit"  => $data["building_docsanit"] ?? null,
      "assmgr_building_docfire"   => $data["building_docfire"] ?? null,
      "assmgr_building_owner"     => $data["building_owner"] ?? null,
      "assmgr_building_usagedoc"  => $data["building_usagedoc"] ?? null,
      "assmgr_building_cadastre"  => $data["building_cadastre"] ?? null,
      "assmgr_building_usagetype" => $data["building_usagetype"] ?? null,
      "assmgr_building_registry"  => $data["building_registry"] ?? null,
      "assmgr_building_purpose"   => $data["building_purpose"] ?? null,
      "assmgr_building_4disabled" => $data["building_4disabled"] ?? null,
    ];
  }

  /**
   * Маппинг данных помещения.
   *
   * @param array $data Данные.
   * @return array
   */
  private function map_room_data(array $data): array
  {
    return [
      "assmgr_room_capacity"    => $data["room_capacity"] ?? null,
      "assmgr_room_area"        => $data["room_area"] ?? null,
      "assmgr_room_number"      => $data["room_number"] ?? null,
      "assmgr_room_technumber"  => $data["room_technumber"] ?? null,
      "assmgr_room_description" => $data["room_description"] ?? null,
    ];
  }
}
