<?php

namespace local_cdo_ok\controllers;

use dml_exception;

class active_group_controller
{
    const TABLE_LOCAL_CDO_OK_ACTIVE_GROUP = 'local_cdo_ok_active_group';

    /**
     * @throws dml_exception
     */
    public function create_update($data): bool
    {
        global $DB;
        $exist_record = $DB->get_record(self::TABLE_LOCAL_CDO_OK_ACTIVE_GROUP,
            ['group_tab' => $data['group_tab']]
        );
        if (empty($exist_record))
            $DB->insert_record(self::TABLE_LOCAL_CDO_OK_ACTIVE_GROUP, $data);
        else {
            $data['id'] = $exist_record->id;
            $DB->update_record(self::TABLE_LOCAL_CDO_OK_ACTIVE_GROUP, (object)$data);
        }

        return $data['active'];
    }

    /**
     * @throws dml_exception
     */
    public function get($params = []): bool
    {
        global $DB;
        $record = $DB->get_record(self::TABLE_LOCAL_CDO_OK_ACTIVE_GROUP, $params, '*');
        
        if (empty($record)) {
            return false;
        }
        
        return (bool)$record->active;
    }
}