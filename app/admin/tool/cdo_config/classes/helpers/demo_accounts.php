<?php

namespace tool_cdo_config\helpers;

class demo_accounts
{
    public static function get_cohort_mapping(int $parallel): int
    {
        $parallel--;
        $cohorts = [
            1, //1
            1199, //2
            1202, //3
            1205, //4
            1208, //5
            1211, //6
            1214, //7
            1217, //8
            1220, //9
            1223, //10
            1226, //11
        ];
        return $cohorts[$parallel];
    }

    public static function get_html_body_for_email()
    {

    }
}