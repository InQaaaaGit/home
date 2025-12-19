<?php
/**
 * Возвращает список заполненных критериев и их названия
 */

$criteriaList = [];
//$criteriaList = get_object_vars($rpd->criteriaList);

/*
foreach ($rpd->controls as $control) {
    foreach ($control->enroleTypes as $enroleType) {
        if (key_exists($enroleType->code, $criteriaList)) {
            $criteriaList[$enroleType->code] = $enroleType->name;
        }
    }
}
*/

    foreach ($rpd->controlsList as $control) {
        //if (key_exists($enroleType->code, $criteriaList)) {
            $criteriaList[$control->code] = $control->name;
        //}
    }
	
	
	if(!empty($rdp_another)){
		
	
		foreach($rdp_another as $rpd_another_data){
			
			foreach ($rpd_another_data->controlsList as $control) {
				//if (key_exists($questions->code, $criteriaList)  && !empty($questions->questions)) {  //lemuria  && !empty($questions)
					//criteriaList[$questions->code] = $criteriaList[$questions->code];
				//}
				
				 $criteriaList[$control->code] = $control->name;
			}
			
		}
		
}
	

return $criteriaList;
