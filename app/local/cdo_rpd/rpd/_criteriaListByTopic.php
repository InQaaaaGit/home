<?php
/**
 * Возвращает список заполненных критериев и их названия по теме
 */

/**
 * @param $topic - $rpd->parts[]->data
 * @param $criteriaList - ['tests' => 'Тесты', 'esse' => 'Эссе']
 * @return array
 */
function getCriteriaListByTopic($topic, $criteriaList)
{
    $criteriaListByTopic = [];

    foreach ($topic->data as $key => $value) {
        if (!empty($value)) {
            if (key_exists($key, $criteriaList)) {
                $criteriaListByTopic[$key] = $criteriaList[$key];
            }
        }
    }

    return $criteriaListByTopic;
}