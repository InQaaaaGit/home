<?php

namespace local_cdo_ag_tools\reports;
global $CFG;


use coding_exception;
use context_course;
use core\context\coursecat;
use dml_exception;
use grade_export_update_buffer;
use grade_helper;
use grade_tree;
use graded_users_iterator;
use local_cdo_ag_tools\helpers\helper;
use MoodleExcelWorkbook;
use PhpOffice\PhpSpreadsheet\Exception;


require_once($CFG->dirroot . '/grade/export/xls/grade_export_xls.php');

class cdo_grade_export_xls extends \grade_export_xls
{

    protected string $filename;

    public function __construct($course, $groupid, $formdata, string $filename)
    {
        $this->filename = $filename;
        parent::__construct($course, $groupid, $formdata);
    }

    public array $files;

    /**
     * @throws coding_exception
     * @throws dml_exception
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function print_grades(): void
    {
        global $CFG;
        require_once($CFG->dirroot . '/lib/excellib.class.php');

        $export_tracking = $this->track_exports();

        $strgrades = get_string('grades');

        // If this file was requested from a form, then mark download as complete (before sending headers).
        \core_form\util::form_download_complete();

        $downloadfilename = $this->filename;
        // Creating a workbook
        $workbook = new cdo_excel("-");

        // Sending HTTP headers
        $workbook->send($downloadfilename);
        // Adding the worksheet
        $myxls = $workbook->add_worksheet($strgrades);

        // Print names of all the fields
        $profilefields = grade_helper::get_user_profile_fields($this->course->id, $this->usercustomfields);
        foreach ($profilefields as $id => $field) {
            $myxls->write_string(0, $id, $field->fullname);
        }

        $pos = count($profilefields);
        $workbook->setCountToBlockColumn($pos);
        if (!$this->onlyactive) {
            $myxls->write_string(0, $pos++, get_string("suspended"));
        }
        $gtree = new grade_tree($this->course->id, false, false);
        $this->columns = $gtree->items;
        /*usort($this->columns, function ($a, $b) {
            return $b->sortorder - $a->sortorder;
        });*/

        foreach ($this->columns as $grade_item) {
            foreach ($this->displaytype as $gradedisplayname => $gradedisplayconst) {
                if (!in_array($grade_item->itemtype, ['core', 'course', 'category'])) {
                    $column_name = $this->format_column_name(
                        $grade_item,
                        false,
                        $gradedisplayname
                    );
                    $myxls->write_string(
                        0,
                        $pos++,
                        $column_name . '_' . $grade_item->id
                    );
                }
            }
            // Add a column_feedback column
            if ($this->export_feedback) {
                $myxls->write_string(0, $pos++, $this->format_column_name($grade_item, true));
            }
        }
        // Last downloaded column header.
        $myxls->write_string(0, $pos++, get_string('timeexported', 'gradeexport_xls'));

        // Print all the lines of data.
        $i = 0;
        $geub = new grade_export_update_buffer();
        $gui = new graded_users_iterator($this->course, $this->columns, $this->groupid);
        $gui->require_active_enrolment($this->onlyactive);
        $gui->allow_user_custom_fields($this->usercustomfields);
        $gui->init();
        while ($userdata = $gui->next_user()) {
            $i++;
            $user = $userdata->user;

            foreach ($profilefields as $id => $field) {

                $fieldvalue = grade_helper::get_user_field_value($user, $field);
                if ($field->shortname==='idnumber') {
                    $myxls->write_string($i, $id, $user->id);
                } else
                $myxls->write_string($i, $id, $fieldvalue);
            }
            $j = count($profilefields);
            if (!$this->onlyactive) {
                $issuspended = ($user->suspendedenrolment) ? get_string('yes') : '';
                $myxls->write_string($i, $j++, $issuspended);
            }
            foreach ($userdata->grades as $itemid => $grade) {
                if (!in_array($grade->grade_item->itemtype , ['core', 'course', 'category'])) {
                    if ($export_tracking) {
                        $status = $geub->track($grade);
                    }
                    foreach ($this->displaytype as $gradedisplayconst) {
                        $gradestr = $this->format_grade($grade, $gradedisplayconst);
                        if (is_numeric($gradestr)) {
                            $myxls->write_number($i, $j++, $gradestr);
                        } else {
                            $myxls->write_string($i, $j++, $gradestr);
                        }
                    }
                    // writing feedback if requested
                    if ($this->export_feedback) {
                        $myxls->write_string($i, $j++, $this->format_feedback($userdata->feedbacks[$itemid], $grade));
                    }
                }
            }
            // Time exported.
            $myxls->write_string($i, $j++, time());
        }
        $gui->close();
        $geub->close();

        /// Close the workbook
        $workbook->close();
        $this->files[] = $workbook->get_files();
        /*exit;*/
    }
}