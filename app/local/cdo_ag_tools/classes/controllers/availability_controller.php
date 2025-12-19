<?php

namespace local_cdo_ag_tools\controllers;

use availability_date\condition;
use availability_profile\condition as condition_profile;
use dml_exception;
use core_availability\tree;

class availability_controller
{
    /**
     * @param array $section_ids
     * @param $email
     * @param null $direction
     * @param null $time
     * @param bool $delete_if_exists
     * @throws dml_exception
     * @throws \coding_exception
     */
    public static function set_section_availability(array $section_ids, $email, $direction = null, $time = null, bool $delete_if_exists = false): void
    {
        global $DB;

        foreach ($section_ids as $section_id) {
            // Получаем текущее условие доступности.
            $record = $DB->get_record('course_sections', ['id' => $section_id], 'id, availability');

            if ($direction !== null && $time !== null) {
                // Создаем новое условие по дате.
                $newCondition = condition::get_json($direction, $time);
                /*   $newCondition['nodeUID'] = uniqid();*/
            } else {
                // Новое условие по email.
                $newCondition = condition_profile::get_json(
                    false,
                    'email',
                    condition_profile::OP_IS_EQUAL_TO,
                    $email
                );
            }

            $condition_changed = false; // Флаг для отслеживания изменений условий доступности.
            $newConditionJson = json_encode($newCondition);
            $availability = null;

            if ($delete_if_exists) {
                // Если нужно удалить существующее условие, ищем и удаляем.
                if (!empty($record->availability)) {
                    $existingAvailability = json_decode($record->availability); // Используем без параметра true

                    // Проверка на валидную структуру
                    if (isset($existingAvailability->op) && isset($existingAvailability->c) && is_array($existingAvailability->c)) {
                        foreach ($existingAvailability->c as $index => $existingCondition) {
                            if (json_encode($existingCondition) === $newConditionJson) {
                                unset($existingAvailability->c[$index]);
                                // Reindex array to avoid gaps.
                                $existingAvailability->c = array_values($existingAvailability->c);
                                $condition_changed = true;
                                break; // Предполагаем, что условие уникально.
                            }
                        }

                        // Обновляем массив showc
                        $existingAvailability->showc = array_fill(0, count($existingAvailability->c), true);
                        $availability = $existingAvailability;
                    }
                }
            } else {
                // Добавляем новое условие, если $delete_if_exists = false.
                if (empty($record->availability)) {
                    // Если условие пустое, создаем новое.
                    $availability = new \stdClass();
                    $availability->op = '|';
                    $availability->c = [$newCondition];
                    $availability->showc = [true];
                    $availability->show = true;
                    $condition_changed = true;
                } else {
                    // Если условие существует, дополняем его.
                    $existingAvailability = json_decode($record->availability); // Используем без параметра true

                    // Добавляем новое условие с оператором "ИЛИ".
                    if (empty($existingAvailability)) {
                        // Если условие пустое, создаем новое.
                        $availability = new \stdClass();
                        $availability->op = '&';
                        $availability->c = [$newCondition];
                        $availability->showc = [true];
                        $availability->show = true;
                        $condition_changed = true;
                    } elseif (!isset($existingAvailability->op) || !isset($existingAvailability->c) || !is_array($existingAvailability->c)) {
                        // Существующее условие может быть простым объектом условия без оператора
                        // Проверяем, что оно имеет структуру условия 
                        if (isset($existingAvailability->type)) {
                            // Это простое условие, создаем новую структуру с оператором "|"
                            $availability = new \stdClass();
                            $availability->op = '|';
                            $availability->c = [$existingAvailability, $newCondition];
                            $availability->showc = [true, true];
                            $availability->show = true;
                        } else {
                            // Это некорректная структура, создаем новую только с новым условием
                            $availability = new \stdClass();
                            $availability->op = '|';
                            $availability->c = [$newCondition];
                            $availability->showc = [true];
                            $availability->show = true;
                        }
                        $condition_changed = true;
                    } elseif ($existingAvailability->op === '|') {
                        // Если существующее условие - массив с оператором "ИЛИ"

                        // Проверяем, есть ли уже такое условие
                        $addCondition = true;
                        foreach ($existingAvailability->c as $existingCondition) {
                            if (json_encode($existingCondition) === $newConditionJson) {
                                $addCondition = false;
                                break;
                            }
                        }

                        if ($addCondition) {
                            $existingAvailability->c[] = $newCondition;
                            // Обновляем массив showc
                            $existingAvailability->showc = array_fill(0, count($existingAvailability->c), true);
                            // Добавляем ключ show
                            $existingAvailability->show = true;
                            $condition_changed = true;
                        }
                        $availability = $existingAvailability;

                    } elseif ($existingAvailability->op === '&') {
                        // Если существующее условие - массив с оператором "И"

                        // Проверяем, есть ли уже такое условие
                        $addCondition = true;
                        foreach ($existingAvailability->c as $existingCondition) {
                            if (json_encode($existingCondition) === $newConditionJson) {
                                $addCondition = false;
                                break;
                            }
                        }

                        if ($addCondition) {
                            // Обновляем массив showc для существующего условия
                            $existingAvailability->showc = array_fill(0, count($existingAvailability->c), true);

                            $availability = new \stdClass();
                            $availability->op = '|';
                            $availability->c = [$existingAvailability, $newCondition];
                            $availability->showc = [true, true];
                            $availability->show = true;
                            $condition_changed = true;
                        } else {
                            $availability = $existingAvailability;
                        }
                    } else {
                        // Обработка других случаев с неизвестным оператором

                        // Проверяем, есть ли уже такое условие
                        $addCondition = true;
                        foreach ($existingAvailability->c as $existingCondition) {
                            if (json_encode($existingCondition) === $newConditionJson) {
                                $addCondition = false;
                                break;
                            }
                        }

                        if ($addCondition) {
                            // Обновляем массив showc для существующего условия
                            $existingAvailability->showc = array_fill(0, count($existingAvailability->c), true);

                            $availability = new \stdClass();
                            $availability->op = '|';
                            $availability->c = [$existingAvailability, $newCondition];
                            $availability->showc = [true, true];
                            $availability->show = true;
                            $condition_changed = true;
                        } else {
                            $availability = $existingAvailability;
                        }
                    }
                }
            }

            // Обновляем запись в таблице course_sections, только если условие было изменено.
            if ($condition_changed) {
                try {
                    // Проверяем структуру через создание экземпляра дерева
                    $tree = new tree($availability);
                    $record->availability = json_encode($availability);
                    $DB->update_record('course_sections', $record);
                } catch (\Exception $e) {
                    debugging('Ошибка создания структуры доступности: ' . $e->getMessage(), DEBUG_DEVELOPER);
                }
            }
        }
    }
}
