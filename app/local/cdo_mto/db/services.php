<?php

use local_cdo_mto\external\eduProgram;
use local_cdo_mto\external\discipline;
use local_cdo_mto\external\building;
use local_cdo_mto\external\room;

defined('MOODLE_INTERNAL') || die();

$functions = [
  'local_cdo_mto_get_building_info' => [
    'classname' => building::class,
    'methodname' => 'get_building_info',
    'description' => '',
    'type' => 'write',
    'ajax' => true
  ],
  'local_cdo_mto_add_building' => [
    'classname' => building::class,
    'methodname' => 'add_building',
    'description' => '',
    'type' => 'write',
    'ajax' => true
  ],
  'local_cdo_mto_patch_building' => [
    'classname' => building::class,
    'methodname' => 'patch_building',
    'description' => '',
    'type' => 'write',
    'ajax' => true
  ],
  'local_cdo_mto_del_building' => [
    'classname' => building::class,
    'methodname' => 'del_building',
    'description' => '',
    'type' => 'write',
    'ajax' => true
  ],

  'local_cdo_mto_get_room_info' => [
    'classname' => room::class,
    'methodname' => 'get_room_info',
    'description' => '',
    'type' => 'write',
    'ajax' => true
  ],
  'local_cdo_mto_add_room' => [
    'classname' => room::class,
    'methodname' => 'add_room',
    'description' => '',
    'type' => 'write',
    'ajax' => true
  ],
  'local_cdo_mto_patch_room' => [
    'classname' => room::class,
    'methodname' => 'patch_room',
    'description' => '',
    'type' => 'write',
    'ajax' => true
  ],

  'local_cdo_mto_get_edu_program_info' => [
    'classname' => eduProgram::class,
    'methodname' => 'get_edu_program_info',
    'description' => '',
    'type' => 'write',
    'ajax' => true
  ],
  'local_cdo_mto_patch_education_program' => [
    'classname' => eduProgram::class,
    'methodname' => 'patch_education_program',
    'description' => '',
    'type' => 'write',
    'ajax' => true
  ],

  'local_cdo_mto_get_discipline_info' => [
    'classname' => discipline::class,
    'methodname' => 'get_discipline_info',
    'description' => '',
    'type' => 'write',
    'ajax' => true
  ],
  'local_cdo_mto_patch_discipline' => [
    'classname' => discipline::class,
    'methodname' => 'patch_discipline',
    'description' => '',
    'type' => 'write',
    'ajax' => true
  ],
];

$services = [
  'cdo_mto_services' => [
    'functions' => [
      'local_cdo_mto_get_building_info',
      'local_cdo_mto_add_building',
      'local_cdo_mto_patch_building',
      'local_cdo_mto_del_building',

      'local_cdo_mto_get_room_info',
      'local_cdo_mto_add_room',
      'local_cdo_mto_patch_room',

      'local_cdo_mto_get_edu_program_info',
      'local_cdo_mto_patch_education_program',

      'local_cdo_mto_get_discipline_info',
      'local_cdo_mto_patch_discipline',
    ],
    'requiredcapability' => '',
    'restrictedusers' => 0,
    'enabled' => 1
  ]
];
