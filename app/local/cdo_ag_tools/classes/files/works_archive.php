<?php

namespace local_cdo_ag_tools\files;

use dml_exception;
use file_exception;
use file_storage;
use moodle_database;
use moodle_url;
use stored_file;
use stored_file_creation_exception;

class works_archive
{

    private array $fileinfo;
    private ?file_storage $fs;

    private int $user;
    private controller_work_archive $controller_work_archive;

    public function __construct(controller_work_archive $controller_work_archive, $context_id, string $filename, int $userid)
    {
        $this->user = $userid;
        $this->controller_work_archive = $controller_work_archive;
        $this->fs = get_file_storage();
        $this->fileinfo = [
            'contextid' => $context_id,   // ID of the context.
            'component' => 'local_cdo_ag_tools', // Your component name.
            'filearea' => 'works',       // Usually = table name.
            'itemid' => 0,              // Usually = ID of row in table.
            'filepath' => '/',            // Any path beginning and ending in /.
            'filename' => $filename,   // Any filename.
            'forcedownload' => true,
        ];
    }

    /**
     * @throws file_exception
     * @throws stored_file_creation_exception
     * @throws dml_exception
     */
    public function create_file($path_to_file): stored_file
    {
        $this->delete_previous_files();
        $file_record = $this->fs->create_file_from_pathname($this->fileinfo, $path_to_file);
        $this->controller_work_archive->save_file($this->user, $file_record->get_id());
        return $file_record;
    }

    /**
     * @throws dml_exception
     */
    public function delete_previous_files(): void
    {
        $files = $this->controller_work_archive->get_files(['userid' => $this->user]);
        foreach ($files as $key => $file) {
            $this->fs->get_file_by_id($file->fileid)->delete();
            $this->controller_work_archive->delete_file(
                ['fileid' => $file->fileid]
            );
        }
    }
}