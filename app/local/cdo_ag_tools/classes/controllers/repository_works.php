<?php

namespace local_cdo_ag_tools\controllers;


use context_system;
use core_course\customfield\course_handler;
use dml_exception;
use local_cdo_ag_tools\files\controller_work_archive;
use local_cdo_ag_tools\files\works_archive;
use local_cdo_ag_tools\helpers\helper;
use local_cdo_ag_tools\qr\generate_pdf;
use moodle_exception;
use repository_exception;
use repository_filesystem;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use zip_archive;

class repository_works
{
    protected repository_filesystem $repository;
    protected array $files = [];
    protected int $user;
    protected object $cfg;
    protected string $qrcode_path;
    private generate_pdf $generate_pdf;
    protected string $archive_path;
    protected string $archive_name;

    /**
     * @throws dml_exception
     */
    public function __construct(generate_pdf $generate_pdf, $userid)
    {
        global $CFG;
        require_once $CFG->dirroot . '/repository/filesystem/lib.php';

        $this->user = $userid;
        $this->cfg = $CFG;
        $this->qrcode_path = $this->cfg->tempdir . '/qrcode.png';
        $this->archive_name = "/Контрольные Работы" . time() . ".zip";
        $this->archive_path = $this->cfg->tempdir . $this->archive_name;
        $repository = get_config('local_cdo_ag_tools', 'file_repository');
        if ($repository) {
            $this->repository = new repository_filesystem(
                (int)$repository
            );
            $this->files = $this->repository->get_listing();
        }
        $this->generate_pdf = $generate_pdf;
    }

    /**
     * @throws dml_exception
     */
    private function get_search_string_for_work_files($parallel_number, $discipline_name): string
    {
        $only_final_works = (bool) get_config('local_cdo_ag_tools', 'only_final_works');
        if ($only_final_works) {
            return "$parallel_number класс. $discipline_name. Итоговая контрольная работа";
        }
        return "$parallel_number класс. $discipline_name";
    }

    /**
     * @throws dml_exception
     */
    private function get_search_string_for_keys_files($parallel_number, $discipline_name): string
    {
        $only_final_works = (bool) get_config('local_cdo_ag_tools', 'only_final_works');
        if ($only_final_works) {
            return "$parallel_number класс. Ключи. $discipline_name. Итоговая контрольная работа";
        }
        return "$parallel_number класс. $discipline_name";
    }
    /**
     * Helper function to determine the best matching section ID based on file path and section names.
     */
    private function get_best_section_id(string $filePath, array $sectionNames): int
    {
        $lengths = [];
        $maxValue = null;
        $sectionId = 0;

        foreach ($sectionNames as $id => $name) {
            $lengths[$id] = strlen(helper::longestCommonSubstring($filePath, $name));
        }

        foreach ($lengths as $key => $value) {
            if ($maxValue === null || $value > $maxValue) {
                $maxValue = $value;
                $sectionId = $key;
            }
        }

        return $sectionId;
    }

    /**
     * @throws repository_exception
     * @throws PdfParserException
     * @throws PdfReaderException
     * @throws moodle_exception
     */
    public function create_work_with_qrcode($files, $fullname, $member, $course_id): array
    {
        $files_to_zip = [];
        $section_names = helper::get_section_names_for_work($course_id);

        foreach ($files as $file) {
            $path_work_original = $this->repository->get_rootpath() . $file['path'];
            $section_id = $this->get_best_section_id($file['path'], $section_names);

            $user_dir = $this->cfg->tempdir . '/' . $fullname;
            if (!is_dir($user_dir)) {
                mkdir($user_dir);
            }
            $path_work_duplicate = $user_dir . '/' . $fullname . $file['title'];
            if (is_file($path_work_original)) {
                $result_copy = copy($path_work_original, $path_work_duplicate);
                if (!$result_copy) {
                    throw new moodle_exception('copyerror', 'repository_filesystem', '', $path_work_duplicate);
                }
                $files_to_zip[$file['title']] = $this->generate_pdf->generate_pdf_with_qrcode(
                    $path_work_duplicate,
                    $this->qrcode_path,
                    $member,
                    $section_id
                );
            } else {
                throw new moodle_exception('filenotfound', 'repository_filesystem', '', $path_work_original);
            }

        }
        return $files_to_zip;
    }

