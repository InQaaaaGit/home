<?php
$independentWork = '';

if (!empty($rpd->questionsForAllThemes) || !empty($rpdSodev->questionsForAllThemes)) {

    $outworkCriteriaList = [
        'tests' => 'Тесты',
        'tests2' => 'Тесты',
        'tests3' => 'Тесты',
        'referat' => 'Темы рефератов',
        'esse' => 'Эссе',
        'questions' => 'Вопросы для самоподготовки',
        'doklad' => 'Темы докладов',
        'domzad' => 'Домашнее задание',
		'exam_question' => 'Вопросы к экзамену'
    ];
    $formsEduc = [
        '' => 'очная',
        '_za' => 'заочная',
        '_oza' => 'очно-заочная',
    ];
    $independentWorkResult = '';


    if(!empty($rdp_another)){

        $rpdSodev->parts = [];
    }

    foreach ($formsEduc as $key => $form) {
        $segmentIndex = 1;
        $topicIndex = 1;
        $propName = 'outwork' . $key;
        $independentWork = '';


        $sind =0;
        foreach (array_merge($rpd->parts, $rpdSodev->parts ?? []) as $part) {

            $sind++;


            $was_setuped_ = false;


            //var_dump($part);

            $theme_increase_counter = 0;

            $topicIndex =0;
            foreach ($part->data as $partData) {


                $topicIndex++;
                //var_dump($partData);

                $outworkHours = $partData->$propName;

                //var_dump()
                if (empty($outworkHours)) {
                    continue;
                }


                //$och = $partData->practice;
                //$zaoch = $partData->practice_za;
                //$och_zaoch = $partData->practice_oza;




                if(empty($och) && empty($zaoch) && empty($och_zaoch)){
                    //continue;
                }



                if(!$was_setuped_){


                    $theme_increase_counter++;

                    $independentWork.= '
					<tr >
						<td colspan="10"><b>Раздел '.$sind.'. '.$part->name_segment.'</b></td>
					</tr> ';

                    $was_setuped_ = true;
                }

                //lemuria 16.01.2023
                $criteriaListByTopic = implode(', ', getCriteriaListByTopic($partData, $outworkCriteriaList));
                $criteriaListByTopic = !empty($criteriaListByTopic) ? '' . $criteriaListByTopic : '';
				
				if(!empty($_REQUEST['rg_data'])){
					
					//var_dump($partData);
					//var_dump($criteriaListByTopic);
				}



                $temporary_data = explode(",",$criteriaListByTopic);
                $was_find_test = false;


                if(!empty($temporary_data)){



                    foreach($temporary_data as $kk_t => &$da_temp){


                        //$da_temp = str_replace($yummy, $healthy, $da_temp);

                        //$da_temp = str_replace($yummy_l, $healthy, $da_temp);
						
						//lemuria 10.10.2024
						 $da_temp = str_replace($yummy_samo, $healthy_samo, $da_temp);

                        $da_temp = str_replace($yummy_samo_l, $healthy_samo, $da_temp);



                        $only_spaces = str_replace(" ","",$da_temp);
                        $da_temp_check_test = mb_strpos($da_temp, "Тест");
                        if($da_temp_check_test !== false){
                            if(!$was_find_test){
                                $was_find_test = true;
                            }

                            else{

                                unset($temporary_data[$kk_t]);
                            }

                        }

                        if(empty($da_temp) or empty($only_spaces)){
                            unset($temporary_data[$kk_t]);
                        }
                    }

                    if(!empty($temporary_data)){
                        $criteriaListByTopic =  implode(",",$temporary_data);
                    }
                    else{
                        $criteriaListByTopic = "";
                    }
                }

                /*

                $independentWork .= <<<EOT
                            <tr>
                                <td colspan="3">
                                    Раздел {$segmentIndex}. $part->name_segment<br>
                                    Тема {$topicIndex}. $partData->name_segment
                                </td>
                                <td colspan="3">Проработка учебного материала с использованием ресурсов учебно-методического и информационного обеспечения дисциплины.</td>
                                <td colspan="1">$outworkHours</td>
                                <td colspan="3">$criteriaListByTopic</td>
                            </tr>
                EOT;
                */



                $independentWork .= <<<EOT
                            <tr>
                                <td colspan="3">
                                    Тема {$sind}.{$topicIndex}. $partData->name_segment
                                </td>
                                <td colspan="3">Проработка учебного материала с использованием ресурсов учебно-методического и информационного обеспечения дисциплины.</td>
                                <td colspan="1">$outworkHours</td>
                                <td colspan="3">$criteriaListByTopic</td>
                            </tr>
                EOT;
                //lemuria 16.01.2023

            }
            $topicIndex = 1;
            $segmentIndex++;
        }

        /** Если таблица не пустая для очной/заочной/очно-заочной формы обучения, то создать таблицу */
        if ($independentWork) {
            $independentWorkResult .= '<p>Форма обучения: <u>' . $form . '</u></p><table>
            <thead>
                <tr style="font-weight: bold; background-color: lightgrey">
                    <th colspan="3">Название разделов и тем</th>
                    <th colspan="3">Вид самостоятельной работы <i>(проработка учебного материала, решение задач, реферат, доклад,
                        контрольная работа,подготовка к сдаче зачета, экзамена и др).</i>
                    </th>
                    <th colspan="1">Объем в часах</th>
                    <th colspan="3">Форма контроля <i>(проверка решения задач, реферата и др.)</i></th>
                </tr>
            </thead>
            <tbody>
            ';
            $independentWorkResult .= $independentWork;
            $independentWorkResult .= '</tbody>
                </table>';
        }
    }
}

