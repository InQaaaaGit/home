<?php
defined('MOODLE_INTERNAL') || die;


if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_heading('priority_show_sections',
        "Приоритет вывода разделов", "Если приоритеты будут выставлены одинаковые - система выставит приоритеты по умолчанию."));

    $settings->add(new admin_setting_configtext("priority_top_courses", "Приоритет топовых курсов", "", 1, PARAM_INT, 1));
    $settings->add(new admin_setting_configtext("priority_open_courses", "Приоритет открытых курсов", "", 2, PARAM_INT, 1));
    $settings->add(new admin_setting_configtext("priority_tech_biblio", "Приоритет технической библиотеки", "", 4, PARAM_INT, 1));
    $settings->add(new admin_setting_configtext("priority_filials", "Приоритет учебных центров", "", 5, PARAM_INT, 1));
    $settings->add(new admin_setting_configtext("priority_all_courses", "Приоритет профессиональных курсов", "", 3, PARAM_INT, 1));

    $settings->add(new admin_setting_heading('slider',
        "Настройки", ""));

    $settings->add(new admin_setting_configtext(
        "exclude_courses_from_frontpage",
        "Исключенные курсы",
        "укажите через , идентификаторы курсов, которые следует исключить",
        ""
    ));

    $settings->add(new admin_setting_heading('top_courses',
        "Настройки для топовых курсов", ""));

    $settings->add(new admin_setting_configtext(
        "ids_top_courses",
        "Идентификаторы топовых курсов",
        "укажите через , идентификаторы курсов, которые следует включить",
        ""
    ));

    $settings->add(new admin_setting_configtext(
        "name_top_courses",
        "Наименование раздела",
        "",
        "Топовые курсы"
    ));

    $settings->add(new admin_setting_heading('open_courses',
        "Настройки для открытых курсов", ""));

    $settings->add(new admin_setting_configtext(
        "name_category_open_course",
        "Наименование для открытых курсов",
        "",
        ""
    ));

    $settings->add(new admin_setting_configtext(
        "name_category_open_course_link",
        "Наименование для ссылки на открытые курсы",
        "",
        ""
    ));

    $settings->add(new admin_setting_configtext(
        "name_category_open_course_id",
        "Идентификатор для перехода на открытые курсы",
        "",
        ""
    ));

    $settings->add(new admin_setting_configtext(
        "description_open_courses",
        "Описание для открытых курсов",
        "",
        ""
    ));

    $settings->add(new admin_setting_configtext(
        "needle_open_courses",
        "Перечислите через , идентификаторы курсов, которые требуется отобразить",
        "",
        ""
    ));


    $settings->add(new admin_setting_heading('tech_biblio',
        "Настройки для технической библиотеки", ""));

    $settings->add(new admin_setting_configtext(
        "tech_biblio_description",
        "Описание раздела технической бибилотеки",
        "",
        ""
    ));

    $settings->add(new admin_setting_configtext(
        "tech_biblio_head",
        "Название раздела технической библиотеки",
        "",
        "Техническая библиотека"
    ));

    $settings->add(new admin_setting_configtext(
        "tech_biblio_id",
        "Идентификатор раздела технической библиотеки",
        "",
        "144"
    ));
    $settings->add(new admin_setting_configtext(
        "tech_biblio_link",
        "Наименование ссылки раздела технической библиотеки",
        "",
        "144"
    ));

    $settings->add(new admin_setting_heading('filials',
        "Настройки для учебных центров", ""));

    $settings->add(new admin_setting_configtext(
        "filials_description",
        "Описание раздела филиалов",
        "",
        ""
    ));
    $settings->add(new admin_setting_configtext(
        "filials_head",
        "Название раздела филиалов",
        "",
        "Учебные центры Компании"
    ));

    $settings->add(new admin_setting_heading('all_courses',
        "Настройки для доступных курсов", ""));

    $settings->add(new admin_setting_configtext(
        "all_courses_head",
        "Название раздела доступных курсов",
        "",
        ""
    ));

    $settings->add(new admin_setting_configtext(
        "all_courses_description",
        "Описание раздела доступных курсов",
        "",
        ""
    ));

    $settings->add(new admin_setting_configtext(
        "all_courses_link",
        "Ссылка на раздел доступных курсов",
        "",
        "/course/index.php"
    ));

    $settings->add(new admin_setting_configtext(
        "all_courses_link_name",
        "Наименование ссылки на раздел доступных курсов",
        "1",
        "Доступные курсы"
    ));

    $settings->add(new admin_setting_configtext(
        "needle_all_courses",
        "Перечислите через , идентификаторы курсов, которые требуется отобразить",
        "",
        ""
    ));


}


