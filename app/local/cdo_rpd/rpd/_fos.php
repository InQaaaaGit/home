<?php

$data['TYPE'] = 'Фонд оценочных средств (ФОС)';


$cw = (48 / count($comps))."%";

$cmps = "";
foreach ($comps as $comp) {
    $cmps .= "<td width=\"{$cw}\">{$comp}</td>";
}

$data['FOS1'] = '<table><thead><tr style="font-weight: bold; background-color: lightgray;"><td width="12%" rowspan="2">№ семестра</td><td width="40%" rowspan="2">Наименование дисциплины (модуля) или практики</td><td width="48%" colspan="3">Индекс компетенции</td></tr><tr style="font-weight: bold; background-color: lightgray;">'.$cmps.'</tr></thead><tbody>';
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
        $semsSort[$sems.'  '.substr(str_shuffle($permitted_chars), 0, 5)] = [$sems, $d, $cms];
    }
}

ksort($semsSort);
foreach ($semsSort as $item) {
    $data['FOS1'] .= "<tr><td width=\"12%\">{$item[0]}</td><td width=\"40%\">{$item[1]}</td>{$item[2]}</tr>";
}

$data['FOS1'] .= "</tbody></table>";


$qind = 1;

$data['FOS2'] = "";

$cind = 1;

foreach (array_merge($rpd->competencies, $rpdSodev->competencies) as $comp) {
    preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
    $data['FOS2'] .= "<tr><td width=\"10%\">{$cind}</td><td width=\"10%\">{$matches[1]}</td><td width=\"30%\">{$matches[2]}</td><td width=\"17%\">{$comp->requirement->know}</td><td width=\"17%\">{$comp->requirement->beAbleTo}</td><td width=\"16%\">{$comp->requirement->own}</td></tr>";
    $cind++;
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
];

$fosmap = [];

$tasksmap =[
    'tests' => [],
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
];

foreach(array_merge($rpd->questionsForAllThemes, $rpdSodev->questionsForAllThemes) as $questions) {
    if ($questions->code == 'tests') {
        $t1 = "";
        $t2 = "";
        $crit = isset($rpd->criteriaList->tests) ? $rpd->criteriaList->tests : "";
        foreach ($questions->questions as $key=>$test) {
            $comps = [];
            foreach ($test->selectedValue as $comp) {
                preg_match('/^([^ ]+) (.*?)$/', $comp->title, $matches);
                $comps[] = $matches[1];
            }
            $comps = '<br>'.implode("<br>", $comps);
            $q = $test->questionDescription;
            $a = $test->questionAnswers;
            $k = $key+1;
            $tasksmap[$questions->code][$test->questionDescription] = $k;
            $t1 .= "<tr><td width=\"20%\">{$comps}</td><td width=\"20%\">{$k}</td><td width=\"60%\">{$q}</td></tr>";
            $t2 .= "<tr><td width=\"50%\">{$k}</td><td width=\"50%\">{$a}</td></tr>";
        }

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
								<th width="50%">№ практического (практических), семинарского (семинарских) занятия (занятий)</th>
								<th width="50%">Правильный ответ</th>
							</tr>
						</thead>
						<tbody>
							{$t2}
						</tbody>
					</table>
EOT;
        }
    }

    if ($questions->code == 'credit_question') {
        $t1 = "";
        $crit = isset($rpd->criteriaList->credit_question) ? $rpd->criteriaList->credit_question : "";
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
            $FOS['credit_question'] = <<<EOT
					<h3>4.%INDEX%. Вопросы и задачи (задания) к зачету</h3>
					<p><i>Вопросы и задачи (задания) к зачету должны обеспечить проверку уровня сформированности необходимых компетенций, соотнесенных с индикаторами формирования компетенций: «знать», «уметь», «владеть».</i></p>
					
					<table>
						<thead>
							<tr style="font-weight: bold; background-color: lightgray;">
								<th width="20%">Индекс компетенции</th>
								<th width="20%">№ задачи (задания)</th>
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
            $FOS['credit_assign'] = <<<EOT
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

    if ($questions->code == 'esse') {
        $t1 = "";
        $crit = isset($rpd->criteriaList->esse) ? $rpd->criteriaList->esse : "";
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
            $FOS['pokazi'] = <<<EOT
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

    if ($questions->code == 'proslush') {
        $t1 = "";
        $crit = isset($rpd->criteriaList->proslush) ? $rpd->criteriaList->proslush : "";
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
        $t1 = "";
        $crit = isset($rpd->criteriaList->exam_question) ? $rpd->criteriaList->exam_question : "";
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
            $FOS['exam_assign'] = <<<EOT
				<h3>4.%INDEX%. Задачи (задания) к экзамену</h3>
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
}

$ind = 1;
foreach ($FOS as $key=>$value) {
    if ($value == "")
        continue;
    $fosmap[$key] = $ind;
    $data['FOS4'] .= str_replace("%INDEX%", $ind, $value);
    $ind++;
}

$data['FOS4'] = str_replace('<ol>', '', $data['FOS4']); // откуда то берется <ol> тег и все ломает

$type_names = [
    'credit_question' => 'Вопросы к зачету',
    'tests' => 'Тесты',
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
//		'exam_assign' => "exam_assign",
//		'credit_assign' => "credit_assign",
];

$ind = 1;
$tind = 1;
$sind = 1;

foreach (array_merge($rpd->parts, $rpdSodev->parts) as $part) {
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
                    if (!in_array($code, $comps))
                        $comps[] = $code;
                }
            }
            sort($tasks);
            $tasks = array_unique($tasks);
            $tasks = implode(",", $tasks);

            if (!empty($type_names[$tp])) {
                $currentControls[] = $type_names[$tp];
                $tps[] = [$fosmap[$tp], "4.".$fosmap[$tp].". ".$type_names[$tp], $tasks];
            }
        }

        $comps = '<br>'.implode("<br>", $comps);
        usort($tps, function($a, $b) {
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
            for ($i = 1; $i < $taskCount; $i ++) {
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