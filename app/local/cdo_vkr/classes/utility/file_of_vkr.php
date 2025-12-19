<?php

namespace local_cdo_vkr\utility;

use assignfeedback_editpdf\pdf;
use Complex\Exception;
use core_h5p\file_storage;
use dml_exception;
use local_cdo_vkr\VKR\create_complex_pdf;
use moodle_exception;
use moodle_url;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfReader\Page;
use setasign\Fpdi\PdfReader\PageBoundaries;
use setasign\Fpdi\PdfReader\PdfReader;
use setasign\Fpdi\PdfReader\PdfReaderException;
use stored_file;

class file_of_vkr
{
    const TABLE = 'local_cdo_vkr_file_of_vkr';
    const AREA_COMMENT = '/commentary/';
    const AREA_REVIEW = '/review/';
    const AREA_WORK = '/work/';

    const AREA_ARCHIVE = '/archive/';
    private $id_vkr;
    public $files;
    /**
     * @var mixed
     */
    private $file_id;
    private $usermodified;
    private $timecreated;
    private $timemodified;

    private $file_fs;
    /**
     * @var mixed
     */
    private $type;
    /**
     * @var mixed
     */
    private  $status;
    /**
     * @var mixed|string
     */
    private  $commentary;
    private  $manager_status;

    public function __construct($id_vkr = 0, $file_id = 0)
    {
        $this->id_vkr = $id_vkr;
        $this->file_id = $file_id;
        if ($file_id !== 0) {
            $fs = get_file_storage();
            $file_fs = $fs->get_file_by_id($file_id);
            if (!is_bool($file_fs))
                $this->file_fs = $file_fs;
            else {
                throw new moodle_exception(200, '', '', 'Файл уже удален');
            }
        }
    }

    /**
     * @throws dml_exception
     */
    public function create_file_of_vkr($file_id, $type, $status, $commentary = ""): file_of_vkr
    {
        global $DB, $USER;
        $this->file_id = $file_id;
        $this->usermodified = $USER->id;
        $this->timecreated = time();
        $this->timemodified = time();
        $this->type = $type;
        $this->status = $status;
        $this->commentary = $commentary;
        $this->manager_status = 1;
        $DB->insert_record(self::TABLE, get_object_vars($this));
        return $this;
    }

    /**
     * @throws dml_exception
     */
    public function update_data_of_file_VKR(array $new_data): bool
    {
        global $DB;
        return $DB->update_record(self::TABLE, (object)$new_data);
    }

    /**
     * @throws dml_exception
     */
    public function delete_file_of_vrk_complex(): bool
    {
        $success_delete = $this->delete_file_of_vkr();
        if ($success_delete) {
            return $this->delete_file();
        }
        return false;
    }

    /**
     * @throws dml_exception
     */
    protected function delete_file_of_vkr(): bool
    {
        global $DB;
        return $DB->delete_records(self::TABLE, ['file_id' => $this->file_id]);
    }

    /**
     * @throws dml_exception
     */
    public function delete_archive_files()
    {
        global $DB;
        $all_files = $this->get_files_of_vkr()->prepared_for_service();
        $fs = get_file_storage();
        if (isset($all_files['work_archive'])) {
            foreach ($all_files['work_archive'] as $work_arch) {
                $file = $fs->get_file_by_id($work_arch['id']);
                if (!is_bool($file)) {
                    $file->delete();
                }

                $DB->delete_records(self::TABLE, ['file_id' => $work_arch['id']]);
            }
        }

    }

    /**
     * @throws dml_exception
     * @throws \coding_exception
     */
    public function delete_files_of_vkr(): array
    {
        global $DB;
        $ids = $this->get_files_of_vkr()->delete_files()->files;
        if (empty($ids)) {
            return [
                'status' => false,
                'message' => get_string('not_found_files', 'local_cdo_vkr')
            ];
        }
        //some preparation for delete table rows
        $ids_string = [];
        foreach ($ids as $id) {
            $ids_string[] = $id->file_id;
        }
        $ids = join(',', $ids_string);
        $result = $DB->delete_records_select(
            self::TABLE,
            "file_id IN ({$ids})",
        );
        return [
            'status' => $result,
            'message' => get_string('not_found_files', 'local_cdo_vkr', count($ids_string))
        ];
    }

    protected function delete_file(): bool
    {
        $fs = get_file_storage();
        return $fs->get_file_by_id($this->file_id)->delete();
    }

    protected function delete_files(): file_of_vkr
    {
        $fs = get_file_storage();
        foreach ($this->files as $id) {
            $file = $fs->get_file_by_id($id->file_id);
            if (!is_bool($file)) {
                $file->delete();
            }

            // deleting files
        }
        return $this;
    }

    /**
     * @throws dml_exception
     */
    public function get_files_of_vkr(): file_of_vkr
    {
        global $DB;
        $this->files = $DB->get_records(self::TABLE, ['id_vkr' => $this->id_vkr], 'timemodified DESC');
        return $this;
    }

    /**
     * @throws dml_exception
     */
    public function get_vkr_file_id()
    {
        global $DB;
        return $DB->get_record(self::TABLE, ['file_id' => $this->file_id])->id;
    }

    /**
     * @throws dml_exception
     */
    public function prepared_for_service(): array
    {
        $array = [];
        $fs = get_file_storage();
        foreach ($this->files as $record) {
            $files_fs = $fs->get_file_by_id($record->file_id);

            if (is_bool($files_fs)) {
                $this->delete_file_of_vkr();
                continue;
            }
            $this->file_fs = $files_fs;

            $fileinfo = $this->prepared_structure_of_file(
                $record->status,
                $record->timemodified,
                $record->commentary ?? ''
            );
            switch ($record->type) {
                case '/' . $record->id_vkr . self::AREA_COMMENT:
                    $array['comment'] = $fileinfo;
                    break;
                case '/' . $record->id_vkr . self::AREA_REVIEW:
                    $array['review'] = $fileinfo;
                    break;
                case '/' . $record->id_vkr . self::AREA_WORK:
                    $array['work'] = $fileinfo;
                    break;
                case '/' . $record->id_vkr . self::AREA_ARCHIVE:
                    $array['work_archive'][] = $fileinfo;
                    break;
                default:
                    //TODO ?
                    break;
            }
        }

        return $array;
    }

    protected function prepared_structure_of_file(string $status, int $timemodified, string $reason = ''): array
    {
        return [
            'id' => $this->file_fs->get_id(),
            'name' => $this->file_fs->get_filename(),
            'url' => $this->get_file_url(),
            'reason' => $reason,
            'user_status' => [
                'id' => $status,
                'date' => date('d.m.Y', $timemodified)
            ]
        ];
    }

    protected function get_file_url(): string
    {
        return moodle_url::make_pluginfile_url(
            $this->file_fs->get_contextid(),
            $this->file_fs->get_component(),
            $this->file_fs->get_filearea(),
            $this->file_fs->get_itemid(),
            $this->file_fs->get_filepath(),
            $this->file_fs->get_filename(),
            true
        )->out();
    }

    public function type_of_vkr_file_structure(): array
    {
        return [
            'id' => $this->file_fs->get_id(),
            'name' => $this->file_fs->get_filename(),
            'url' => $this->get_file_url(),
            'user_status' => [
                'id' => $this->status, //always notAcquired
                #'date' => $this->timecreated
                'date' => date('d.m.Y', $this->timecreated)
            ]
        ];
    }


}