    /**
     * @throws PdfParserException
     * @throws repository_exception
     * @throws PdfReaderException
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function generate_output_zip_with_works($send_file = false): void
    {
        $zip = new zip_archive();
        $fn = $this->archive_path;
        if (file_exists($fn) && filesize($fn) > 0) {
            @unlink($fn);
        }
        if ($zip->open($fn) !== TRUE) {
            throw new moodle_exception('cannotopen', 'zip', '', $fn);
        }

        try {
            $courses = enrol_get_users_courses($this->user, true);
            $to_archive = [];
            foreach ($courses as $course) {
                $parallels_of_course = [];
                #$course_full = get_course($course->id);
                $to_archive = []; // every course have own files
                $user_groups = groups_get_all_groups($course->id, $this->user);

                #$customfields = course_handler::create()->export_instance_data_object($course->id);
                $customfields = helper::get_customfields_value($course->id);
                if ($customfields)
                    $discipline_name = trim(strip_tags($customfields->customfield_value));
                if (!empty($discipline_name)) {

                    foreach ($user_groups as $group) {
                        $work_file = [];
                        $parallel_number = '';
                        if (preg_match('/\d+(\.\d+)?/', $group->name, $matches)) {
                            $parallel_number = $matches[0];
                            $parallels_of_course[] = $parallel_number;
                            $work_file = $this->repository->search(
                                $this->get_search_string_for_work_files($parallel_number, $discipline_name)
                            );
                        }
                        if (!empty($work_file['list'])) {
                            foreach (groups_get_groups_members($group->id) as $member) {
                                // for each member of group create pdf with QR
                                $fullname = fullname($member);
                                $to_archive[] = [
                                    'fullname' => $fullname,
                                    'parallel_number' => $parallel_number,
                                    'files' => $this->create_work_with_qrcode(
                                        $work_file['list'],
                                        $fullname,
                                        $member,
                                        $course->id
                                    )
                                ];
                            }
                        } else {
                            mtrace('empty work files: ' . $parallel_number);
                        }
                    }

                    $discipline_clean = str_replace('(', '', $discipline_name);
                    $discipline_clean = str_replace(')', '', $discipline_clean);

                    $zip->add_directory("/" . $discipline_clean);

                    foreach ($parallels_of_course as $parallel) {
                        $work_keys_file = $this->repository->search(
                            $this->get_search_string_for_keys_files($parallel, $discipline_name)
                        );
                        foreach ($work_keys_file['list'] as $work_file) {
                            $file_key = $this->repository->get_rootpath() . $work_file['path'];
                            if (!file_exists($file_key)) {
                                mtrace("Warning: File not found: " . $file_key);
                                continue;
                            }
                            $path_in_zip = "/" . $discipline_clean . "/" . $work_file['title'];
                            $result = $zip->add_file_from_pathname($path_in_zip, $file_key);
                            if ($result === false) {
                                throw new moodle_exception('cannotaddfiletozip', 'zip', '', $work_file['title']);
                            }
                        }
                    }

                    foreach ($to_archive as $files) {
                        foreach ($files['files'] as $key_parallel => $file_parallel) {
                            foreach ($file_parallel as $file) {
                                if (!file_exists($file)) {
                                    mtrace("Warning: File not found: " . $file);
                                    continue;
                                }
                                $path = "/" . $discipline_clean . "/" . $files['fullname'] . ".pdf" ;
                                mtrace("path: " . $path . " " . $file);
                                $result = $zip->add_file_from_pathname($path, $file);
                                if ($result === false) {
                                    throw new moodle_exception('cannotaddfiletozip', 'zip', '', $files['fullname']);
                                }
                            }
                        }
                    }
                } else {
                    echo 'not find custom field value in course with id: ' . $course->id . "\n";
                }
            }

            $result = $zip->close();
            if ($result === false) {
                throw new moodle_exception('cannotclosezip', 'zip', '', $fn);
            }

            if ($send_file) {
                send_file($this->archive_path, $this->archive_name);
            } else {
                $cwa = new controller_work_archive();
                $wa = new works_archive($cwa, context_system::instance()->id, $this->archive_name, $this->user);
                $file_record = $wa->create_file($this->archive_path);
            }
        } catch (\Exception $e) {
            // Clean up temporary files in case of error
            if (file_exists($fn)) {
                @unlink($fn);
            }
            foreach ($to_archive as $key => $files) {
                foreach ($files['files'] as $file_parallel) {
                    foreach ($file_parallel as $item) {
                        if (file_exists($item)) {
                            @unlink($item);
                        }
                    }
                }
            }
            throw $e;
        }

        // Clean up temporary files after successful archive creation
        foreach ($to_archive as $key => $files) {
            foreach ($files['files'] as $file_parallel) {
                foreach ($file_parallel as $item) {
                    if (file_exists($item)) {
                        @unlink($item);
                    }
                }
            }
        }
    }

    public function get_repository_files_works($search_param, $return_full_path = false): array
    {
        $works_data = [];
        foreach ($this->repository->get_listing()['list'] as $periods) {
            foreach ($this->repository->get_listing($periods['path'])['list'] as $classes) {
                foreach ($this->repository->get_listing($classes['path'])['list'] as $disciplines) {
                    foreach ($this->repository->get_listing($disciplines['path'])['list'] as $works) {
                        if (strpos($classes['title'], $search_param) !== false) {
                            if ($return_full_path) {
                                $works_data[] = $works;
                            } else {
                                $works_data[$periods['title']][$classes['title']][$disciplines['title']] = $works;
                            }
                        }
                    }
                }
            }
        }
        return ($works_data);
    }

}
