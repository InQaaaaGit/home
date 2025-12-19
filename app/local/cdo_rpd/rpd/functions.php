<?php

use local_cdo_rpd\services\print_rpd;

function replace_data($html, $data, $newpage)
{
    foreach ($data as $k => $v) {
        $html = str_replace("%{$k}%", $v, $html);
    }

    if ($newpage)
        $html = explode("%NEWPAGE%", $html);
    return $html;
}

function mb_lcfirst($str)
{
    return mb_strtolower(mb_substr($str, 0, 1)) . mb_substr($str, 1);
}

function mb_ucfirst($str)
{
    return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
}

/** Замена названия факультета на институт */
function replaceFacultyOnInstitutions($institutions, $faculty)
{
    foreach ($institutions as $key => $inst) {
        foreach ($inst as $instFac) {
            if (mb_lcfirst($faculty) == mb_lcfirst($instFac)) {
                return mb_lcfirst($key);
            }
        }
    }

    return $faculty;
}

function splitcomp($comp)
{
    if ($comp["Код"] == "" || $comp["Наименование"] == "") {

        //$comp["Название"] = 'ОПКу-1 Способен выстраивать и реализовывать траекторию саморазвития в профессиональной сфере';

        //var_dump($comp["Название"]);

        //lemuria 16.01.2023
        preg_match('/^([А-Яа-яЁё]+[у]?-[\d]+) (.*?)$/', $comp["Название"], $matches);

        //var_dump($matches);
        //exit('rating');

        if (count($matches)) {
            $comp["Код"] = $matches[1];
            $comp["Наименование"] = $matches[2];
        } else {
            preg_match('/^([А-Яа-я]+ [\d]+) (.*?)$/', $comp["Название"], $matches);
            $comp["Код"] = empty($matches[1]) ? null : $matches[1];
            $comp["Наименование"] = isset($matches[2]) ? $matches[2] : "";
        }
    }
    //var_dump($comp["Название"]);
    return $comp;
}

/** Возвращает список должностей пользователя */
function getUserPositions($user): array
{

    $positions = [];
    if (is_string($user) || is_array($user)) {
        return $positions;
    }
    // Звание
    //if (isset($user->academic_degree)) {
    if (property_exists($user, "academic_degree")) {
        $positions['academic_degree'] = $user->academic_degree;
    }
    // должность
    //if (isset($user->academic_degree)) {
    if (property_exists($user, "academic_title")) {
        $positions['academic_title'] = $user->academic_title;
    }
    if (!empty($user->current_position)) {
        $positions['positions'][$user->current_position] = [
            'name' => $user->current_position, // ФИО
            'faculty' => $user->faculty->name, // Факультет
            'department' => $user->department->name, // Кафедра
        ];
    }
    if (!empty($user->positions_list)) {
        foreach ($user->positions_list as $item) {
            $positions['positions'][$item->current_position] = [
                'name' => $item->current_position,
                'faculty' => $item->faculty->name,
                'department' => $item->department->name,
            ];
        }
    }

    return $positions;
}

// возвращает актуальные данные должности
function getUserActualPositions($positions): array
{
    return [
        'academic_degree' => !empty($positions['academic_degree']) ? $positions['academic_degree'] : "",
        'academic_title' => !empty($positions['academic_title']) ? $positions['academic_title'] : "",
        'position_name' => $positions['current_position'],
        'department' => $positions['department']['name'],
        'faculty' => $positions['faculty']['name'],
    ];

    /*$dolghnost = [
        'Заведующий кафедрой',
        'Профессор',
        'Доцент',
        'Старший преподаватель',
        'Ассистент',
    ];

    foreach ($dolghnost as $item) {
        //if ($positions['positions'][$item]) {
        if (isset($positions['positions'][$item])) {
            return [
                'academic_degree' => !empty($positions['academic_degree']) ? $positions['academic_degree'] : "",
                'academic_title' => !empty($positions['academic_title']) ? $positions['academic_title'] : "",
                'position_name' => $positions['positions'][$item]['name'],
                'department' => $positions['positions'][$item]['department'],
                'faculty' => $positions['positions'][$item]['faculty'],
            ];
        }
    }

    return [];*/
}


