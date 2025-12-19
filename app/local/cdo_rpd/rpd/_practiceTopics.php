<?php

$practiceTopics = '';
$tind = 1;
$sind = 1;


#$merged_themes = array_merge($rpd->parts, $rpdSodev->parts ?? []);
if (empty($rpdSodev)) {
    $parts = [];
} else {
    $parts = $rpdSodev->parts;
}
$merged_themes = array_merge($rpd->parts, $parts);
$count_razdel = count($merged_themes);
$theme_increase_counter = 0;
foreach (array_merge($rpd->parts, $rpdSodev->parts ?? []) as $part) {
    //lemuria 16.01.2023


    $name_razdel_was_setuped  = false;

    /*
    if($count_razdel > 1){
        $practiceTopics .= '<h4><b> Раздел ' . $sind . '. ' . $part->name_segment . '</b></h4>';
    }
    */

    //$theme_increase_counter ++;

    $tind =0;
    foreach ($part->data as $d) {

        //lemuria 16.01.2023
        if(!isset($d->data->praktik)){
            $praktik =[];
        }
        else{
            $praktik  = $d->data->praktik;
        }

        //var_dump($d);

        if(empty($praktik)){
            //continue;
        }

        //$chas = $d->data->praktik;

        $tind++;

        $och = $d->practice;
        $zaoch = $d->practice_za;
        $och_zaoch = $d->practice_oza;

        if(empty($och) && empty($zaoch) && empty($och_zaoch)){
            continue;
        }

        //$sind

        //$theme_increase_counter++;
        if(!$name_razdel_was_setuped){
            $theme_increase_counter++;
            if($count_razdel > 1){
                $practiceTopics .= '<h4><b> Раздел ' . $theme_increase_counter.'. ' . $part->name_segment . '</b></h4>';

                $name_razdel_was_setuped = true;
            }

        }


        //var_dump($praktik);
        //lemuria 16.01.2023
        $practiceTopics .= "<h4>Тема {$theme_increase_counter}.{$tind}. {$d->name_segment}</h4>";    //lemuria 16.01.2023 //<p>{$d->description}</p>
        if (!empty($d->seminaryQuestion) || !empty($d->seminaryQuestion_za) || !empty($d->seminaryQuestion_oza)) {
            $practiceTopics .= 'Вопросы к теме:';
        }
        if (!empty($d->seminaryQuestion)) {
            $practiceTopics .= "<p>Очная форма</p><p>" . nl2br($d->seminaryQuestion, false) . "</p>";
        }
        if (!empty($d->seminaryQuestion_za)) {
            $practiceTopics .= "<p>Заочная форма</p><p>" . nl2br($d->seminaryQuestion_za, false) . "</p>";
        }
        if (!empty($d->seminaryQuestion_oza)) {
            $practiceTopics .= "<p>Очно-заочная форма</p><p>" . nl2br($d->seminaryQuestion_oza, false) . "</p>";
        }

    }
    $sind++;
}




//lemuria
if(!empty($rdp_another)){


    foreach($rdp_another as $rpd_another_data){

        $tind = 1;
        $sind = 1;

        $merged_themes = array_merge($rpd['parts'], $rpdSodev->parts ?? []);
        $count_razdel = count($merged_themes);

        foreach ($rpd_another_data->parts as $part) {
            if($count_razdel > 1){
                $practiceTopics .= '<h4><b> Раздел ' . $sind . '. ' . $part->name_segment . '</b></h4>';
            }

            foreach ($part->data as $d) {

                //lemuria 16.01.2023
                if(!isset($d->data->praktik)){
                    $praktik =[];
                }
                else{
                    $praktik  = $d->data->praktik;
                }

                if(empty($praktik)){
                    continue;
                }


                $och = $d->practice;
                $zaoch = $d->practice_za;
                $och_zaoch = $d->practice_oza;

                if(empty($och) && empty($zaoch) && empty($och_zaoch)){
                    continue;
                }


                $practiceTopics .= "<h4>Тема {$tind}. {$d->name_segment}</h4>";   //lemuria 16.01.2023 //<p>{$d->description}</p>
                if (!empty($d->seminaryQuestion) || !empty($d->seminaryQuestion_za) || !empty($d->seminaryQuestion_oza)) {
                    $practiceTopics .= 'Вопросы к теме:';
                }
                if (!empty($d->seminaryQuestion)) {
                    $practiceTopics .= "<p>" . nl2br($d->seminaryQuestion, false) . "</p>";
                }
                if (!empty($d->seminaryQuestion_za)) {
                    $practiceTopics .= "<p>" . nl2br($d->seminaryQuestion_za, false) . "</p>";
                }
                if (!empty($d->seminaryQuestion_oza)) {
                    $practiceTopics .= "<p>" . nl2br($d->seminaryQuestion_oza, false) . "</p>";
                }
                $tind++;
            }
            $sind++;


        }

    }
}

return $practiceTopics;
