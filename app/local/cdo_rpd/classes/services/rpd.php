<?php

namespace local_cdo_rpd\services;

use coding_exception;
use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use dml_exception;
use invalid_parameter_exception;
use tool_cdo_config\di;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\exceptions\cdo_type_response_exception;

/**
 * Класс сервиса для работы с РПД (рабочими программами дисциплин)
 *
 * Предоставляет набор web-сервисов для взаимодействия с системой 1C
 * и управления рабочими программами дисциплин в LMS Moodle.
 *
 * @package    local_cdo_rpd
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rpd extends external_api
{

    /**
     * Описание параметров для метода назначения разработчика РПД в 1C
     *
     * @return external_function_parameters
     */
    public static function set_developer_on_rpd_1c_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'user_id' => new external_value(PARAM_TEXT, 'id', VALUE_REQUIRED),
                'rpd_id' => new external_value(PARAM_TEXT, 'id', VALUE_REQUIRED),
                'is_primary_dev' => new external_value(PARAM_TEXT, 'id', VALUE_REQUIRED),
                'blockControl' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, ''),
                'CRUD' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, ''),
            ]
        );
    }

    /**
     * Назначает разработчика на рабочую программу дисциплины в системе 1C
     *
     * Метод отправляет запрос в 1C для установки основного или со-разработчика РПД,
     * с указанием прав доступа к блокам управления.
     *
     * @param string $user_id ID пользователя в системе
     * @param string $rpd_id ID рабочей программы дисциплины
     * @param string $is_primary_dev Флаг основного разработчика (true/false)
     * @param string $blockControl Список блоков управления, доступных разработчику
     * @param string $CRUD Операция CRUD (Create, Read, Update, Delete)
     * @return array Результат выполнения запроса от 1C
     * @throws cdo_type_response_exception Ошибка типа ответа от 1C
     * @throws coding_exception Ошибка кодирования
     * @throws cdo_config_exception Ошибка конфигурации CDO
     * @throws invalid_parameter_exception Ошибка валидации параметров
     */
    public static function set_developer_on_rpd_1c($user_id, $rpd_id, $is_primary_dev, $blockControl, $CRUD): array
    {
        // Валидация входных параметров согласно описанию.
        $params = self::validate_parameters(self::set_developer_on_rpd_1c_parameters(),
            [
                'user_id' => $user_id,
                'rpd_id' => $rpd_id,
                'is_primary_dev' => $is_primary_dev,
                'blockControl' => $blockControl,
                'CRUD' => $CRUD,
            ]
        );
        
        // Получение опций запроса через DI-контейнер.
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        
        // Выполнение запроса к 1C и получение результата.
        $request = di::get_instance()->get_request('set_developer_on_rpd_1c')->request($options);
        return $request->get_request_result()->to_array();
    }

    /**
     * Описание структуры возвращаемых данных для метода set_developer_on_rpd_1c
     *
     * @return void
     */
    public static function set_developer_on_rpd_1c_returns() {

    }

    /**
     * Описание параметров для метода получения списка РПД по кафедре из 1C
     *
     * @return external_function_parameters
     */
    public static function get_rpd_on_department_1c_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'user_id' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, 0),
                'type' => new external_value(PARAM_TEXT, 'id', VALUE_DEFAULT, ''),
            ]
        );
    }

    /**
     * Получает список рабочих программ дисциплин по кафедре текущего пользователя из 1C
     *
     * Метод определяет роль текущего пользователя (преподаватель, заведующий кафедрой,
     * ученый секретарь) и запрашивает соответствующий список РПД из системы 1C.
     *
     * @return array Список рабочих программ дисциплин кафедры
     * @throws cdo_type_response_exception Ошибка типа ответа от 1C
     * @throws cdo_config_exception Ошибка конфигурации CDO
     * @throws coding_exception Ошибка кодирования
     * @throws dml_exception Ошибка работы с базой данных
     */
    public static function get_rpd_on_department_1c(): array
    {
        global $USER;
        
        // Формирование параметров запроса с ID текущего пользователя и его ролью.
        $params = [
            'user_id' => $USER->id,
            'type' => self::get_current_user_info()['type']
        ];
        
        // Настройка опций запроса через DI-контейнер.
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        
        // Выполнение запроса к 1C для получения списка РПД по кафедре.
        $request = di::get_instance()->get_request('get_rpd_on_department_1c')->request($options);
        return $request->get_request_result()->to_array();
    }

    /**
     * Описание структуры возвращаемых данных для метода get_rpd_on_department_1c
     *
     * @return void
     */
    public static function get_rpd_on_department_1c_returns() {}

    /**
     * Описание параметров для метода сохранения РПД в 1C
     *
     * @return external_function_parameters
     */
    public static function save_rpd_to_1c_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'JSON' => new external_value(PARAM_RAW_TRIMMED, '', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * Сохраняет рабочую программу дисциплины в систему 1C
     *
     * Метод принимает данные РПД в формате JSON и отправляет их в 1C для сохранения.
     * Используется для создания новых РПД или обновления существующих.
     *
     * @param string $JSON JSON-строка с данными рабочей программы дисциплины
     * @return array Результат выполнения операции сохранения от 1C
     * @throws cdo_type_response_exception Ошибка типа ответа от 1C
     * @throws coding_exception Ошибка кодирования
     * @throws cdo_config_exception Ошибка конфигурации CDO
     * @throws invalid_parameter_exception Ошибка валидации параметров
     */
    public static function save_rpd_to_1c($JSON): array
    {
        // Валидация JSON-параметра.
        $params = self::validate_parameters(self::save_rpd_to_1c_parameters(),
            [
                'JSON' => $JSON
            ]
        );
        
        // Настройка опций запроса с передачей JSON-данных напрямую.
        $options = di::get_instance()->get_request_options();
        $options->set_properties($JSON);
        
        // Выполнение запроса к 1C для сохранения РПД.
        $request = di::get_instance()->get_request('save_rpd_to_1c')->request($options);
        return $request->get_request_result()->to_array();
    }

    /**
     * Описание структуры возвращаемых данных для метода save_rpd_to_1c
     *
     * @return void
     */
    public static function save_rpd_to_1c_returns() {

    }

    /**
     * Описание параметров для метода получения информации о текущем пользователе
     *
     * @return external_function_parameters
     */
    public static function get_current_user_info_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            []
        );
    }

    /**
     * Получает информацию о текущем пользователе и определяет его роль в системе РПД
     *
     * Метод проверяет права доступа пользователя и на их основе определяет тип:
     * - teacher (преподаватель) - имеет право просмотра РПД
     * - headofdepartment (заведующий кафедрой) - имеет административные права
     * - ExecutiveSecretary (ученый секретарь) - имеет права ученого секретаря
     *
     * @return array Массив с ID и типом текущего пользователя
     * @throws coding_exception Ошибка кодирования
     * @throws dml_exception Ошибка работы с базой данных
     */
    public static function get_current_user_info(): array
    {
        global $USER;
        $type = '';
        
        // Проверка прав доступа для определения роли пользователя.
        // Проверки выполняются в порядке возрастания приоритета.
        if (has_capability('local/cdo_rpd:view_rpd', context_system::instance())) {
            $type = 'teacher';
        }
        if (has_capability('local/cdo_rpd:view_admin_rpd', context_system::instance())) {
            $type = 'headofdepartment';
        }
        if (has_capability('local/cdo_rpd:view_executive_secretary', context_system::instance())) {
            $type = 'ExecutiveSecretary';
        }
        
        return [
            'id' => $USER->id,
            'type' => $type
        ];
    }

    /**
     * Описание структуры возвращаемых данных для метода get_current_user_info
     *
     * @return external_single_structure Структура с полями id и type пользователя
     */
    public static function get_current_user_info_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_TEXT, 'id', VALUE_REQUIRED),
                'type' => new external_value(PARAM_TEXT, 'type', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * Описание параметров для метода получения компетенций для РПД
     *
     * @return external_function_parameters
     */
    public static function get_competencies_for_rpd_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'edu_plan' => new external_value(PARAM_TEXT, 'edu_plan', VALUE_REQUIRED),
                'discipline' => new external_value(PARAM_TEXT, 'id discipline', VALUE_REQUIRED),
                'rpd_id' => new external_value(PARAM_TEXT, 'id discipline', VALUE_REQUIRED),
                'user_id' => new external_value(PARAM_TEXT, 'id discipline', VALUE_REQUIRED),
                'module_guid' => new external_value(PARAM_RAW, 'id discipline', VALUE_DEFAULT, "")
            ]
        );
    }

    /**
     * Получает полную информацию о компетенциях и структуре РПД из системы 1C
     *
     * Метод запрашивает из 1C все данные, необходимые для формирования РПД:
     * - Компетенции и результаты обучения (знать, уметь, владеть)
     * - Формы обучения и учебную нагрузку
     * - Список литературы (основная, дополнительная, методическая)
     * - Материально-техническое обеспечение (аудитории, инвентарь, ПО)
     * - Структуру разделов и тем дисциплины
     * - Формы контроля и оценочные средства
     *
     * @param string $edu_plan ID учебного плана
     * @param string $discipline ID дисциплины
     * @param string $rpd_id ID рабочей программы дисциплины
     * @param string $user_id ID пользователя
     * @param string|null $guid_module GUID модуля (опционально)
     * @return mixed Полная структура данных РПД от 1C
     * @throws cdo_config_exception Ошибка конфигурации CDO
     * @throws cdo_type_response_exception Ошибка типа ответа от 1C
     * @throws coding_exception Ошибка кодирования
     * @throws invalid_parameter_exception Ошибка валидации параметров
     */
    public static function get_competencies_for_rpd($edu_plan, $discipline, $rpd_id, $user_id, string|null $guid_module = ""): mixed
    {
        // Валидация всех входных параметров.
        $params = self::validate_parameters(self::get_competencies_for_rpd_parameters(),
            [
                'edu_plan' => $edu_plan,
                'discipline' => $discipline,
                'rpd_id' => $rpd_id,
                'user_id' => $user_id,
                'module_guid' => $guid_module
            ]
        );
        
        // Подготовка и выполнение запроса к 1C.
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_competencies_for_rpd')->request($options);
        
        return $request->get_request_result()->to_array();
    }

    /**
     * Описание структуры возвращаемых данных для метода get_competencies_for_rpd
     *
     * Метод возвращает сложную структуру данных, включающую:
     * - controlsList - список видов контроля
     * - competencies - компетенции с результатами обучения
     * - forms - формы обучения с распределением нагрузки
     * - part1 - цели и задачи дисциплины
     * - books - литература (основная, дополнительная, методическая)
     * - info - общая информация о РПД и разработчиках
     * - controls - формы промежуточной аттестации
     * - parts - структура разделов и тем дисциплины
     * - MTO - материально-техническое обеспечение
     *
     * @return void
     */
    public static function get_competencies_for_rpd_returns(): void
    {
        new external_single_structure(
            [
                // Список доступных типов контроля (экзамен, зачет и т.д.).
                'controlsList' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'code' => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
                            'name' => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
                            'required' => new external_value(PARAM_BOOL, 'success', VALUE_OPTIONAL),
                            'template' => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
                            'short_code' => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
                        ]
                    )
                ),
                // Компетенции и индикаторы их достижения.
                'competencies' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
                            'competenceguid' => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
                            'title' => new external_value(PARAM_TEXT, 'info', VALUE_OPTIONAL),
                            'requirement' => new external_single_structure(
                                [
                                    'know' => new external_value(PARAM_RAW, 'know', VALUE_OPTIONAL),
                                    'beAbleTo' => new external_value(PARAM_RAW, 'beAbleTo', VALUE_OPTIONAL),
                                    'own' => new external_value(PARAM_RAW, 'own', VALUE_OPTIONAL),

                                ]
                            )
                        ]
                    )
                ),
                // Формы обучения (очная, заочная и т.д.) с распределением учебной нагрузки.
                'forms' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'name' => new external_value(PARAM_TEXT, 'success', VALUE_OPTIONAL),
                            'guidform' => new external_value(PARAM_TEXT, 'success', VALUE_OPTIONAL),
                            'load' => new external_multiple_structure(
                                new external_single_structure(
                                    [
                                        'type' => new external_value(PARAM_TEXT, 'success', VALUE_OPTIONAL),
                                        'value' => new external_value(PARAM_TEXT, 'success', VALUE_OPTIONAL),
                                        'typeguid' => new external_value(PARAM_TEXT, 'success', VALUE_OPTIONAL),
                                    ]
                                )
                            )
                        ],
                        "struct for form edu",
                        1
                    ),
                    "hourse on every form education",
                    1),
                // Раздел 1 РПД: цели и задачи освоения дисциплины.
                'part1' => new external_single_structure(
                    [
                        'target' => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL),
                        'taskfordisc' => new external_value(PARAM_RAW, 'success', VALUE_OPTIONAL)
                    ]
                ),
                // Учебно-методическое и информационное обеспечение дисциплины.
                'books' => new external_single_structure(
                [
                    // Основная литература.
                    'mainSelected' => new external_multiple_structure(
                            new external_single_structure(
                                [
                                    'id' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'book' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'author' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'link' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'year' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'publishing' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'count' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'commentary' => new external_value(PARAM_RAW, 'success', VALUE_DEFAULT, ""),
                                    'approval' => new external_value(PARAM_BOOL, 'success', VALUE_DEFAULT, false),
                                ]
                            ), "desc", 0
                        ),
                        // Дополнительная литература.
                        'additionalSelected' => new external_multiple_structure(
                            new external_single_structure(
                                [
                                    'id' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'book' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'author' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'link' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'year' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'publishing' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'count' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                ]
                            ), "desc", 0
                        ),
                        // Методическая литература и разработки.
                        'methodicalSelected' => new external_multiple_structure(
                            new external_single_structure(
                                [
                                    'id' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'book' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'author' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'link' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'year' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'publishing' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                    'count' => new external_value(PARAM_RAW, 'success', VALUE_REQUIRED),
                                ]
                            ), "desc", 0
                        )
                    ]
                ),
                // Общая информация о РПД: направление, дисциплина, год, разработчики и т.д.
                'info' =>
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_TEXT, 'id', VALUE_REQUIRED),
                            'direction' => new external_value(PARAM_TEXT, 'direction', VALUE_REQUIRED),
                            'discipline' => new external_value(PARAM_TEXT, 'discipline', VALUE_REQUIRED),
                            'discipline_code' => new external_value(PARAM_TEXT, 'discipline', VALUE_OPTIONAL),
                            'year' => new external_value(PARAM_TEXT, 'year', VALUE_REQUIRED),
                            'educationLevel' => new external_value(PARAM_TEXT, 'educationLevel', VALUE_REQUIRED),
                            'trainingLevel' => new external_value(PARAM_TEXT, 'trainingLevel', VALUE_REQUIRED),
                            'module_id' => new external_value(PARAM_TEXT, 'module_id', VALUE_REQUIRED),
                            'module_name' => new external_value(PARAM_TEXT, 'module_name', VALUE_REQUIRED),
                            'typeAndModule' => new external_value(PARAM_TEXT, 'module_name', VALUE_OPTIONAL),
                            'discipline_index' => new external_value(PARAM_TEXT, 'discipline_index', VALUE_REQUIRED),
                            'type' => new external_value(PARAM_TEXT, 'type', VALUE_REQUIRED),
                            'status' => new external_value(PARAM_TEXT, 'type', VALUE_REQUIRED),
                            'edu_plan' => new external_single_structure(
                                [
                                    'id' => new external_value(PARAM_TEXT, 'id', VALUE_OPTIONAL)
                                ]
                            ),
                            'developers' => new external_single_structure(
                                [
                                    'mainDeveloper' => new external_multiple_structure(
                                        new external_single_structure(
                                            [
                                                'id' => new external_value(PARAM_RAW, 'id', VALUE_OPTIONAL),
                                                'user' => new external_value(PARAM_RAW, 'id', VALUE_OPTIONAL),
                                                'blockControl' => new external_value(PARAM_RAW, 'id', VALUE_OPTIONAL)
                                            ]
                                        )
                                    ),
                                    'coDevelopers' => new external_multiple_structure(
                                        new external_single_structure(
                                            [
                                                'id' => new external_value(PARAM_RAW, 'id', VALUE_OPTIONAL),
                                                'user' => new external_value(PARAM_RAW, 'id', VALUE_OPTIONAL),
                                                'blockControl' => new external_value(PARAM_RAW, 'id', VALUE_OPTIONAL)
                                            ]
                                        )
                                    )
                                ]
                            )

                        ]
                    )
                ,
                // Формы промежуточной аттестации по семестрам.
                'controls' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'enrole' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                            'code' => new external_value(PARAM_TEXT, 'uid', VALUE_OPTIONAL),
                            'enroleTypes' => new external_multiple_structure(
                                new external_single_structure(
                                    [
                                        'name' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                                        'code' => new external_value(PARAM_TEXT, 'uid', VALUE_OPTIONAL),
                                        'required' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'template' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                    ]
                                )
                            )
                        ]
                    ),
                    "",
                    0
                ),
                // Структура и содержание дисциплины (разделы, темы, виды занятий).
                'parts' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                            'name_segment' => new external_value(PARAM_TEXT, 'uid', VALUE_OPTIONAL),
                            'data' => new external_multiple_structure(
                                new external_single_structure(
                                    [
                                        'description' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                                        'id' => new external_value(PARAM_TEXT, 'uid', VALUE_OPTIONAL),
                                        'interactive' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'interactive_oza' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'interactive_za' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'lab' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'lab_oza' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'lab_za' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'lection' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'lection_oza' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'lection_za' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'name_segment' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'outwork' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'outwork_oza' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'outwork_za' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'practice' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'practicePrepare' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'practicePrepare_oza' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'practicePrepare_za' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'practice_oza' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'practice_za' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'seminaryQuestion' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                        'type' => new external_value(PARAM_BOOL, 'uid', VALUE_OPTIONAL),
                                    ]
                                )
                            )
                        ]
                    )
                ),
                // Материально-техническое обеспечение: аудитории, оборудование, ПО.
                'MTO' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'name' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                            'uid' => new external_value(PARAM_TEXT, 'uid', VALUE_REQUIRED),
                            'code' => new external_value(PARAM_TEXT, 'uid', VALUE_OPTIONAL),
                            'auditorium' => new external_multiple_structure(
                                new external_single_structure(
                                    [
                                        'name' => new external_value(PARAM_TEXT, 'name', VALUE_REQUIRED),
                                        'uid' => new external_value(PARAM_TEXT, 'uid', VALUE_REQUIRED),
                                        'inventory' => new external_multiple_structure(
                                            new external_single_structure(
                                                [
                                                    'fullname' => new external_value(PARAM_TEXT, 'fullname', VALUE_REQUIRED),
                                                    'uid' => new external_value(PARAM_TEXT, 'uid', VALUE_REQUIRED)
                                                ]
                                            )
                                        ),
                                        'software' => new external_multiple_structure(
                                            new external_single_structure(
                                                [
                                                    'fullname' => new external_value(PARAM_TEXT, 'fullname', VALUE_REQUIRED),
                                                    'uid' => new external_value(PARAM_TEXT, 'uid', VALUE_REQUIRED),

                                                ]
                                            )
                                        ),

                                    ]
                                )
                            )
                        ]
                    )),

            ]);
    }

    /**
     * Описание параметров для метода получения списка РПД по ID пользователя
     *
     * @return external_function_parameters
     */
    public static function get_rpd_list_by_user_id_parameters(): external_function_parameters
    {
        global $USER;
        return new external_function_parameters(
            [
                'user_id' => new external_value(PARAM_TEXT, 'user_id', VALUE_DEFAULT, $USER->id)
            ]
        );
    }

    /**
     * Получает список рабочих программ дисциплин для текущего пользователя
     *
     * Метод запрашивает из системы 1C список всех РПД, к которым имеет доступ
     * текущий пользователь (как разработчик, со-разработчик или администратор).
     *
     * @return array Список РПД пользователя с основной информацией
     * @throws cdo_type_response_exception Ошибка типа ответа от 1C
     * @throws coding_exception Ошибка кодирования
     * @throws cdo_config_exception Ошибка конфигурации CDO
     * @throws invalid_parameter_exception Ошибка валидации параметров
     */
    public static function get_rpd_list_by_user_id(): array
    {
        global $USER;
        
        // Валидация параметров с ID текущего пользователя.
        $params = self::validate_parameters(self::get_rpd_list_by_user_id_parameters(),
            [
                'user_id' => $USER->id,
            ]
        );
        
        // Подготовка и выполнение запроса к 1C.
        $options = di::get_instance()->get_request_options();
        $options->set_properties($params);
        $request = di::get_instance()->get_request('get_rpd_list_by_user_id')->request($options);
        
        return $request->get_request_result()->to_array();
    }

    /**
     * Описание структуры возвращаемых данных для метода get_rpd_list_by_user_id
     *
     * @return void
     */
    public static function get_rpd_list_by_user_id_returns()
    {

    }

}