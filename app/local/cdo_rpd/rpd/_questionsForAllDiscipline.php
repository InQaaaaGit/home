<?php
$themes = [];

if (!empty($rpd->questionsForDiscipline)) {
    echo "<pre>";
    var_dump($rpd);
    die;
    foreach ($rpd->questionsForDiscipline as $discipline) {
        if (in_array($discipline->code, ['referat', 'exam_question'])) {
            foreach ($discipline->question as $question) {
                $themes[$discipline->code] .= $question->questionDescription . '<br>';

            }
        }
    }
}
echo "<pre>";
var_dump($themes);
die;
return $themes;
