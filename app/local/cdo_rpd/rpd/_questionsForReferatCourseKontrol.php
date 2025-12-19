<?php
$independentWork = '';

function addReferats($questions) {
    $result = '';
    $tind = 1;
    foreach ($questions as $question) {
        $result .= 'Тема ' . $tind . '. ' . strip_tags($question,"<img>") . '<br>';
        $tind++;
    }

    return $result;
}

if (!empty($rpd->questionsForAllThemes) || !empty($rpdSodev->questionsForAllThemes ?? [])) {

    $arThemes = [
        // todo добавить курсовые работы
		'course_work' => [],
        'kontrol_work' => [],
        'referat' => [],
    ];
	
	
	
	$controlsList_2 = [];


    foreach (array_merge($rpd->controlsList, $rpdSodev->controlsList ?? []) as $control){
        $controlsList_2[$control->code] = $control->code;
    }
	
	

    foreach (array_merge($rpd->questionsForAllThemes, $rpdSodev->questionsForAllThemes ?? []) as $discipline) {
		
		
		if(!in_array($discipline->code,$controlsList_2)){
			continue;
		}
		
        if (key_exists($discipline->code, $arThemes)) {
            //foreach ($discipline->questions as $question) {
                //$arThemes[$discipline->code][] = $question->questionDescription;
            //}
        }

         if( $discipline->code=='course_work'){

           // foreach ($discipline->competences->themes as $theme) {
                //$arThemes[$discipline->code][] = $theme->themeName;
            //}
			
			
			foreach ($discipline->competences->themes as $question) {
                //$arThemes[$discipline->code][] = $question->themeName;
            }

        }
		
		elseif (key_exists($discipline->code, $arThemes)) {
            foreach ($discipline->questions as $question) {
                $arThemes[$discipline->code][] = $question->questionDescription;
            }
        }
    }
	
		
	
		
	if(isset($rpd->questionsForDiscipline->course_work->themes)) {
		
		$array_search = $rpd->questionsForDiscipline->course_work->themes;
		
		 foreach (array_merge($rpd->questionsForDiscipline->course_work->themes, $rpdSodev->questionsForDiscipline->course_work->themes ?? []) as $discipline) {
		     $arThemes['course_work'][] = $discipline->themeName;
		 }
	}
	
	
	//edit for course_work and import
	
	
	if(!empty($_REQUEST['qqqq'])){
		var_dump($arThemes['course_work']); 
		exit('sdfsdf');
	}



    // todo добавить курсовые
    if (isset($arThemes['kontrol_work'])) {

        $themes_list = '';
        $themes_list = addReferats($arThemes['kontrol_work']);
        if(!empty($themes_list)){

            $independentWork .= '<b>Контрольные работы</b><br>';
            $independentWork .= $themes_list;

        }
    }
    if (isset($arThemes['referat'])) {

        $themes_list = '';
        $themes_list = addReferats($arThemes['referat']);
        if(!empty($themes_list)){

            $independentWork .= '<br><b>Темы рефератов</b><br>';
            $independentWork .= $themes_list;

        }
    }
    if (isset($arThemes['course_work'])) {

        $themes_list = '';
        $themes_list = addReferats($arThemes['course_work']);
        if(!empty($themes_list)){

            $independentWork .= '<br><b>Темы курсовой работы</b><br>';
            $independentWork .= $themes_list;

        }
    }
    /*
    if (isset($arThemes['referat'])) {
        $independentWork .= '<br><b>Темы рефератов</b><br>';
        $independentWork .= addReferats($arThemes['referat']);
    }

    if (isset($arThemes['course_work'])) {
        $independentWork .= '<br><b>Темы курсовой работы</b><br>';
        $independentWork .= addReferats($arThemes['course_work']);
    }
    */

}




//lemuria
if(!empty($rdp_another)){


    foreach($rdp_another as $rpd_another_data){


        if (!empty($rpd_another_data->questionsForAllThemes)) {

            $arThemes = [
                // todo добавить курсовые работы
                'kontrol_work' => [],
                'referat' => [],
            ];

            foreach ($rpd_another_data->questionsForAllThemes as $discipline) {
                if (key_exists($discipline->code, $arThemes)) {
                    foreach ($discipline->questions as $question) {
                        $arThemes[$discipline->code][] = $question->questionDescription;
                    }
                }

                else if( $discipline->code=='course_work'){


                    foreach ($discipline->competences->themes as $theme) {
                        $arThemes[$discipline->code][] = $theme->themeName;
                    }

                }
            }



            // todo добавить курсовые
            if ($arThemes['kontrol_work']) {
                $independentWork .= '<b>Контрольные работы</b><br>';
                $independentWork .= addReferats($arThemes['kontrol_work']);
            }
            if ($arThemes['referat']) {
                $independentWork .= '<br><b>Темы рефератов</b><br>';
                $independentWork .= addReferats($arThemes['referat']);
            }

            if ($arThemes['course_work']) {
                $independentWork .= '<br><b>Темы курсовой работы</b><br>';
                $independentWork .= addReferats($arThemes['course_work']);
            }

        }


    }

}



$independentWork = !empty($independentWork) ? $independentWork : 'Данный вид работы не предусмотрен УП.';

return $independentWork;
