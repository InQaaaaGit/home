<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
global $CFG, $USER, $OUTPUT, $DB, $PAGE;

require_once __DIR__ . '/classes/Api.php';
require_once __DIR__ . '/classes/Helpers.php';

header('Content-type: application/json');
require_login();
if ($USER->id <= 1) {
  echo "Зайдите в свой личный кабинет!";
  die;
}

// Проверка прав доступа
require_capability('block/cdo_files_learning_plan:view', context_system::instance());

$type = optional_param("type", "", PARAM_TEXT);

$ans = new stdClass();

switch ($type) {

    case 'settings':

        $ans->user_id = $USER->id;
        $ans->list_secretary = Helpers::getListSecretary();

        break;

    case 'education_programs':

      $secretary = optional_param('secretary', 0, PARAM_INT);

      if ( $secretary > 0)
          $user_id = $secretary;
      else $user_id = $USER->id;

      $ans = Api::getEducationPrograms($user_id);

      #print_r($ans->endpoint); die;

      if (is_array($ans))
          foreach ($ans as &$an) {
              $name = !empty($an['education_type']) ? $an['education_type'] : '';
              $name .= !empty($an['education_level']) ? ' - ' . $an['education_level'] : '';
              $name .= !empty($an['specialty']) ? ' - ' . $an['specialty'] : '';
              $name .= !empty($an['profile']) ? ' - ' . $an['profile'] : '';
              $name .= !empty($an['year']) ? ' (' . $an['year'] . ')' : '';
              $an['preview'] = $name;
          }


        if (array_key_exists('test', $_GET))
            Helpers::dump($ans);
        break;

    case 'education_program':

        $secretary = optional_param('secretary', 0, PARAM_INT);
        $doc_number = optional_param('doc_number', "", PARAM_TEXT);

        if (empty($doc_number)) {
            $ans = ['error' => 'Недостаточно фактических параметров.'];
            break;
        }

        if ( $secretary > 0 )
            $user_id = $secretary;
        else $user_id = $USER->id;

        $ans = Api::getEducationProgram($user_id, $doc_number);

        foreach ($ans['files'] as $file)
            if($file['comment'] == 'Характеристики образовательной программы' || $file['comment'] == 'Матрица компетенций')
                $file['edu_plan'] = null;


    if (array_key_exists('test', $_GET))
        Helpers::dump($ans);
    break;

    default:
        $ans = ['error' => 'Неизвестный метод'];

}

echo json_encode($ans, 256);