if(!empty($_REQUEST['rg_data'])){
					
					
					//exit('sdfsdf');
				}
//exit('sdfsdf');


//lemuria
if(!empty($rdp_another)){


    foreach($rdp_another as $rpd_another_data){


        foreach ($formsEduc as $key => $form) {
            $segmentIndex = 1;
            $topicIndex = 1;
            $propName = 'outwork' . $key;
            $independentWork = '';

            foreach ($rpd_another_data->parts as $part) {
                foreach ($part->data as $partData) {
                    $outworkHours = $partData->$propName;
                    if (empty($outworkHours)) {
                        continue;
                    }

                    $criteriaListByTopic = implode(', ', getCriteriaListByTopic($partData, $outworkCriteriaList));
                    $criteriaListByTopic = !empty($criteriaListByTopic) ? 'Проверка: ' . $criteriaListByTopic : '';
                    $independentWork .= <<<EOT
                            <tr>
                                <td colspan="3">
                                    Раздел {$segmentIndex}. $part->name_segment<br>
                                    Тема {$topicIndex}. $partData->name_segment
                                </td>
                                <td colspan="3">Проработка учебного материала с использованием ресурсов учебно-методического и информационного обеспечения дисциплины.</td>
                                <td colspan="1">$outworkHours</td>
                                <td colspan="3">$criteriaListByTopic</td>
                            </tr>
                EOT;

                    $topicIndex++;
                }
                $topicIndex = 1;
                $segmentIndex++;
            }

            /** Если таблица не пустая для очной/заочной/очно-заочной формы обучения, то создать таблицу */

            //lemuria 16.01.2023 К СДАЧЕ
            if ($independentWork) {
                $independentWorkResult .= '<p>Форма обучения: <u>' . $form . '</u></p><table>
            <thead>
                <tr style="font-weight: bold; background-color: lightgrey">
                    <th colspan="3">Название разделов и тем</th>
                    <th colspan="3">Вид самостоятельной работы <i>(проработка учебного материала, решение задач, реферат, доклад,
                        контрольная работа,подготовка к сдаче зачета, экзамена и др).</i>
                    </th>
                    <th colspan="1">Объем в часах</th>
                    <th colspan="3">Форма контроля <i>(проверка решения задач, реферата и др.)</i></th>
                </tr>
            </thead>
            <tbody>
            ';
                $independentWorkResult .= $independentWork;
                $independentWorkResult .= '</tbody>
                </table>';
            }
        }


    }


}


//$independentWorkResult = str_replace($yummy, $healthy, $independentWorkResult);

//$independentWorkResult = str_replace($yummy_l, $healthy, $independentWorkResult);


		 $independentWorkResult = str_replace($yummy_samo, $healthy_samo, $independentWorkResult);

                        $independentWorkResult = str_replace($yummy_samo_l, $healthy_samo, $independentWorkResult);


return $independentWorkResult;
