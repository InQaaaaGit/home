<?php
$functions = [
    'get_current_user_info' => [
        'classname' => local_cdo_rpd\services\rpd::class,
        'methodname' => 'get_current_user_info',
        'description' => 'Получает информацию о пользователе и его доступах',
        'type' => 'read',
        'ajax' => true
    ],
    'get_literature_for_approve' => [
        'classname' => local_cdo_rpd\services\literature::class,
        'methodname' => 'get_literature_for_approve',
        'description' => 'Получает список литературы отправленный на согласование',
        'type' => 'read',
        'ajax' => true
    ],
    'get_competencies_for_rpd' => [
        'classname' => local_cdo_rpd\services\rpd::class,
        'methodname' => 'get_competencies_for_rpd',
        'description' => 'Получает структуру РПД',
        'type' => 'read',
        'ajax' => true
    ],
    'get_building_from_1c' => [
        'classname' => local_cdo_rpd\services\mto::class,
        'methodname' => 'get_building_from_1c',
        'description' => 'Поулчает данные о зданиях и корпусах',
        'type' => 'read',
        'ajax' => true
    ],
    'get_auditorium_by_parent_building_from_1c' => [
        'classname' => local_cdo_rpd\services\mto::class,
        'methodname' => 'get_auditorium_by_parent_building_from_1c',
        'description' => 'Поулчает данные о аудиториях и помещениях в выбранном здании',
        'type' => 'read',
        'ajax' => true
    ],
    'get_software_by_auditorium_from_1c' => [
        'classname' => local_cdo_rpd\services\mto::class,
        'methodname' => 'get_software_by_auditorium_from_1c',
        'description' => 'Поулчает данные о ПО в аудиториях',
        'type' => 'read',
        'ajax' => true
    ],
    'get_inventory_by_auditorium_from_1c' => [
        'classname' => local_cdo_rpd\services\mto::class,
        'methodname' => 'get_inventory_by_auditorium_from_1c',
        'description' => 'Поулчает данные о МТО в аудиториях',
        'type' => 'read',
        'ajax' => true
    ],
    'save_rpd_to_1c' => [
        'classname' => local_cdo_rpd\services\rpd::class,
        'methodname' => 'save_rpd_to_1c',
        'description' => 'СОхраняет РПД',
        'type' => 'read',
        'ajax' => true
    ],
    'get_rpd_on_department_1c' => [
        'classname' => local_cdo_rpd\services\rpd::class,
        'methodname' => 'get_rpd_on_department_1c',
        'description' => 'Получает все РПД по пользователю',
        'type' => 'read',
        'ajax' => true
    ],
    'set_developer_on_rpd_1c' => [
        'classname' => local_cdo_rpd\services\rpd::class,
        'methodname' => 'set_developer_on_rpd_1c',
        'description' => 'Распределяет РПД на ППС',
        'type' => 'read',
        'ajax' => true
    ],
    'get_rpd_list_by_user_id_from_1c' => [
        'classname' => local_cdo_rpd\services\rpd::class,
        'methodname' => 'get_rpd_list_by_user_id',
        'description' => 'Получает РПД по ППС',
        'type' => 'read',
        'ajax' => true
    ],
    'search_in_1c_library' => [
        'classname' => local_cdo_rpd\services\literature::class,
        'methodname' => 'search_in_1c_library',
        'description' => 'Поиск библиотечного фонда',
        'type' => 'read',
        'ajax' => true
    ],
    'get_list_library_workers' => [
        'classname' => local_cdo_rpd\services\literature::class,
        'methodname' => 'get_list_library_workers',
        'description' => 'Получение списка сотрудников библиотеки',
        'type' => 'read',
        'ajax' => true
    ],
    'get_list_specialities_for_distribution' => [
        'classname' => local_cdo_rpd\services\literature::class,
        'methodname' => 'get_list_specialities_for_distribution',
        'description' => 'Получить специальности к распределению',
        'type' => 'read',
        'ajax' => true
    ],
    'add_worker_on_special' => [
        'classname' => local_cdo_rpd\services\literature::class,
        'methodname' => 'add_worker_on_special',
        'description' => 'назначить сотрудника на специальность библиотеки',
        'type' => 'read',
        'ajax' => true
    ],
    'get_list_rpd_for_library_worker_on_specialty' => [
        'classname' => local_cdo_rpd\services\literature::class,
        'methodname' => 'get_list_rpd_for_library_worker_on_specialty',
        'description' => 'назначить сотрудника на специальность библиотеки',
        'type' => 'read',
        'ajax' => true
    ],
    'send_literature_for_approve' => [
        'classname' => local_cdo_rpd\services\literature::class,
        'methodname' => 'send_literature_for_approve',
        'description' => 'Направить литературу на согласование',
        'type' => 'read',
        'ajax' => true
    ],
    'set_status' => [
        'classname' => local_cdo_rpd\services\management::class,
        'methodname' => 'set_status',
        'description' => 'Направить литературу на согласование',
        'type' => 'read',
        'ajax' => true
    ],

];