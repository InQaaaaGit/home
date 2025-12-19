<?php

namespace local_cdo_debts\output\debts;

use coding_exception;
use DateTime;
use local_cdo_debts\irbis\integrate;
use renderer_base;
use Throwable;
use tool_brickfield\local\areas\core_course\fullname;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\tools\dumper;

class renderable implements \renderable, \templatable
{

    private string $template_library = 'local_cdo_debts/library';
    private string $template_academic = 'local_cdo_debts/academic';
    private string $template_finance = 'local_cdo_debts/finance';
    public int $type_render;
    private $template;

    public function __construct(int $type_render = 0)
    {
        $this->type_render = $type_render;
    }

    //TODO превартить все в функциональный подкласс

    /**
     * @throws cdo_config_exception
     */
    public function get_library_debts_irbis(): array
    {
        global $USER;
        $debts = [];
        $irbis = new integrate();
        $irbis->set_user('FEA', '27'); // логин, пароль
        $irbis->set_arm('B');       // C - Каталогизатор
        $irbis->set_server('10.99.99.83');
        if ($irbis->login()) {
            $irbis->set_db('RDR');
            $search_fio = $USER->lastname . ' ' . $USER->firstname . ' ' . $USER->middlename;
            $search_exp = '"A=' . $search_fio . '"';
            #
            $student = $irbis->records_search($search_exp, 1000, 1);
            if ((int)$student['found'] === 1) {
                if ($irbis->error_code != 0) {
                    throw new cdo_config_exception(0, $irbis->error());
                }
                $stud_info = $student['records'][0]; //
                $parsed_stud_info = explode('#', $stud_info); // берем за данность, что MFN хранится в секции до #
                $reader = $irbis->record_read($parsed_stud_info[0]);
                if ($irbis->error_code != 0) {
                    throw new cdo_config_exception(0, $irbis->error());
                }
                if (isset($reader['fields'][40]))
                    foreach ($reader['fields'][40] as $item) {
                        $parse_result = $irbis->parse_field($item);
                        //С - наименование события;
                        //Е - Дата возврата = должна существовать чтобы определить что это книга
                        if (array_key_exists('C', $parse_result) && array_key_exists('E', $parse_result)) {
                            $time_str = strtotime($parse_result['E']);
                            if ($time_str < time()) { // Если дата возврата дальше чем текущая - не вернул книгу вовремя. берем за данность
                                $date = date('d.m.Y', $time_str);
                                $debts_item['doc_id'] = $parse_result['A'];
                                $debts_item['title'] = $parse_result['C'];
                                $debts_item['date_take'] = '';
                                $debts_item['date_return_plan'] = $date;
                                $debts_item['date_return_fact'] = '';
                                $debts_item['expired_text'] = '';
                                $debts[] = $debts_item;
                            }

                        }
                    }
            } elseif ((int)$student['found'] > 1) {
                throw new cdo_config_exception(0, 'Найдено более одной записи по ФИО=' . $search_exp);
            }
        }
        return ['library_debts' => $debts];
    }

    public function get_library_debts(): array
    {
        global $USER;

        $this->template = $this->template_library;
        $options = di::get_instance()->get_request_options();
        $options->set_properties(["user_id" => $USER->id]);

        try {
            $request = di::get_instance()->get_request('library_debts')->request($options);
            $data = $request->get_request_result()->to_array();
            foreach ($data['data'] as &$book) {
                $book['date_take'] = (new DateTime($book['date_take']))->format('d-m-Y H:i:s');
                $book['date_return_plan'] = (new DateTime($book['date_return_plan']))->format('d-m-Y H:i:s');
                $book['date_return_fact'] = (new DateTime($book['date_return_fact']))->format('d-m-Y H:i:s');
            }

            return $data;
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    public function get_academic_debts(): array
    {
        global $USER;
        $this->template = $this->template_academic;
        $options = di::get_instance()->get_request_options();
        $options->set_properties(["id" => $USER->id]);
        #$options->set_properties(["id" => 5]);

        try {
            $request = di::get_instance()->get_request('academic_debts')->request($options);

            $data = $request->get_request_result()->to_array();
            sort($data);
            return ["academic_debts" => $data];
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    public function get_financial_debts(): array
    {
        global $USER;
        $this->template = $this->template_finance;
        $options = di::get_instance()->get_request_options();
        $options->set_properties(["id" => $USER->id]);

        try {
            $request = di::get_instance()->get_request('financial_debts')->request($options);

            $data = $request->get_request_result()->to_array();
            sort($data);

            return ["financial_debts" => $data];
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    /**
     * @throws coding_exception
     * @throws cdo_config_exception
     */
    public function get_debts(): array
    {
        switch ($this->type_render) {
            case 1:
                $data = $this->get_library_debts();
                #$data = $this->get_library_debts_irbis();
                if (empty($data['library_debts'])) {
                    $data['data_empty'] = get_string('library_debts_not_found', 'local_cdo_debts');
                }
                $data['template'] = $this->template_library;
                break;
            case 3:
                $data = $this->get_financial_debts();
                if (empty($data['financial_debts'])) {
                    $data['data_empty'] = get_string('financial_debts_not_found', 'local_cdo_debts');
                }
                $data['template'] = $this->template_finance;

                break;
            case 2:
            default:
                $data = $this->get_academic_debts();

                if (empty($data['academic_debts'])) {
                    $data['data_empty'] = get_string('academic_debts_not_found', 'local_cdo_debts');
                }
                $data['template'] = $this->template_academic;
                break;
        }
        return $data;
    }

    /**
     * @throws cdo_config_exception
     * @throws coding_exception
     */
    public function export_for_template(renderer_base $output): array
    {
        global $PAGE, $CFG;
        $array = $this->get_debts();
        $array['url'] = $PAGE->url;
        return $array;
    }
}