/** Возвращает список должностей пользователя */
function getUserPositionsAll($user): array
{
    $positions = [];
    /*if (is_string($user) || is_array($user)) {
        return $positions;
    }*/
    $dolghnost = [
        'Заведующий кафедрой',
        'Профессор',
        'Доцент',
        'Старший преподаватель',
        'Преподаватель',
        'Ассистент',
    ];



   # if (!empty($user->current_position) && in_array($user->current_position, $dolghnost)) {
    if (!empty($user['current_position']) && in_array($user['current_position'], $dolghnost)) {
        $positions['positions'][] = [
            'name' => $user['current_position'], // ФИО
            'department' => $user['department']['name'],
            'faculty' => $user['faculty']['name'],
        ];
    }
    if (!empty($user['positions_list'])) {
    #if (!empty($user->positions_list)) {
        foreach ($user['positions_list'] as $item) {

            if ($item['type_of_employment'] != 'На условиях почасовой оплаты труда' && in_array($user['current_position'], $dolghnost)) {
                $positions['positions'][] = [
                    'name' => $item['current_position'],
                    'faculty' => $item['faculty']['name'],
                    'department' => $item['department']['name'],
                    'type_of_employment' => $item['type_of_employment'],
                ];
            }
        }
    }

    return $positions;
}


// возвращает актуальные данные должности
function getUserActualPositionsAll($positions)
{
    $dolghnost = [
        'Заведующий кафедрой',
        'Профессор',
        'Доцент',
        'Старший преподаватель',
        'Ассистент',
    ];

    $positions = [];

    foreach ($dolghnost as $item) {
        //if ($positions['positions'][$item]) {
        if (isset($positions['positions'][$item])) {
            $positions [] = [
                'academic_degree' => !empty($positions['academic_degree']) ? $positions['academic_degree'] : "",
                'academic_title' => !empty($positions['academic_title']) ? $positions['academic_title'] : "",
                'position_name' => $positions['positions'][$item]['name'],
                'department' => $positions['positions'][$item]['department'],
                'faculty' => $positions['positions'][$item]['faculty'],
            ];
        }
    }

    return $positions;
}

/**
 * @throws \tool_cdo_config\exceptions\cdo_type_response_exception
 * @throws coding_exception
 * @throws \tool_cdo_config\exceptions\cdo_config_exception
 * @throws invalid_parameter_exception
 */
function getTableDevelopers($developers)
{

    global $CFG;
    /*require_once($CFG->dirroot."/blocks/rpd/externallib.php");
    require_once($CFG->dirroot.'/CDO/handler.php');
    require_once($CFG->dirroot.'/ulsu/services.php');*/
    /*$serviceULSU = new ulsu_1c_services();*/

    $result = '';
    $count = 1;
    foreach ($developers as $developer) {
        #$pooos = getUserPositionsAll(json_decode($serviceULSU->GetUserInfo(['id' => $developer['id']])));
        $pooos = getUserPositionsAll((print_rpd::get_user_info($developer['id'])));
        if (!empty($_REQUEST['rg_test_rg'])) {

        }

        if ($count == 1) {
            $result .= '
            <tr style="text-align: center">
			    <td rowspan="' . (count($developers) + 1) . '"><br><br>Разработчик</td>
			  
			    <td><br>' . $developer['pos'] . ' ' . $developer['post'] . '</td>
			    <td><br>' . $developer['name'] . '</td>
            </tr>
            ';
        } else {
            $result .= '
                <tr style="text-align: center">
                    <td><br>' . $developer['pos'] . ' ' . $developer['post'] . '</td>
                    <td><br>' . $developer['name'] . '</td>
                </tr>
            ';
        }

        $count++;
    }

    $result .= '
            <tr style="text-align: center">
		    	
	    		<td><br>Должность, ученая степень, звание</td>
    			<td><br>ФИО</td>
            </tr>';

    return $result;
}
