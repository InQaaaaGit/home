<?php

namespace tool_cdo_showcase_tools\helpers;

use core_enrol_external;
use gradereport_user\external\user;
use tool_cdo_showcase_tools\external\get_course_letter_grades;

/**
 * Helper класс для работы с курсами
 * 
 * Содержит методы для обогащения данных курсов дополнительной информацией
 */
class courses_helper
{
    /**
     * Обогащение данных курсов дополнительной информацией
     * 
     * @param array $courses Массив курсов
     * @param int $userId ID пользователя
     * @return array Обогащенные данные курсов
     */
    public static function enrichCoursesData(array $courses, int $userId): array
    {
        $enrichedCourses = [];
        
        foreach ($courses as $course) {
            // Добавляем название категории
            $course['category_name'] = gradereport_helper::get_category_name($course['category']);
            
            // Получаем преподавателей курса
            $teachers = teachers_helper::get_teachers_on_course($course['id']);
            $course['teachers'] = $teachers;

            // Получаем зачисленных пользователей
            $course['enrolled_users'] = self::getEnrolledUsers($course['id']);
            $course['enrolled_users_count'] = count($course['enrolled_users']);
            
            // Получаем оценки пользователей
            $course['user_grades'] = user::get_grade_items($course['id']);
            foreach ($course['user_grades']['usergrades'] as &$usergrades) {
                foreach ($usergrades['gradeitems'] as &$usergrade) {
                    $usergrade['itemname'] = empty($usergrade['itemname']) ?
                        gradereport_helper::get_grade_categories_name($usergrade['iteminstance']) : $usergrade['itemname'];
                }
            }
            
            // Получаем шкалу оценок
            $course['scale'] = get_course_letter_grades::execute($course['id'], $userId);
            
            $enrichedCourses[] = $course;
        }

        return $enrichedCourses;
    }

    /**
     * Получение зачисленных пользователей курса
     * 
     * @param int $courseId ID курса
     * @return array Массив зачисленных пользователей
     */
    public static function getEnrolledUsers(int $courseId): array
    {
        // Используем стандартный метод Moodle API для получения зачисленных пользователей
        return core_enrol_external::get_enrolled_users($courseId);
    }
}
