<?php

global $CFG, $USER, $OUTPUT, $DB, $PAGE;

require_once __DIR__ . '/../../config.php';
require_once $CFG->libdir . '/adminlib.php';
//require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/classes/Api.php';
require_once __DIR__ . '/classes/config.php';

header('Content-type: application/json');

require_login();

// Проверка прав доступа
require_capability('block/cdo_files_learning_plan:edit', context_system::instance());

$update_mode = optional_param("update_mode", "", PARAM_TEXT);

$ans = '';
switch ($update_mode) {
    case 'new_file':

        $params = array();
        $imagefile = $_FILES['file'];
        if (!isset($imagefile)) {
            $ans = json_encode(['error' => 'Необходимо выбрать хотя бы один файл.']);
        } else {
            /* if (is_array($imagefile['name'])) {
                for ($i = 0; $i < count($imagefile['name']); $i++)
                    if ($imagefile["error"][$i] == 0)
                        $params["imagefile[$i]"] = curl_file_create(
                            $imagefile['tmp_name'][$i],
                            $imagefile['type'][$i],
                            $imagefile['name'][$i]
                        );
            } else {
                $params["imagefile[0]"] = curl_file_create(
                    $imagefile['tmp_name'],
                    $imagefile['type'],
                    $imagefile['name']
                );
            } */
	    if (is_array($imagefile['tmp_name'])) {$imagefile['tmp_name']=$imagefile['tmp_name'][0];}
	    if (is_array($imagefile['name'])) {$imagefile['name']=$imagefile['name'][0];}
            $urlParams = [
                "mode" => $update_mode,
                "doc_id" => optional_param("doc_id", "", PARAM_TEXT),
                "discipline_id" => optional_param("discipline_id", "", PARAM_TEXT),
                "section" => optional_param("section", "", PARAM_TEXT),
                "discipline_index" => optional_param("discipline_index", "", PARAM_TEXT),
                "module_id" => optional_param("module_id", "", PARAM_TEXT),
                "type" => optional_param("type", "", PARAM_TEXT),
            ];

            $content = file_get_contents($imagefile['tmp_name']);
            $pathToSave = '/mnt/exchange/' . $USER->id;
            if (!file_exists($pathToSave)) {
                $result = mkdir($pathToSave, 0777, true);
            }
            $extension = getExtensionFile($imagefile['name']);
            $filename = $pathToSave . '/' . uniqid() . '.' . $extension;
            file_put_contents($filename, $content);
            $urlParams['imagefile'] = $filename;
            $urlParams['imagename'] = str_replace('.' . $extension, '', $imagefile['name']);
            $urlParams['file_content'] = $content;
            $urlParams['file_name'] = $imagefile['name'];
            $ans = Api::putDisciplineProgramFile($urlParams, $params);
        }

        break;
    case 'delete_file':

        $urlParams = [
            "mode" => $update_mode,
            "doc_id" => optional_param("doc_id", "", PARAM_TEXT),
            'discipline_id' => optional_param("discipline_id", "", PARAM_TEXT),
            'discipline_index' => optional_param("discipline_index", "", PARAM_TEXT),
            'guidfile' => optional_param("guidfile", "", PARAM_TEXT),
            'module_id' => optional_param("module_id", "", PARAM_TEXT),
            'type' => optional_param("type", "", PARAM_TEXT),
        ];
        //echo json_encode($urlParams, 256); die();

        $ans = Api::putDisciplineProgramFile($urlParams);

        break;
    case 'update_notes':
        $urlParams = [
            "mode" => $update_mode,
            "doc_id" => optional_param("doc_id", "", PARAM_TEXT),
            "discipline_id" => optional_param("discipline_id", "", PARAM_TEXT),
            "discipline_index" => optional_param("discipline_index", "", PARAM_TEXT),
            "module_id" => optional_param("module_id", "", PARAM_TEXT),
            "type" => optional_param("type", "", PARAM_TEXT),
        ];
        $bodyParams = [
            "notes" => optional_param("notes", "", PARAM_TEXT),
        ];

        $ans = Api::putDisciplineProgramFile($urlParams, $bodyParams);

        break;
    case 'update_link':
    case 'new_link':

        $ans = Api::putEducationProgramLink([],
            [
                "mode" => $update_mode,
                "doc_id" => optional_param("doc_id", "", PARAM_TEXT),
                "link_guid" => optional_param("link_guid", "", PARAM_TEXT),
                "link_name" => optional_param("link_name", "", PARAM_TEXT),
                "link_URL" => optional_param("link_URL", "", PARAM_TEXT),
                "discipline_id" => optional_param("discipline_id", "", PARAM_TEXT),
                "discipline_index" => optional_param("discipline_index", "", PARAM_TEXT),
                "module_id" => optional_param("module_id", "", PARAM_TEXT),
                "type" => optional_param("type", "", PARAM_TEXT),
            ]
        );

        break;
    case 'delete_link':
        $params = [
            "mode" => $update_mode,
            "doc_id" => optional_param("doc_id", "", PARAM_TEXT),
            "link_guid" => optional_param("link_guid", "", PARAM_TEXT),
            "discipline_id" => optional_param("discipline_id", "", PARAM_TEXT),
        ];

        $ans = Api::putEducationProgramLink([], $params);

        break;
    case 'delete_file_program':
        $urlParams = [
            'mode' => 'delete_file',
            'file_id' => optional_param("file_id", "", PARAM_TEXT),
        ];
        $params = [
            "doc_id" => optional_param("doc_id", "", PARAM_TEXT)
        ];
        $ans = Api::deleteEducationProgramFile($urlParams, $params);

        break;
    case 'update_program_file':

        $learning_program = optional_param("learning_program", "", PARAM_TEXT);
        $edu_plan = optional_param("edu_plan", "", PARAM_TEXT);

        $urlParams = ['mode' => optional_param("mode", "new_file", PARAM_TEXT)];

        $params = [
            'comment' => optional_param("comment", "", PARAM_TEXT),
            'doc_id' => !empty($edu_plan) ? $edu_plan : $learning_program,
            'user_id' => $USER->id,
            'file_id' => optional_param("id", "", PARAM_TEXT),
        ];

        if (array_key_exists('file', $_FILES) && !empty($_FILES['file']['tmp_name'])) {
//            $params['imagefile'] = curl_file_create(
//                $_FILES['file']['tmp_name'],
//                $_FILES['file']['type'],
//                $_FILES['file']['name']
//            );
            $imagefile = $_FILES['file'];
            $content = file_get_contents($imagefile['tmp_name']);
            $pathToSave = get_config('cdo_block_files_learning_plan', 'path_to_save_files') . '/' . $USER->id;
            $pathToSave = '/mnt/exchange';
            if (!file_exists($pathToSave)) {
                mkdir($pathToSave, 0777, true);
            }
            $extension = getExtensionFile($imagefile['name']);
            $filename = $pathToSave . '/' . uniqid() . '.' . $extension;
            file_put_contents($filename, $content);
            $params['imagefile'] = $filename;
            $params['file_content'] = $content;
            $params['file_name'] = $imagefile['name'];
            $params['imagename'] = str_replace('.' . $extension, '', $imagefile['name']);
        }

        $ans = Api::putEducationProgramFile($urlParams, $params);

        break;

    case 'update_description':

        $ans = Api::putEducationProgramFile(
            ['mode' => $update_mode],
            [
                'doc_id' => optional_param("learning_program", "", PARAM_TEXT),
                'description' => optional_param("description", "", PARAM_TEXT),
                'user_id' => $USER->id,
                'file_id' => optional_param("id", "", PARAM_TEXT),
            ]
        );

        break;


    default:
        $ans = ['error' => "Некорректный метод"];

}
echo json_encode($ans);

function dump($data, $die = true)
{
    echo '<pre>' . print_r($data, 1) . '</pre>';
    if ($die) die();
}

function getExtensionFile($file)
{
    $path_info = pathinfo($file);
    if(array_key_exists('extension', $path_info))
        return strtolower($path_info['extension']);
    else return '';
}
