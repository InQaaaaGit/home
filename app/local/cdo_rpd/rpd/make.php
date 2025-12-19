<?php

use local_cdo_rpd\services\print_rpd;
use local_cdo_rpd\services\rpd;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
set_time_limit(1000);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/functions.php';
global $CFG, $USER, $PAGE;

require_once($CFG->dirroot . '/local/cdo_rpd/rpd/services.php');

$type = required_param("type", PARAM_TEXT);


if ($type != 'zip') {
    require_once($CFG->dirroot . '/local/cdo_rpd/rpd/tcpdf.php');
    ob_start();
} else {

    require_login();
    require_once($CFG->libdir . '/pdflib.php');

}

$yummy_samo = array(
    "вопросы для обсуждения",
    "вопросы для самоподготовки",
    "вопросы к зачету",
    "домашнее задание",
    "задания к зачету",
    "задания на деловые игры",
    "задачи к экзамену",
    "кейс-задания",
    "задания к контрольным работам",
    "задание на курсовую работу (проект), типовые вопросы при защите кр (проекта)",
    "задания к лабораторным работам",
    "онлайн курс",
    "показы",
    "практические задачи (задания)",
    "прослушивания",
    "просмотры",
    "темы докладов",
    "темы рефератов",
    "тесты 3",
    "тесты 2",
    "тесты",
    "эссе",
);


$yummy_samo_l = array(
    "Вопросы для обсуждения",
    "Вопросы для самоподготовки",
    "Вопросы к зачету",
    "Домашнее задание",
    "Задания к зачету",
    "Задания на деловые игры",
    "Задачи к экзамену",
    "Кейс-задания",
    "Задания к контрольным работам",
    "Задание на курсовую работу (проект), типовые вопросы при защите кр (проекта)",
    "Задания к лабораторным работам",
    "Онлайн курс",
    "Показы",
    "Практические задачи (задания)",
    "Прослушивания",
    "Просмотры",
    "Темы докладов",
    "Темы рефератов",
    "Тесты 3",
    "Тесты 2",
    "Тесты",
    "Эссе"
);


$healthy_samo = array(
    "Устный опрос",
    "Устный опрос",
    "",
    "Оценивание выполнения задания",
    "",
    "Оценивание деловой игры",
    "",
    "Оценивание выполнения задани",
    "Оценивание контрольной работы",
    "",
    "Защита лабораторной работы",
    "",
    "Оценивание показа",
    "Проверка решения задачи (выполнения задания)",
    "Оценивание прослушивания",
    "Оценивание просмотра",
    "Оценивание доклада",
    "Оценивание реферата",
    "Тестирование",
    "Тестирование",
    "Тестирование",
    "Оценивание эссе"
);
//lemuria 10.10.2024


$yummy = array(
    "вопросы для обсуждения",
    "вопросы для самоподготовки",
    "вопросы к зачету",
    "вопросы к экзамену",
    "домашнее задание",
    "задания к зачету",
    "задания на деловые игры",
    "задачи к экзамену",
    "кейс-задания",
    "задания к контрольным работам",
    "задание на курсовую работу (проект), типовые вопросы при защите кр (проекта)",
    "задания к лабораторным работам",
    "онлайн курс",
    "показы",
    "практические задачи (задания)",
    "прослушивания",
    "просмотры",
    "темы докладов",
    "темы рефератов",
    "тесты 3",
    "тесты 2",
    "тесты",
    "эссе",
    "Вопросы к Экзамену"
);


$yummy_l = array(
    "Вопросы для обсуждения",
    "Вопросы для самоподготовки",
    "Вопросы к зачету",
    "Вопросы к экзамену",
    "Домашнее задание",
    "Задания к зачету",
    "Задания на деловые игры",
    "Задачи к экзамену",
    "Кейс-задания",
    "Задания к контрольным работам",
    "Задание на курсовую работу (проект), типовые вопросы при защите кр (проекта)",
    "Задания к лабораторным работам",
    "Онлайн курс",
    "Показы",
    "Практические задачи (задания)",
    "Прослушивания",
    "Просмотры",
    "Темы докладов",
    "Темы рефератов",
    "Тесты 3",
    "Тесты 2",
    "Тесты",
    "Эссе",
    "Вопросы к Экзамену"
);


$healthy = array(
    "Устный опрос",
    "Устный опрос",
    "",
    "",
    "Оценивание выполнения задания",
    "",
    "Оценивание деловой игры",
    "",
    "Оценивание выполнения задани",
    "Оценивание контрольной работы",
    "",
    "Защита лабораторной работы",
    "",
    "Оценивание показа",
    "Проверка решения задачи (выполнения задания)",
    "Оценивание прослушивания",
    "Оценивание просмотра",
    "Оценивание доклада",
    "Оценивание реферата",
    "Тестирование",
    "Тестирование",
    "Тестирование",
    "Оценивание эссе",
    ""
);


$healthy_itogo = array(
    "Устный опрос",
    "Устный опрос",
    "Зачет",
    "Экзамен",
    "Оценивание выполнения задания",
    "Зачет",
    "Оценивание деловой игры",
    "Экзамен",
    "Оценивание выполнения задани",
    "Оценивание контрольной работы",
    "Защита курсовой работы (проекта)",
    "Защита лабораторной работы",
    "",
    "Оценивание показа",
    "Проверка решения задачи (выполнения задания)",
    "Оценивание прослушивания",
    "Оценивание просмотра",
    "Оценивание доклада",
    "Оценивание реферата",
    "Тестирование",
    "Тестирование",
    "Тестирование",
    "Оценивание эссе"
);


if (!empty($_GET['user_id'])) {
    $user_id_rpd = $_GET['user_id'];


    $user_info = $DB->get_record('user', array('id' => $user_id_rpd));

    $user_current_info = $USER;

    //$USER = $user_info;

    $_REQUEST['user_id'] = $user_id_rpd;

}


$edu_plan = required_param("edu_plan", PARAM_TEXT);
$rpd_id = required_param("rpd_id", PARAM_TEXT);
$discipline = required_param("discipline", PARAM_TEXT);
$serviceRPD = new rpd();
#$serviceCDO = new exchange_services();
$serviceULSU = new ulsu_1c_services();


if (!empty($_GET['user_id'])) {

    //$USER = $user_current_info;
}

// Main - Платонова, sodev - Васильева. Иностранный|Биология клетки    rpd_id=000039268&edu_plan=000014306&discipline=000000016
// Main - Платонова, sodev - Васильева. Иностранный|Экология Б1.О      rpd_id=000039672&edu_plan=000013965&discipline=000000016
// Main - Платонова, sodev - Васильева. Проф ин яз|Им модел и анал дан rpd_id=000042738&edu_plan=000013966&discipline=000000498


$rpdSodevDisc = [
    'in_yan_biology' => [ // Иностранный|Биология клетки
        'edu_plan' => '000014306',
        'discipline' => '000000016',
        'rpd_id' => '000039268',
        'user_id' => 4399,
    ],
    'in_yan_ekology' => [ // Иностранный|Экология Б1.О
        'edu_plan' => '000013965',
        'discipline' => '000000016',
        'rpd_id' => '000039672',
        'user_id' => 4399,
    ],
    'in_yan_im_model' => [ // Проф ин яз|Им модел и анал дан
        'edu_plan' => '000042738',
        'discipline' => '000000498',
        'rpd_id' => '000042738',
        'user_id' => 4399,
    ],
];
$soDevDiscipline = null;
if ($rpd_id == '000039268') {
    $soDevDiscipline = 'in_yan_biology';
} elseif ($rpd_id == '000039672') {
    $soDevDiscipline = 'in_yan_ekology';
} elseif ($rpd_id == '000042738') {
    $soDevDiscipline = 'in_yan_im_model';
}

//lemuria
$guid = '';

if (!empty($_GET['guid'])) {
    $guid = $_GET['guid'];
}
//lemuria

$current_user_id = $USER->id;

$user_id_rpd = 0;

if (!empty($_GET['user_id'])) {
    $user_id_rpd = $_GET['user_id'];


    $user_info = $DB->get_record('user', array('id' => $user_id_rpd));

    //$USER = $user_info;

} else {

    $user_id_rpd = $USER->id;
}


#$rpd = $serviceRPD->get_RPDInfo($edu_plan, $discipline, $rpd_id, $user_id_rpd, $guid);
$rpd = json_decode(print_rpd::get_rpd_info($edu_plan, $discipline, $rpd_id, $user_id_rpd, $guid));

if (!empty($_GET['user_id'])) {

    //$USER = $user_current_info;
}

$developers = [];

if (isset($rpd->developers)) {

    foreach ($rpd->developers as $developer) {

        $developers[$developer->user_id] = $developer->guid;
    }

}
$rdp_another = [];

//lemuria check it 16.09.2024 rpd_another
if (count($developers) > 1) {

    $first = false;

    foreach ($developers as $user_id => $guid) {
        if ($first == true) {
            $rdp_another[$guid] = $serviceRPD->get_RPDInfo($edu_plan, $discipline, $rpd_id, $USER->id, $guid);
        } else {
            $rpd = $serviceRPD->get_RPDInfo($edu_plan, $discipline, $rpd_id, $USER->id, $guid);

            $first = true;
        }

    }

}

if (!empty($_GET['user_id'])) {
    $user_info = $DB->get_record('user', array('id' => $current_user_id));
    //$USER = $user_info;

}


$rpdSodev = [];
if ($soDevDiscipline != null) {
    $rpdSodev = $serviceRPD->get_RPDInfo(
        $rpdSodevDisc[$soDevDiscipline]['edu_plan'],
        $rpdSodevDisc[$soDevDiscipline]['discipline'],
        $rpdSodevDisc[$soDevDiscipline]['rpd_id'],
        $rpdSodevDisc[$soDevDiscipline]['user_id']
    );
}

$param = $_GET;
$param['rpd_id'] = $rpd_id;
$param['edu_plan'] = $edu_plan;
//$param['user_id'] = $USER->id;

$param['user_id'] = $user_id_rpd;
$param['discipline'] = $discipline;

#$plan = json_decode($serviceULSU->GetPlan($param), true);
$plan = json_decode(
    print_rpd::get_plan(
        $param['rpd_id'],
        $param['edu_plan'],
        $param['discipline'],
        $param['rpd_id']
    ),
    true
);


$params_list = [];
$params_list['rpd_id'] = $rpd_id;


#$plan_list = json_decode($serviceULSU->GetListPlans($params_list), true);
$plan_list = json_decode(print_rpd::get_list_plans($params_list['rpd_id']), true);


$is_exist_zaochka = false;


$is_exist_zaochka_ochka = false;


$edu_zaochka_id = '';

$edu_zaochka_ochka_id = '';


if (!empty($_REQUEST['rg_rg_rg'])) {


    //var_dump($plan_list);

    //exit('dfdf');
}


$if_exist_ochka = false;

if (!empty($plan_list)) {

    foreach ($plan_list as $plan_l) {

        if ($plan_l['form'] == 'заочная') {

            $edu_zaochka_id = $plan_l['plan_id'];
            $is_exist_zaochka = true;
            $param['edu_plan'] = $edu_zaochka_id;
            $plan_zaochka = json_decode($serviceULSU->GetPlan($param), true);
            break;
        }


        if ($plan_l['form'] == 'очно-заочная') {

            $edu_zaochka_ochka_id = $plan_l['plan_id'];
            $is_exist_zaochka_ochka = true;
            $param['edu_plan'] = $edu_zaochka_ochka_id;
            $plan_zaochka_ochka = json_decode($serviceULSU->GetPlan($param), true);
            break;
        }


    }
}


if (!empty($plan_list)) {

    foreach ($plan_list as $plan_l) {

        if ($plan_l['form'] == 'очная') {

            $if_exist_ochka = true;

            $edu_zaochka_id = $plan_l['plan_id'];
            $is_exist_zaochka = true;
            $param['edu_plan'] = $edu_zaochka_id;
            $plan_och = json_decode($serviceULSU->GetPlan($param), true);

            $plan = json_decode($serviceULSU->GetPlan($param), true);  //lemuria 16.09.2024


            if ($edu_plan != $edu_zaochka_id) {
                $plan_och = json_decode($serviceULSU->GetPlan($param), true);

                $plan = $plan_och;  //lemuria 16.09.2024
                break;
            } else {
                //$plan_och = false;   //lemuria 16.09.2024
            }

        }
    }
}


$sem2crs = [
    "Первый семестр" => 1,
    "Второй семестр" => 1,
    "Третий семестр" => 2,
    "Четвертый семестр" => 2,
    "Пятый семестр" => 3,
    "Шестой семестр" => 3,
    "Седьмой семестр" => 4,
    "Восьмой семестр" => 4,
    "Девятый семестр" => 5,
    "Десятый семестр" => 5,
    "Одиннадцатый семестр" => 6,
    "Двенадцатый семестр" => 6,
];

$sem2sem = [
    "Первый семестр" => 1,
    "Второй семестр" => 2,
    "Третий семестр" => 3,
    "Четвертый семестр" => 4,
    "Пятый семестр" => 5,
    "Шестой семестр" => 6,
    "Седьмой семестр" => 7,
    "Восьмой семестр" => 8,
    "Девятый семестр" => 9,
    "Десятый семестр" => 10,
    "Одиннадцатый семестр" => 11,
    "Двенадцатый семестр" => 12,
];


$sem2crs_zaoch = [
    "Первый семестр" => 1,
    "Второй семестр" => 1,
    "Третий семестр" => 2,
    "Четвертый семестр" => 2,
    "Пятый семестр" => 3,
    "Шестой семестр" => 3,
    "Седьмой семестр" => 4,
    "Восьмой семестр" => 4,
    "Девятый семестр" => 5,
    "Десятый семестр" => 5,
    "Одиннадцатый семестр" => 6,
    "Двенадцатый семестр" => 6,
];

$sem2sem_zaoch = [
    "Первый семестр" => 1,
    "Второй семестр" => 2,
    "Третий семестр" => 3,
    "Четвертый семестр" => 4,
    "Пятый семестр" => 5,
    "Шестой семестр" => 6,
    "Седьмой семестр" => 7,
    "Восьмой семестр" => 8,
    "Девятый семестр" => 9,
    "Десятый семестр" => 10,
    "Одиннадцатый семестр" => 11,
    "Двенадцатый семестр" => 12,
];

$institutions = [
    'Институт экономики и бизнеса' => [
        'Факультет управления',
        'Факультет экономики',
        'Бизнес-факультет',
        'Молодежная финансово-экономическая академия',
        'Молодежная школа финансового просвещения Ульяновского государственного университета',
    ],
    'Институт медицины, экологии и физической культуры' => [
        'Медицинский факультет им. Т.З. Биктимирова',
        'Факультет физической культуры и реабилитации',
        'Экологический факультет',
        'Факультет последипломного медицинского и фармацевтического образования',
        'Медицинский колледж им. А.Л.Поленова',
        'Отдел учебно-исследовательской работы',
        'Симуляционный центр медицинского моделирования',
        'Спортивный комплекс "Заря"',
        'Естественно-научный музей',
        'Виварий',
        'Многофункциональный центр реабилитации',
        'Учебный центр тактической медицины',
        'факультет стоматологии, фармации и последипломного медицинского образования'
    ],
    'Институт международных отношений' => [
        'Факультет лингвистики, межкультурных связей и профессиональной коммуникации',
        'Международный факультет',
        'Учебно-методический центр иностранных языков и профессионального развития "LinguaProfi"',
    ],
    'Институт открытого образования' => [
        'Экономическое отделение',
        'Юридическое отделение',
        'Современный открытый колледж «СОКОЛ»',
    ],
];

// Для генерации случайной строки
$permitted_chars = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz';

$pdisc = $plan["Дисциплины"][$rpd->info->discipline];


$courses = [];
$minsem = 100;

$chair = "";
$comps = [];
$precourses = [];
$postcourses = [];

$disc2comp = [];
$comp2disc = [];
$disc2sem = [];


$minsem_zaoch = 100;

$courses_zaoch = [];
$comps_zaoch = [];
$precourses_zaoch = [];
$postcourses_zaoch = [];

$disc2comp_zaoch = [];
$comp2disc_zaoch = [];
$disc2sem_zaoch = [];

$pdisc_zaoch = [];


//lemuria 11.10.2024


$minsem_zaoch_ochka = 100;

$courses_zaoch_ochka = [];
$comps_zaoch_ochka = [];
$precourses_zaoch_ochka = [];
$postcourses_zaoch_ochka = [];

$disc2comp_zaoch_ochka = [];
$comp2disc_zaoch_ochka = [];
$disc2sem_zaoch_ochka = [];

$pdisc_zaoch_ochka = [];


