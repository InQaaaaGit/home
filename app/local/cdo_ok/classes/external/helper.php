<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Вспомогательный класс для работы с external API структурами.
 *
 * @package    local_cdo_ok
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cdo_ok\external;

use core_external\external_single_structure;
use core_external\external_value;

/**
 * Класс-хелпер для формирования структур external API.
 */
class helper {

    /**
     * Получение структуры объекта вопроса для external API.
     *
     * @param array $addons Дополнительные поля для добавления в структуру
     * @return external_single_structure Структура объекта вопроса
     */
    public static function get_external_question_object(array $addons = []): external_single_structure {
        $baseStructure = [
            'question' => new external_value(PARAM_TEXT, 'Текст вопроса', VALUE_REQUIRED),
            'type' => new external_value(PARAM_INT, 'Тип вопроса', VALUE_REQUIRED),
            'usermodified' => new external_value(PARAM_INT, 'ID пользователя, изменившего запись', VALUE_REQUIRED),
            'timecreated' => new external_value(PARAM_INT, 'Время создания', VALUE_REQUIRED),
            'timemodified' => new external_value(PARAM_INT, 'Время последнего изменения', VALUE_REQUIRED),
            'parameters' => new external_value(PARAM_TEXT, 'Параметры вопроса', VALUE_REQUIRED),
            'first_value_of_type' => new external_value(PARAM_TEXT, 'Значение первого параметра', VALUE_DEFAULT, ''),
            'second_value' => new external_value(PARAM_TEXT, 'Значение второго параметра', VALUE_DEFAULT, ''),
            'group_tab' => new external_value(PARAM_INT, 'Группа, к которой принадлежит вопрос', VALUE_REQUIRED),
            'visible' => new external_value(PARAM_BOOL, 'Статус видимости вопроса', VALUE_REQUIRED),
            'actions' => new external_value(PARAM_TEXT, 'Доступные действия', VALUE_DEFAULT, ''),
            'sort' => new external_value(PARAM_INT, 'Порядок сортировки', VALUE_REQUIRED),
            'id' => new external_value(PARAM_INT, 'ID вопроса', VALUE_OPTIONAL),
        ];

        return new external_single_structure(array_merge($baseStructure, $addons));
    }
}