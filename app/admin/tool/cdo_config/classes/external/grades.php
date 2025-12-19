<?php

namespace tool_cdo_config\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_warnings;
use context_course;
use context_module;
use required_capability_exception;
use invalid_parameter_exception;
use moodle_exception;

// Подключаем необходимые библиотеки Moodle для работы с оценками
global $CFG;
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/lib.php');

class grades extends external_api
{
    const mod_name='assign';
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function set_grade_to_first_assign_in_section_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'sectionid' => new external_value(PARAM_INT, 'ID секции курса'),
            'grade' => new external_value(PARAM_FLOAT, 'Оценка для выставления'),
            'userid' => new external_value(PARAM_INT, 'ID пользователя для выставления оценки', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Выставляет оценку в первый assignment, найденный в указанной секции
     * 
     * @param int $sectionid ID секции курса
     * @param float $grade Оценка для выставления
     * @param int $userid ID пользователя (0 = текущий пользователь)
     * @return array Результат операции
     * @throws invalid_parameter_exception
     * @throws required_capability_exception
     * @throws moodle_exception
     */
    public static function set_grade_to_first_assign_in_section(int $sectionid, float $grade, int $userid = 0): array
    {
        global $DB, $USER;

        // Валидация параметров
        $params = self::validate_parameters(
            self::set_grade_to_first_assign_in_section_parameters(),
            ['sectionid' => $sectionid, 'grade' => $grade, 'userid' => $userid]
        );

        $sectionid = $params['sectionid'];
        $grade = $params['grade'];
        $userid = $params['userid'];

        // Если userid не указан, используем текущего пользователя
        if ($userid === 0) {
            $userid = $USER->id;
        }

        // Получаем секцию и курс
        $section = $DB->get_record('course_sections', ['id' => $sectionid], '*', MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $section->course], '*', MUST_EXIST);

        // Проверяем контекст и права доступа
        $context = context_course::instance($course->id);
        self::validate_context($context);

        // Проверяем права на выставление оценок
        require_capability('mod/assign:grade', $context);

        $warnings = [];
        $success = false;
        $assignmentid = null;

        try {
            // Ищем первый assignment в секции через прямой запрос к БД
            $sql = "SELECT cm.id, cm.instance, cm.section 
                    FROM {course_modules} cm
                    JOIN {modules} m ON cm.module = m.id
                    WHERE cm.course = :courseid 
                      AND cm.section = :section
                      AND m.name = :modname
                      AND cm.deletioninprogress = 0
                    ORDER BY cm.id ASC
                    LIMIT 1";
            
            $params = [
                'courseid' => $course->id,
                'section' => $section->id,
                'modname' => self::mod_name
            ];
            
            $cmrecord = $DB->get_record_sql($sql, $params);
            
            if (!$cmrecord) {
                throw new moodle_exception('noassignmentfound', 'tool_cdo_config', '', $sectionid);
            }
            
            $assignmentid = $cmrecord->instance;
            
            // Получаем полную информацию о course module
            $cm = get_coursemodule_from_id('assign', $cmrecord->id, $course->id);
            if (!$cm) {
                throw new moodle_exception('noassignmentfound', 'tool_cdo_config', '', $sectionid);
            }

            // Получаем assignment
            $assignment = $DB->get_record('assign', ['id' => $assignmentid], '*', MUST_EXIST);
            
            // Проверяем контекст модуля
            $modulecontext = context_module::instance($cm->id);
            require_capability('mod/assign:grade', $modulecontext);

            // Валидируем оценку
            if ($grade < 0 || $grade > $assignment->grade) {
                throw new invalid_parameter_exception(
                    sprintf('Оценка должна быть между 0 и %s', $assignment->grade)
                );
            }

            // Получаем grade_item для assignment
            $gradeitem = \grade_item::fetch([
                'courseid' => $course->id,
                'itemtype' => 'mod',
                'itemmodule' => 'assign',
                'iteminstance' => $assignmentid
            ]);

            if (!$gradeitem) {
                throw new moodle_exception('gradeitemnotfound', 'tool_cdo_config');
            }

            // Проверяем, существует ли уже оценка
            $existinggrade = \grade_grade::fetch([
                'itemid' => $gradeitem->id,
                'userid' => $userid
            ]);

            if ($existinggrade) {
                $existinggrade->finalgrade = $grade;
                $existinggrade->rawgrade = $grade;
                $existinggrade->timemodified = time();
                $existinggrade->update();
            } else {
                $gradegrade = new \grade_grade();
                $gradegrade->itemid = $gradeitem->id;
                $gradegrade->userid = $userid;
                $gradegrade->finalgrade = $grade;
                $gradegrade->rawgrade = $grade;
                $gradegrade->timecreated = time();
                $gradegrade->timemodified = time();
                $gradegrade->insert();
            }

            // Пересчитываем итоговые оценки курса
            //\grade_regrade_final_grades($course->id, $userid);

            $success = true;
            
        } catch (moodle_exception $e) {
            $warnings[] = [
                'item' => 'assignment',
                'itemid' => $assignmentid ?? 0,
                'warningcode' => $e->errorcode ?? 'unknown',
                'message' => $e->getMessage()
            ];
        }

        return [
            'success' => $success,
            'assignmentid' => $assignmentid,
            'warnings' => $warnings
        ];
    }

