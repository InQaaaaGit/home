<?php

namespace block_slider;

use core_course_category;
use core_course_list_element;
use coursecat_helper;
use lang_string;
use moodle_url;

class cdo_controller
{
    protected $courses;
    public $preparedCourses;

    public function getAllCourses($needleCourses = "")
    {
        global $CFG;
        $chelper = new coursecat_helper();
        $chelper->set_show_courses(20)->
        set_courses_display_options(array(
            'recursive' => true,
            'limit' => $CFG->frontpagecourselimit,
            'viewmoreurl' => new moodle_url('/course/index.php'),
            'viewmoretext' => new lang_string('fulllistofcourses')));

        $chelper->set_attributes(array('class' => 'frontpage-course-list-all'));
        $courses = core_course_category::top()->get_courses($chelper->get_courses_display_options(), true, 1);

        $this->courses = $courses;
        return $this;
    }

    public function getCourseWithEnrollmentMethod($needleCourses = "")
    {
        global $DB;
        $where = "";
        if (isset($needleCourses)) {
            $where = "AND c.id in ({$needleCourses})";
        }

        $list = $DB->get_records_sql(
            "SELECT  *
                FROM {enrol} e INNER JOIN {course} c ON c.id=e.courseid
                WHERE e.enrol = 'self'
                  AND status = 0  
                {$where}
                LIMIT 4    
            ;"
        );

        $this->courses = $list;
        return $this;
    }

    public function getOpenCoursesWithContent()
    {
        global $CFG;
        $new_course_array = [];

        foreach ($this->courses as $course) {
            $course_element = new core_course_list_element($course);
            foreach ($course_element->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                    $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
                if ($isimage) {
                    $course->image_url = $url;
                }

            }
            if (empty($course->image_url)) {
                // Используем стандартное изображение курса из Moodle
                $url = $CFG->wwwroot . "/pix/i/course.svg";
                $course->image_url = $url;
            }

            $new_course_array[] = $course;
        }
        $this->preparedCourses = $new_course_array;
        return $this;
    }

    public function getIncludedCourses(string $needleCourses = ""): cdo_controller
    {
        global $DB;
        $where = "";
        if (!empty($needleCourses)) {

            $where = "WHERE c.id in ({$needleCourses})";
        }

        $list = $DB->get_records_sql(
            "SELECT *
                FROM {course} c
                {$where}
                LIMIT 4    
            ;"
        );

        $this->courses = $list;
        return $this;
    }

    public function getOutputCategoryBox(string $coursecat = "")
    {
        global $OUTPUT, $CFG;
        require_once($CFG->dirroot . '/blocks/slider/lib.php');
        $categories = get_categories_with_overview($CFG->name_category_open_course_id, [$coursecat], true);
        return $OUTPUT->render_from_template('block_slider/sections/components/category_box',
            [
                "open_category" => $categories
            ]
        );
    }

    public function check_category_is_parent(string $cat_id, string $parent_id): int
    {
        global $DB;
        $where = "WHERE cc.id = $cat_id AND cc.path LIKE '%/$parent_id/%'";

        $list = $DB->get_records_sql(
            "SELECT *
                FROM {course_categories} cc
                {$where}    
            ;"
        );
        return count($list);
    }
}