//11.10.2024 
if ($is_exist_zaochka_ochka && !empty($plan_zaochka_ochka)) {


    $pdisc_zaoch_ochka = $plan_zaochka_ochka["Дисциплины"][$rpd->info->discipline];


    foreach ($plan_zaochka_ochka["Дисциплины"] as $d => $v) {

        if (isset($v["Компетенции"])) {
            foreach ($v["Компетенции"] as $c) {
                if (!$c)
                    continue;
                if (!isset($disc2comp_zaoch_ochka[$d]))
                    $disc2comp_zaoch_ochka[$d] = [];
                $c = splitcomp($c);
                $disc2comp_zaoch_ochka[$d][] = $c["Код"];

                if (!isset($comp2disc_zaoch_ochka[$c["Код"]]))
                    $comp2disc_zaoch_ochka[$c["Код"]] = [];
                $comp2disc_zaoch_ochka[$c["Код"]][] = $d;
            }
        }
        foreach ($v["Записи"] as $v1) {
            if (!isset($disc2sem_zaoch_ochka[$d]))
                $disc2sem_zaoch_ochka[$d] = [];
            $disc2sem_zaoch_ochka[$d][$sem2sem_zaoch[$v1["ПериодКонтроля"]]] = true;
            if ($sem2sem_zaoch[$v1["ПериодКонтроля"]] < $minsem_zaoch_ochka)
                $minsem_zaoch_ochka = $sem2sem_zaoch[$v1["ПериодКонтроля"]];
        }
    }
}


if ($is_exist_zaochka && !empty($plan_zaochka)) {


    $pdisc_zaoch = $plan_zaochka["Дисциплины"][$rpd->info->discipline];


    foreach ($plan_zaochka["Дисциплины"] as $d => $v) {
        //lemuria
        if (isset($v["Компетенции"])) {
            foreach ($v["Компетенции"] as $c) {
                if (!$c)
                    continue;
                if (!isset($disc2comp_zaoch[$d]))
                    $disc2comp_zaoch[$d] = [];
                $c = splitcomp($c);
                $disc2comp_zaoch[$d][] = $c["Код"];

                if (!isset($comp2disc_zaoch[$c["Код"]]))
                    $comp2disc_zaoch[$c["Код"]] = [];
                $comp2disc_zaoch[$c["Код"]][] = $d;
            }
        }
        foreach ($v["Записи"] as $v1) {
            if (!isset($disc2sem_zaoch[$d]))
                $disc2sem_zaoch[$d] = [];
            $disc2sem_zaoch[$d][$sem2sem_zaoch[$v1["ПериодКонтроля"]]] = true;
            if ($sem2sem_zaoch[$v1["ПериодКонтроля"]] < $minsem_zaoch)
                $minsem_zaoch = $sem2sem_zaoch[$v1["ПериодКонтроля"]];
        }
    }
}


foreach ($plan["Дисциплины"] as $d => $v) {

    //lemuria
    if (isset($v["Компетенции"])) {
        foreach ($v["Компетенции"] as $c) {
            if (!$c)
                continue;
            if (!isset($disc2comp[$d]))
                $disc2comp[$d] = [];
            $c = splitcomp($c);
            $disc2comp[$d][] = $c["Код"];

            if (!isset($comp2disc[$c["Код"]]))
                $comp2disc[$c["Код"]] = [];
            $comp2disc[$c["Код"]][] = $d;
        }
    }
    foreach ($v["Записи"] as $v1) {
        if (!isset($disc2sem[$d]))
            $disc2sem[$d] = [];
        $disc2sem[$d][$sem2sem[$v1["ПериодКонтроля"]]] = true;
        if ($sem2sem[$v1["ПериодКонтроля"]] < $minsem)
            $minsem = $sem2sem[$v1["ПериодКонтроля"]];
    }
}

foreach ($pdisc["Записи"] as $pp) {
    if ($pp["Подразделение"] && !$chair)
        $chair = $pp["Подразделение"];
    $courses[$sem2crs[$pp["ПериодКонтроля"]]] = true;
}


if (!empty($pdisc_zaoch["Записи"])) {

    foreach ($pdisc["Записи"] as $pp) {

        $courses_zaoch[$sem2crs[$pp["ПериодКонтроля"]]] = true;
    }
}


if (!empty($pdisc_zaoch_ochka["Записи"])) {


    foreach ($pdisc_zaoch_ochka["Записи"] as $pp) {

        $courses_zaoch_ochka[$sem2crs[$pp["ПериодКонтроля"]]] = true;
    }
}


if (!empty($plan_och)) {


    $pdisc__2 = $plan_och["Дисциплины"][$rpd->info->discipline];

    foreach ($pdisc__2["Записи"] as $pp) {
        if ($pp["Подразделение"]) {
            $chair = $pp["Подразделение"];
        }

    }

}


/*
foreach ($rpd->competencies as $pp) {
    //preg_match('/^([^ ]+) (.*?)$/', $pp->title, $matches);



    //preg_match('/^([А-Яа-я\]{1,5}\-?\s[\d]+)\s(.*?)$/si', $pp->title, $matches);

    preg_match('/^([^ ]+) (.*?)$/', $pp->title, $matches);


    $save__ = $matches[1];
    $comps[] = $matches[1];

    if ( count($matches) > 2) {
        preg_match('/^([А-Яа-я]{1,4}\s[\d]+)\s(.*?)$/', $pp->title, $matches);



        if(count($matches) ==2 ){
            //$comps[] = $matches[1];
        }
        else{
            //$comps[] = $save__;
        }
    }
    else{
        //$comps[] = $matches[1];
    }
}
*/

foreach ($rpd->competencies as $pp) {


    $short_title = $pp->short_code;

    if (!empty($short_title)) {
        $comps[] = $pp->short_code;
    } else {
        preg_match('/^([^ ]+) (.*?)$/', $pp->title, $matches);


        $save__ = $matches[1];
        $comps[] = $matches[1];
    }

}


$global_current_comps = $comps;

foreach ($comps as $comp) {
    foreach ($comp2disc[$comp] as $disc) {
        $flag = false;
        $flag1 = false;

        foreach ($disc2sem[$disc] as $d => $v) {
            if ($d <= $minsem) {
                $flag = true;
            }
            if ($d > $minsem) {
                $flag1 = true;
            }
        }

        //10.10.2024
        if ($rpd->info->discipline == $disc) {
            continue;
        }

        if ($flag && !in_array($disc, $precourses))
            $precourses[] = $disc;

        if ($flag1 && !in_array($disc, $postcourses))
            $postcourses[] = $disc;
    }
}


$developers = "";
$devssign = "";
$developersPost = [];

// Главные разработчики
$newChair = [];

/*
foreach ($rpd->info->developers->mainDeveloper as $dev) {
    $positions = getUserActualPositions(getUserPositions(json_decode($serviceULSU->GetUserInfo(['id' => $dev->id]))));
    $f = [];
    if (!empty($positions['position_name'])) {
        $f[] = $positions['position_name'];
    }
    if (!empty($positions['academic_degree'])) {
        $f[] = $positions['academic_degree'];
    }
    if (!empty($positions['academic_title'])) {
        $f[] = $positions['academic_title'];
    }

    $newChair[] = $positions['department'];
    $f = implode(", ", $f);
    $developers .= "<tr><td>{$dev->user}</td><td>{$positions['department']}</td><td>{$f}</td></tr>";
    $devssign .= "{$dev->user}<br>";

    $developersPost[] = [
        'name' => $dev->user,
        'post' => $f,
    ];
}
*/
foreach ($rpd->info->developers->mainDeveloper as $dev) {
    $prof_info = print_rpd::get_user_info($dev->id);
    $positions = getUserActualPositions($prof_info);
    #$positions = getUserActualPositions(getUserPositions(json_decode($serviceULSU->GetUserInfo(['id' => $dev->id]))));
   # $positions = getUserActualPositions(getUserPositions(((object)$prof_info)));
    # $positions = getUserActualPositions(getUserPositions((print_rpd::get_user_info($dev->id))));

    #$pooos = getUserPositionsAll(json_decode($serviceULSU->GetUserInfo(['id' => $dev->id])));
    $pooos = getUserPositionsAll($prof_info);
    #$pooos = ($prof_info);

    $f = [];


    if (!empty($positions['position_name'])) {
        //$f[] = $positions['position_name'];
    }
    if (!empty($positions['academic_degree'])) {
        $f[] = $positions['academic_degree'];
    }
    if (!empty($positions['academic_title'])) {
        $f[] = $positions['academic_title'];
    }

    $newChair[] = $positions['department'];


    if (empty($f)) {
        $f = '';
    } else {
        $f = implode(", ", $f);

    }
    $count_p_string = "";
    $first = true;
    if (!array_key_exists('positions', $pooos)) {
        $count_p = 0;
    } else {
        $count_p = count($pooos['positions'] ?? 0);
        $current_pos_d = [];
        foreach ($pooos['positions'] as $pos) {

            $name_dev = $dev->user;

            if (!$first) {
                $name_dev = '';
            }


            if (!empty($current_pos_) && in_array($pos['department'] . $f, $current_pos_)) {
                continue;
            }

            $fd_s = '';

            if (!empty($f)) {

                $fd_s .= ',' . $f;
            }

            if ($count_p > 1) {
                if ($count_p > 1 && $first) {
                    $developers .= "<tr><td {$count_p_string}>{$name_dev}</td><td>{$pos['department']}</td><td>{$pos['name']}{$fd_s}</td></tr>";
                } elseif (!$first) {
                    $developers .= "<tr><td>{$pos['department']}</td><td>{$pos['name']}{$fd_s}</td></tr>";

                }

            } else {
                $developers .= "<tr><td>{$name_dev}</td><td>{$pos['department']}</td><td>{$pos['name']}{$fd_s}</td></tr>";
            }

            $current_pos_[] = $pos['department'] . $f;

            $devssign .= "{$dev->user}<br>";
            $first = false;
        }

    }

    if ($count_p < 2) {
        $count_p_string = '';
    } else {
        $count_p_string = 'rowspan="' . $count_p . '"';
    }


    $developersPost[] = [
        'name' => $dev->user,
        'post' => $f,
        'id' => $dev->id,
        'pos' => $pos['name'],
    ];
}


// Соразработчики
foreach ($rpd->info->developers->coDevelopers as $dev) {
    $positions = getUserActualPositions(getUserPositions(json_decode($serviceULSU->GetUserInfo(['id' => $dev->id]))));
    $f = [];
    if (!empty($positions['position_name'])) {
        $f[] = $positions['position_name'];
    }
    if (!empty($positions['academic_degree'])) {
        $f[] = $positions['academic_degree'];
    }
    if (!empty($positions['academic_title'])) {
        $f[] = $positions['academic_title'];
    }

    $newChair[] = $positions['department'];
    $f = implode(", ", $f);


    $pooos = getUserPositionsAll(json_decode($serviceULSU->GetUserInfo(['id' => $dev->id])));

    // $developers .= "<tr><td>{$dev->user}</td><td>{$positions['department']}</td><td>{$f}</td></tr>";
    // $devssign .= "{$dev->user}<br>";

    $first = true;

    $count_p = count($pooos['positions']);
    if ($count_p < 2) {
        $count_p = '';
    } else {
        $count_p = 'rowspan="' . $count_p . '"';
    }


    $current_pos_d = [];
    foreach ($pooos['positions'] as $pos) {

        $name_dev = $dev->user;

        if (!$first) {
            $name_dev = '';
        }


        if (!empty($current_pos_) && in_array($pos['department'] . $f, $current_pos_)) {
            continue;
        }

        if ($count_p > 1) {
            if ($count_p > 1 && $first) {
                $developers .= "<tr><td {$count_p}>{$name_dev}</td><td>{$pos['department']}</td><td>{$f}</td></tr>";
            } elseif (!$first) {
                $developers .= "<tr><td>{$pos['department']}</td><td>{$f}</td></tr>";

            }

        } else {
            $developers .= "<tr><td>{$name_dev}</td><td>{$pos['department']}</td><td>{$f}</td></tr>";
        }

        $current_pos_[] = $pos['department'] . $f;

        $devssign .= "{$dev->user}<br>";
        $first = false;
    }

    $developersPost[] = [
        'name' => $dev->user,
        'post' => $f,
        'id' => $dev->id,
    ];
}

$faculty = $facultyApproval = $plan["Факультет"];

$param = array();
$param['chair_name'] = str_replace(" ", "+", $chair);
#$chairInfo = json_decode($serviceULSU->GetChairInfo($param), true);
$chairInfo = print_rpd::get_chair_info($param['chair_name']);

if ($chairInfo['Подразделение'] != "")
    $faculty = $chairInfo['Подразделение'];

$style = file_get_contents('templates/style.html');
$controls = [];

if (!empty($rpd->controls[0]->enroleTypes)) {   // зачет
    $controls[] = $rpd->controls[0]->enrole;
}
if (!empty($rpd->controls[1]->enroleTypes)) {   // эказмен
    $controls[] = $rpd->controls[1]->enrole;
}
ksort($courses);
ksort($courses_zaoch);
ksort($courses_zaoch_ochka);


$course_title = '';


if ($if_exist_ochka == true) {

    if (!empty($courses)) {
        $course_title = implode(", ", array_keys($courses)) . ' - очная форма обучения';
    }

}

if (!empty($courses_zaoch)) {

    if ($if_exist_ochka == true) {

        $course_title .= '; ' . implode(", ", array_keys($courses_zaoch)) . ' - заочная форма обучения';

    } else {
        $course_title .= implode(", ", array_keys($courses_zaoch)) . ' - заочная форма обучения';

    }
}

if (!empty($courses_zaoch_ochka)) {

    if ($if_exist_ochka == true || !empty($courses_zaoch)) {

        $course_title .= '; ' . implode(", ", array_keys($courses_zaoch_ochka)) . ' - очно-заочная форма обучения';

    } else {
        $course_title .= implode(", ", array_keys($courses_zaoch_ochka)) . ' - очно-заочная форма обучения';

    }
}


$sorted_comps = [];
foreach ($comps as $key__s => $numberss) {
    $numbers = explode('-', $numberss);
    if (count($numbers) == 2) {
        $sorted_comps[$key__s] = $numbers[1];

    } else {
        $sorted_comps[$key__s] = 0;
    }
}

$new_comps = [];


asort($sorted_comps);

foreach ($sorted_comps as $key__s => $numberss) {
    $new_comps[] = $comps[$key__s];
}


$comps = $new_comps;

//lemuria 16.01
$opk_data = [];
$pk_data = [];
$yk_data = [];
$another_data = [];


foreach ($comps as $_c_key => $c_data) {


    $first_letter = mb_substr($c_data, 0, 1);

    $first_letter = mb_strtolower($first_letter);


    if ($first_letter == 'у') {
        $yk_data[] = $c_data;
    } elseif ($first_letter == 'о') {

        $opk_data[] = $c_data;
    } elseif ($first_letter == 'п') {
        $pk_data[] = $c_data;
    } else {
        $another_data[] = $c_data;
    }
}


$sorted_rpd_comps = [];
$sorted_rpd_comps = $yk_data;
$sorted_rpd_comps = array_merge($sorted_rpd_comps, $opk_data);
$sorted_rpd_comps = array_merge($sorted_rpd_comps, $pk_data);
$sorted_rpd_comps = array_merge($sorted_rpd_comps, $another_data);

//lemuria 16.01


$data = [
    'DISCIPLINE' => $rpd->info->discipline,
    'DIRECTION' => $rpd->info->direction,
    'CONTROL' => implode(', ', $controls),
    'TARGETS' => $rpd->part1->target,
    'TASKS' => $rpd->part1->taskfordisc,
    'PROFILE' => $rpd->info->profile,
    'STARTYEAR' => $rpd->info->year,
    'DEVELOPERS' => $developers,
    'DEVSSIGN' => $devssign,
    'DBLOCK' => $rpd->info->type,
    'QUESTIONS' => '',
    'LIB1' => '',
    'LIB2' => '',
    'LIB3' => '',
    'MTO_INVENTORY' => '',
    'MTO_SOFTWARE' => '',
    'CREDCRIT' => '',
    'FOS41CRIT' => isset($rpd->criteriaList->tests) ? $rpd->criteriaList->tests : "",
    'FOS45CRIT' => isset($rpd->criteriaList->esse) ? $rpd->criteriaList->esse : "",
    'FOS48CRIT' => isset($rpd->criteriaList->credit_question) ? $rpd->criteriaList->credit_question : "",
    'FOS481CRIT' => isset($rpd->criteriaList->credit_assign) ? $rpd->criteriaList->credit_assign : "",
    'LABCRIT' => isset($rpd->criteriaList->lab_work) ? $rpd->criteriaList->lab_work : "",
    //'CHAIR' => implode(', ', $newChair),
    'CHAIR' => $chair,
    'FACULTY' => str_replace('институт', 'института', str_replace('факультет', 'факультета', replaceFacultyOnInstitutions($institutions, $facultyApproval))),
    'FACULTY_TABLE' => mb_ucfirst($faculty),
    'COURSE' => $course_title,
    'COMPS' => implode(", ", $sorted_rpd_comps),
    'PRECOURSES' => implode(", ", $precourses),
    'POSTCOURSES' => implode(", ", $postcourses),
    'CRITERIA_LIST' => '',
    'TABLE_DEVELOPERS' => getTableDevelopers($developersPost),
];


$data['FACULTY'] = str_replace("инженерно-физический", "инженерно-физического", $data['FACULTY']);

