<?php

namespace tool_cdo_showcase_tools\external;

use core_course_category;
use core_course_external;
use core_enrol_external;
use core_external\external_files;
use core_external\external_format_value;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use dml_exception;
use gradereport_user\external\user;
use tool_cdo_showcase_tools\helpers\courses_helper;
use tool_cdo_showcase_tools\helpers\external_helper;
use tool_cdo_showcase_tools\helpers\gradereport_helper;
use tool_cdo_showcase_tools\helpers\teachers_helper;

require_once $CFG->dirroot . '/enrol/externallib.php';
require_once $CFG->dirroot . '/course/externallib.php';

class courses extends core_enrol_external
{
    /**
     * Получение курсов пользователя с фильтрацией по категории
     * 
     * @param string $email Email пользователя
     * @param bool $returnusercount Включать ли количество пользователей
     * @param string $categoryId ID категории для фильтрации (пустая строка = все категории)
     * @return array Массив курсов пользователя из указанной категории
     * @throws dml_exception
     */
    public static function get_users_courses($email, $returnusercount = true, $categoryId = ''): array
    {
        $user = get_complete_user_data('email', $email);
        if (!$user) {
            throw new dml_exception('По почте не найдено пользователя', 'По почте не найдено пользователя');
        }

        // Получаем все курсы пользователя
        $userCourses = parent::get_users_courses($user->id, $returnusercount);
        
        // Если категория не указана, возвращаем все курсы пользователя
        if (empty($categoryId)) {
            return courses_helper::enrichCoursesData($userCourses, $user->id);
        }

        // Получаем курсы из указанной категории и всех дочерних категорий
        $category = core_course_category::get($categoryId);
        $categoryCourses = $category->get_courses([
            'recursive' => true,
        ]);

        // Создаем массив ID курсов из категории для быстрого поиска
        $categoryCourseIds = array_column($categoryCourses, 'id');
        
        // Находим пересечение: курсы пользователя, которые есть в указанной категории
        $intersectionCourses = [];
        foreach ($userCourses as $course) {
            if (in_array($course['id'], $categoryCourseIds)) {
                $intersectionCourses[] = $course;
            }
        }

        return courses_helper::enrichCoursesData($intersectionCourses, $user->id);
    }

