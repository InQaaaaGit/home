<?php
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {


  // Папка для хранения файлов
  $settings->add(new admin_setting_configtext(
    'cdo_block_files_learning_plan/path_to_save_files',
    'Папка для хранения файлов',
    '',
    ''
  ));

}


