<?php


function fn_rg_rpd_out_questions($parts,$controlsList_2){


$examQuestions = '';
$qi = 1;
   foreach($parts as $k_p =>$p_data_main){

		if(empty($p_data_main->data)){
			continue;
		}
		
		foreach($p_data_main->data as $last_part_data_main){
			
			
			if(empty($last_part_data_main->data)){
			    continue;
			}
			
			foreach($last_part_data_main->data as $kkk=> $last_part_data){
				
				
				if(!in_array($kkk,$controlsList_2)){
					continue;
				}
				

		        if($kkk == 'exam_question'){
					
					if(!empty($last_part_data)){
						foreach($last_part_data as $question){
						
							$examQuestions .= $qi . '. ' . strip_tags($question->questionDescription,"<img>") . '<br>';
							$qi++;
						
						}
					}
				}
			}
		}
   }
   
   return $examQuestions;
   
}

$examQuestions = '';
if (!empty($rpd->questionsForAllThemes) || !empty($rpdSodev->questionsForAllThemes)) {
    $qi = 1;

    foreach (array_merge($rpd->questionsForAllThemes, $rpdSodev->questionsForAllThemes ?? []) as $discipline) {
        if ($discipline->code == 'exam_question') {
			
            foreach ($discipline->questions as $question) {
                $examQuestions .= $qi . '. ' . strip_tags($question->questionDescription,"<img>") . '<br>';
                $qi++;
            }
        }
    }
}


	$controlsList_2 = [];


    foreach (array_merge($rpd->controlsList, $rpdSodev->controlsList ?? []) as $control){
    #foreach (array_merge($rpd['controlsList'], $rpdSodev->controlsList ?? []) as $control){
        $controlsList_2[$control->code] = $control->code;
    }


$merged_data_parts = array_merge($rpd->parts, $rpdSodev->parts ?? []);

$examQuestions = fn_rg_rpd_out_questions($merged_data_parts,$controlsList_2);



//lemuria
if(!empty($rdp_another)){
	
	$examQuestions_another ='';
	
	foreach($rdp_another as $rpd_another_data){
		$qi = 1;
		
		if (!empty($rpd_another_data->questionsForAllThemes)) {
			
			
			foreach ($rpd_another_data->questionsForAllThemes as $discipline) {
				
				if ($discipline->code == 'exam_question') {
					 foreach ($discipline->questions as $question) {
						$examQuestions_another .= $qi . '. ' . strip_tags($question->questionDescription,"<img>") . '<br>';
						$qi++;
					}
				}
				
			}
			
		}
		
	}
	
	if(!empty($examQuestions_another)){
		
		$examQuestions .='<br>--------------------------------------------------------------------------------<br>'.$examQuestions_another;
	}
	
	
}


return $examQuestions;