    public static function get_users_courses_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            array(
                'email' => new external_value(PARAM_TEXT, 'Email пользователя'),
                'returnusercount' => new external_value(PARAM_BOOL,
                    'Включать ли количество зачисленных пользователей для каждого курса? Это может добавить несколько секунд к времени ответа'
                    . ' если пользователь находится на нескольких больших курсах, поэтому установите false если значение не будет использоваться'
                    . ' для улучшения производительности.',
                    VALUE_DEFAULT, true),
                'apiRequestParam' => new external_value(PARAM_TEXT, 'ID категории для фильтрации курсов (пустая строка = все категории)', VALUE_DEFAULT, ''),
            )
        );
    }

    public static function get_users_courses_returns(): external_multiple_structure
    {

        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_INT, 'id of course'),
                    'shortname' => new external_value(PARAM_RAW, 'short name of course'),
                    'fullname' => new external_value(PARAM_RAW, 'long name of course'),
                    'displayname' => new external_value(PARAM_RAW, 'course display name for lists.', VALUE_OPTIONAL),
                    'enrolledusercount' => new external_value(PARAM_INT, 'Number of enrolled users in this course',
                        VALUE_OPTIONAL),
                    'idnumber' => new external_value(PARAM_RAW, 'id number of course'),
                    'visible' => new external_value(PARAM_INT, '1 means visible, 0 means not yet visible course'),
                    'summary' => new external_value(PARAM_RAW, 'summary', VALUE_OPTIONAL),
                    'summaryformat' => new external_format_value('summary', VALUE_OPTIONAL),
                    'format' => new external_value(PARAM_PLUGIN, 'course format: weeks, topics, social, site', VALUE_OPTIONAL),
                    'courseimage' => new external_value(PARAM_RAW, 'The course image URL', VALUE_OPTIONAL),
                    'showgrades' => new external_value(PARAM_BOOL, 'true if grades are shown, otherwise false', VALUE_OPTIONAL),
                    'lang' => new external_value(PARAM_LANG, 'forced course language', VALUE_OPTIONAL),
                    'enablecompletion' => new external_value(PARAM_BOOL, 'true if completion is enabled, otherwise false',
                        VALUE_OPTIONAL),
                    'completionhascriteria' => new external_value(PARAM_BOOL, 'If completion criteria is set.', VALUE_OPTIONAL),
                    'completionusertracked' => new external_value(PARAM_BOOL, 'If the user is completion tracked.', VALUE_OPTIONAL),
                    'category' => new external_value(PARAM_INT, 'course category id', VALUE_OPTIONAL),
                    'category_name' => new external_value(PARAM_TEXT, 'course category id', VALUE_OPTIONAL),
                    'progress' => new external_value(PARAM_FLOAT, 'Progress percentage', VALUE_OPTIONAL),
                    'completed' => new external_value(PARAM_BOOL, 'Whether the course is completed.', VALUE_OPTIONAL),
                    'startdate' => new external_value(PARAM_INT, 'Timestamp when the course start', VALUE_OPTIONAL),
                    'enddate' => new external_value(PARAM_INT, 'Timestamp when the course end', VALUE_OPTIONAL),
                    'marker' => new external_value(PARAM_INT, 'Course section marker.', VALUE_OPTIONAL),
                    'lastaccess' => new external_value(PARAM_INT, 'Last access to the course (timestamp).', VALUE_OPTIONAL),
                    'isfavourite' => new external_value(PARAM_BOOL, 'If the user marked this course a favourite.', VALUE_OPTIONAL),
                    'hidden' => new external_value(PARAM_BOOL, 'If the user hide the course from the dashboard.', VALUE_OPTIONAL),
                    'overviewfiles' => new external_files('Overview files attached to this course.', VALUE_OPTIONAL),
                    'showactivitydates' => new external_value(PARAM_BOOL, 'Whether the activity dates are shown or not'),
                    'showcompletionconditions' => new external_value(PARAM_BOOL, 'Whether the activity completion conditions are shown or not'),
                    'timemodified' => new external_value(PARAM_INT, 'Last time course settings were updated (timestamp).',
                        VALUE_OPTIONAL),
                    'teachers' => new external_multiple_structure(
                        new external_single_structure(
                            [
                                'id' => new external_value(PARAM_INT, 'userid', VALUE_DEFAULT, []),
                                'firstname' => new external_value(PARAM_TEXT, 'userid', VALUE_DEFAULT, ''),
                                'lastname' => new external_value(PARAM_TEXT, 'userid', VALUE_DEFAULT, ''),
                                'middlename' => new external_value(PARAM_TEXT, 'userid', VALUE_DEFAULT, ''),
                                'email' => new external_value(PARAM_TEXT, 'userid', VALUE_DEFAULT, ''),
                            ]
                        ),
                    '',
                        VALUE_DEFAULT,
                        []
                    ),
                    'enrolled_users' => external_helper::get_enrolled_users_returns(),
                    'user_grades' => user::get_grade_items_returns(),
                    'scale' => get_course_letter_grades::execute_returns()
                ]
            )
        );
    }

    public static function get_courses_parameters(): external_function_parameters
    {
        $params = new external_function_parameters(
            array(
                'options' => new external_single_structure(
                array('ids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id')
                    , 'List of course id. If empty return all courses
                                            except front page course.',
                    VALUE_OPTIONAL)
                ), 'options - operator OR is used', VALUE_DEFAULT, array()),
                'apiRequestParam' => new external_value(PARAM_TEXT, 'ID категории для фильтрации курсов (пустая строка = все категории)', VALUE_DEFAULT, ''),
            ),

        );
        return $params;
    }

    /**
     * Получение курсов с возможностью фильтрации по категории
     * 
     * @param array $options Опции для получения курсов
     * @param string $categoryId ID категории для фильтрации
     * @return array Массив курсов
     */
    public static function get_courses(array $options = array(), $categoryId = ''): array
    {
        $courses = core_course_external::get_courses($options);

        // Получаем ID пользователя из контекста или используем текущего пользователя
        global $USER;
        $userId = $USER->id;
        
        return courses_helper::enrichCoursesData($courses, $userId);
    }

    public static function get_courses_returns(): external_multiple_structure
    {
        return self::get_users_courses_returns();
    }

    /**
     * Получение курсов из определенной категории
     * 
     * @param array $options Опции для получения курсов
     * @param string $category_id ID категории для фильтрации курсов
     * @return array Массив курсов из указанной категории в стандартном формате Moodle
     */
    public static function get_courses_by_category(array $options = array(), string $category_id = ''): array
    {
        // Если категория не указана, возвращаем все курсы через стандартный API
        if (empty($category_id)) {
            return core_course_external::get_courses($options);
        }

        // Получаем курсы из указанной категории и всех дочерних категорий
        $category = core_course_category::get($category_id);
        $categoryCourses = $category->get_courses([
            'recursive' => true,
        ]);

        // Создаем массив ID курсов из категории
        $categoryCourseIds = array_column($categoryCourses, 'id');
        
        // Устанавливаем ID курсов из категории в опции
        $options['ids'] = $categoryCourseIds;

        // Получаем курсы через стандартный API с ID из категории
        return core_course_external::get_courses($options);
    }

    /**
     * Параметры для функции get_courses_by_category
     * 
     * @return external_function_parameters
     */
    public static function get_courses_by_category_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            array(
                'options' => new external_single_structure(
                    array(
                        'ids' => new external_multiple_structure(
                            new external_value(PARAM_INT, 'Course id'),
                            'List of course id. If empty return all courses except front page course.',
                            VALUE_OPTIONAL
                        )
                    ),
                    'options - operator OR is used',
                    VALUE_DEFAULT,
                    array()
                ),
                'category_id' => new external_value(PARAM_TEXT, 'ID категории для фильтрации курсов (пустая строка = все категории)', VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Возвращаемая структура для функции get_courses_by_category
     * 
     * @return external_multiple_structure
     */
    public static function get_courses_by_category_returns(): external_multiple_structure
    {
        return core_course_external::get_courses_returns();
       // return self::get_users_courses_returns();
    }

}