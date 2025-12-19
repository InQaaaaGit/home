<?php

namespace local_cdo_ag_tools\grades;

use context_course;
use dml_exception;
use grade_item;
use gradereport_user\external\user;
use local_cdo_ag_tools\controllers\accumulate;
use local_cdo_ag_tools\helpers\get_grade_items_helper;
use moodle_exception;

class grades_with_double_coefficient
{
   /// const FIELD_NAME = 'double';
    const FIELD_NAME = 'doubling';
    private int $fieldid_double_coefficient;

    /**
     * @throws dml_exception
     */
    public function __construct()
    {
        $customfield = $this->get_customfield_id_double_coefficient();
        if ($customfield && isset($customfield->id)) {
            $this->fieldid_double_coefficient = (int)$customfield->id;
        } else {
            $this->fieldid_double_coefficient = 0;
        }
    }

    /**
     * @throws dml_exception
     */
    protected function get_all_modules_with_double_coefficient(): array
    {
        global $DB;
        return $DB->get_records('customfield_data',
            [
                'fieldid' => $this->fieldid_double_coefficient,
                'intvalue' => 1
            ]
        );
    }

    /**
     * @throws dml_exception
     */
    public function get_customfield_data_double_coefficient($cmid)
    {
        global $DB;
        return $DB->get_record(
            'customfield_data',
            [
                'instanceid' => $cmid,
                'fieldid' => $this->fieldid_double_coefficient,
                'intvalue' => 1
            ],
            '*'
        );
    }

    /**
     * @throws dml_exception
     */
    protected function get_customfield_id_double_coefficient()
    {
        global $DB;
        return $DB->get_record(
            'customfield_field',
            [
                'shortname' => self::FIELD_NAME,
            ]
        );
    }

