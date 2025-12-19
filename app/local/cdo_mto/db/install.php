<?php

defined('MOODLE_INTERNAL') || die;

function xmldb_local_cdo_mto_install() {
  global $DB;

  $baseUrl  = 'http://10.205.100.22/webyniv/hs';
  $login    = 'CDO_HTTP_USER';
  $password = 'GvI+nGk!fUux';
  $date     = time();

  $records = [
    [
      'name' => '(МТО) Корпуса',
      'description' => '(МТО) Корпуса',
      'method' => 'GET',
      'endpoint' => $baseUrl . '/AssetManager/Structures',
      'login' => $login,
      'password' => $password,
      'no_auth' => 0,
      'auth_token' => 0,
      'use_mock' => 0,
      'headers' => '',
      'code' => 'get_MTO_building_info',
      'dto' => 'local_cdo_mto\\DTO\\building\\structures_info_dto',
      'timecreated' => $date,
      'timemodified' => $date
    ],
    [
      'name' => '(МТО) Аудиторный фонд',
      'description' => '(МТО) Аудиторный фонд',
      'method' => 'GET',
      'endpoint' => $baseUrl . '/AssetManager/Structures',
      'login' => $login,
      'password' => $password,
      'no_auth' => 0,
      'auth_token' => 0,
      'use_mock' => 0,
      'headers' => '',
      'code' => 'get_MTO_room_info',
      'dto' => 'local_cdo_mto\\DTO\\room\\structures_info_dto',
      'timecreated' => $date,
      'timemodified' => $date
    ],
    [
      'name' => '(МТО) Образовательные программы',
      'description' => '(МТО) Образовательные программы',
      'method' => 'GET',
      'endpoint' => $baseUrl . '/AssetManager/Structures',
      'login' => $login,
      'password' => $password,
      'no_auth' => 0,
      'auth_token' => 0,
      'use_mock' => 0,
      'headers' => '',
      'code' => 'get_MTO_edu_program_info',
      'dto' => 'local_cdo_mto\\DTO\\edu_program\\structures_info_dto',
      'timecreated' => $date,
      'timemodified' => $date
    ],
    [
      'name' => '(МТО) Дисциплины',
      'description' => '(МТО) Дисциплины',
      'method' => 'GET',
      'endpoint' => $baseUrl . '/AssetManager/Structures',
      'login' => $login,
      'password' => $password,
      'no_auth' => 0,
      'auth_token' => 0,
      'use_mock' => 0,
      'headers' => '',
      'code' => 'get_MTO_discipline_info',
      'dto' => 'local_cdo_mto\\DTO\\discipline\\structures_info_dto',
      'timecreated' => $date,
      'timemodified' => $date
    ],
    [
      'name' => '(МТО) Образовательные программы - Редактирование',
      'description' => '(МТО) Образовательные программы - Редактирование',
      'method' => 'POST',
      'endpoint' => $baseUrl . '/cdo_eois_Campus/UpdateNameSpecialtyProfile',
      'login' => $login,
      'password' => $password,
      'no_auth' => 0,
      'auth_token' => 0,
      'use_mock' => 0,
      'headers' => '',
      'code' => 'patch_MTO_edu_program',
      'dto' => 'local_cdo_mto\\DTO\\edu_program\\patch_dto',
      'timecreated' => $date,
      'timemodified' => $date
    ],
    [
      'name' => '(МТО) Корпуса - Создание',
      'description' => '(МТО) Корпуса - Создание',
      'method' => 'POST',
      'endpoint' => $baseUrl . '/AssetManager/Structures?mode=create_building',
      'login' => $login,
      'password' => $password,
      'no_auth' => 0,
      'auth_token' => 0,
      'use_mock' => 0,
      'headers' => '',
      'code' => 'post_MTO_create_building',
      'dto' => 'local_cdo_mto\\DTO\\building\\create_dto',
      'timecreated' => $date,
      'timemodified' => $date
    ],
    [
      'name' => '(МТО) Корпуса - удаление',
      'description' => '(МТО) Корпуса - удаление',
      'method' => 'DELETE',
      'endpoint' => $baseUrl . '/AssetManager/Structures',
      'login' => $login,
      'password' => $password,
      'no_auth' => 0,
      'auth_token' => 0,
      'use_mock' => 0,
      'headers' => '',
      'code' => 'del_MTO_building',
      'dto' => 'local_cdo_mto\\DTO\\building\\delete_dto',
      'timecreated' => $date,
      'timemodified' => $date
    ],
    [
      'name' => '(МТО) Корпуса - Редактирование',
      'description' => '(МТО) Корпуса - Редактирование',
      'method' => 'PATCH',
      'endpoint' => $baseUrl . '/AssetManager/Structures?mode=change_characteristics',
      'login' => $login,
      'password' => $password,
      'no_auth' => 0,
      'auth_token' => 0,
      'use_mock' => 0,
      'headers' => '',
      'code' => 'patch_MTO_building',
      'dto' => 'local_cdo_mto\\DTO\\building\\patch_dto',
      'timecreated' => $date,
      'timemodified' => $date
    ],
    [
      'name' => '(МТО) Аудиторный фонд - Редактирование',
      'description' => '(МТО) Аудиторный фонд - Редактирование',
      'method' => 'PATCH',
      'endpoint' => $baseUrl . '/AssetManager/Structures?mode=change_characteristics',
      'login' => $login,
      'password' => $password,
      'no_auth' => 0,
      'auth_token' => 0,
      'use_mock' => 0,
      'headers' => '',
      'code' => 'patch_MTO_room',
      'dto' => 'local_cdo_mto\\DTO\\room\\patch_dto',
      'timecreated' => $date,
      'timemodified' => $date
    ],
    [
      'name' => '(МТО) Дисциплины - Редактирование',
      'description' => '(МТО) Дисциплины - Редактирование',
      'method' => 'PATCH',
      'endpoint' => $baseUrl . '/AssetManager/Process?mode=change_disciplines',
      'login' => $login,
      'password' => $password,
      'no_auth' => 0,
      'auth_token' => 0,
      'use_mock' => 0,
      'headers' => '',
      'code' => 'patch_MTO_discipline',
      'dto' => 'local_cdo_mto\\DTO\\discipline\\patch_dto',
      'timecreated' => $date,
      'timemodified' => $date
    ],
    [
      'name' => '(МТО) Аудиторный фонд - Создание',
      'description' => '(МТО) Аудиторный фонд - Создание',
      'method' => 'POST',
      'endpoint' => $baseUrl . '/AssetManager/Structures?mode=create_room_in_building',
      'login' => $login,
      'password' => $password,
      'no_auth' => 0,
      'auth_token' => 0,
      'use_mock' => 0,
      'headers' => '',
      'code' => 'post_MTO_create_room',
      'dto' => 'local_cdo_mto\\DTO\\room\\create_dto',
      'timecreated' => $date,
      'timemodified' => $date
    ]
  ];

  foreach ($records as $record) {
    $DB->insert_record('cdo_config', $record);
  }
}
