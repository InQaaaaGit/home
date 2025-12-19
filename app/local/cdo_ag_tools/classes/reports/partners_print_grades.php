<?php

namespace local_cdo_ag_tools\reports;

use coding_exception;
use context_course;
use dml_exception;
use local_cdo_ag_tools\helpers\helper;
use PhpOffice\PhpSpreadsheet\Exception;
use required_capability_exception;
use stdClass;
use zip_archive;
use function Aws\map;

class partners_print_grades
{
    protected array $files = [];

    private function get_parameters(array $itemids): stdClass
    {
        $param = new stdClass();
        map($itemids, function ($itemid) use ($param) {
            $param->itemids[$itemid] = "1";
        });
        /*  $param->itemids = [
              2 => "1",
              3 => "1",
          ];*/
        $param->decimals = "2";
        $param->export_onlyactive = 1;
        $param->export_feedback = "0";
        $param->display = [
            'real' => "1",
            'percentage' => "0",
            'letter' => "0"
        ];
        return $param;
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws coding_exception
     * @throws dml_exception
     * @throws required_capability_exception
     */
    public function init(): void
    {
        global $CFG, $USER;
        if (!is_dir($CFG->tempdir . '/grades')) {
            mkdir( $CFG->tempdir . "/grades/");
        }
        $this->clear_temp_files($CFG->tempdir . '/grades');
        $courses = enrol_get_users_courses($USER->id, true);
        require_once $CFG->libdir . '/filestorage/zip_archive.php';
        $zip = new \zip_archive();

        $fn = $CFG->tempdir . '/grades/archive.zip';
        if ($zip->open($fn) !== TRUE) {
            exit("cannot open <$fn>\n");
        }
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            require_capability('moodle/grade:export', $context);
            require_capability('gradeexport/xls:view', $context);
            $cms = array_keys(get_course_mods($course->id));
            $param = $this->get_parameters($cms);
            $user_groups = groups_get_all_groups($course->id, $USER->id);
            $shortname = format_string($course->fullname);
            $category = helper::get_category_name_helper($course->category);
            foreach ($user_groups as $group) {
                $filename = $category . "_" . $shortname . "_" . $group->name . ".xlsx";
                $downloadfilename = clean_filename($filename);
                $gex = new cdo_grade_export_xls(get_course($course->id), $group->id, $param, $downloadfilename);
                $gex->print_grades();
                $this->files[$downloadfilename] = $gex->files;
            }
            foreach ($this->files as $key => $file) {
                $zip->add_file_from_pathname($key, $file[0]);
            }
        }
        $zip->close();
        send_file($fn, 'Оценки.zip');

    }

    protected function clear_temp_files($folderPath): void
    {
        $fileList = glob($folderPath . '/*');
        foreach ($fileList as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}