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

class report6 implements report_i
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
    private $cells_for_merge;
    private $cells_for_colorized;

    /**
     * @throws coding_exception
     */
    public function __construct(questions_controller $questions_controller,
                                answers_controller   $answers_controller)
    {
        $this->answers_controller = $answers_controller;
        $this->additional_info = $this->get_users_additional_info();
        $this->filename = get_string('report:report6_name', 'local_cdo_ok');
        $this->cells_for_merge = [];
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
                $answer->edu_spec = '';
                $answer->edu_structure = '';
            }
        }
        $score = [];
        $data_answers = [];
        $data_answers_edu_spec = [];
        $data_answers_edu_structure = [];
        $data_answers_edu_discipline = [];
        usort($answers, function ($a, $b) {
            // Сортировка по первому свойству
            if ($a->edu_spec != $b->edu_spec) {
                return strcmp($a->edu_spec, $b->edu_spec);
            }

            // Сортировка по второму свойству
            if ($a->edu_structure != $b->edu_structure) {
                return strcmp($a->edu_structure, $b->edu_structure);
            }

            // Сортировка по третьему свойству
            return strcmp($a->discipline, $b->discipline);
        });
        $data_answers_edu_discipline_row = [];
        //Группировка для вычленения средних по группировкам
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
            //$data_answers_edu_discipline_row[$answer->discipline][] = $answer;
        }
        $score = array_values($score);
        foreach ($score as &$item) {
            $item['score_average'] = $item['average'] / $item['count'];// TODO округление
            unset($item['average']);
        }

        $iteratee = 0;

        foreach ($score as $item_score) {
            $data_answers_edu_discipline_row[$item_score['discipline']][] = $item_score;
        }
       /* echo '<pre>';
        var_dump($data_answers_edu_discipline_row); die();*/
        $data_answers_edu_discipline_row = array_values($data_answers_edu_discipline_row);
        foreach ($data_answers_edu_discipline_row as $discipline_grouped) {
            $i = 0;
            $counter = 0;
            $students = 0;

            foreach ($discipline_grouped as $itemA) {
                $iteratee++;
                $students += $itemA['count'];
                $counter += $itemA['score_average'];
               /* $students += $itemA->count;
                $counter += $itemA->score_average;*/
                $i++;
            }

            $itemA['score_average'] = $counter / $i;
            $itemA['question'] = ' СР.ЗН.';
            $itemA['count'] = $students / $i;
            /*$itemA->score_average = $counter / $i;
            $itemA->question = '';
            $itemA->count = $students / $i;*/

            array_splice($score, $iteratee, 0, [$itemA]);

        }

        usort($score, function ($a, $b) {
            // Сортировка по первому свойству
            if ($a['edu_spec'] != $b['edu_spec']) {
                return strcmp($a['edu_spec'], $b['edu_spec']);
            }

            // Сортировка по второму свойству
            if ($a['edu_structure'] != $b['edu_structure']) {
                return strcmp($a['edu_structure'], $b['edu_structure']);
            }

            if ($a['discipline'] != $b['discipline']) {
                return strcmp($a['discipline'], $b['discipline']);
            }

            // Сортировка по третьему свойству
            return strcmp($b['question'], $a['question']);
        });

        $row_iterate = 2;
        foreach ($score as $item_score) {

            if ($item_score['question'] === " СР.ЗН.") {
                $this->cells_for_colorized[] = "D$row_iterate:F$row_iterate";
            }
            $row_iterate++;

            $data_answers_edu_spec[$item_score['edu_spec']][] = $item_score;
            $data_answers_edu_structure[$item_score['edu_structure']][] = $item_score;
            $data_answers_edu_discipline[$item_score['discipline']][] = $item_score;

        }
        $this->add_cells_for_merge('A', $data_answers_edu_spec);
        $this->add_cells_for_merge('B', $data_answers_edu_structure);
        $this->add_cells_for_merge('C', $data_answers_edu_discipline);

        return $score;

    }

    private function add_cells_for_merge($alphabet, $data)
    {
        $row_spec_start = 2;
        foreach ($data as $item) {
            $row_number_end = $row_spec_start + count($item) - 1;
            $this->cells_for_merge[] = "$alphabet$row_spec_start:$alphabet$row_number_end";
            $row_spec_start = $row_number_end + 1;
        }
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
        return $this->cells_for_merge;
    }

    public function get_cells_color(): array
    {
        return $this->cells_for_colorized;
    }
}