//lemuria
if (!empty($rdp_another)) {

    foreach ($rdp_another as $rpd_another_data) {
        if (!empty($rpd_another_data->part1->target)) {
            $data['TARGETS'] .= $rpd_another_data->part1->target;
        }


        if (!empty($rpd_another_data->part1->target)) {
            $data['TASKS'] .= $rpd_another_data->part1->taskfordisc;
        }
    }
}


$criteriaList = require(__DIR__ . '/_criteriaList.php');
require(__DIR__ . '/_criteriaListByTopic.php');

$criteriaListTable42 = [];
foreach ($rpd->questionsForAllThemes as $questions) {
    $existQuestion = false;
    foreach ($questions as $question) {
        if (!empty($question->questionDescription)) {
            $existQuestion = true;
            break;
        }
    }

    if (key_exists($questions->code, $criteriaList) && !empty($questions->questions)) {  //lemuria  && !empty($questions)
        $criteriaListTable42[$questions->code] = $criteriaList[$questions->code];
    }
}


if (!empty($rdp_another)) {
    foreach ($rdp_another as $rpd_another_data) {
        foreach ($rpd_another_data->questionsForAllThemes as $questions) {
            if (key_exists($questions->code, $criteriaList) && !empty($questions->questions)) {  //lemuria  && !empty($questions)
                $criteriaListTable42[$questions->code] = $criteriaList[$questions->code];
            }
        }
    }
}


$data['CRITERIA_LIST'] = implode(', ', $criteriaListTable42);


if ($type == 'zip') {

//взять архив согласования.


    $file_name_for_save_aanotation = $CFG->dirroot . '/ulsu/rpd/annotation.pdf';
    $file_name_for_save_rpd = $CFG->dirroot . '/ulsu/rpd/rpd.pdf';
    $file_name_for_save_fos = $CFG->dirroot . '/ulsu/rpd/fos.pdf';

    if (file_exists($file_name_for_save_aanotation)) {
        unlink($file_name_for_save_aanotation);
    }

    if (file_exists($file_name_for_save_rpd)) {
        unlink($file_name_for_save_rpd);
    }

    if (file_exists($file_name_for_save_fos)) {
        unlink($file_name_for_save_fos);
    }


    if (file_exists($CFG->dirroot . '/ulsu/rpd/agreement.pdf')) {
        unlink($CFG->dirroot . '/ulsu/rpd/agreement.pdf');
    }


    $url_string = $CFG->wwwroot . '/ulsu/rpd/make.php?type=annotation&user_id=' . $_REQUEST['user_id'] . '&subtype=zip&rpd_id=' . $_GET['rpd_id'] . '&discipline=' . $_GET['discipline'] . '&edu_plan=' . $_GET['edu_plan'];

    $ch = curl_init($url_string);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $return = curl_exec($ch);
    $errors = curl_error($ch);
    curl_close($ch);


    $url_string = $CFG->wwwroot . '/ulsu/rpd/make.php?type=rpd&user_id=' . $_REQUEST['user_id'] . '&subtype=zip&rpd_id=' . $_GET['rpd_id'] . '&discipline=' . $_GET['discipline'] . '&edu_plan=' . $_GET['edu_plan'];

    $ch = curl_init($url_string);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $return = curl_exec($ch);
    $errors = curl_error($ch);
    curl_close($ch);


    $url_string = $CFG->wwwroot . '/ulsu/rpd/make.php?type=fos&user_id=' . $_REQUEST['user_id'] . '&subtype=zip&rpd_id=' . $_GET['rpd_id'] . '&discipline=' . $_GET['discipline'] . '&edu_plan=' . $_GET['edu_plan'];

    $ch = curl_init($url_string);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $return = curl_exec($ch);
    $errors = curl_error($ch);
    curl_close($ch);


    $file_zip = $CFG->dirroot . '/ulsu/rpd/archive.zip';

    if (file_exists($file_zip)) {
        // unlink($file_zip);
    }


    //$user_info = $DB->get_record('user', array('id'=>$current_user_id));
    //$USER = $user_info;

    //$zip = new ZipArchive();
    //$zip->open($CFG->dirroot.'/ulsu/rpd/archive.zip',  ZipArchive::CREATE);

    require_once($CFG->dirroot . '/blocks/rpd/classes/services/pdf_rpd.php');

    $pdf_ob = new \block_rpd\services\pdf_rpd();
    $pdf_rpd = $pdf_ob->create_agreement_form($_GET['rpd_id']);


    $pdf_rpd->Output($CFG->dirroot . '/ulsu/rpd/agreement.pdf', 'F');


    $files_name = substr($rpd->info->discipline, 0, 40) . '_' . $rpd->info->type . '_' . substr($rpd->info->profile, 0, 20) . '_' . $rpd->info->year;


    if (file_exists($CFG->wwwroot . '/ulsu/rpd/' . ag_at_translit($files_name) . '.zip')) {
        unlink($CFG->wwwroot . '/ulsu/rpd/' . ag_at_translit($files_name) . '.zip');
    }


    $zip = new ZipArchive();
    $zip->open($CFG->dirroot . '/ulsu/rpd/' . ag_at_translit($files_name) . '.zip', ZipArchive::CREATE);


    $zip->addFile($file_name_for_save_aanotation, "ANNOTATION_" . ag_at_translit($files_name) . ".pdf");
    $zip->addFile($file_name_for_save_rpd, "RPD_" . ag_at_translit($files_name) . ".pdf");
    $zip->addFile($file_name_for_save_fos, "FOS_" . ag_at_translit($files_name) . ".pdf");

    $zip->addFile($CFG->dirroot . '/ulsu/rpd/agreement.pdf', "SOGLASOVANIE_" . ag_at_translit($files_name) . ".pdf");

    //взять архив согласования.


    $zip->close();


    $file_name_download = $CFG->wwwroot . '/ulsu/rpd/' . ag_at_translit($files_name) . '.zip';

    $file_name_customer = ag_at_translit($files_name) . '.zip';

    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=$file_name_customer");
    header("Content-length: " . filesize($file_name_download));
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile($file_name_download);
    unlink($file_name_download);
    exit();

}

function ag_at_translit($st)
{
    $st = mb_strtolower($st, "utf-8");
    $st = str_replace([
        '?', '!', '.', ',', ':', ';', '*', '(', ')', '{', '}', '[', ']', '%', '#', '№', '@', '$', '^', '-', '+', '/', '\\', '=', '|', '"', '\'',
        'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'з', 'и', 'й', 'к',
        'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х',
        'ъ', 'ы', 'э', ' ', 'ж', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я'
    ], [
        '_', '_', '.', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_',
        'a', 'b', 'v', 'g', 'd', 'e', 'e', 'z', 'i', 'y', 'k',
        'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h',
        'j', 'i', 'e', '_', 'zh', 'ts', 'ch', 'sh', 'shch',
        '', 'yu', 'ya'
    ], $st);
    $st = preg_replace("/[^a-z0-9_.]/", "", $st);
    $st = trim($st, '_');

    $prev_st = '';
    do {
        $prev_st = $st;
        $st = preg_replace("/_[a-z0-9]_/", "_", $st);
    } while ($st != $prev_st);

    $st = preg_replace("/_{2,}/", "_", $st);
    return $st;
}


foreach ($rpd->competencies as $pp) {
    preg_match('/^([^ ]+) (.*?)$/', $pp->title, $matches);
    //$comps[] = $matches[1];
}


//lemuria 16.01.2023
function fn_ar_gg_sort_detailed_competences($comps)
{

    $current_number_comps = "";
    $sorted_comps = [];
    foreach ($comps as $key__s => $numberss) {

        $good_string = $numberss->title;

        preg_match('/^([^ ]+) (.*?)$/', $good_string, $matches);

        $short_code_comp = $numberss->short_code;

        //lemuria 22.09.2024

        if (!empty($short_code_comp)) {
            $current_number_comps = explode('-', $current_number_comps);
            if (count($current_number_comps) == 2) {
                $current_number_comps = $current_number_comps[1];
            } else {
                $current_number_comps = 10000;
            }
            $sorted_comps[] = array('sort_key' => $current_number_comps, 'data' => $numberss);
        } else {

            if (isset($matches[1])) {
                $current_number_comps = $matches[1];
                $current_number_comps = explode('-', $current_number_comps);
                if (count($current_number_comps) == 2) {
                    $current_number_comps = $current_number_comps[1];
                } else {
                    $current_number_comps = 10000;
                }
                $sorted_comps[] = array('sort_key' => $current_number_comps, 'data' => $numberss);
            } else {

                $sorted_comps[] = array('sort_key' => 10000, 'data' => $numberss);
            }
        }


    }


    usort($sorted_comps, "fn_ag_gg_key_sort_own_comps");


    $new_comps = [];


    foreach ($sorted_comps as $key__s => $numberss) {
        $new_comps[] = $numberss['data'];
    }

    return $new_comps;

}


function fn_ag_gg_key_sort_own_comps($a, $b)
{


    if ($a['sort_key'] == $b['sort_key']) {
        return 0;
    }
    return ($a['sort_key'] < $b['sort_key']) ? -1 : 1;

}


function fn_ar_gg_sort_competences($rpd)
{

    $new_sorted_rpd_table_comps = [];

    $opk_data_comps = [];
    $pk_data_comps = [];
    $yk_data_comps = [];
    $another_data_comps = [];


    foreach ($rpd->competencies as $_c_key => $c_data) {

        $title = $c_data->title;
        $title = trim($title);

        $first_letter = mb_substr($c_data->title, 0, 1);
        $first_letter = mb_strtolower($first_letter);


        if ($first_letter == 'у') {
            $yk_data_comps[] = $c_data;
        } elseif ($first_letter == 'о') {

            $opk_data_comps[] = $c_data;
        } elseif ($first_letter == 'п') {
            $pk_data_comps[] = $c_data;
        } else {
            $another_data_comps[] = $c_data;
        }
    }


    $yk_data_comps = fn_ar_gg_sort_detailed_competences($yk_data_comps);
    $opk_data_comps = fn_ar_gg_sort_detailed_competences($opk_data_comps);
    $pk_data_comps = fn_ar_gg_sort_detailed_competences($pk_data_comps);
    $another_data_comps = fn_ar_gg_sort_detailed_competences($another_data_comps);


    //$sorted_rpd_comps =    [];
    $new_sorted_rpd_table_comps = $yk_data_comps;
    $new_sorted_rpd_table_comps = array_merge($new_sorted_rpd_table_comps, $opk_data_comps);
    $new_sorted_rpd_table_comps = array_merge($new_sorted_rpd_table_comps, $pk_data_comps);
    $new_sorted_rpd_table_comps = array_merge($new_sorted_rpd_table_comps, $another_data_comps);

    $rpd->competencies = $new_sorted_rpd_table_comps;

    return $rpd;

}

//lemuria 16.01.2023

