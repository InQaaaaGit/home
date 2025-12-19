<?php

$laboratoryWorks = '';

if (!empty($rpd->questionsForDiscipline->lab_work->themes) || !empty($rpdSodev->questionsForDiscipline->lab_work->themes)) {
    foreach (array_merge($rpd->questionsForDiscipline->lab_work->themes, $rpdSodev->questionsForDiscipline->lab_work->themes ?? []) as $theme) {

        //lemuria 16.01.2023

        $laboratoryWorks .= htmlspecialchars($theme->name) . '<br>';

        if(!empty($theme->target)){
            $laboratoryWorks .= 'Цели: '.htmlspecialchars($theme->target) . '<br>';
        }

        if(!empty($theme->content)){
            $laboratoryWorks .= 'Содержание: '.htmlspecialchars($theme->content) . '<br>';
        }

        if(!empty($theme->result)){
            $laboratoryWorks .= 'Результаты: '.htmlspecialchars($theme->result) . '<br>';
        }

        if(!empty($theme->link)){
            $laboratoryWorks .= 'Ссылка: '.$theme->link . '<br>';
        }

        //lemuria 16.01.2023

    }
}

$laboratoryWorks = !empty($laboratoryWorks) ? $laboratoryWorks : 'Данный вид работы не предусмотрен УП.';
return $laboratoryWorks;
