<?php
namespace local_cdo_unti2035bas\form;

require_once($CFG->libdir . '/formslib.php');

use moodleform;

/**
 * Форма для отправки ведомостей оценок
 */
class grade_statements_form extends moodleform {

    /**
     * Определение формы
     */
    public function definition(): void
    {
        global $DB;
        
        $mform = $this->_form;

        // Заголовок
       // $mform->addElement('header', 'general', get_string('grade_course_selection', 'local_cdo_unti2035bas'));

        // Получаем курсы из потоков
        $sql = "
            SELECT DISTINCT s.courseid, c.fullname, s.comment 
            FROM {cdo_unti2035bas_stream} s 
            LEFT JOIN {course} c ON c.id = s.courseid 
            WHERE s.deleted = 0 
            ORDER BY c.fullname
        ";
        $course_records = $DB->get_records_sql($sql);

        // Формируем список курсов для выпадающего списка
        $course_options = ['' => '-- ' . get_string('choosedots') . ' --'];
        
        foreach ($course_records as $record) {
            $course_name = $record->fullname ?: get_string('coursenotfound', 'local_cdo_unti2035bas');
            $display_name = "ID: {$record->courseid} - {$course_name}";
            if (!empty($record->comment)) {
                $display_name .= " ({$record->comment})";
            }
            $course_options[$record->courseid] = $display_name;
        }

        // Выпадающий список курсов
        $mform->addElement(
            'select', 
            'course_id', 
            get_string('grade_course_selection', 'local_cdo_unti2035bas'), 
            $course_options
        );
        $mform->addRule('course_id', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('course_id', 'grade_course_selection', 'local_cdo_unti2035bas');

        // Скрытое поле для действия
        $mform->addElement('hidden', 'action', 'send');
        $mform->setType('action', PARAM_ALPHA);

        // Кнопки
        $this->add_action_buttons(false, get_string('send_grade_statements_link', 'local_cdo_unti2035bas'));
    }

    /**
     * Валидация формы
     *
     * @param array $data массив данных формы
     * @param array $files массив файлов
     * @return array массив ошибок
     */
    public function validation($data, $files): array
    {
        $errors = parent::validation($data, $files);

        if (empty($data['course_id'])) {
            $errors['course_id'] = get_string('required');
        }

        return $errors;
    }
} 
