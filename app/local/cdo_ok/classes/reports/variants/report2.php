<?php

namespace local_cdo_ok\reports\variants;

use coding_exception;
use dml_exception;
use lang_string;
use local_cdo_ok\controllers\answers_controller;
use local_cdo_ok\controllers\questions_controller;
use local_cdo_ok\reports\report_i;
use local_cdo_ok\reports\report_trait;
use local_cdo_ok\services\service_ok_1c;
use moodle_exception;

class report2 implements report_i
{
    use report_trait;

    /**
     * @var questions_controller
     */
    public $questions_controller;
    /**
     * @var answers_controller
     */
    private $answers_controller;
    /**
     * @var mixed
     */
    private $additional_info;
    /**
     * @var lang_string|string
     */
    private $filename;

    /**
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function __construct(
        questions_controller $questions_controller,
        answers_controller   $answers_controller
    )
    {
        $this->questions_controller = $questions_controller;
        $this->answers_controller = $answers_controller;
        $this->additional_info = $this->get_users_additional_info();

        $this->filename = get_string('report:report2_name', 'local_cdo_ok');
    }

    public function get_filename(): string
    {
        return $this->filename;
    }

    public function get_header(): array
    {
        $strings = [
            'report:fio', 'report:group', 'report:edu_structure', 'report:edu_spec',
            'report:edu_year', 'report:edu_level', 'report:edu_form'
        ];
        $header = (array)get_strings(
            array_merge($strings,),
            'local_cdo_ok'
        );
        $questions = $this->questions_controller->get(['group_tab' => 1, 'visible' => 1]);
        $question_body = array_column($questions, 'question');
        return array_values($header + $question_body);
    }

    /**
     * @throws dml_exception
     */
    public function get_data(): array
    {
        $all_answers = $this->answers_controller->get_answer_with_sort('WHERE ok.group_tab=1');
        $user_answer = [];
        foreach ($all_answers as $all_answer) {
            $user_answer[$all_answer->user_id][] = $all_answer->answer;
        }
        $data = [];
        foreach ($user_answer as $key => $value) {

            $index = (array)$this->find_object_by_id($key, $this->additional_info);

            $index = array_values($index);
            array_pop($index); // последний элемент должен быть user_id и его удалить до вывода в xlsx
            $data[] = array_merge($index, $value);
        }

        return $data;
    }

    public function get_cells_for_merge(): array
    {
        return [];
    }
    public function get_cells_color(): array
    {
        return [];
    }
}