<?php

namespace local_cdo_ag_tools\helpers;

use core\notification;
use grade_item;
use PhpOffice\PhpSpreadsheet\IOFactory;
global $CFG;
require_once($CFG->dirroot . '/lib/excellib.class.php');
class upload
{
    public static function parse_and_save_grades_from_uploaded_data($uploaded_file): void
    {
        $spreadsheet = IOFactory::load($uploaded_file);
        $sheet = $spreadsheet->getAllSheets()[0];
        $data = [];
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            foreach ($cellIterator as $cell) {
                $data[$row->getRowIndex()][] = $cell->getValue();
            }
        }
        $grade_items_id = [];
        $grade_items = array_shift($data);
        // 6 - const for starting grade_items column
        for ($i = 6; $i < count($grade_items) - 1; $i++) {
            // after underscore always must be grade_item id
            $grade_items_id[$i] = explode('_', $grade_items[$i])[1];
        }

        foreach ($data as $user_grades) {
            $user_id = $user_grades[2];
            $user = get_complete_user_data('id', $user_id);
            $fullname = fullname($user);// 2 - const for idnumber
            if ($user_id) {
                foreach ($grade_items_id as $key => $grade_item_id) {
                    $grade_item = grade_item::fetch(
                        [
                            'id' => $grade_item_id,
                        ]
                    );
                    if ($grade_item && is_number($user_grades[$key])) {
                        $result = $grade_item->update_final_grade(
                            $user_id,
                            $user_grades[$key]
                        );
                        notification::info("Оценка для пользователя $fullname обновлена на $user_grades[$key]. Модуль $grade_item->itemname");

                    }
                }
            }
        }
    }
}