if ($type == 'annotation') {
    $data['TYPE'] = 'Аннотация рабочей программы дисциплины';
    $cmps = "<ul>";


    $rpd = fn_ar_gg_sort_competences($rpd);


    foreach ($rpd->competencies as $c) {
        $c = splitcomp(["Код" => "", "Наименование" => "", "Название" => $c->title]);


        $cmps .= "<li>{$c['Наименование']} ({$c['Код']})</li>";
    }


    $cmps .= "</ul>";
    $data['ACOMPS'] = $cmps;


    if (!empty($_REQUEST['rg_comp'])) {


    } else {


        $data['ANN31'] = "<ul style=\"white-space: pre\">";
        $data['ANN32'] = "<ul style=\"white-space: pre\">";
        $data['ANN33'] = "<ul style=\"white-space: pre\">";

        foreach ($rpd->competencies as $comp) {
            $data['ANN31'] .= $comp->requirement->know . "<br>";
            $data['ANN32'] .= $comp->requirement->beAbleTo . "<br>";
            $data['ANN33'] .= $comp->requirement->own . "<br>";

        }
        $data['ANN31'] .= "</ul>";
        $data['ANN32'] .= "</ul>";
        $data['ANN33'] .= "</ul>";


    }


    //lemuria
    if (!empty($rdp_another)) {

        foreach ($rdp_another as $rpd_another_data) {

            if (!empty($rpd_another_data->competencies)) {

                $cmps = "<ul>";

                foreach ($rpd_another_data->competencies as $c) {
                    $c = splitcomp(["Код" => "", "Наименование" => "", "Название" => $c->title]);
                    $cmps .= "<li>{$c['Наименование']} ({$c['Код']})</li>";
                }

                $cmps .= "</ul>";
                $data['ACOMPS'] .= $cmps;
                $data['ANN31'] .= "<ul style=\"white-space: pre\">";
                $data['ANN32'] .= "<ul style=\"white-space: pre\">";
                $data['ANN33'] .= "<ul style=\"white-space: pre\">";

                foreach ($rpd_another_data->competencies as $comp) {
                    $data['ANN31'] .= $comp->requirement->know . "<br>";
                    $data['ANN32'] .= $comp->requirement->beAbleTo . "<br>";
                    $data['ANN33'] .= $comp->requirement->own . "<br>";

                }
                $data['ANN31'] .= "</ul>";
                $data['ANN32'] .= "</ul>";
                $data['ANN33'] .= "</ul>";
            }
        }
    }


    $rpd->auditWork = preg_replace('#data:image\/[^;]+;base64,#', '@', $rpd->auditWork);
    $rpd->outwork = preg_replace('#data:image\/[^;]+;base64,#', '@', $rpd->outwork);
    $data['ANN51'] .= $rpd->auditWork;
    $data['ANN52'] .= $rpd->outwork;

} else if ($type == 'fos') {
    $data['TYPE'] = 'Фонд оценочных средств (ФОС)';


//another rpd is existQuestion


    if (!empty($rdp_another)) {


        foreach ($rdp_another as $rpd_another_data) {

            foreach ($rpd_another_data->competencies as $pp) {
                preg_match('/^([^ ]+) (.*?)$/', $pp->title, $matches);
                $comps[] = $matches[1];
            }
        }


    }


    foreach ($comps as $iii_i => $comp) {

        if (is_null($comp)) {
            unset($comps[$iii_i]);
        }

    }


    $cw = (48 / count($comps)) . "%";

    $cmps = "";


    foreach ($comps as $comp) {
        $cmps .= "<td width=\"{$cw}\">{$comp}</td>";
    }


    $data['FOS1'] = '<table><thead><tr style="font-weight: bold; background-color: lightgray;"><td width="12%" rowspan="2">№ семестра</td><td width="40%" rowspan="2">Наименование дисциплины (модуля) или практики</td><td width="48%" colspan="3">Индекс компетенции</td></tr><tr style="font-weight: bold; background-color: lightgray;">' . $cmps . '</tr></thead><tbody>';
    $semsSort = [];
    foreach ($plan["Дисциплины"] as $d => $v) {
        $cmps = [];
        foreach ($v["Компетенции"] as $c) {
            $c = splitcomp($c);
            $cmps[] = $c["Код"];
        }
        $flag = false;
        $cms = "";


        foreach ($comps as $comp) {
            if (in_array($comp, $cmps)) {
                $cms .= "<td width=\"{$cw}\">+</td>";
                $flag = true;
            } else {
                $cms .= "<td width=\"{$cw}\">-</td>";
            }
        }
        ksort($disc2sem[$d]);
        $sems = implode(', ', array_keys($disc2sem[$d]));
        if ($flag) {
            // ключ массива такой, что бы сортировать можно было
            $semsSort[$sems . '  ' . substr(str_shuffle($permitted_chars), 0, 5)] = [$sems, $d, $cms];
        }
    }


    if (!empty($_REQUEST['rg_rg_rg'])) {
        //var_dump($semsSort);
    }


    ksort($semsSort);


    if (!empty($_REQUEST['rg_rg_rg'])) {
        //var_dump($semsSort);

        //exit('dfdfdfdf');
    }


    function rg_sort_sup_cmp($a, $b)
    {
        //return strcmp($a["0"], $b["0"]);

        if (intval($a['0']) == intval($b['0'])) {
            return 0;
        }
        return (intval($a['0']) < intval($b['0'])) ? -1 : 1;
    }


    $fos_sort_table = $semsSort;


    usort($fos_sort_table, "rg_sort_sup_cmp");


    foreach ($fos_sort_table as $item) {
        $data['FOS1'] .= "<tr><td width=\"12%\">{$item[0]}</td><td width=\"40%\">{$item[1]}</td>{$item[2]}</tr>";
    }

    $data['FOS1'] .= "</tbody></table>";


    $qind = 1;

    $data['FOS2'] = "";

    $cind = 1;


    $rpd = fn_ar_gg_sort_competences($rpd);

    foreach ($rpd->competencies as $comp) {


        if (empty($comp->requirement->own) && empty($comp->requirement->know) && empty($comp->requirement->beAbleTo) && !empty($rdp_another)) {

            continue;
        }

        $shorrt_code = $comp->short_code;
        preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);

        if (!empty($shorrt_code)) {

            $matches[1] = $shorrt_code;
        }


        $data['FOS2'] .= "<tr><td width=\"10%\">{$cind}</td><td width=\"10%\">{$matches[1]}</td><td width=\"30%\">{$matches[2]}</td><td width=\"17%\">{$comp->requirement->know}</td><td width=\"17%\">{$comp->requirement->beAbleTo}</td><td width=\"16%\">{$comp->requirement->own}</td></tr>";
        $cind++;
    }


    //lemuria
    if (!empty($rdp_another)) {
        foreach ($rdp_another as $rpd_another_data) {
            foreach ($rpd_another_data->competencies as $comp) {

                preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);

                if (!empty($shorrt_code)) {

                    $matches[1] = $shorrt_code;
                }


                $data['FOS2'] .= "<tr><td width=\"10%\">{$cind}</td><td width=\"10%\">{$matches[1]}</td><td width=\"30%\">{$matches[2]}</td><td width=\"17%\">{$comp->requirement->know}</td><td width=\"17%\">{$comp->requirement->beAbleTo}</td><td width=\"16%\">{$comp->requirement->own}</td></tr>";
                $cind++;
            }
        }
    }


    $data['FOS3'] = "";

    $data['FOS4'] = "";

    $tind = 1;
    $qaind = 1;
    $q1ind = 1;
    $eind = 1;
    $cind = 1;
    $dind = 1;
    $eaind = 1;

    $find = 1;

    $FOS = [
        'tests' => "",
        'tests2' => "",
        'tests3' => "",
        'delov_igri' => "",
        'esse' => "",
        'questions' => "",
        'keys_work' => "",
        'doklad' => "",
        'referat' => "",
        'prosmotri' => "",
        'pokazi' => "",
        'proslush' => "",
        'kontrol_work' => "",
        'praktik' => "",
        'exam_question' => "",
        'exam_assign' => "",
        'credit_question' => "",
        'credit_assign' => "",
        'domzad' => "",
        'questions_obsugd' => "",
    ];

    $fosmap = [];

    $tasksmap = [
        'tests' => [],
        'tests2' => [],
        'tests3' => [],
        'delov_igri' => [],
        'esse' => [],
        'questions' => [],
        'keys_work' => [],
        'doklad' => [],
        'referat' => [],
        'prosmotri' => [],
        'pokazi' => [],
        'proslush' => [],
        'kontrol_work' => [],
        'praktik' => [],
        'exam_question' => [],
        'exam_assign' => [],
        'credit_question' => [],
        'credit_assign' => [],
        'domzad' => [],
        'questions_obsugd' => [],
        'credit_assign' => [],
    ];


    $controlsList = [];


    foreach ($rpd->controlsList as $control) {
        $controlsList[$control->code] = $control->code;
    }


    foreach ($rpd->questionsForAllThemes as $questions) {

        //check for empty questions

        if (empty($questions->questions)) {
            continue;
        }

        if (!in_array($questions->code, $controlsList)) {
            continue;
        }

        $need_skip = true;

        foreach ($questions->questions as $key_ => $test_) {

            if (!empty($test_->questionDescription)) {
                $need_skip = false;
            }
        }

        if ($need_skip === true) {
            continue;
        }

        //super hard changes for all tests
        //$questions->code == 'tests2'
        //$questions->code == 'tests3'
        if ($questions->code == 'tests') {
            $t1 = "";
            $t2 = "";
            $crit = isset($rpd->criteriaList->tests) ? $rpd->criteriaList->tests : "";
            foreach ($questions->questions as $key => $test) {
                $comps = [];
                foreach ($test->selectedValue as $comp) {
                    //lemuria 16.02.2023
                    //preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);

                    //preg_match('/^([А-Яа-я]{1,10}\s[\d]+)\s(.*?)$/', $comp->title, $matches);

                    preg_match('/^([А-Яа-я\]{1,5}\-?\s[\d]+)\s(.*?)$/si', $comp->title, $matches);


                    //$comps[] = $matches[1];

                    //lemuria 23.09.2024
                    if (!empty($comp->short_code)) {
                        $comps[] = $comp->short_code;

                    } else {
                        $comps[] = $matches[1];
                    }
                    //lemuria 16.02.2023
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $test->questionDescription;
                $a = $test->questionAnswers;
                $k = $key + 1;
                $tasksmap[$questions->code][$test->questionDescription] = $k;
                if (!empty($q) && !empty($a)) {
                    $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
                    $t2 .= "<tr><td width=\"50%\">{$k}</td><td width=\"50%\">{$a}</td></tr>";
                }
            }

            $t1 = str_replace('<ol>', '', $t1); // откуда то берется <ol> тег и все ломает
            $t1 = str_replace('<li>', '', $t1); // откуда то берется <ol> тег и все ломает

            $t1 = str_replace('</ol>', '', $t1); // откуда то берется <ol> тег и все ломает
            $t1 = str_replace('</li>', '', $t1); // откуда то берется <ol> тег и все ломает


            $pattern = '/<ol[^>]*?id="[^"]*"\s*>/';
            $replacement = ''; // Указываем пустую строку для замены
            $t1 = preg_replace($pattern, $replacement, $t1);

            if ($t1 != "") {
                $FOS['tests'] = <<<EOT
					<h3>4.%INDEX%. Тесты (тестовые задания) для текущего контроля и контроля самостоятельной работы обучающихся</h3><table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ задания</th>
							<th width="60%">Тест (тестовое задание)</th>
						</tr>
					</thead>
					<tbody>{$t1}</tbody></table><p><b>Критерии и шкала оценки:</b><br>
					{$crit}
					<h4 style="text-align: center;">Ключ к тестовым заданиям</h4>
					<p><i>Прикладывается к тестам (тестовым заданиям).</i></p>
					<table>
						<thead>
							<tr style="font-weight: bold; background-color: lightgray;">
								<th width="50%">№ тестового задания</th>
								<th width="50%">Вариант правильного ответа</th>
							</tr>
						</thead>
						<tbody>
							{$t2}
						</tbody>
					</table>
EOT;
            }
        }

        //lemuria 21.02.2024

        if ($questions->code == 'tests2') {
            $t1 = "";
            $t2 = "";
            $crit = isset($rpd->criteriaList->tests) ? $rpd->criteriaList->tests : "";
            foreach ($questions->questions as $key => $test) {
                $comps = [];
                foreach ($test->selectedValue as $comp) {
                    //lemuria 16.02.2023
                    //preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);

                    //preg_match('/^([А-Яа-я]{1,10}\s[\d]+)\s(.*?)$/', $comp->title, $matches);

                    preg_match('/^([А-Яа-я\]{1,5}\-?\s[\d]+)\s(.*?)$/si', $comp->title, $matches);


                    //$comps[] = $matches[1];

                    //lemuria 23.09.2024
                    if (!empty($comp->short_code)) {
                        $comps[] = $comp->short_code;

                    } else {
                        $comps[] = $matches[1];
                    }
                    //lemuria 16.02.2023
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $test->questionDescription;
                $a = $test->questionAnswers;
                $k = $key + 1;
                $tasksmap[$questions->code][$test->questionDescription] = $k;
                if (!empty($q) && !empty($a)) {
                    $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
                    $t2 .= "<tr><td width=\"50%\">{$k}</td><td width=\"50%\">{$a}</td></tr>";
                }
            }


            if ($t1 != "") {
                $FOS['tests2'] = <<<EOT
					<h3>4.%INDEX%. Тесты (тестовые задания) для текущего контроля и контроля самостоятельной работы обучающихся</h3><table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ задания</th>
							<th width="60%">Тест (тестовое задание)</th>
						</tr>
					</thead>
					<tbody>{$t1}</tbody></table><p><b>Критерии и шкала оценки:</b><br>
					{$crit}
					<h4 style="text-align: center;">Ключ к тестовым заданиям</h4>
					<p><i>Прикладывается к тестам (тестовым заданиям).</i></p>
					<table>
						<thead>
							<tr style="font-weight: bold; background-color: lightgray;">
								<th width="50%">№ тестового задания</th>
								<th width="50%">Вариант правильного ответа</th>
							</tr>
						</thead>
						<tbody>
							{$t2}
						</tbody>
					</table>
EOT;
            }
        }

        if ($questions->code == 'tests3') {
            $t1 = "";
            $t2 = "";
            $crit = isset($rpd->criteriaList->tests) ? $rpd->criteriaList->tests : "";
            foreach ($questions->questions as $key => $test) {
                $comps = [];
                foreach ($test->selectedValue as $comp) {
                    //lemuria 16.02.2023
                    //preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);

                    //preg_match('/^([А-Яа-я]{1,10}\s[\d]+)\s(.*?)$/', $comp->title, $matches);

                    preg_match('/^([А-Яа-я\]{1,5}\-?\s[\d]+)\s(.*?)$/si', $comp->title, $matches);


                    //$comps[] = $matches[1];

                    //lemuria 23.09.2024
                    if (!empty($comp->short_code)) {
                        $comps[] = $comp->short_code;

                    } else {
                        $comps[] = $matches[1];
                    }
                    //lemuria 16.02.2023
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $test->questionDescription;
                $a = $test->questionAnswers;
                $k = $key + 1;
                $tasksmap[$questions->code][$test->questionDescription] = $k;
                if (!empty($q) && !empty($a)) {
                    $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
                    $t2 .= "<tr><td width=\"50%\">{$k}</td><td width=\"50%\">{$a}</td></tr>";
                }
            }


            if ($t1 != "") {
                $FOS['tests3'] = <<<EOT
					<h3>4.%INDEX%. Тесты (тестовые задания) для текущего контроля и контроля самостоятельной работы обучающихся</h3><table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ задания</th>
							<th width="60%">Тест (тестовое задание)</th>
						</tr>
					</thead>
					<tbody>{$t1}</tbody></table><p><b>Критерии и шкала оценки:</b><br>
					{$crit}
					<h4 style="text-align: center;">Ключ к тестовым заданиям</h4>
					<p><i>Прикладывается к тестам (тестовым заданиям).</i></p>
					<table>
						<thead>
							<tr style="font-weight: bold; background-color: lightgray;">
								<th width="50%">№ тестового задания</th>
								<th width="50%">Вариант правильного ответа</th>
							</tr>
						</thead>
						<tbody>
							{$t2}
						</tbody>
					</table>
EOT;
            }
        }
        //lemuria 21.02.2024


        if ($questions->code == 'credit_question') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->credit_question) ? $rpd->criteriaList->credit_question : "";

            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                if (!empty($q)) {
                    $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
                }
            }

            if ($t1 != "") {


                if (!empty($_REQUEST['rg_dom'])) {


                    //var_dump($t1);

                    //exit('sdfsdfsdf');
                }

                $t1 = str_replace('<ol>', '', $t1); // откуда то берется <ol> тег и все ломает
                $t1 = str_replace('<li>', '', $t1); // откуда то берется <li> тег и все ломает

                $t1 = str_replace('</ol>', '', $t1); // откуда то берется <ol> тег и все ломает
                $t1 = str_replace('</li>', '', $t1); // откуда то берется <li> тег и все ломает


                $t1 = str_replace('<ul>', '', $t1); // откуда то берется <ul> тег и все ломает

                $t1 = str_replace('</ul>', '', $t1); // откуда то берется <ul> тег и все ломает

                $FOS['credit_question'] = <<<EOT
					<h3>4.%INDEX%. Вопросы к зачету</h3>
					<p><i>Вопросы и задачи (задания) к зачету должны обеспечить проверку уровня сформированности необходимых компетенций, соотнесенных с индикаторами формирования компетенций: «знать», «уметь», «владеть».</i></p>
					
					<table>
						<thead>
							<tr style="font-weight: bold; background-color: lightgray;">
								<th width="20%">Индекс компетенции</th>
								<th width="20%">№ п/п</th>
								<th width="60%">Формулировка вопроса</th>
							</tr>
						</thead>
						<tbody>
							{$t1}
						</tbody>
					</table>
					<p><b>Критерии и шкала оценки:</b><br>
					{$crit}
EOT;
            }
        }

        if ($questions->code == 'credit_assign') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->credit_assign) ? $rpd->criteriaList->credit_assign : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;

                if (!empty($q)) {
                    $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
                }
            }

            if ($t1 != "") {
                $FOS['credit_assign'] = <<<EOT
				<h3>4.%INDEX%. Задания к зачету</h3>
					<p><i>Вопросы и задачи (задания) к зачету должны обеспечить проверку уровня сформированности необходимых компетенций, соотнесенных с индикаторами формирования компетенций: «знать», «уметь», «владеть».</i></p>
					
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ задачи (задания)</th>
							<th width="60%">Условие задачи (формулировка задания)</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        //lemuria 20.02.2024


        if ($questions->code == 'questions_obsugd') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->questions_obsugd) ? $rpd->criteriaList->questions_obsugd : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['questions_obsugd'] = <<<EOT
				<h3>4.%INDEX%. Вопросы для обcуждения</h3>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ п/п</th>
							<th width="60%">Формулировка вопроса</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }


        if ($questions->code == 'delov_igri') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->delov_igri) ? $rpd->criteriaList->delov_igri : "";

            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['delov_igri'] = <<<EOT
					<h3>4.%INDEX%. Задания на деловые игры</h3>
					<table>
						<thead>
							<tr style="font-weight: bold; background-color: lightgray;">
								<th width="20%">Индекс компетенции</th>
								<th width="20%">№ задачи (задания)</th>
								<th width="60%">Условие задачи (формулировка задания)</th>
							</tr>
						</thead>
						<tbody>
							{$t1}
						</tbody>
					</table>
					<p><b>Критерии и шкала оценки:</b><br>
					{$crit}
EOT;
            }
        }


        /*
        if ($questions->code == 'questions_obsugd') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->questions_obsugd) ? $rpd->criteriaList->questions_obsugd : "";
            foreach ($questions->questions as $key=>$cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>'.implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key+1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['questions_obsugd'] = <<<EOT
                <table>
                    <thead>
                        <tr style="font-weight: bold; background-color: lightgray;">
                            <th width="20%">Индекс компетенции</th>
                            <th width="20%">№ задачи (задания)</th>
                            <th width="60%">Условие задачи (формулировка задания)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$t1}
                    </tbody>
                </table>
                <p><b>Критерии и шкала оценки:</b><br>
                {$crit}
EOT;
            }
        }
        */