    /**
     * Returns description of method return value
     * @return external_single_structure
     */
    public static function set_grade_to_first_assign_in_section_returns(): external_single_structure
    {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Успешность операции'),
            'assignmentid' => new external_value(PARAM_INT, 'ID найденного assignment', VALUE_OPTIONAL),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Returns description of method parameters for setting category and course grades
     * @return external_function_parameters
     */
    public static function set_category_and_course_grades_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'ID курса'),
            'gradetype' => new external_value(PARAM_TEXT, 'Тип оценки: category или course'),
            'categoryname' => new external_value(PARAM_TEXT, 'Название категории оценок', VALUE_DEFAULT, ''),
            'userid' => new external_value(PARAM_INT, 'ID пользователя'),
            'grade' => new external_value(PARAM_FLOAT, 'Оценка для выставления')
        ]);
    }

    /**
     * Устанавливает оценку в категорию оценок или итоговую оценку за курс
     *
     * @param int $courseid ID курса
     * @param string $gradetype Тип оценки: category или course
     * @param string $categoryname Название категории оценок
     * @param int $userid ID пользователя
     * @param float $grade Оценка для выставления
     * @return array Результат операции
     * @throws invalid_parameter_exception
     * @throws required_capability_exception
     * @throws moodle_exception
     */
    public static function set_category_and_course_grades($courseid, $gradetype, $categoryname, $userid, $grade): array
    {
        global $DB, $USER;

        // Валидация параметров
        $params = self::validate_parameters(
            self::set_category_and_course_grades_parameters(),
            [
                'courseid' => $courseid,
                'gradetype' => $gradetype,
                'categoryname' => $categoryname,
                'userid' => $userid,
                'grade' => $grade
            ]
        );

        $courseid = $params['courseid'];
        $gradetype = $params['gradetype'];
        $categoryname = $params['categoryname'];
        $userid = $params['userid'];
        $grade = $params['grade'];

        // Проверяем корректность типа оценки
        if (!in_array($gradetype, ['category', 'course'])) {
            throw new \invalid_parameter_exception('Тип оценки должен быть category или course');
        }

        // Получаем курс
        $course = $DB->get_record('course', ['id' => $courseid]);
        if (!$course) {
            throw new \invalid_parameter_exception('Курс не найден');
        }

        // Для типа 'category' проверяем наличие категории
        if ($gradetype === 'category') {
            if (empty($categoryname)) {
                throw new \invalid_parameter_exception('Для типа category необходимо указать название категории');
            }
            
            // Получаем категорию оценок по названию и курсу
            $category = $DB->get_record('grade_categories', [
                'fullname' => $categoryname,
                'courseid' => $courseid
            ]);
            if (!$category) {
                throw new \invalid_parameter_exception('Категория оценок не найдена');
            }
        }

        // Проверяем контекст и права доступа
        $context = context_course::instance($course->id);
        self::validate_context($context);

        // Проверяем права на выставление оценок
        require_capability('moodle/grade:edit', $context);

        $warnings = [];
        $success = false;
        $categorygradeitemid = null;
        $coursegradeitemid = null;

        try {
            $gradeitemid = null;
            $gradeitem = null;

            if ($gradetype === 'category') {
                global $CFG;
                require_once $CFG->libdir . '/gradelib.php';
                // Получаем grade_item для категории
                $gradeitem = \grade_item::fetch([
                    'courseid' => $course->id,
                    'itemtype' => 'category',
                    'iteminstance' => $category->id
                ]);

                if (!$gradeitem) {
                    throw new moodle_exception('categorygradeitemnotfound', 'tool_cdo_config');
                }

                $gradeitemid = $gradeitem->id;

                // Валидируем оценку категории
                if ($grade < 0 || ($gradeitem->grademax > 0 && $grade > $gradeitem->grademax)) {
                    throw new \invalid_parameter_exception(
                        sprintf('Оценка категории должна быть между 0 и %s', $gradeitem->grademax)
                    );
                }

            } elseif ($gradetype === 'course') {
                // Получаем итоговый grade_item для курса
                $gradeitem = \grade_item::fetch_course_item($course->id);
                if (!$gradeitem) {
                    throw new moodle_exception('coursegradeitemnotfound', 'tool_cdo_config');
                }

                $gradeitemid = $gradeitem->id;

                // Валидируем итоговую оценку за курс
                if ($grade < 0 || ($gradeitem->grademax > 0 && $grade > $gradeitem->grademax)) {
                    throw new \invalid_parameter_exception(
                        sprintf('Итоговая оценка должна быть между 0 и %s', $gradeitem->grademax)
                    );
                }
            }

            // Устанавливаем оценку
            $gradegrade = new \grade_grade();
            $gradegrade->itemid = $gradeitem->id;
            $gradegrade->userid = $userid;

            // Проверяем, существует ли уже оценка
            $existinggrade = \grade_grade::fetch([
                'itemid' => $gradeitem->id,
                'userid' => $userid
            ]);

            if ($existinggrade) {
                $existinggrade->finalgrade = $grade;
                $existinggrade->timemodified = time();
                $existinggrade->update();
            } else {
                $gradegrade->finalgrade = $grade;
                $gradegrade->timecreated = time();
                $gradegrade->timemodified = time();
                $gradegrade->insert();
            }

            // Пересчитываем итоговые оценки курса
            //   \grade_regrade_final_grades($course->id, $userid);

            $success = true;

        } catch (\Throwable $e) {
            $warnings[] = [
                'item' => 'grade',
                'itemid' => $gradeitemid ?? 0,
                'warningcode' => $e instanceof moodle_exception ? $e->errorcode : 'general_error',
                'message' => $e->getMessage()
            ];
        }

        return [
            'success' => $success,
            'gradeitemid' => $gradeitemid ?? null,
            'gradetype' => $gradetype,
            'warnings' => $warnings
        ];
    }

    /**
     * Returns description of method return value for setting category and course grades
     * @return external_single_structure
     */
    public static function set_category_and_course_grades_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Успешность операции'),
            'gradeitemid' => new external_value(PARAM_INT, 'ID grade_item', VALUE_OPTIONAL),
            'gradetype' => new external_value(PARAM_TEXT, 'Тип установленной оценки'),
            'warnings' => new external_warnings()
        ]);
    }
}
