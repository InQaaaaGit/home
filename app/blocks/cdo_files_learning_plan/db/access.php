<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
  'block/cdo_files_learning_plan:view' => [
    'captype' => 'read', // Тип возможности: чтение (read)
    'contextlevel' => CONTEXT_SYSTEM, // Уровень контекста: системный уровень
    'archetypes' => [
      'student' => CAP_PROHIBIT, // Запрещаем доступ студентам
      'teacher' => CAP_ALLOW,    // Разрешаем доступ преподавателям
      'manager' => CAP_ALLOW     // Разрешаем доступ менеджерам
    ]
  ],

  'block/cdo_files_learning_plan:edit' => [
    'captype' => 'write', // Тип возможности: запись (write)
    'contextlevel' => CONTEXT_SYSTEM, // Уровень контекста: системный уровень
    'archetypes' => [
      'student' => CAP_PROHIBIT,  // Запрещаем редактирование студентам
      'teacher' => CAP_ALLOW,     // Разрешаем редактирование преподавателям
      'manager' => CAP_ALLOW      // Разрешаем редактирование менеджерам
    ]
  ],
];
