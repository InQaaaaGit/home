<?php

namespace local_cdo_ok\reports\variants;

use coding_exception;
use dml_exception;
use lang_string;
use local_cdo_ok\controllers\answers_controller;
use local_cdo_ok\controllers\questions_controller;
use local_cdo_ok\helper\helper;
use local_cdo_ok\reports\printer;
use local_cdo_ok\reports\report_i;
use local_cdo_ok\reports\report_trait;
use local_cdo_ok\services\integration;

class report3 implements report_i
{
    use report_trait;

    private $spreadsheet;
    /**
     * @var questions_controller
     */
    private $questions_controller;
    /**
     * @var answers_controller
     */
    private $answers_controller;
    /**
     * @var mixed
     */
    private $additional_info;

    public $cells_for_merge;
    /**
     * @var lang_string|string
     */
    private $filename;

    /**
     * @throws coding_exception
     */
    public function __construct(
        questions_controller $questions_controller,
        answers_controller   $answers_controller
    )
    {
        $this->questions_controller = $questions_controller;
        $this->answers_controller = $answers_controller;
        $this->additional_info = $this->get_users_additional_info();
        $this->cells_for_merge = [];
        $this->filename = get_string('report:report3_name', 'local_cdo_ok');

    }

    public function get_filename(): string
    {
        return $this->filename;
    }

    public function get_header(): array
    {
        $strings = [
            'report:fio', 'report:group', 'report:edu_structure', 'report:edu_spec',
            'report:edu_year', 'report:edu_level', 'report:edu_form',
            'report:question_name', 'report:answer'
        ];
        return (array)get_strings(
            $strings,
            'local_cdo_ok'
        );
    }

    /**
     * @throws dml_exception
     */
    public function get_data($spreadsheet = null): array
    {
        $all_answers = $this->answers_controller->get_answer_with_sort('WHERE ok.group_tab=1');
        $user_answer = [];
        foreach ($all_answers as $all_answer) {
            $user_answer[$all_answer->user_id][] = $all_answer;
        }
        $row_iterator = 1;
        $data = [];
        foreach ($user_answer as $key => $answer) {
            $index = (array)$this->find_object_by_id($key, $this->additional_info);
            $index = array_values($index);
            array_pop($index);
            $start_rows = $row_iterator + 1;
            foreach ($answer as $answer_item) {
                $row_iterator++;
                $new_info = $index;

                $new_info[] = $answer_item->question;
                $new_info[] = $answer_item->answer;
                $data[] = $new_info;
            }

            # $worksheet = $this->spreadsheet->getActiveSheet();
            $last_column_name = 'I';
            for ($i = 'A'; $i != $last_column_name; $i++) {
                $this->cells_for_merge[] = "$i$start_rows:$i$row_iterator";
            }
        }
        return $data;
    }

    public function get_cells_for_merge(): array
    {
        return $this->cells_for_merge;
    }

    public function get_cells_color(): array
    {
        return [];
    }
}