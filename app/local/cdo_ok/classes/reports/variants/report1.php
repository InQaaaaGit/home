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

class report1 implements report_i
{
    use report_trait;

    private $sort_by_type = true;
    /**
     * @var answers_controller
     */
    protected $answers_controller;
    /**
     * @var mixed
     */
    public $additional_info;
    /**
     * @var lang_string|string
     */
    private $filename;

    /**
     * @throws coding_exception
     */
    public function __construct(questions_controller $questions_controller,
                                answers_controller   $answers_controller)
    {
        $this->answers_controller = $answers_controller;
        $this->additional_info = $this->get_users_additional_info();
        $this->filename = get_string('report:report1_name', 'local_cdo_ok');
    }

    public function get_filename(): string
    {
        return $this->filename;
    }
    /**
     * @throws dml_exception
     */
    public function get_data(): array
    {
        $answers = $this->answers_controller->get_answer_with_sort('WHERE ok.type=1 AND ok.group_tab=0');
        
        foreach ($answers as $answer) {
            $index = helper::findObjectById($answer->user_id, $this->additional_info);
            if ($index !== null) {
                $answer->edu_spec = $index->edu_spec ?? '';
                $answer->edu_structure = $index->edu_structure ?? '';
            } else {
                // Пользователь не найден в данных 1С - оставляем поля пустыми
                $answer->edu_spec = '';
                $answer->edu_structure = '';
            }
        }
        $score = [];
        foreach ($answers as $answer) {
            $key = str_replace(" ", "_", $answer->discipline) . "_" .
                str_replace(" ", "", $answer->edu_structure) . '_' .
                str_replace(" ", "", $answer->edu_spec) . '_' .
                str_replace(" ", "", $answer->question);
            if (!isset($score[$key])) {
                $score[$key] = [
                    'edu_spec' => $answer->edu_spec,
                    'edu_structure' => $answer->edu_structure,
                    'discipline' => $answer->discipline,
                    'question' => $answer->question,
                    'count' => 1,
                    'average' => (int)$answer->answer,
                ];
            } else {
                $score[$key]['average'] += (int)$answer->answer;
                $score[$key]['count']++;
            }
        }
        foreach ($score as &$item) {
            $item['score_average'] = $item['average'] / $item['count'];
            unset($item['average']);
        }
        return $score;

    }

    public function get_header(): array
    {
        $strings = [
            'report:edu_spec', 'report:edu_structure', 'report:discipline',
            'report:question_name', 'report:quantity_users', 'report:average_value',
        ];
        return (array)get_strings(
            ($strings),
            'local_cdo_ok'
        );
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