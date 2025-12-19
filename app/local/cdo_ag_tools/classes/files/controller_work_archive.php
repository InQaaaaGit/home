<?php

namespace local_cdo_ag_tools\files;

use dml_exception;
use moodle_database;
use moodle_url;
use stdClass;

class controller_work_archive
{
    const table_name = 'cdo_ag_tools_user_files';
    private moodle_database $db;

    public function __construct()
    {
        global $DB;
        $this->db = $DB;
    }

    private function file_saving_structure(int $userid, int $fileid): stdClass
    {
        $file_structure = new stdClass();
        $file_structure->userid = $userid;
        $file_structure->fileid = $fileid;
        $file_structure->timecreated = time();
        return $file_structure;
    }


    /**
     * @throws dml_exception
     */
    public function save_file($userid, $fileid): bool|int
    {
        $structure = $this->file_saving_structure($userid, $fileid);
        return $this->db->insert_record(self::table_name, $structure);
    }

    /**
     * @throws dml_exception
     */
    public function get_file($id)
    {
        $record_set = $this->db->get_records(self::table_name, ['userid' => $id]);
        return array_pop($record_set);
    }

    /**
     * @throws dml_exception
     */
    public function create_file_link($user_id): ?moodle_url
    {
        $record = $this->get_file($user_id);
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($record->fileid);
        if ($file) {
            return moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename(),
                true
            );
        }
        return null;
    }

    /**
     * @throws dml_exception
     */
    public function get_files($condition): array
    { //['userid' => $user_id]
        return $this->db->get_records(self::table_name, $condition);
    }

    /**
     * @throws dml_exception
     */
    public function delete_file(array $condition): void
    {
        $this->db->delete_records(self::table_name, $condition);
    }
}