<?php

namespace local_cdo_vkr\VKR;

use bootstrap_renderer;
use dml_exception;
use local_cdo_vkr\utility\file_of_vkr;
use moodle_exception;
use stdClass;

class main implements main_interface
{
    const TABLE = 'local_cdo_vkr_vkrs';

    /**
     * @throws dml_exception
     */
    public function get_list_of_VKR($constraint = []): array
    {
        global $DB;

        $records = $DB->get_records(self::TABLE, $constraint);
        return $this->prepare_records_for_service($records);
    }

    /**
     * @throws dml_exception
     */
    public function update_data_of_VKR(array $new_data): bool
    {
        global $DB;
        $params = (object)$new_data;

        return $DB->update_record(self::TABLE, $params);
    }

    protected function prepare_records_for_service(array $records): array
    {
        $resultArray = [];
        foreach ($records as $record) {
            $result = (array)$record;
            $result['FIO'] = $record->fio;
            $result['agreedEBS'] = $record->agreedebs;
            $result['status'] = [
                'id' => $record->status_id,
                'changed' => $record->status_changed
            ];
            $result['manager'] = [
                'id' => $record->manager_id,
                'name' => $record->manager
            ];
            unset($result['usermodified']);
            unset($result['timemodified']);
            unset($result['status_id']);
            unset($result['status_changed']);
            unset($result['manager_id ']);
            $resultArray[] = $result;
        }
        return $resultArray;
    }

    /**
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function delete_data_of_VKR($id_vkr): bool
    {
        $fov = new file_of_vkr($id_vkr);
        $vkrs = $fov->get_files_of_vkr();
        $fs = get_file_storage();
        $ids = [];
        foreach ($vkrs->files as $vkr) {
            if ($file = $fs->get_file_by_id($vkr->file_id)) {
                $file->delete();
            } else {
                /*bootstrap_renderer::early_error(
                    $vkr->file_id . ' не найден',
                    'ok',
                    'a',
                    ['1']
                );*/
            }
            $ids[] = (int) $vkr->file_id;
        }
        if (!empty($ids)) {
            #$str = implode(',', $ids);
            $fov->delete_files_of_vkr($ids);
        }

        return true;
    }
}