//lemuria 20.02.2024
        if ($questions->code == 'esse') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->esse) ? $rpd->criteriaList->esse : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['esse'] = <<<EOT
				<h3>4.%INDEX%. Эссе для контроля самостоятельной работы обучающихся</h3>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ задания</th>
							<th width="60%">Формулировка вопроса/задания(условие задачи)</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        if ($questions->code == 'questions') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->questions) ? $rpd->criteriaList->questions : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['questions'] = <<<EOT
				<h3>4.%INDEX%. Вопросы для текущего контроля при выполнении лабораторных работ, практикумов, самостоятельной работы</h3>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ задания</th>
							<th width="60%">Формулировка вопроса</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        if ($questions->code == 'keys_work') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->keys_work) ? $rpd->criteriaList->keys_work : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['keys_work'] = <<<EOT
				<h3>4.%INDEX%. Кейс-задания</h3>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ задания</th>
							<th width="60%">Формулировка вопроса</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        if ($questions->code == 'doklad') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->doklad) ? $rpd->criteriaList->doklad : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['doklad'] = <<<EOT
				<h3>4.%INDEX%. Доклад для контроля самостоятельной работы обучающихся</h3>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ темы</th>
							<th width="60%">Тематика докладов</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        if ($questions->code == 'referat') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->referat) ? $rpd->criteriaList->referat : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['referat'] = <<<EOT
				<h3>4.%INDEX%. Реферат для контроля самостоятельной работы обучающихся</h3>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ темы</th>
							<th width="60%">Тематика рефератов</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        if ($questions->code == 'prosmotri') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->prosmotri) ? $rpd->criteriaList->prosmotri : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['prosmotri'] = <<<EOT
				<h3>4.%INDEX%. Просмотры</h3>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№</th>
							<th width="60%">Описание</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        if ($questions->code == 'pokazi') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->pokazi) ? $rpd->criteriaList->pokazi : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['pokazi'] = <<<EOT
				<h3>4.%INDEX%. Показы</h3>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№</th>
							<th width="60%">Описание</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        if ($questions->code == 'proslush') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->proslush) ? $rpd->criteriaList->proslush : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['proslush'] = <<<EOT
				<h3>4.%INDEX%. Прослушивания</h3>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№</th>
							<th width="60%">Описание</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        if ($questions->code == 'kontrol_work') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->kontrol_work) ? $rpd->criteriaList->kontrol_work : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['kontrol_work'] = <<<EOT
				<h3>4.%INDEX%. Контрольные работы</h3>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№</th>
							<th width="60%">Описание</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        if ($questions->code == 'praktik') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->praktik) ? $rpd->criteriaList->praktik : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['praktik'] = <<<EOT
				<h3>4.%INDEX%. Практические задания</h3>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№</th>
							<th width="60%">Описание</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        if ($questions->code == 'exam_question') {
            $merged_data_parts = array_merge($rpd->parts, $rpdSodev->parts ?? []);

            $t1 = "";
            $crit = isset($rpd->criteriaList->exam_question) ? $rpd->criteriaList->exam_question : "";


            $examQuestions = '';
            $qi = 1;
            foreach ($merged_data_parts as $k_p => $p_data_main) {

                if (empty($p_data_main->data)) {
                    continue;
                }

                foreach ($p_data_main->data as $last_part_data_main) {


                    if (empty($last_part_data_main->data)) {
                        continue;
                    }

                    foreach ($last_part_data_main->data as $kkk => $last_part_data) {

                        if ($kkk == 'exam_question') {

                            if (!empty($last_part_data)) {
                                foreach ($last_part_data as $question) {

                                    $comps = [];
                                    foreach ($question->selectedValue as $comp) {

                                        $comps[] = $comp->short_code;
                                    }
                                    $comps = '<br>' . implode("<br>", $comps);

                                    $k = $key + 1;
                                    //$t1 .= $qi . '. ' . strip_tags($question->questionDescription,"<img>") . '<br>';

                                    $q = strip_tags($question->questionDescription, "<img>");


                                    $t1 .= "<tr><td width=\"17%\">{$comps}</td><td width=\"13%\">{$qi}</td><td width=\"70%\">{$q}</td></tr>";
                                    $qi++;

                                }
                            }
                        }
                    }
                }
            }

            /*
            foreach ($questions->questions as $key=>$cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>'.implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key+1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"17%\">{$comps}</td><td width=\"13%\">{$k}</td><td width=\"70%\">{$q}</td></tr>";
            }
            */

            foreach ($questions->questions as $key => $cq) {

                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;

            }

            if ($t1 != "") {
                $FOS['exam_question'] = <<<EOT
				<h3>4.%INDEX%. Вопросы к экзамену</h3>
				<p><i>Вопросы экзаменационного билета должны обеспечить проверку уровня сформированности необходимых компетенций, соотнесенных с индикаторами формирования компетенций: «знать», «уметь», «владеть».</i></p><br>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="17%">Индекс компетенции</th>
							<th width="13%">№ вопроса</th>
							<th width="70%">Формулировка вопроса</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

        if ($questions->code == 'exam_assign') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->exam_assign) ? $rpd->criteriaList->exam_assign : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['exam_assign'] = <<<EOT
				<h3>4.%INDEX%. Задачи (задания)</h3>
				<p><i>Задачи (задания) экзаменационного билета должны обеспечить проверку уровня сформированности необходимых компетенций, соотнесенных с индикаторами формирования компетенций: «знать», «уметь», «владеть».</i></p>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ задачи (задания)</th>
							<th width="60%">Условие задачи (формулировка задания)</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }


        if ($questions->code == 'domzad') {
            $t1 = "";
            $crit = isset($rpd->criteriaList->domzad) ? $rpd->criteriaList->domzad : "";
            foreach ($questions->questions as $key => $cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>' . implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key + 1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }

            if ($t1 != "") {
                $FOS['domzad'] = <<<EOT
				<h3>4.%INDEX%. Домашнее задание</h3>
				<p><i>Задачи (задания)  должны обеспечить проверку уровня сформированности необходимых компетенций, соотнесенных с индикаторами формирования компетенций: «знать», «уметь», «владеть».</i></p>
				<table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ задачи (задания)</th>
							<th width="60%">Условие задачи (формулировка задания)</th>
						</tr>
					</thead>
					<tbody>
						{$t1}
					</tbody>
				</table>
				<p><b>Критерии и шкала оценки:</b><br>
				{$crit}
EOT;
            }
        }

    }


    //lemuria
    //дополнительные рпд
    include(__DIR__ . '/fos_questions.php');


    $ind = 1;


    foreach ($FOS as $key => $value) {
        if ($value == "" && $value != 'delov_igri') {
            continue;
        }
        $fosmap[$key] = $ind;
        $data['FOS4'] .= str_replace("%INDEX%", $ind, $value);
        $ind++;
    }

    $data['FOS4'] = preg_replace('#\s(id|class)="[^"]+"#', '', $data['FOS4']);


    $data['FOS4'] = str_replace('<ol>', '', $data['FOS4']); // откуда то берется <ol> тег и все ломает


    $data['FOS4'] = str_replace('<li>', '', $data['FOS4']); // откуда то берется <ol> тег и все ломает

    $data['FOS4'] = str_replace('</ol>', '', $data['FOS4']); // откуда то берется <ol> тег и все ломает
    $data['FOS4'] = str_replace('</li>', '', $data['FOS4']); // откуда то берется <ol> тег и все ломает


    $course_works = '';

    if (!empty($rpd->questionsForDiscipline->course_work->tasks) || !empty($rpdSodev->questionsForDiscipline->course_work->tasks)) {

        $ii_taks = 1;

        /*
        $t1 = "";
            $crit = isset($rpd->criteriaList->domzad) ? $rpd->criteriaList->domzad : "";
            foreach ($questions->questions as $key=>$cq) {
                $comps = [];
                foreach ($cq->selectedValue as $comp) {
                    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                    $comps[] = $matches[1];
                }
                $comps = '<br>'.implode("<br>", $comps);
                $q = $cq->questionDescription;
                $k = $key+1;
                $tasksmap[$questions->code][$cq->questionDescription] = $k;
                $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            }
            */


        foreach (array_merge($rpd->questionsForDiscipline->course_work->tasks, $rpdSodev->questionsForDiscipline->course_work->tasks ?? []) as $theme) {


            $comps = [];
            foreach ($theme->competences as $comp) {
                //preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                preg_match('/^([А-Яа-я\]{1,5}\-?\s[\d]+)\s(.*?)$/si', $comp->title, $matches);
                $comps[] = $matches[1];
            }
            $comps = '<br>' . implode("<br>", $comps);


            $t111 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$ii_taks}</td><td width=\"60%\">{$theme->taskName}</td></tr>";
            $ii_taks++;
        }


        $crit = isset($rpd->criteriaList->course_work) ? $rpd->criteriaList->course_work : "";


        $course_works = '<h3>4.' . $ind . '. Курсовая работа/Курсовой проект</h3><table>
					<thead>
						<tr style="font-weight: bold; background-color: lightgray;">
							<th width="20%">Индекс компетенции</th>
							<th width="20%">№ задачи (задания)</th>
							<th width="60%">Условие задачи (формулировка типового задания)</th>
						</tr>
					</thead>
					<tbody>
						' . $t111 . '
					</tbody>
				</table><p><b>Критерии и шкала оценки:</b><br>
				' . $crit;

        $ind++;
    }

    $course_works = !empty($course_works) ? $course_works : '';
    $data['FOS4'] .= $course_works;


    $type_names = [
        'credit_question' => 'Вопросы к зачету',
        'tests' => 'Тесты',
        'tests2' => 'Тесты',
        'tests3' => 'Тесты',
        'delov_igri' => 'Задания на деловые игры',
        'esse' => "Эссе",
        'questions' => "Вопросы для контроля",
        'keys_work' => "Кейс-задания",
        'doklad' => "Доклады",
        'referat' => "Рефераты",
        'prosmotri' => "Просмотры",
        'pokazi' => "Показы",
        'proslush' => "Прослушивания",
        'kontrol_work' => "Контрольные работы",
        'praktik' => "Практические задания",
        'exam_question' => "Вопросы к экзамену",
        'domzad' => "Домашнее задание",
        'questions_obsugd' => "Вопросы для обсуждения",
        'exam_assign' => "Задачи к экзамену",
        'credit_assign' => "Задания к зачету",
    ];

    $ind = 1;
    $tind = 1;
    $sind = 1;
    $theme_increase_counter = 0;
    foreach ($rpd->parts as $part) {
        $data['FOS3'] .= <<<EOT
				<tr>
					<td colspan="6"><h3>Раздел {$sind}. {$part->name_segment}</h3></td>
				</tr>
EOT;

        $theme_increase_counter++;
        foreach ($part->data as $d) {
            $comps = [];
            $tps = [];
            $currentControls = [];

            $replaces_tasks_kontrol = [];


            $replaces_sorted_task_kontrol = [];

            foreach ($d->data as $tp => $t) {

                if (count($t) == 0)
                    continue;
                $tasks = [];
                foreach ($t as $tt) {
                    foreach ($tt->selectedValue as $ttt) {
                        if (isset($tasksmap[$tp][$tt->questionDescription]))
                            $tasks[] = $tasksmap[$tp][$tt->questionDescription];
                        $code = splitcomp(["Код" => "", "Наименование" => "", "Название" => $ttt->title])['Код'];
                        if (!in_array($code, $comps) && in_array($code, $global_current_comps))
                            $comps[] = $code;
                    }
                }
                sort($tasks);
                $tasks = array_unique($tasks);
                $tasks = implode(",", $tasks);


                if (!empty($type_names[$tp])) {
                    $currentControls[] = $type_names[$tp];

                    $task_name = $type_names[$tp];


                    $task_name = str_replace($yummy_l, $healthy_itogo, $task_name);
                    $task_name = str_replace($yummy, $healthy_itogo, $task_name);


                    $tps[] = [$fosmap[$tp], "4." . $fosmap[$tp] . ". " . $type_names[$tp], $tasks, $task_name];


                    $task_name = $type_names[$tp];


                    $task_name = str_replace($yummy_l, $healthy_itogo, $task_name);
                    $task_name = str_replace($yummy, $healthy_itogo, $task_name);

                    $replaces_tasks_kontrol[] = $task_name;
                }
            }

            $comps = '<br>' . implode("<br>", $comps);
            usort($tps, function ($a, $b) {
                return $a[0] > $b[0];
            });

            $tsks1 = [];
            $tsks2 = [];


            foreach ($tps as $tp) {
                $tsks1[] = $tp[1];
                $tsks2[] = $tp[2];

                $replaces_sorted_task_kontrol[] = $tp[3];
            }
            $tsks2 = str_replace(',', ', ', $tsks2);

            // Массив обязательно должен содержать 0 индекс, что бы не было ошибки
            $tsks1[0] = empty($tsks1[0]) ? '' : $tsks1[0];
            $tsks2[0] = empty($tsks2[0]) ? '' : $tsks2[0];


            $currentControls = implode(', ', $currentControls);
            $taskCount = count($tsks1);


            //$replaced_form_kontrols = '';

            //$replaced_form_kontrols = str_replace($yummy_l, $healthy_itogo, $tsks1[0]);
            //$replaced_form_kontrols = str_replace($yummy, $healthy_itogo, $replaced_form_kontrols);

            //					<td width="15%" rowspan="{$taskCount}">{$currentControls}</td>


            $data['FOS3'] .= <<<EOT
				<tr>
					<td width="10%" rowspan="{$taskCount}">{$ind}</td>
					<td width="17%" rowspan="{$taskCount}">Тема {$theme_increase_counter}.{$tind}. {$d->name_segment}</td>
					<td width="18%" rowspan="{$taskCount}">{$comps}</td>
					<td width="28%">{$tsks1[0]}</td>
					<td width="12%">{$tsks2[0]}</td>
					<td width="15%" >{$replaces_sorted_task_kontrol[0]}</td>
				</tr>
EOT;
            if ($taskCount > 1) {
                for ($i = 1; $i < $taskCount; $i++) {


                    //$replaced_form_kontrols = str_replace($yummy_l, $healthy_itogo, $replaces_sorted_task_kontrol[$i]);
                    //$replaced_form_kontrols = str_replace($yummy, $healthy_itogo, $replaced_form_kontrols);


                    $data['FOS3'] .= <<<EOT
				<tr>
					<td width="28%">{$tsks1[$i]}</td>
					<td width="12%">{$tsks2[$i]}</td>
					<td width="15%" >{$replaces_sorted_task_kontrol[$i]}</td>
				</tr>
EOT;
                }
            }

            $ind++;
            $tind++;
        }
        $sind++;
        $tind = 1;
    }


    //lemuria
    if (!empty($rdp_another)) {


        foreach ($rdp_another as $rpd_another_data) {
            $ind = 1;
            $tind = 1;
            $sind = 1;

            foreach ($rpd_another_data->parts as $part) {
                $data['FOS3'] .= <<<EOT
				<tr>
					<td colspan="6"><h3>Раздел {$sind}. {$part->name_segment}</h3></td>
				</tr>
EOT;
                foreach ($part->data as $d) {
                    $comps = [];
                    $tps = [];
                    $currentControls = [];

                    foreach ($d->data as $tp => $t) {
                        if (count($t) == 0)
                            continue;
                        $tasks = [];
                        foreach ($t as $tt) {
                            foreach ($tt->selectedValue as $ttt) {
                                if (isset($tasksmap[$tp][$tt->questionDescription]))
                                    $tasks[] = $tasksmap[$tp][$tt->questionDescription];
                                $code = splitcomp(["Код" => "", "Наименование" => "", "Название" => $ttt->title])['Код'];
                                if (!in_array($code, $comps) && in_array($code, $global_current_comps))
                                    $comps[] = $code;
                            }
                        }
                        sort($tasks);
                        $tasks = array_unique($tasks);
                        $tasks = implode(",", $tasks);

                        if (!empty($type_names[$tp])) {
                            $currentControls[] = $type_names[$tp];
                            $tps[] = [$fosmap[$tp], "4." . $fosmap[$tp] . ". " . $type_names[$tp], $tasks];
                        }
                    }

                    $comps = '<br>' . implode("<br>", $comps);
                    usort($tps, function ($a, $b) {
                        return $a[0] > $b[0];
                    });

                    $tsks1 = [];
                    $tsks2 = [];
                    foreach ($tps as $tp) {
                        $tsks1[] = $tp[1];
                        $tsks2[] = $tp[2];
                    }
                    $tsks2 = str_replace(',', ', ', $tsks2);

                    // Массив обязательно должен содержать 0 индекс, что бы не было ошибки
                    $tsks1[0] = empty($tsks1[0]) ? '' : $tsks1[0];
                    $tsks2[0] = empty($tsks2[0]) ? '' : $tsks2[0];

                    $currentControls = implode(', ', $currentControls);
                    $taskCount = count($tsks1);
                    $data['FOS3'] .= <<<EOT
				<tr>
					<td width="10%" rowspan="{$taskCount}">{$ind}</td>
					<td width="17%" rowspan="{$taskCount}">Тема {$tind}. {$d->name_segment}</td>
					<td width="18%" rowspan="{$taskCount}">{$comps}</td>
					<td width="28%">{$tsks1[0]}</td>
					<td width="12%">{$tsks2[0]}</td>
					<td width="15%" rowspan="{$taskCount}">{$currentControls}</td>
				</tr>
EOT;
                    if ($taskCount > 1) {
                        for ($i = 1; $i < $taskCount; $i++) {
                            $data['FOS3'] .= <<<EOT
				<tr>
					<td width="28%">{$tsks1[$i]}</td>
					<td width="12%">{$tsks2[$i]}</td>
				</tr>
EOT;
                        }
                    }

                    $ind++;
                    $tind++;
                }
                $sind++;
                $tind = 1;
            }

        }

    }

    //замена фос3

    //$data['FOS3'] = str_replace($yummy_l, $healthy_itogo, $data['FOS3']);
    //$data['FOS3'] = str_replace($yummy, $healthy_itogo, $data['FOS3']);

} else {
    $data['TYPE'] = 'Рабочая программа дисциплины';
    $table = "";


    //lemuria 16.01.2023

    $new_sorted_rpd_table_comps = [];

    $opk_data_comps = [];
    $pk_data_comps = [];
    $yk_data_comps = [];
    $another_data_comps = [];


    foreach ($rpd->competencies as $_c_key => $c_data) {


        $title = $c_data->title;

        $title = trim($title);

        $first_letter = mb_substr($c_data->title, 0, 1);
        $first_letter = mb_strtolower($first_letter);


        if ($first_letter == 'у') {
            $yk_data_comps[] = $c_data;
        } elseif ($first_letter == 'о') {

            $opk_data_comps[] = $c_data;
        } elseif ($first_letter == 'п') {
            $pk_data_comps[] = $c_data;
        } else {
            $another_data_comps[] = $c_data;
        }
    }


    $yk_data_comps = fn_ar_gg_sort_detailed_competences($yk_data_comps);
    $opk_data_comps = fn_ar_gg_sort_detailed_competences($opk_data_comps);
    $pk_data_comps = fn_ar_gg_sort_detailed_competences($pk_data_comps);
    $another_data_comps = fn_ar_gg_sort_detailed_competences($another_data_comps);


    //$sorted_rpd_comps =    [];
    $new_sorted_rpd_table_comps = $yk_data_comps;
    $new_sorted_rpd_table_comps = array_merge($new_sorted_rpd_table_comps, $opk_data_comps);
    $new_sorted_rpd_table_comps = array_merge($new_sorted_rpd_table_comps, $pk_data_comps);
    $new_sorted_rpd_table_comps = array_merge($new_sorted_rpd_table_comps, $another_data_comps);

    //foreach ($rpd->competencies as $comp) {

    //}
    //lemuria 16.01.2023


    foreach ($new_sorted_rpd_table_comps as $comp) {

        if (!empty($rdp_another) && empty($comp->requirement->know) && empty($comp->requirement->beAbleTo) && empty($comp->requirement->own)) {
            continue;
        }

        $table .= "<tr " . 'style="text-align: justify"' . "><td>{$comp->title}</td><td><br><b>знать</b>:<br>{$comp->requirement->know}<br><b>уметь</b>:<br>{$comp->requirement->beAbleTo}<br><b>владеть</b>:<br>{$comp->requirement->own}</td></tr>";
    }

    //lemuria 16.01.2023
    $data['TABLE3'] = $table;


    //lemuria
    if (!empty($rdp_another)) {

        foreach ($rdp_another as $rpd_another_data) {
            if (!empty($rpd_another_data->competencies)) {
                foreach ($rpd_another_data->competencies as $comp) {
                    $data['TABLE3'] .= "<tr " . 'style="text-align: justify"' . "><td>{$comp->title}</td><td><br><b>знать:</b><br>{$comp->requirement->know}<br><b>уметь</b>:<br>{$comp->requirement->beAbleTo}<br><b>владеть</b>:<br>{$comp->requirement->own}</td></tr>";
                }
            }
        }
    }

    $table = "";

    //lemuria 25.09.2024
    foreach ($plan["Дисциплины"] as $d => $v) {
        ksort($disc2sem[$d]);
    }


    if (!empty($plan_zaochka)) {

        foreach ($plan_zaochka["Дисциплины"] as $d => $v) {
            ksort($disc2sem_zaoch[$d]);
        }
    }


    if (!empty($_REQUEST['rg_rg_rg'])) {

        //var_dump($pdisc_zaoch);

        //exit('dfdddfdf');

    }


    foreach ($rpd->forms as $form) {

        $is_zaochka = false;
        if ($form->name == 'заочная') {
            $is_zaochka = true;
        }

        $vals = [];
        $sum = 0;


        $interactve = 0;

        foreach ($form->load as $load) {

            $tyoeguid = $load->typeguid;

            $vals[$load->type] = $load->value;


            if ($tyoeguid != 'Интерактивные') {
                $sum += $load->value;
            } else {
                $interactve += $load->value;
            }
        }

        $aud = $vals['Лекции'] + $vals['Практические'] + $vals['Лабораторные'];
        $aud = $aud ?: '-';
        $bysems = "";

        if ($is_zaochka) {
            $sems = array_keys($disc2sem_zaoch[$rpd->info->discipline]);
        } else {
            $sems = array_keys($disc2sem[$rpd->info->discipline]);
        }
        $sms = "";

        foreach ($sems as $sem) {
            $sms .= "<th>{$sem}</th>";
        }

        $tc1 = 1 + count($sems);
        $tc2 = count($sems);

        $sdata = ['lec' => 0, 'pr' => 0, 'lab' => 0, 'sam' => 0, 'leci' => 0, 'pri' => 0, 'labi' => 0, 'sami' => 0, 'kon' => '', 'itog' => 0, 'kur' => ''];

        $ssems = [];

        if ($is_zaochka) {

            $search_data = $pdisc_zaoch;
        } else {

            $search_data = $pdisc;

        }

        foreach ($search_data["Записи"] as $rec) {
            if (!isset($ssems[$sem2sem[$rec["ПериодКонтроля"]]]))
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]] = $sdata;
            switch ($rec["Нагрузка"]) {
                case "Лабораторные":
                    $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['lab'] = $rec["Количество"];
                    $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['itog'] += intval($rec["Количество"]);
                    break;
                case "СРС":
                    $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['sam'] = $rec["Количество"];
                    $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['itog'] += intval($rec["Количество"]);
                    break;
                case "Лекции":
                    $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['lec'] = $rec["Количество"];
                    $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['itog'] += intval($rec["Количество"]);
                    break;
                case "Практические":
                    $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['pr'] = $rec["Количество"];
                    $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['itog'] += intval($rec["Количество"]);
                    break;
                case "Курсовая работа":
                    $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['kur'] = $rec["Нагрузка"];
                    break;
                case "Экзамен":
                case "Зачет":
                    $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['kon'] = $rec["Нагрузка"];
                    $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['itog'] += intval($rec["Количество"]);
                    break;
            }
        }


        $vals['kon'] = [];
        $vals['kur'] = [];

        $dsems = ['aud' => "", 'lec' => "", 'pr' => "", 'lab' => "", 'sam' => "", 'kon' => "", 'itog' => "", 'kur' => ""];

        $global_sum_hours = 0;

        $global_kursovaya = '';

        foreach ($sems as $sem) {
            $aud1 = $ssems[$sem]['lec'] + $ssems[$sem]['lab'] + $ssems[$sem]['pr'];
            $dsems['aud'] .= "<td>{$aud1}</td>";
            $dsems['lec'] .= "<td>" . ($ssems[$sem]['lec'] == '<td>0</td>' ? '-' : $ssems[$sem]['lec']) . "</td>";
            $dsems['lab'] .= "<td>" . ($ssems[$sem]['lab'] == '<td>0</td>' ? '-' : $ssems[$sem]['lab']) . "</td>";
            $dsems['pr'] .= "<td>{$ssems[$sem]['pr']}</td>";
            $dsems['sam'] .= "<td>{$ssems[$sem]['sam']}</td>";
            $dsems['kon'] .= "<td>{$ssems[$sem]['kon']}</td>";
            $dsems['itog'] .= "<td>{$ssems[$sem]['itog']}</td>";
            $dsems['kur'] .= "<td>" . (empty($ssems[$sem]['kur']) ? '-' : $ssems[$sem]['kur']) . "</td>";

            if (!empty($ssems[$sem]['kur'])) {

                $global_kursovaya = $ssems[$sem]['kur'];
            }
//            $dsems['kur'] .= "<td>" . ($ssems[$sem]['kur'] == '<td>0</td>' ? '-' : $ssems[$sem]['kur']) . "</td>";

            $global_sum_hours = $global_sum_hours + $ssems[$sem]['itog'];

            if ($ssems[$sem]['kon'] != "" && !in_array($ssems[$sem]['kon'], $vals['kon']))
                $vals['kon'][] = $ssems[$sem]['kon'];
            if ($ssems[$sem]['kur'] != "") {
                $vals['kur'][] = $ssems[$sem]['kur'];
            } else {
                $vals['kur'][] = '<td>-</td>';
            }
        }

        $vals['kon'] = implode(", ", $vals['kon']) ?: '-';
        $vals['kur'] = implode(", ", $vals['kur']) ?: '-';

        $inds = "<th>1</th><th>2</th><th>3</th>";

        for ($i = 0; $i < count($sems) - 1; $i++) {
            $inds .= "<th>" . ($i + 4) . "</th>";
        }

        foreach ($dsems as $key => $dsem) {
            $dsemWithoutTag = strip_tags($dsem, "<img>");
            $dsems[$key] = empty($dsemWithoutTag) || $dsemWithoutTag === 0 || $dsemWithoutTag === "0" ? '<td>-</td>' : $dsem;
        }

        foreach ($vals as $key => $val) {
            $vals[$key] = !empty($val) && $val != 0 && $val != "0" ? $val : '-';
        }

        // костыль
        if (($dsems['kon'] != '-') && ($vals['kon'] == '-')) {
            /**
             * 1. Разделяет строку на массивы
             * 2. Удаляет теги из строки
             * 3. Оставляет уникальные значения массива
             * 4. Объединяет массив в строку
             */
            $vals['kon'] = explode('</td><td>', $dsems['kon']);
            foreach ($vals['kon'] as &$val) {
                $val = strip_tags($val, "<img>");
            }
            $vals['kon'] = implode(', ', array_unique($vals['kon']));
        }

        $dsems['cur_contr'] = '';
        for ($i = 0; $i < count($sems); $i++) {
            $dsems['cur_contr'] .= '<td>' . $data['CRITERIA_LIST'] . '</td>';
        }


        //lemuria 22.07.2023
        $different_for_exam = 0;

        $itog_at = filter_var($dsems['itog'], FILTER_SANITIZE_NUMBER_INT);

        $itog_at = intval($global_sum_hours);
        $different_for_exam = intval($itog_at) - intval($sum);

        $sum_final = intval($sum) + $different_for_exam;


        //26.10.2024
        //$sum_final = intval($sum);


        if ($vals['kur'] == '-' && !empty($global_kursovaya)) {
            $vals['kur'] = $global_kursovaya;
        }


        if ($is_zaochka) {

        }


        /* lemuria SEVEN POINT

        */


        $replaced_array = [];


        $data['CRITERIA_LIST'] = mb_strtolower($data['CRITERIA_LIST']);

        $dsems['cur_contr'] = mb_strtolower($dsems['cur_contr']);


        $modified_cur_contr = explode("<td>", $dsems['cur_contr']);

        $modified_critaria_list = explode(",", $data['CRITERIA_LIST']);

        if (!empty($modified_cur_contr)) {

            foreach ($modified_cur_contr as $keu_mod => $mod) {
                $mod = str_replace($yummy, $healthy, $mod);

                $mod = str_replace($yummy_l, $healthy, $mod);


                $only_spaces = str_replace(" ", "", $mod);
                if (empty($mod) or empty($only_spaces)) {
                    //unset($modified_cur_contr[$keu_mod]);
                    //$modified_cur_contr[$keu_mod] = '';
                    unset($modified_cur_contr[$keu_mod]);
                } else {
                    $mod = str_replace("</td>", "", $mod);


                    $modified_cur_contr[$keu_mod] = trim($mod);
                }
            }
        }

        $new_cleared_array = [];


        if (!empty($modified_cur_contr)) {

            foreach ($modified_cur_contr as $k => $mod__) {

                $temporary_data = explode(",", $mod__);
                $was_find_test = false;
                if (!empty($temporary_data)) {
                    foreach ($temporary_data as $kk_t => $da_temp) {
                        $only_spaces = str_replace(" ", "", $da_temp);
                        $da_temp_check_test = mb_strpos($da_temp, "Тест");
                        if ($da_temp_check_test !== false) {
                            if (!$was_find_test) {
                                $was_find_test = true;
                            } else {

                                unset($temporary_data[$kk_t]);
                            }

                        }

                        if (empty($da_temp) or empty($only_spaces)) {
                            unset($temporary_data[$kk_t]);
                        }
                    }

                    if (!empty($temporary_data)) {
                        $modified_cur_contr[$k] = implode(",", $temporary_data);
                    } else {
                        $modified_cur_contr[$k] = "";
                    }
                }
            }

        }


        if (!empty($modified_critaria_list)) {

            foreach ($modified_critaria_list as $keu_mod => $mod) {
                $mod = str_replace($yummy, $healthy, $mod);
                $mod = str_replace($yummy_l, $healthy, $mod);
                $only_spaces = str_replace(" ", "", $mod);
                if (empty($mod) or empty($only_spaces)) {
                    unset($modified_critaria_list[$keu_mod]);
                }
            }
        }

        $modified_critaria_list = array_unique($modified_critaria_list);


        $modified_cur_contr = array_unique($modified_cur_contr);


        $data['CRITERIA_LIST'] = implode(",", $modified_critaria_list);

        $dsems['cur_contr'] = '';


        if (!empty($modified_cur_contr)) {


            foreach ($modified_cur_contr as $c__c) {


                $dsems['cur_contr'] .= '<td>' . $c__c . '</td>';
            }

        }

        if ($different_for_exam <= 0) {
            $vals['kon'] = 'Зачёт';

            $different_for_exam = '';

            $dsems['kon'] = '<td>Зачёт</td>';
        } else {

            $different_for_exam = '(' . $different_for_exam . ')';
        }

        if (!empty($_REQUEST['rg_rg_rg'])) {


            var_dump($interactve);

            exit('dfdf');
        }


        #$cur_control_explode_table = '<td colspan="' . $tc2 . '">' . $dsems['cur_contr'] . '</td>';

        $cur_control_explode_table = $dsems['cur_contr'];


        $table .= <<<EOT
		<div>Форма обучения: <u>{$form->name}</u></div>
		<table>
			<thead>
				<tr style="font-weight: bold; background-color: lightgray;">
					<th rowspan="3">Вид учебной работы</th>
					<th colspan="{$tc1}">Количество часов (форма обучения <u>{$form->name}</u>)</th>
				</tr>
				<tr style="font-weight: bold; background-color: lightgray;">
					<th rowspan="2">Всего по плану</th>
					<th colspan="{$tc2}">В т.ч. по семестрам</th>
				</tr>
				<tr style="font-weight: bold; background-color: lightgray;">
					{$sms}
				</tr>
				<tr style="font-weight: bold; background-color: lightgray;">
					{$inds}
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Контактная работа обучающихся с преподавателем в соответствии с УП</td>
					<td>{$aud}</td>
					{$dsems['aud']}
				</tr>
				<tr>
					<td>Аудиторные занятия:</td>
					<td>{$aud}</td>
					{$dsems['aud']}
				</tr>
				<tr>
					<td>Лекции</td>
					<td>{$vals['Лекции']}</td>
					{$dsems['lec']}
				</tr>
				<tr>
					<td>Семинары и практические занятия</td>
					<td>{$vals['Практические']}</td>
					{$dsems['pr']}
				</tr>
				<tr>
					<td>Лабораторные работы, практикумы</td>
					<td>{$vals['Лабораторные']}</td>
					{$dsems['lab']}
				</tr>
				<tr>
					<td>Самостоятельная работа</td>
					<td>{$vals['СРС']}</td>
					{$dsems['sam']}
				</tr>
				<tr>
					<td>Форма текущего контроля знаний и контроля самостоятельной работы: тестирование, контр. работа, коллоквиум, реферат и др. (не менее 2 видов)</td>
					{$dsems['cur_contr']}
					{$cur_control_explode_table}
				</tr>
				<tr>
					<td>Курсовая работа</td>
					{$vals['kur']}
					{$dsems['kur']}
				</tr>
				<tr>
					<td>Виды промежуточной аттестации (экзамен, зачет)</td>
					<td>{$vals['kon']} {$different_for_exam}</td>
					{$dsems['kon']}
				</tr>
				<tr>
					<td>Всего часов по дисциплине</td>
					<td>{$sum_final}</td>
					{$dsems['itog']}
					
				</tr>
			</tbody>
		</table>
		<br><br><br>