    /**
     * @throws dml_exception
     */
    public function get_categories_that_need_doubling_coefficient(): array
    {
        global $DB;
        $like = "'/223%'";
        return $DB->get_records_sql("SELECT c.id
                                        FROM mdl_course_categories cc
                                        INNER JOIN mdl_course c ON c.category = cc.id
                                            WHERE path LIKE {$like}");
    }

    /**
     * @throws moodle_exception
     */
    public function update_grades_with_coefficient(): void
    {
        global $CFG;
        require_once $CFG->dirroot . '/question/editlib.php';
        $all_modules = $this->get_all_modules_with_double_coefficient();
        $all_courses = [];
        foreach ($all_modules as $module) {
            [$m, $cm] = get_module_from_cmid($module->instanceid);
            $all_courses[] = $cm->course;
        }
        foreach ($all_courses as $course) {
            $this->update_grade_with_coefficient($course);
        }
    }

    /**
     * @throws moodle_exception
     */
    public function accumulate_coefficient_task_for_course($courseid): void
    {
        $this->update_grade_with_coefficient($courseid);
    }

    /**
     * @throws moodle_exception
     * @throws dml_exception
     */
    public function accumulate_coefficient_task($conditions = []): void
    {
        $courses_for_accumulate = accumulate::get_accumulate_row($conditions);
        if (empty($courses_for_accumulate)) {
            mtrace('courses not found');
        }
        foreach ($courses_for_accumulate as $course) {
            mtrace('course:' . $course->id);
            $this->update_grade_with_coefficient($course->courseid);
            accumulate::delete_accumulate_row($course->courseid);
        }
    }

    /**
     * @throws moodle_exception
     */
    public function update_grade_for_single_user($user, $course_id): void
    {
        $this->update_grade_for_users([$user], $course_id);
    }

    /**
     * @throws moodle_exception
     */
    protected function update_grade_with_coefficient($course_id): void
    {
        $enrolled_users = get_enrolled_users(context_course::instance($course_id));
        $this->update_grade_for_users($enrolled_users, $course_id);
    }

    /**
     * @throws moodle_exception
     */
    protected function update_grade_for_users(array $users, $course_id): void
    {
        foreach ($users as $enrolled_user) {
            $this->get_user_grade_items_for_aggregate((int)$course_id, $enrolled_user->id);
        }
    }

    /**
     * @throws moodle_exception
     */
    public function get_user_grade_items_for_aggregate($course_id, $user_id): void
    {
        $needed_grades_items = [];
        // Получаем оценки пользователя в выбранном курсе
        try {
            $user_grade_items = get_grade_items_helper::get_grade_items_helper($course_id, $user_id);
        } catch (\Exception $e) {
            mtrace($e->getMessage());
        }

        // Получаем все модули курса с дополнительным полем
        $modinfo = get_fast_modinfo($course_id);
        $cms = [];
        foreach ($modinfo->get_cms() as $cm) {
            $customfield_record = $this->get_customfield_data_double_coefficient($cm->id);
            //   mtrace('course mod:' . $cm->id);
            if (!is_bool($customfield_record))
                $cms[$cm->instance] = (bool)$customfield_record->value;
        }

        // собираем grade_items по своим категориям, категорий может быть N
        $needle_category_resum = [];
        foreach ($user_grade_items['usergrades'] as $user_grade_item) {
            foreach ($user_grade_item['gradeitems'] as $grade_item) {
                //if (!!\grade_category::fetch(['id' => $grade_item['categoryid']])->aggregateonlygraded) {
                if (array_key_exists((int)$grade_item['iteminstance'], $cms)) {
                    $is_doubling_coefficient = $cms[(int)$grade_item['iteminstance']];
                    if ($is_doubling_coefficient) {
                        //  mtrace('find doubling cm' . (int)$grade_item['iteminstance']);
                        $needle_category_resum[] = (int)$grade_item['categoryid'];
                        $needed_grades_items[$grade_item['categoryid']][] = $grade_item; // hack
                    }
                }
                $needed_grades_items[$grade_item['categoryid']][] = $grade_item;
                //  }
            }
        }

        // Фильтруем grade_items по признаку того, что не в каждой категории требуется перерасчет оценки за категорию
        $summary = [];
        foreach ($needed_grades_items as $key => $value) {
            foreach ($needle_category_resum as $item) {
                if ($key === $item) {
                    $summary[] = [
                        'categoryid' => $key,
                        'grade_items' => $value
                    ];
                }
            }
        }

        //В отфильтрованных grade_items ищем запись grade_item за всю категорию признак 'itemtype' === 'category'
        foreach ($summary as &$item) {
            foreach ($user_grade_items['usergrades'] as $user_grade_item) {
                foreach ($user_grade_item['gradeitems'] as $grade_item) {
                    if ($item['categoryid'] === (int)$grade_item['iteminstance']
                        && $grade_item['itemtype'] === 'category') {
                        $item['category_for_change'] = $grade_item;
                    }
                }
            }
        }

        //Считаем значение средней оценки при условии задвоенной оценки одного из модулей и сразу обновляем ее
        foreach ($summary as &$summary_object) {
            #$all_grades = count($summary_object['grade_items']);
            $all_grades = 0;
            $sum = 0;
            foreach ($summary_object['grade_items'] as $grade_item) {
                if (!empty($grade_item['graderaw'])) {
                    $sum += $grade_item['graderaw'];
                    $all_grades++;
                } else {
                    if (!\grade_category::fetch(['id' => $grade_item['categoryid']])->aggregateonlygraded) {
                        $sum += 0;
                        $all_grades++;
                    }
                }
            }
            if ($all_grades > 0) {
                $summary_object['final_grade_to_change'] = round($sum / $all_grades);
                $grade_item = grade_item::fetch(
                    [
                        'id' => $summary_object['category_for_change']['id'],
                        'courseid' => $course_id
                    ]
                );
                if (is_bool($grade_item)) {
                    //  mtrace("grade item in $course_id is bool " . $summary_object['category_for_change']['id']);
                }
                if ($grade_item) {
                    $result = $grade_item->update_final_grade(
                        $user_id,
                        $summary_object['final_grade_to_change'], source: 'editgrade'
                    );
                    //    mtrace("user_id: $user_id; final_grade: " . $summary_object['final_grade_to_change'] .
                    //       " courseid: $course_id; grade_item: " . $summary_object['category_for_change']['id']);
                    if (!$result) {
                        throw new \core\exception\moodle_exception('231123');
                    }
                }
            }
        }
    }
}
