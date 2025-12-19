<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Форма для массовой синхронизации оценок с 1С
 *
 * @package     local_cdo_ag_tools
 * @copyright   InQaaaa
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cdo_ag_tools\forms;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * Форма синхронизации с 1С
 */
class sync_1c_form extends \moodleform {

    /**
     * Определение формы
     */
    public function definition() {
        $mform = $this->_form;

        // Поле выбора даты
        $mform->addElement('date_selector', 'datefrom', 
            get_string('sync_from_date', 'local_cdo_ag_tools'),
            ['optional' => true, 'timezone' => 99]);
        $mform->addHelpButton('datefrom', 'sync_from_date', 'local_cdo_ag_tools');
        $mform->setDefault('datefrom', 0);

        // Кнопки
        $this->add_action_buttons(true, get_string('start_sync_to_1c', 'local_cdo_ag_tools'));
    }

    /**
     * Валидация формы
     *
     * @param array $data Данные формы
     * @param array $files Файлы
     * @return array Ошибки валидации
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['datefrom']) && $data['datefrom'] > time()) {
            $errors['datefrom'] = get_string('error_future_date', 'local_cdo_ag_tools');
        }

        return $errors;
    }
}