EOT;
    }

    /*

    <td>Форма текущего контроля знаний и контроля самостоятельной работы: тестирование, контр. работа, коллоквиум, реферат и др. (не менее 2 видов)</td>
                    <td>{$data['CRITERIA_LIST']}</td>
                    {$dsems['cur_contr']}
                    */

    //{$dsems['itog']}
    $data['TABLE42'] = $table;


    $table = "";
    $part5 = "";

    $all_lec = 0;
    $all_pr = 0;
    $all_lab = 0;
    $all_int = 0;
    $all_out = 0;

    $tind = 1;
    $qind = 1;
    $sind = 1;


    $forms__dat['zao'] = false;
    $forms__dat['ochka'] = false;
    $forms__dat['ochkazaochka'] = false;


    /*
     foreach ($rpd->forms as $form__) {

         if($form__['name'] == 'очная' ){
             $forms__dat['ochka'] = true;
         }
         if($form__['name'] == 'заочная' ){
             $forms__dat['zao'] = true;
         }

         if($form__['name'] == 'очнозаочная' ){
             $forms__dat['ochkazaochka'] = true;
         }


     }
     */

    $number_q_str = 1;
    $theme_increase_counter = 0;
    foreach ($rpd->parts as $part) {
        $themes = "";
        $part5 .= "<h4>Раздел {$sind}. {$part->name_segment}</h4>";

        if (empty($part->data)) {
            continue;
        }
        $tind = 1;

        $theme_increase_counter++;

        foreach ($part->data as $d) {
            //$tind =1;
            //$theme_increase_counter++;

            $part5 .= "<h4>Тема {$theme_increase_counter}.{$tind}. {$d->name_segment}</h4><p>{$d->description}</p>";
            $all = $d->lection + $d->practice + $d->lab + $d->outwork;
            $all_lec += $d->lection;
            $all_pr += $d->practice;
            $all_lab += $d->lab;
            $all_int += $d->interactive;
            $all_out += $d->outwork;
            $criteriaListByTopic = implode(', ', getCriteriaListByTopic($d, $criteriaList));
            $themes .= <<<EOT
				<tr>
					<td width="20%">Тема {$tind}. {$d->name_segment}</td>
					<td>{$all}</td>
					<td>{$d->lection}</td>
					<td>{$d->practice}</td>
					<td>{$d->lab}</td>
					<td>{$d->interactive}</td>
					<td>{$d->outwork}</td>
					<td>{$criteriaListByTopic}</td>
				</tr>
EOT;
            if (!empty($d->data->credit_question)) {
                foreach ($d->data->credit_question as $cq) {
                    // $q = preg_replace('/^(<p[^>]*>)/i', "\${1}{$qind}. ", $cq->questionDescription);
                    $q = strip_tags($cq->questionDescription, "<img><p>");
                    //$q = $qind . ". ". strip_tags($cq->questionDescription,array("<img>"));


                    $q = preg_replace('#\s(id|class)="[^"]+"#', '', $q);

                    $_q_list = explode("<p>", $q);

                    $quest_string = '';


                    foreach ($_q_list as $k_c_q => $qdrr) {

                        if (empty($qdrr)) {
                            unset($_q_list[$k_c_q]);
                        }
                    }

                    if (!empty($_q_list)) {


                        foreach ($_q_list as $q_l) {


                            $q_l = strip_tags($q_l, "<img>");

                            $quest_string .= "<p>" . $number_q_str . ". " . $q_l . "</p>";

                            $number_q_str++;

                        }
                    }


                    //$data['QUESTIONS'] .= "{$q}". '<br>';
                    $data['QUESTIONS'] .= "{$quest_string}" . '<br>';
                    $qind++;
                }
            }
            $tind++;

        }

        $table .= <<<EOT
			<tr>
				<td colspan="8" style="text-align: center;"><b>Раздел {$sind}. {$part->name_segment}</b></td>
			</tr>
			{$themes}
EOT;
        $sind++;
    }

    $all = $all_lec + $all_pr + $all_lab + $all_out;
    $table .= <<<EOT
		<tr>
			<td width="20%"><b>Итого подлежит изучению</b></td>
			<td>{$all}</td>
			<td>{$all_lec}</td>
			<td>{$all_pr}</td>
			<td>{$all_lab}</td>
			<td>{$all_int}</td>
			<td>{$all_out}</td>
			<td></td>
		</tr>
