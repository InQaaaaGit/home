<?php
$data['TYPE'] = 'Рабочая программа дисциплины';
$table = "";

foreach (array_merge($rpd->competencies, $rpdSodev->competencies) as $comp) {
    $table .= "<tr " . 'style="text-align: justify"' . "><td>{$comp->title}</td><td><br>знать:<br>{$comp->requirement->know}<br>уметь:<br>{$comp->requirement->beAbleTo}<br>владеть:<br>{$comp->requirement->own}</td></tr>";
}
$data['TABLE3'] = $table;

$table = "";
foreach ($rpd->forms as $form) {
    $vals = [];
    $sum = 0;

    foreach ($form->load as $load) {
        $vals[$load->type] = $load->value;
        $sum += $load->value;
    }

    $aud = $vals['Лекции']+$vals['Практические']+$vals['Лабораторные'];
    $aud = $aud ?: '-';
    $bysems = "";
    $sems = array_keys($disc2sem[$rpd->info->discipline]);
    $sms = "";

    foreach ($sems as $sem) {
        $sms .= "<th>{$sem}</th>";
    }

    $tc1 = 1 + count($sems);
    $tc2 = count($sems);

    $sdata = ['lec' => 0, 'pr' => 0, 'lab' => 0, 'sam' => 0, 'leci' => 0, 'pri' => 0, 'labi' => 0, 'sami' => 0, 'kon' => '', 'itog' => 0, 'kur' => ''];

    $ssems = [];

    foreach ($pdisc["Записи"] as $rec) {
        if (!isset($ssems[$sem2sem[$rec["ПериодКонтроля"]]]))
            $ssems[$sem2sem[$rec["ПериодКонтроля"]]] = $sdata;
        switch ($rec["Нагрузка"]) {
            case "Лабораторные":
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['lab'] = $rec["Количество"];
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['itog'] += $rec["Количество"];
                break;
            case "СРС":
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['sam'] = $rec["Количество"];
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['itog'] += $rec["Количество"];
                break;
            case "Лекции":
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['lec'] = $rec["Количество"];
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['itog'] += $rec["Количество"];
                break;
            case "Практические":
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['pr'] = $rec["Количество"];
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['itog'] += $rec["Количество"];
                break;
            case "Курсовая работа":
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['kur'] = $rec["Нагрузка"];
                break;
            case "Экзамен":
            case "Зачет":
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['kon'] = $rec["Нагрузка"];
                $ssems[$sem2sem[$rec["ПериодКонтроля"]]]['itog'] += $rec["Количество"];
                break;
        }
    }

    $vals['kon'] = [];
    $vals['kur'] = [];

    $dsems = ['aud' => "", 'lec' => "", 'pr' => "", 'lab' => "", 'sam' => "", 'kon' => "", 'itog' => "", 'kur' => ""];
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

    for ($i = 0; $i < count($sems)-1; $i++) {
        $inds .= "<th>".($i+4)."</th>";
    }

    foreach ($dsems as $key => $dsem) {
        $dsemWithoutTag = strip_tags($dsem);
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
            $val = strip_tags($val);
        }
        $vals['kon'] = implode(', ', array_unique($vals['kon']));
    }

    $dsems['cur_contr'] = '';
    for ($i = 0; $i < count($sems); $i++) {
        $dsems['cur_contr'] .= '<td>' . $data['CRITERIA_LIST'] . '</td>';
    }

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
					<td>{$data['CRITERIA_LIST']}</td>
					{$dsems['cur_contr']}
				</tr>
				<tr>
					<td>Курсовая работа</td>
					<td>{$vals['kur']}</td>
					{$dsems['kur']}
				</tr>
				<tr>
					<td>Виды промежуточной аттестации (экзамен, зачет)</td>
					<td>{$vals['kon']}</td>
					{$dsems['kon']}
				</tr>
				<tr>
					<td>Всего часов по дисциплине</td>
					<td>{$sum}</td>
					{$dsems['itog']}
				</tr>
			</tbody>
		</table>
		<br><br><br>
EOT;
}
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

foreach (array_merge($rpd->parts, $rpdSodev->parts) as $part) {
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
					<td>Тема {$tind}. {$d->name_segment}</td>
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
                $q = $qind . ". ". strip_tags($cq->questionDescription);
                // echo htmlspecialchars($q);
                $data['QUESTIONS'] .= "<p>{$q}</p>";
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
			<td><b>Итого подлежит изучению</b></td>
			<td>{$all}</td>
			<td>{$all_lec}</td>
			<td>{$all_pr}</td>
			<td>{$all_lab}</td>
			<td>{$all_int}</td>
			<td>{$all_out}</td>
			<td></td>
		</tr>
EOT;
$data['TABLE43'] = $table;
$data['PART5'] = $part5;

$data['PRACTICE_TOPICS'] = require(__DIR__ . '/_practiceTopics.php');
$data['CREDCRIT'] = require(__DIR__ . '/_laboratoryWorks.php');
$data['EXAM_QUESTIONS'] = require(__DIR__ . '/_questionsForExam.php');
$data['INDEPENDENT_WORK'] = require(__DIR__ . '/_independentWork.php');
$data['REFERATS'] = require(__DIR__ . '/_questionsForReferatCourseKontrol.php');

$lind = 1;
foreach (array_merge($rpd->books->mainSelected, $rpdSodev->books->mainSelected) as $book) {
    $link = $book->link != "" ? " : [сайт]. — URL: {$book->link}" : "";
    $isbn = $book->id != "" ? ".— ISBN {$book->id}" : "";
    $data['LIB1'] .= "<div>{$lind}. {$book->author}. {$book->book} / {$book->author}{$isbn}.— {$book->publishing}, {$book->year} {$link}</div>";
    $lind++;
}

$lind = 1;
foreach (array_merge($rpd->books->additionalSelected, $rpdSodev->books->additionalSelected) as $book) {
    $link = $book->link != "" ? " : [сайт]. — URL: {$book->link}" : "";
    $isbn = $book->id != "" ? ".— ISBN {$book->id}" : "";
    $data['LIB2'] .= "<div>{$lind}. {$book->author}. {$book->book} / {$book->author}{$isbn}.— {$book->publishing}, {$book->year} {$link}</div>";
    $lind++;
}

$lind = 1;
foreach (array_merge($rpd->books->methodicalSelected, $rpdSodev->books->methodicalSelected) as $book) {
    $link = $book->link != "" ? " : [сайт]. — URL: {$book->link}" : "";
    $isbn = $book->id != "" ? ".— ISBN {$book->id}" : "";
    $data['LIB3'] .= "<div>{$lind}. {$book->author}. {$book->book} / {$book->author}{$isbn}.— {$book->publishing}, {$book->year} {$link}</div>";
    $lind++;
}

$mind = 1;
foreach (array_merge($rpd->MTO, $rpdSodev->MTO) as $mto) {
    foreach ($mto->auditorium as $aud) {
        $inventory = [];
        foreach ($aud->inventory as $inv) {
            $inventory[] = $inv->fullname;
        }
        $inventory = implode(", ", $inventory);

        $software = [];
        foreach ($aud->software as $soft) {
            $software[] = "- ".$soft->fullname;
        }
        $software = implode("<br>", $software);

        $data['MTO'] .= !empty($inventory) ? "({$inventory})<br>" : '';
        $data['MTO'] .= "<br>{$software}";
        $mind++;
    }
}

$data['MTO'] .= '<br>';