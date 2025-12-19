<?php

namespace block_cdo_schedule\output\schedule;

use coding_exception;
use context_system;
use core\exception\moodle_exception;
use dml_exception;
use html_writer;
use Locale;
use moodle_url;
use renderer_base;
use SoapClient;
use Throwable;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;
use tool_cdo_config\tools\dumper;
use block_cdo_schedule\handlers\schedule_handler;

class renderable implements \renderable, \templatable
{
    private string $template = 'block_cdo_schedule/main';
    private array $days = [
        'Воскресенье', 'Понедельник', 'Вторник', 'Среда',
        'Четверг', 'Пятница', 'Суббота'
    ];
    private string $RU_language = 'ru';

    /**
     * Use not everywhere
     * @return string
     * @throws cdo_type_response_exception
     * @throws cdo_config_exception
     * @throws coding_exception
     */
    public function get_subgroups(): string
    {
        global $USER;
        $options = di::get_instance()->get_request_options();
        $options->set_properties(
            [
                'id' => $USER->id
            ]
        );

        $request = di::get_instance()->get_request('get_subgroup')->request($options);
        $data = $request->get_request_result()->to_array();

        return implode(";", $data['data']);

    }

    /**
     * @param string $period
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_student_schedule(string $period = 'week'): array
    {
        global $USER;

        $data = [];
        if (has_capability('block/cdo_schedule:viewstudentschedule', context_system::instance())) {
            if (get_config("cdo_schedule", "block_cdo_schedule_use_sub_groups")) {
                $subgroups = $this->get_subgroups();
            }
            $options = di::get_instance()->get_request_options();
            $options->set_properties(
                [
                    'date' => date('Ymd'),
                    'period' => $period,
                    'user_id' => $USER->id
                ]
            );
            if ($USER->id == '24193080') {
                var_dump($options);
            }
            $request = di::get_instance()->get_request('get_schedule')->request($options);
            $data = $request->get_request_result()->to_array();
            foreach ($data as &$schedule) {
                $weekday = date('w');
                $schedule['active'] = '';
                $schedule['show'] = '';
                if ((int)$weekday === (int)$schedule['order']) {
                    $schedule['active'] = 'active';
                    $schedule['show'] = 'show';
                }
                $schedule['order_name'] = $this->days[$schedule['order']];
                $schedule['count_lessons'] = count($schedule['items']);
            }
        }
        return $data;
    }

    public function get_student_schedule_bit(): array
    {
        $data = [];
        if (has_capability('block/cdo_schedule:viewstudentschedule', context_system::instance())) {
            if (get_config("cdo_schedule", "block_cdo_schedule_use_sub_groups")) {
                $subgroups = $this->get_subgroups();
            }
            $options = di::get_instance()->get_request_options();
            $options->set_properties(
                [
                    "ДатаС" => date('c'),
                    "ДатаПо" => date('c')
                ]
            );

            $request = di::get_instance()->get_request('get_schedule');
            try {
                $client = new SoapClient($request->get("endpoint"), [
                    "login" => "webservices",
                    "password" => "WebTest17"
                ]);
                $schedule = $client->ПолучитьРасписаниеБезГруппы(
                    [
                        "ДатаС" => date('c'),
                        "ДатаПо" => date('c')
                    ]
                );
                $dto = [];
                foreach ($schedule->return->List as $schedule_element) {
                    $l = $schedule_element->Groups->List->Group->Name;
                    if ($l === "ИППУ19-4Д") {
                        $groups_item = [];
                        foreach ($schedule_element->Groups->List as $group_element) {
                            $group = [
                                "id" => $group_element->GUID,
                                "name" => $group_element->Name,
                                "subgroup" => "",
                                "subgroup_id" => ""
                            ];
                            $groups_item[] = $group;
                        }

                        $schedule_item = [
                            "date" => $schedule_element->Day->Val,
                            "order" => 0,
                            "items" => [
                                "date" => $schedule_element->Day->Val,
                                "order" => 0,
                                "start_time" => $schedule_element->STime->Val,
                                "end_time" => $schedule_element->ETime->Val,
                                "groups" => $groups_item,
                                "lessons" => [
                                    "discipline" => [
                                        "id" => $schedule_element->Discipline->GUID,
                                        "name" => $schedule_element->Discipline->Name,
                                    ],
                                    "lesson_type" => [
                                        "id" => $schedule_element->LoadType->Name,
                                        "uid" => $schedule_element->ViewTypes->GUID,
                                        "name" => $schedule_element->ViewTypes->Name
                                    ],
                                    "period_of_study" => [
                                        "id" => $schedule_element->AcademicYear->GUID,
                                        "name" => $schedule_element->AcademicYear->Name,
                                        "semestr_number" => $schedule_element->Semester,
                                        "start_year" => "",
                                        "end_year" => ""
                                    ],
                                    "lesson_key" => "",
                                    "lesson_replacement_key" => ""
                                ],
                                "classrooms" => [
                                    "room" => [
                                        "id" => $schedule_element->Auditory->GUID,
                                        "name" => $schedule_element->Auditory->Name
                                    ],
                                    "building" => [
                                        "id" => $schedule_element->Corps->GUID,
                                        "name" => $schedule_element->Corps->Name
                                    ]
                                ],
                                "teachers" => [
                                    "id" => $schedule_element->Teacher->GUID,
                                    "name" => $schedule_element->Teacher->Name
                                ]
                            ]
                        ];
                        $dto[] = $schedule_item;
                    }
                }
            } catch (\SoapFault $e) {

            }
            /*$data = $request->get_request_result()->to_array();
            foreach ($data as &$schedule) {
                $schedule['order_name'] = $this->days[$schedule['order']];
                $schedule['count_lessons'] = count($schedule['items']);
            }*/
        }
        return $data;
    }

    /**
     * @param string $period
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function get_teacher_schedule(string $period = 'week'): array
    {
        global $USER;

        $data = [];
        if (has_capability('block/cdo_schedule:viewteacherschedule', context_system::instance())) {
            $options = di::get_instance()->get_request_options();
            $options->set_properties(
                [
                    'date' => date('Ymd'),
                    'period' => $period,
                    'user_id' => $USER->id,
                    'type' => 'teacher'
                ]
            );
            $request = di::get_instance()->get_request('get_schedule')->request($options);
            $data = $request->get_request_result()->to_array();
            foreach ($data as &$schedule) {
                if ($USER->lang === $this->RU_language) {
                    $schedule['order_name'] = $this->days[$schedule['order']];
                } else {
                    $schedule['order_name'] = date('l', strtotime($schedule['date']));
                }
                $schedule['count_lessons'] = count($schedule['items']);
                $weekday = date('w');
                $schedule['active'] = '';
                $schedule['show'] = '';
                if ((int)$weekday === (int)$schedule['order']) {
                    $schedule['active'] = 'active';
                    $schedule['show'] = 'show';
                }

                foreach ($schedule['items'] as &$schedule_item) {
                    $attendance_link = new moodle_url(
                        '/blocks/cdo_schedule/attendance.php',
                        [
                            'date_1c' => $schedule['date_1c'],
                            'start_time' => $schedule_item['start_time'],
                            'end_time' => $schedule_item['end_time'],
                            'group' => $schedule_item['groups'][0]['id'],
                            'discipline' => $schedule_item['lesson']['discipline']['id'],
                            'edu_plan' => $schedule_item['lesson']['edu_plan'],
                            'lesson_type' => $schedule_item['lesson']['lesson_type']['id'],
                            'period_of_study' => $schedule_item['lesson']['period_of_study']['period']['id'],
                            'training_course' => $schedule_item['lesson']['training_course'],
                            'employee' => $schedule_item['teachers'][0]['id']
                        ]
                    );
                    $schedule_item['html_attendance_link'] = html_writer::link($attendance_link, 'Посещаемость');
                }
            }
        }
        return $data;
    }

    /**
     * @param string $period
     * @return array
     */
    public function get_schedule(string $period = 'week'): array
    {
        try {
            $data['schedule_student'] = $this->get_student_schedule($period); // []
            $data['schedule_teacher'] = $this->get_teacher_schedule($period);
            $data['have_schedule_student'] = !empty($data['schedule_student']);
            $data['have_schedule_teachers'] = !empty($data['schedule_teacher']);
            $data['have_both_schedule'] = $data['have_schedule_student'] && $data['have_schedule_teachers'];

            return $data;
        } catch (Throwable $e) {
            return [
                'error_message' => $e->getMessage()
            ];
        }
    }

    public function prepared_schedule_to_full_schedule($type = 0): array
    {
        // Для публичного доступа используем альтернативный метод
        return $this->get_public_schedule_data();
    }

    /**
     * Получение данных расписания для публичного доступа
     * 
     * @return array
     * @throws cdo_config_exception
     * @throws cdo_type_response_exception
     * @throws coding_exception
     */
    public function get_public_schedule_data(): array
    {
        // Используем метод из schedule_handler, который не требует авторизации
        $data_return = schedule_handler::get_full_schedule_data('', '', '', '');
        
        $prepared_data = [];
        foreach ($data_return as $schedule_student_item) {
            foreach ($schedule_student_item['items'] as $schedule_student_item_items) {
                $last_string = '';
                $need_array = $schedule_student_item_items['teachers'];
                $added_information = $schedule_student_item_items['subgroups']['name'];

                foreach ($need_array as $item) {
                    $last_string = $item['name'];
                }

                $text_body =  '<b>' . $schedule_student_item_items['lesson']['discipline']['name'] . '</b><br>';
                if (!empty($schedule_student_item_items['classrooms']['building']['name'])) {
                    $text_body .= $schedule_student_item_items['classrooms']['building']['name'] . '<br>';
                }
                if (!empty($schedule_student_item_items['classrooms']['room']['name'])) {
                    $text_body .= $schedule_student_item_items['classrooms']['room']['name'] . '<br>';
                }
                if (!empty($schedule_student_item_items['lesson']['lesson_type']['name'])) {
                    $text_body .= $schedule_student_item_items['lesson']['lesson_type']['name'] . '<br>';
                }
                if (!empty($last_string)) {
                    $text_body .= $last_string . '<br>';
                }
                if (!empty($added_information)) {
                    $text_body .= $added_information;
                }

                // Генерируем уникальный ID для события
                $unique_id = uniqid('event_', true);
                
                $prepared_data[] = [
                    'id' => $unique_id,
                    'start_date' => date('Y-m-d', strtotime($schedule_student_item_items['date'])) . ' ' . $schedule_student_item_items['start_time'],
                    'end_date' => date('Y-m-d', strtotime($schedule_student_item_items['date'])) . ' ' . $schedule_student_item_items['end_time'],
                    'text' => $text_body,
                    'room' => $schedule_student_item_items['classrooms']['room']['name'],
                    'address' => $schedule_student_item_items['classrooms']['building']['name'],
                    'teacher' => $last_string,
                ];
            }
        }
        
        return $prepared_data;
    }

    /**
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array
    {
        $array = $this->get_schedule();
        $array['template'] = $this->template;
        return $array;
    }
}