EOT;


    //$table = "";
    //$part5 = "";

    // $all_lec = 0;
    //$all_pr = 0;
    //$all_lab = 0;
    //$all_int = 0;
    // $all_out = 0;

    //$tind = 1;
    //$qind = 1;
    // $sind = 1;

    $sup_table = '';


    $independed_table = '';

    foreach ($rpd->forms as $forms_data) {


        $all_lec = 0;
        $all_pr = 0;
        $all_lab = 0;
        $all_int = 0;
        $all_out = 0;

        $tind = 1;
        $qind = 1;
        $sind = 1;

        $bbind = 1;

        $themes_add = '';
        $table_add = '';

        $themes_number = 1;

        //lemuria задача по дополнительной таблице 4.2
        foreach ($rpd->parts as $part) {
            $themes = "";
            // $part5 .= "<h4>Раздел {$sind}. {$part->name_segment}</h4>";
            $themes_add = '';

            $theme_number_control = 1;
            foreach ($part->data as $d) {
                //$part5 .= "<h4>Тема {$tind}. {$d->name_segment}</h4><p>{$d->description}</p>";


                $bbind = 1;

                if ($forms_data->name == 'очная') {
                    $all = $d->lection + $d->practice + $d->lab + $d->outwork;
                    $all_lec += $d->lection;
                    $all_pr += $d->practice;
                    $all_lab += $d->lab;
                    $all_int += $d->interactive;
                    $all_out += $d->outwork;
                }

                if ($forms_data->name == 'заочная') {
                    $all = $d->lection_za + $d->practice_za + $d->lab_za + $d->outwork_za;
                    $all_lec += $d->lection_za;
                    $all_pr += $d->practice_za;
                    $all_lab += $d->lab_za;
                    $all_int += $d->interactive_za;
                    $all_out += $d->outwork_za;
                }

                if ($forms_data->name == 'очно-заочная') {
                    $all = $d->lection_oza + $d->practice_oza + $d->lab_oza + $d->outwork_oza;
                    $all_lec += $d->lection_oza;
                    $all_pr += $d->practice_oza;
                    $all_lab += $d->lab_oza;
                    $all_int += $d->interactive_oza;
                    $all_out += $d->outwork_oza;
                }

                $criteriaListByTopic = implode(', ', getCriteriaListByTopic($d, $criteriaList));


                $temporary_data = explode(",", $criteriaListByTopic);
                $was_find_test = false;


                if (!empty($temporary_data)) {


                    foreach ($temporary_data as $kk_t => &$da_temp) {


                        $da_temp = str_replace($yummy, $healthy, $da_temp);

                        $da_temp = str_replace($yummy_l, $healthy, $da_temp);


                        $only_spaces = str_replace(" ", "", $da_temp);
                        $da_temp_check_test = mb_strpos($da_temp, "Тест");
                        if ($da_temp_check_test !== false) {
                            if (!$was_find_test) {
                                $was_find_test = true;
                            } else {

                                unset($temporary_data[$kk_t]);
                            }

                        }

                        if (empty($da_temp) or empty($only_spaces)) {
                            unset($temporary_data[$kk_t]);
                        }
                    }

                    if (!empty($temporary_data)) {
                        $criteriaListByTopic = implode(",", $temporary_data);
                    } else {
                        $criteriaListByTopic = "";
                    }
                }


                //lemuria
                if ($forms_data->name == 'очная') {
                    $themes_add .= <<<EOT
				<tr>
					<td >Тема {$sind}.{$theme_number_control}. {$d->name_segment}</td>
					<td>{$all}</td>
					<td>{$d->lection}</td>
					<td>{$d->practice}</td>
					<td>{$d->lab}</td>
					<td>{$d->interactive}</td>
					<td>{$d->outwork}</td>
					<td>{$criteriaListByTopic}</td>
				</tr>
EOT;
                }
                //lemuria 13.02.2024 width="20%"
                if ($forms_data->name == 'заочная') {
                    $themes_add .= <<<EOT
				<tr>
					<td >Тема {$sind}.{$theme_number_control}. {$d->name_segment}</td>
					<td>{$all}</td>
					<td>{$d->lection_za}</td>
					<td>{$d->practice_za}</td>
					<td>{$d->lab_za}</td>
					<td>{$d->interactive_za}</td>
					<td>{$d->outwork_za}</td>
					<td>{$criteriaListByTopic}</td>
				</tr>
EOT;
                }

                //lemuria 13.02.2024 width="20%"
                if ($forms_data->name == 'очно-заочная') {
                    $themes_add .= <<<EOT
				<tr>
					<td >Тема {$sind}.{$theme_number_control}. {$d->name_segment}</td>
					<td>{$all}</td>
					<td>{$d->lection_oza}</td>
					<td>{$d->practice_oza}</td>
					<td>{$d->lab_oza}</td>
					<td>{$d->interactive_oza}</td>
					<td>{$d->outwork_oza}</td>
					<td>{$criteriaListByTopic}</td>
				</tr>
EOT;
                }

                $bbind++;
                $tind++;

                $theme_number_control++;

            }

            $themes_number++;


            $table_add .= '
			<tr >
				<td colspan="8"><b>Раздел ' . $sind . '. ' . $part->name_segment . '</b></td>
			</tr> ' . $themes_add;

            //$table_add.= $themes_add;

            $independed_table .= '
			<tr >
				<td colspan="4"><b>Раздел ' . $sind . '. ' . $part->name_segment . '</b></td>
			</tr> ';

            $sind++;
        }


        $all = $all_lec + $all_pr + $all_lab + $all_out;
        $table_add .= <<<EOT
		<tr>
			<td> <b>Итого подлежит изучению</b></td>
			<td>{$all}</td>
			<td>{$all_lec}</td>
			<td>{$all_pr}</td>
			<td>{$all_lab}</td>
			<td>{$all_int}</td>
			<td>{$all_out}</td>
			<td></td>
		</tr>
EOT;

        //style="border: 2px solid white !important;"
        //$table_add='';


        $sup_table .= '<h3>4.3. Содержание дисциплины. Распределение часов по темам и видам учебной работы</h3>
<div>Форма обучения: <u>' . $forms_data->name . '</u></div>
<table >
	<thead>
	<tr style="font-weight: bold; background-color: lightgray;">
		<th  rowspan="3">Название разделов и тем</th>
		<th rowspan="3">Всего</th>
		<th colspan="5">Виды учебных занятий</th>
		<th rowspan="3">Форма текущего контроля знаний </th>
	</tr>
	<tr style="font-weight: bold; background-color: lightgray;">
		<th colspan="3">Аудиторные занятия</th>
		<th rowspan="2">Занятия в интерактивной форме</th>
		<th rowspan="2">Самостоятельная работа</th>
	</tr>
	<tr style="font-weight: bold; background-color: lightgray;">
		<th>Лекции</th>
		<th>Практические занятия, семинары</th>
		<th>Лабораторные работы, практикумы</th>
	</tr>
	<tr style="font-weight: bold; background-color: lightgray;">
		<th >1</th>
		<th>2</th>
		<th>3</th>
		<th>4</th>
		<th>5</th>
		<th>6</th>
		<th>7</th>
		<th>8</th>
	</tr>
	</thead>
	<tbody 	 >' . $table_add . '
	</tbody>
</table>';


    }


    if (!empty($rdp_another)) {


        foreach ($rdp_another as $rpd_another_data) {
            $all_lec = 0;
            $all_pr = 0;
            $all_lab = 0;
            $all_int = 0;
            $all_out = 0;

            $tind = 1;
            $qind = 1;
            $sind = 1;


            $another_questions = '';

            foreach ($rpd_another_data->parts as $part) {
                $themes = "";
                $part5 .= "<h4>Раздел {$sind}. {$part->name_segment}</h4>";

                foreach ($part->data as $d) {
                    $part5 .= "<h4>Тема {$tind}. {$d->name_segment}</h4><p>{$d->description}</p>";
                    $all = $d->lection + $d->practice + $d->lab + $d->outwork;
                    $all_lec += $d->lection;
                    $all_pr += $d->practice;
                    $all_lab += $d->lab;
                    $all_int += $d->interactive;
                    $all_out += $d->outwork;
                    $criteriaListByTopic = implode(', ', getCriteriaListByTopic($d, $criteriaList));
                    $themes .= <<<EOT
				<tr>
					<td width="20%">Тема {$tind}. {$d->name_segment}</td>
					<td>{$all}</td>
					<td>{$d->lection}</td>
					<td>{$d->practice}</td>
					<td>{$d->lab}</td>
					<td>{$d->interactive}</td>
					<td>{$d->outwork}</td>
					<td>{$criteriaListByTopic}</td>
				</tr>
EOT;

                    if (!empty($d->data->credit_question)) {
                        foreach ($d->data->credit_question as $cq) {
                            // $q = preg_replace('/^(<p[^>]*>)/i', "\${1}{$qind}. ", $cq->questionDescription);
                            $q = $qind . ". " . strip_tags($cq->questionDescription, "<img>");
                            // echo htmlspecialchars($q);
                            //$data['QUESTIONS'] .= "{$q}". '<br>';
                            $another_questions .= "{$q}" . '<br>';
                            $qind++;
                        }
                    }
                    $tind++;
                }

                $table .= <<<EOT
			<tr>
				<td colspan="8" style="text-align: center;"><b>Раздел {$sind}. {$part->name_segment}</b></td>
			</tr>
			{$themes}
EOT;

                $independed_table .= '
			<tr >
				<td colspan="4"><b>Раздел ' . $sind . ' ' . $part->name_segment . '</b></td>
			</tr> ';

                $sind++;
            }


            if (!empty($another_questions)) {
                $data['QUESTIONS'] .= '<br>--------------------------------------------------------------------------------<br>' . $another_questions;
            }

            $all = $all_lec + $all_pr + $all_lab + $all_out;
            $table .= <<<EOT
		<tr>
			<td width="20%"><b>Итого подлежит изучению</b></td>
			<td>{$all}</td>
			<td>{$all_lec}</td>
			<td>{$all_pr}</td>
			<td>{$all_lab}</td>
			<td>{$all_int}</td>
			<td>{$all_out}</td>
			<td></td>
		</tr>
EOT;

        }
    }


    //***********************************

    if (!empty($rdp_another)) {


        //$sup_table ='';

        foreach ($rdp_another as $rpd_another_data) {

            foreach ($rpd_another_data->forms as $forms_data) {


                $all_lec = 0;
                $all_pr = 0;
                $all_lab = 0;
                $all_int = 0;
                $all_out = 0;

                $tind = 1;
                $qind = 1;
                $sind = 1;

                $themes_add = '';
                $table_add = '';

                foreach ($rpd_another_data->parts as $part) {
                    $themes = "";

                    $theme_number_control = 0;
                    // $part5 .= "<h4>Раздел {$sind}. {$part->name_segment}</h4>";

                    foreach ($part->data as $d) {
                        //$part5 .= "<h4>Тема {$tind}. {$d->name_segment}</h4><p>{$d->description}</p>";

                        if ($forms_data->name == 'очная') {
                            $all = $d->lection + $d->practice + $d->lab + $d->outwork;
                            $all_lec += $d->lection;
                            $all_pr += $d->practice;
                            $all_lab += $d->lab;
                            $all_int += $d->interactive;
                            $all_out += $d->outwork;
                        }

                        if ($forms_data->name == 'заочная') {
                            $all = $d->lection_za + $d->practice_za + $d->lab_za + $d->outwork_za;
                            $all_lec += $d->lection_za;
                            $all_pr += $d->practice_za;
                            $all_lab += $d->lab_za;
                            $all_int += $d->interactive_za;
                            $all_out += $d->outwork_za;
                        }


                        if ($forms_data->name == 'очно-заочная') {
                            $all = $d->lection_oza + $d->practice_oza + $d->lab_oza + $d->outwork_oza;
                            $all_lec += $d->lection_oza;
                            $all_pr += $d->practice_oza;
                            $all_lab += $d->lab_oza;
                            $all_int += $d->interactive_oza;
                            $all_out += $d->outwork_oza;
                        }

                        $criteriaListByTopic = implode(', ', getCriteriaListByTopic($d, $criteriaList));


                        if ($forms_data->name == 'очная') {
                            $themes_add .= <<<EOT
				<tr>
					<td width="20%">Тема {$tind}. {$d->name_segment}</td>
					<td>{$all}</td>
					<td>{$d->lection}</td>
					<td>{$d->practice}</td>
					<td>{$d->lab}</td>
					<td>{$d->interactive}</td>
					<td>{$d->outwork}</td>
					<td>{$criteriaListByTopic}</td>
				</tr>
EOT;
                        }

                        if ($forms_data->name == 'заочная') {
                            $themes_add .= <<<EOT
				<tr>
					<td width="20%">Тема {$tind}. {$d->name_segment}</td>
					<td>{$all}</td>
					<td>{$d->lection_za}</td>
					<td>{$d->practice_za}</td>
					<td>{$d->lab_za}</td>
					<td>{$d->interactive_za}</td>
					<td>{$d->outwork_za}</td>
					<td>{$criteriaListByTopic}</td>
				</tr>
EOT;
                        }
                        if ($forms_data->name == 'очно-заочная') {
                            $themes_add .= <<<EOT
				<tr>
					<td width="20%">Тема {$tind}. {$d->name_segment}</td>
					<td>{$all}</td>
					<td>{$d->lection_oza}</td>
					<td>{$d->practice_oza}</td>
					<td>{$d->lab_oza}</td>
					<td>{$d->interactive_oza}</td>
					<td>{$d->outwork_oza}</td>
					<td>{$criteriaListByTopic}</td>
				</tr>
EOT;
                        }


                        $tind++;
                    }

                    $table_add .= <<<EOT
			<tr>
				<td colspan="8" style="text-align: center;"><b>Раздел {$sind}. {$part->name_segment}</b></td>
			</tr>
			{$themes_add}
EOT;
                    $sind++;
                }


                $all = $all_lec + $all_pr + $all_lab + $all_out;
                $table_add .= <<<EOT
		<tr>
			<td width="20%"><b>Итого подлежит изучению</b></td>
			<td>{$all}</td>
			<td>{$all_lec}</td>
			<td>{$all_pr}</td>
			<td>{$all_lab}</td>
			<td>{$all_int}</td>
			<td>{$all_out}</td>
			<td></td>
		</tr>
EOT;


                $sup_table .= '<h3>4.3. Содержание дисциплины. Распределение часов по темам и видам учебной работы</h3>
<div>Форма обучения: <u>' . $forms_data->name . '</u></div>
<table>
	<thead>
	<tr style="font-weight: bold; background-color: lightgray;">
		<th width="20%"  rowspan="3">Название разделов и тем</th>
		<th rowspan="3">Всего</th>
		<th colspan="5">Виды учебных занятий</th>
		<th rowspan="3">Форма текущего контроля знаний </th>
	</tr>
	<tr style="font-weight: bold; background-color: lightgray;">
		<th colspan="3">Аудиторные занятия</th>
		<th rowspan="2">Занятия в интерактивной форме</th>
		<th rowspan="2">Самостоятельная работа</th>
	</tr>
	<tr style="font-weight: bold; background-color: lightgray;">
		<th>Лекции</th>
		<th>Практические занятия, семинары</th>
		<th>Лабораторные работы, практикумы</th>
	</tr>
	<tr style="font-weight: bold; background-color: lightgray;">
		<th>1</th>
		<th>2</th>
		<th>3</th>
		<th>4</th>
		<th>5</th>
		<th>6</th>
		<th>7</th>
		<th>8</th>
	</tr>
	</thead>
	<tbody>' . $table_add . '
	</tbody>
</table>';


            }

        }

    }


    //$sup_table = mb_strtolower($sup_table);

    $sup_table = str_replace($yummy_l, $healthy, $sup_table);


    $sup_table = str_replace($yummy, $healthy, $sup_table);


