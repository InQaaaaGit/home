<?php

abstract class Helpers
{
    public static function getListSecretary()
    {
        global $DB;
        $text_query = "
        SELECT
          mdl_user.id,
          mdl_user.lastname,
          mdl_user.firstname,
          mdl_user.middlename,  
          mdl_user.username,
          CONCAT(mdl_user.lastname, ' ', mdl_user.firstname, ' ', mdl_user.middlename) as preview
        FROM mdl_user
          INNER JOIN mdl_user_info_data on mdl_user.id = mdl_user_info_data.userid and mdl_user_info_data.fieldid=2 and data like '%ExecutiveSecretary%'
        ORDER BY mdl_user.lastname,mdl_user.firstname,mdl_user.middlename
        ";
        $rec_set = $DB->get_recordset_sql($text_query);
        $result = [];
        foreach ($rec_set as $item)
            $result[] = $item;
        $rec_set->close();
        return $result;
    }

    public static function dump($data, $die = true)
    {
        echo '<pre>' . print_r($data, 1) . '</pre>';
        if ($die) die();
    }
}
