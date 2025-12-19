<?php

namespace local_cdo_ok\services;

use curl;

class integration
{
    public static function get_users_info() {

    }
    public static function test_case() {
        $json = '[
    {
        "fio": "Идизода Фазлидини Исроил",
        "group": "СДО-СО-19/3",
        "edu_structure": "медицинский колледж им. А.Л.Поленова",
        "edu_spec": "Сестринское дело (9 кл.)",
        "edu_year": "2022 - 2023",
        "edu_level": "Специалист (базовый уровень СПО)",
        "edu_form": "очная",
        "user_id": "1710"
    },
    {
        "fio": "Ишметов Петр Андреевич",
        "group": "СДО-СО-19/3",
        "edu_structure": "медицинский колледж им. А.Л.Поленова",
        "edu_spec": "Сестринское дело (9 кл.)",
        "edu_year": "2022 - 2023",
        "edu_level": "Специалист (базовый уровень СПО)",
        "edu_form": "очная",
        "user_id": "8906"
    }
]';

        return json_decode($json);
    }
}