//$sup_table = array_unique($sup_table);


//$sup_table = '';


    $data['TABLE43'] = $sup_table;
    $data['PART5'] = $part5;
}


$data['PRACTICE_TOPICS'] = require(__DIR__ . '/_practiceTopics.php');
$data['CREDCRIT'] = require(__DIR__ . '/_laboratoryWorks.php');
$data['EXAM_QUESTIONS'] = require(__DIR__ . '/_questionsForExam.php');
$data['INDEPENDENT_WORK'] = require(__DIR__ . '/_independentWork.php');
$data['REFERATS'] = require(__DIR__ . '/_questionsForReferatCourseKontrol.php');


$yummy_samo_form = array(
    "вопросы для обсуждения",
    "вопросы для самоподготовки",
    "домашнее задание",
    "задания на деловые игры",
    "кейс-задания",
    "задания к контрольным работам",
    "задания к лабораторным работам",
    "онлайн курс",
    "показы",
    "практические задачи (задания)",
    "прослушивания",
    "просмотры",
    "темы докладов",
    "темы рефератов",
    "тесты 3",
    "тесты 2",
    "тесты",
    "эссе",
);


$is_course_work = false;

//if(!empty($_REQUEST['rg_rg_rg_rg'])){


foreach ($criteriaList as $__list) {
    $__list = mb_strtolower($__list);

    $__list = trim($__list);


    if ($__list == 'курсовая работа') {
        $is_course_work = true;
    }
}


//}
//lemuria 05.11.2024
$form_annotation_control = $data['CRITERIA_LIST'];
$form_annotation_control = explode(",", $form_annotation_control);


//var_dump($form_annotation_control);

if (!empty($form_annotation_control)) {

    foreach ($form_annotation_control as $f_c_key => $form_c) {
        $form_c = mb_strtolower($form_c);

        $form_c = trim($form_c);

        if (!in_array($form_c, $yummy_samo_form)) {

            unset($form_annotation_control[$f_c_key]);
        }


    }
} else {

    $form_annotation_control = '';
}

if (!empty($_REQUEST['rg_rg_rg_rg'])) {
    //var_dump($form_annotation_control);

    // exit('dfdfdf');
}

if (empty($form_annotation_control)) {
    $form_annotation_control = '';
} else {
    $form_annotation_control = implode(",", $form_annotation_control);
}

//}


if ($is_course_work == true) {

    $data['CONTROL'] .= ',Защита курсовой работы (проекта)';

}

$data['form_annotation_control'] = $form_annotation_control;


// $data['TABLE43'] = '';


$data['TITLE_EXAM_QUESTION'] = [];
if ($data['EXAM_QUESTIONS']) {
    $data['TITLE_EXAM_QUESTION'][] = 'ЭКЗАМЕНУ';
}
if ($data['QUESTIONS']) {
    $data['TITLE_EXAM_QUESTION'][] = 'ЗАЧЕТУ';
}
$data['TITLE_EXAM_QUESTION'] = implode(', ', $data['TITLE_EXAM_QUESTION']);

if (empty($data['TITLE_EXAM_QUESTION'])) {
    $data['TITLE_EXAM_QUESTION'] = 'ЗАЧЕТУ';
}

$forms = [];
foreach ($rpd->forms as $f)
    $forms[] = $f->name;
$data['FORMS'] = implode(", ", $forms);

$data['HOURS'] = 0;
foreach ($pdisc["Записи"] as $f) {
    $data['HOURS'] += $f["Количество"];
}
$data['ZET'] = $data['HOURS'] / 36;

if ($rpd->info->discipline == 'Элективные дисциплины по физической культуре и спорту') {
    $data['ZET'] = '-';
} else {

    $data['ZET'] .= ' ЗЕТ';
}


function getNumEnding($number, $endingArray)
{
    if (is_string($number)) {
        return $endingArray[0];
    }
    $number = $number % 100;
    if ($number >= 11 && $number <= 19) {
        $ending = $endingArray[2];
    } else {
        $i = $number % 10;
        switch ($i) {
            case (1):
                $ending = $endingArray[0];
                break;
            case (2):
            case (3):
            case (4):
                $ending = $endingArray[1];
                break;
            default:
                $ending = $endingArray[2];
        }
    }
    return $ending;
}

$data['HOURS'] .= ' ' . getNumEnding($data['HOURS'], array('час', 'часа', 'часов'));


//единиц
$data['EDINICA'] = getNumEnding($data['ZET'], array('единица', 'единицы', 'единиц'));

$lind = 1;


foreach ($rpd->books->mainSelected as $book) {


    $link = $book->link != "" ? " : [сайт]. — URL: {$book->link}" : "";
    $isbn = $book->id != "" ? ".— ISBN {$book->id}" : "";

    if (!empty($book->author) && !empty($book->year) && !empty($book->publishing)) {
        $data['LIB1'] .= "<div>{$lind}. {$book->author}. {$book->book} / {$book->author}{$isbn}.— {$book->publishing}, {$book->year} {$link}</div>";
    } else {
        $data['LIB1'] .= "<div>{$lind}. {$book->book} / {$isbn} {$link}</div>";
    }

    $lind++;
}

$lind_additionalSelected = 1;
foreach ($rpd->books->additionalSelected as $book) {
    $link = $book->link != "" ? " : [сайт]. — URL: {$book->link}" : "";
    $isbn = $book->id != "" ? ".— ISBN {$book->id}" : "";
    if (!empty($book->author) && !empty($book->year) && !empty($book->publishing)) {
        $data['LIB2'] .= "<div>{$lind_additionalSelected}. {$book->author}. {$book->book} / {$book->author}{$isbn}.— {$book->publishing}, {$book->year} {$link}</div>";
    } else {
        $data['LIB2'] .= "<div>{$lind_additionalSelected}. {$book->book} / {$isbn}</div>";
    }
    $lind_additionalSelected++;
}

$lind_methodicalSelected = 1;
foreach ($rpd->books->methodicalSelected as $book) {
    $link = $book->link != "" ? " : [сайт]. — URL: {$book->link}" : "";
    $isbn = $book->id != "" ? ".— ISBN {$book->id}" : "";
    if (!empty($book->author) && !empty($book->year) && !empty($book->publishing)) {
        $data['LIB3'] .= "<div>{$lind_methodicalSelected}. {$book->author}. {$book->book} / {$book->author}{$isbn}.— {$book->publishing}, {$book->year} {$link}</div>";
    } else {
        $data['LIB3'] .= "<div>{$lind_methodicalSelected}. {$book->book} / {$isbn}.</div>";
    }
    $lind_methodicalSelected++;
}


$mind = 1;
foreach ($rpd->MTO as $mto) {
    foreach ($mto->auditorium as $aud) {
        $inventory = [];
        foreach ($aud->inventory as $inv) {
            $inventory[] = "- " . $inv->fullname;
        }
        $inventory = implode("<br>", $inventory);

        $software = [];
        foreach ($aud->software as $soft) {
            $software[] = "- " . $soft->fullname;
        }
        $software = implode("<br>", $software);

        $data['MTO_INVENTORY'] .= !empty($inventory) ? "{$inventory}<br>" : '';
        $data['MTO_SOFTWARE'] .= "{$software}";
        $mind++;
    }
}


//lemuria
//
if (!empty($rdp_another)) {


    foreach ($rdp_another as $rpd_another_data) {


    }

}

if (!empty($rdp_another)) {


    foreach ($rdp_another as $rpd_another_data) {

        $lind = 1;
        $lind_additionalSelected = 1;
        $lind_methodicalSelected = 1;

        $data['LIB1'] .= '<br>--------------------------------------------------------------------------------<br>';
        $data['LIB2'] .= '<br>--------------------------------------------------------------------------------<br>';
        $data['LIB3'] .= '<br>--------------------------------------------------------------------------------<br>';
        //$data['MTO_SOFTWARE'] .='<br>--------------------------------------------------------------------------------<br>';
        //$data['MTO_INVENTORY'] .='<br>--------------------------------------------------------------------------------<br>';

        $mind = 1;
        foreach ($rpd->MTO as $mto) {
            foreach ($mto->auditorium as $aud) {
                $inventory = [];
                foreach ($aud->inventory as $inv) {
                    $inventory[] = "- " . $inv->fullname;
                }
                $inventory = implode("<br>", $inventory);

                $software = [];
                foreach ($aud->software as $soft) {
                    $software[] = "- " . $soft->fullname;
                }
                $software = implode("<br>", $software);

                $data['MTO_INVENTORY'] .= !empty($inventory) ? "{$inventory}<br>" : '';
                $data['MTO_SOFTWARE'] .= "{$software}";
                $mind++;
            }
        }


        //$lind = 1;
        foreach ($rpd_another_data->books->mainSelected as $book) {
            $link = $book->link != "" ? " : [сайт]. — URL: {$book->link}" : "";
            $isbn = $book->id != "" ? ".— ISBN {$book->id}" : "";

            if (!empty($book->author) && !empty($book->year) && !empty($book->publishing)) {


            } else {
                $data['LIB1'] .= "<div>{$lind}. {$book->book} / {$isbn} {$link}</div>";

            }

            $lind++;
        }

        //$lind_additionalSelected = 1;
        foreach ($rpd_another_data->books->additionalSelected as $book) {
            $link = $book->link != "" ? " : [сайт]. — URL: {$book->link}" : "";
            $isbn = $book->id != "" ? ".— ISBN {$book->id}" : "";
            $data['LIB2'] .= "<div>{$lind_additionalSelected}. {$book->book} / {$isbn}</div>";
            $lind_additionalSelected++;
        }

        //$lind_methodicalSelected = 1;
        foreach ($rpd_another_data->books->methodicalSelected as $book) {
            $link = $book->link != "" ? " : [сайт]. — URL: {$book->link}" : "";
            $isbn = $book->id != "" ? ".— ISBN {$book->id}" : "";
            $data['LIB3'] .= "<div>{$lind_methodicalSelected}. {$book->book} / {$isbn}.</div>";
            $lind_methodicalSelected++;
        }


        /*
        $data['global_lib'] .= '<div style="text-align: justify">
    <b>а) Список рекомендуемой литературы</b><br>
    <b>основная</b><br>
    '.$data['LIB1'].'<br>
    <b>дополнительная</b><br>
    '.$data['LIB2'].'<br>
    <b>учебно-методическая</b><br>
    '.$data['LIB3'].'<br>
    <b>б) Программное обеспечение</b><br>
    - Операционная система "Альт образование"<br>
    - Офисный пакет "Мой офис"<br>
    '.$data['MTO_SOFTWARE'].'
    ';
    */
    }

}


$data['global_lib'] = '<div style="text-align: justify">
	<b>а) Список рекомендуемой литературы</b><br>
	<b>основная</b><br>
	' . $data['LIB1'] . '<br>
	<b>дополнительная</b><br>
	' . $data['LIB2'] . '<br>
	<b>учебно-методическая</b><br>
	' . $data['LIB3'] . '<br>
	<b>б) Программное обеспечение</b><br>
	- Операционная система "Альт образование"<br>
	- Офисный пакет "Мой офис"<br>
	' . $data['MTO_SOFTWARE'] . '
	';


$data['global_lib'] .= '<br>
	<b>в) Профессиональные базы данных, информационно-справочные системы</b>
<p><b>1. Электронно-библиотечные системы:</b></p>
<p>1.1. Цифровой образовательный ресурс IPRsmart : электронно-библиотечная система : сайт / ООО Компания «Ай Пи Ар Медиа». - Саратов, [2024]. – URL: http://www.iprbookshop.ru. – Режим доступа: для зарегистрир. пользователей. - Текст : электронный.</p>
<p>1.2. Образовательная платформа ЮРАЙТ : образовательный ресурс, электронная библиотека : сайт / ООО Электронное издательство ЮРАЙТ. – Москва, [2024]. - URL: https://urait.ru. – Режим доступа: для зарегистрир. пользователей. - Текст : электронный.</p>
<p>1.3. База данных «Электронная библиотека технического ВУЗа (ЭБС «Консультант студента») : электронно-библиотечная система : сайт / ООО Политехресурс. – Москва, [2024]. – URL: https://www.studentlibrary.ru/cgi-bin/mb4x. – Режим доступа: для зарегистрир. пользователей. – Текст : электронный.</p>
<p>1.4. Консультант врача. Электронная медицинская библиотека : база данных : сайт / ООО Высшая школа организации и управления здравоохранением-Комплексный медицинский консалтинг. – Москва, [2024]. – URL: https://www.rosmedlib.ru. – Режим доступа: для зарегистрир. пользователей. – Текст : электронный.</p>
<p>1.5. Большая медицинская библиотека :  электронно-библиотечная система : сайт / ООО Букап. – Томск, [2024]. – URL: https://www.books-up.ru/ru/library/ . – Режим доступа: для зарегистрир. пользователей. – Текст : электронный.</p>
<p>1.6. ЭБС Лань : электронно-библиотечная система : сайт / ООО ЭБС Лань. – Санкт-Петербург, [2024]. – URL:  https://e.lanbook.com. – Режим доступа: для зарегистрир. пользователей. – Текст : электронный.</p>
<p>1.7. ЭБС <b>Znanium.com</b> : электронно-библиотечная система : сайт / ООО Знаниум. - Москва, [2024]. - URL:  http://znanium.com . – Режим доступа : для зарегистрир. пользователей. - Текст : электронный.</p>
<p><b>2. КонсультантПлюс</b> [Электронный ресурс]: справочная правовая система. /ООО «Консультант Плюс» - Электрон. дан. - Москва : КонсультантПлюс, [2024].</p>
<p><b>3. eLIBRARY.RU:</b> научная электронная библиотека : сайт  / ООО «Научная Электронная Библиотека». – Москва, [2024]. – URL: http://elibrary.ru. – Режим доступа : для авториз. пользователей. – Текст : электронный</p>
<p><b>4. Федеральная государственная информационная система «Национальная электронная библиотека» :</b> электронная библиотека : сайт / ФГБУ РГБ. – Москва, [2024]. – URL: https://нэб.рф. – Режим доступа : для пользователей научной библиотеки. – Текст : электронный.</p>
<p><b>5. Российское образование :</b> федеральный портал / учредитель ФГАУ «ФИЦТО». – URL: http://www.edu.ru. – Текст : электронный.</p>
</div>';


$data['MTO_INVENTORY'] .= '<br>';

class MYPDF extends TCPDF
{
    public function Header()
    {
        $headerData = $this->getHeaderData();
        $this->SetFont('freeserif', '', 12);
        $this->writeHTML($headerData['string']);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$header = file_get_contents('templates/header.html');
$header = replace_data($header, $data, false);

$pdf->setHeaderData($ln = '', $lw = 0, $ht = '', $hs = $header, $tc = array(0, 0, 0), $lc = array(0, 0, 0));
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// $pdf->SetAutoPageBreak(true, 0);

$html = file_get_contents("templates/{$type}.html");


if (!empty($data['EXAM_QUESTIONS']) && !empty($data['QUESTIONS'])) {


    $data['EXAM_QUESTIONS'] = '<p><b>Вопросы к экзамену</b></p>' . $data['EXAM_QUESTIONS'];

    $diviner = '<p><b>Вопросы к зачету</b></p>';
} else {

    $diviner = '';
}
$data['EXAM_QUESTIONS'] .= $diviner;


$data['council_date'] = '«__» _______________ 20__';
$data['council_number'] = '______';
$data['council_structer'] = '';

if (isset($rpd->title->council_date) && !empty($rpd->title->council_date)) {
    $data['council_date'] = $rpd->title->council_date;
}

if (isset($rpd->title->council_number) && !empty($rpd->title->council_number)) {
    $data['council_number'] = $rpd->title->council_number;
}


if (!empty($rpd_another)) {

    $html = str_replace("%NEWPAGE%", "", $html);
}

$html = replace_data($html, $data, true);


foreach ($data as &$date) {
    $date = preg_replace('#^data:image/[^;]+;base64,#', '@', $date);
}

#$pdf->writeHTML($style . $html[0], true, false, true, false, '');

foreach ($html as $h) {
    $pdf->AddPage();
    $pdf->writeHTML($style . $h, true, false, true, false, '');
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
ob_end_clean();

if (!empty($_GET['subtype']) && $_GET['subtype'] == 'zip') {

    if (!isset($_GET['type'])) {
        $_GET['type'] = 'rdp';
    }
    $file_name_for_save = $CFG->dirroot . '/local/cdo_rpd/' . $_GET['type'] . '.pdf';

    if (file_exists($file_name_for_save)) {
        unlink($file_name_for_save);
    }

    $pdf->Output($file_name_for_save, 'F');

    return $file_name_for_save;

} else {
    ob_clean();
    header('content-type: application/pdf');
    $pdf->Output('file.pdf', 'I');

}


//subtype