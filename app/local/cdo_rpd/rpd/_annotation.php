<?php

$data['TYPE'] = 'Аннотация рабочей программы дисциплины';
$cmps = "<ul>";

foreach ($rpd->competencies as $c) {
    $c = splitcomp(["Код" => "", "Наименование" => "", "Название" => $c->title]);
    $cmps .= "<li>{$c['Наименование']} ({$c['Код']})</li>";
}

$cmps .= "</ul>";
$data['ACOMPS'] = $cmps;
$data['ANN31'] = "<ul style=\"white-space: pre\">";
$data['ANN32'] = "<ul style=\"white-space: pre\">";
$data['ANN33'] = "<ul style=\"white-space: pre\">";

foreach (array_merge($rpd->competencies, $rpdSodev->competencies ?? []) as $comp) {
    $data['ANN31'] .= $comp->requirement->know."<br>";
    $data['ANN32'] .= $comp->requirement->beAbleTo."<br>";
    $data['ANN33'] .= $comp->requirement->own."<br>";

}
$data['ANN31'] .= "</ul>";
$data['ANN32'] .= "</ul>";
$data['ANN33'] .= "</ul>";

$data['ANN51'] .= array_merge($rpd->auditWork, $rpdSodev->auditWork ?? []);
$data['ANN52'] .= array_merge($rpd->outwork, $rpdSodev->outwork ?? []);