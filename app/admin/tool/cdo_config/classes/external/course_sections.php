<?php

namespace tool_cdo_config\external;

use availability_date\condition;
use context_course;
use core_external;
use external_multiple_structure;
use dml_exception;
use Exception;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use format_base;
use format_flexsections;
use invalid_parameter_exception;
use moodle_exception;
use stdClass;
use tool_cdo_config\exceptions\cdo_config_exception;
use tool_cdo_config\tools\dumper;

require_once($CFG->libdir . '/externallib.php');

class course_sections extends external_api
{
    const FORMAT_TOPICS_COMPONENT = 'format_topics';
    const ITEM_TYPE = 'sectionname';

    public static function create_course_section_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'course_id' => new external_value(PARAM_INT, 'course id needed', VALUE_REQUIRED),
                'topic_name' => new external_value(PARAM_RAW, 'topic_name', VALUE_REQUIRED),
                'description' => new external_value(PARAM_RAW, 'description', VALUE_DEFAULT, ""),
                'parent' => new external_value(PARAM_INT, 'parent', VALUE_DEFAULT, 0),
            ]
        );
    }

    /**
     * @throws moodle_exception
     * @throws invalid_parameter_exception
     */
    public static function create_course_section($course_id, $topic_name, $description = "", $parent = 0): stdClass
    {
        global $CFG, $DB;
        $params = self::validate_parameters(self::create_course_section_parameters(),
            [
                'course_id' => $course_id,
                'topic_name' => $topic_name,
                'description' => $description,
                'parent' => $parent
            ]
        );
        if (!empty($params['parent'])) {
            $course_section = course_create_section($params['course_id']);
            $record_format = $DB->get_record('course_format_options', ['sectionid' => $course_section->id, 'name' => 'parent']);
            $DB->update_record('course_format_options',
                (object)[
                    'id' => $record_format->id,
                    'value' => $params['parent']
                ]
            );
        }
        //needle functions
        require_once($CFG->dirroot . "/course/lib.php");
        $course_section = course_create_section($params['course_id']);
        $data['summary'] = $params['description'];
        $data['name'] = $params['topic_name'];
        course_update_section($params['course_id'], $course_section, $data);

        return $course_section;
    }

    public static function create_course_section_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'id section', VALUE_REQUIRED),
                'course' => new external_value(PARAM_TEXT, 'course', VALUE_REQUIRED),
                'section' => new external_value(PARAM_TEXT, 'section', VALUE_REQUIRED),
            ]
        );
    }

    public static function get_course_section_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'section' => new external_value(PARAM_INT, 'section id needed', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_course_section($section_id)
    {
        global $DB;

        $params = self::validate_parameters(self::get_course_section_parameters(),
            [
                'section' => $section_id
            ]
        );

        $section = $DB->get_record("course_sections",
            [
                'id' => $params['section']
            ]
        );
        #dumper::dd($section);
        return $section;
    }

    public static function get_course_section_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'section id needed', VALUE_REQUIRED),
                'section' => new external_value(PARAM_INT, 'section id needed', VALUE_REQUIRED),
                'course' => new external_value(PARAM_INT, 'section id needed', VALUE_REQUIRED),
                'name' => new external_value(PARAM_RAW, 'section id needed', VALUE_REQUIRED),
                'summary' => new external_value(PARAM_RAW, 'section id needed', VALUE_REQUIRED),
                'timemodified' => new external_value(PARAM_INT, 'section id needed', VALUE_REQUIRED),
            ]
        );

    }

    public static function update_section_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'section' => new external_value(PARAM_INT, 'section id needed', VALUE_REQUIRED),
                'topic_name' => new external_value(PARAM_RAW, 'topic_name', VALUE_DEFAULT, ""),
                'description' => new external_value(PARAM_RAW, 'description', VALUE_DEFAULT, ""),
                'availability' => new external_value(PARAM_INT, 'availability', VALUE_DEFAULT, 0),
                'section_number' => new external_value(PARAM_INT, 'section_number', VALUE_DEFAULT, 0)
            ]
        );
    }

    /**
     * @throws moodle_exception
     * @throws invalid_parameter_exception
     * @throws Exception
     */
    public static function update_section($section_id, $topic_name = "", $description = "", $availability = 0, $section_number = 0)
    {
        global $CFG;
        $params = [];
        /*$params = self::validate_parameters(self::update_section_parameters(),
            [
                'section' => $section_id,
                'topic_name' => $topic_name,
                'description' => $description,
                'availability' => $availability
            ]
        );*/

        $section = self::get_course_section($section_id);
        if (empty($section)) {
            throw new cdo_config_exception(3008, "section=" . $section_id);
        }

        require_once($CFG->dirroot . "/course/lib.php");

        $params['summary'] = $description;

        if (!empty($availability)) {
            $availability_time = json_encode(
                [
                    'op' => '&',
                    'c' => [condition::get_json(condition::DIRECTION_FROM, $availability)],
                    'showc' => [true]
                ],
                256
            );
            $params['availability'] = $availability_time;
        }
        if (!empty($topic_name)) {
            $params['name'] = $topic_name;
        }
        if (!empty($section_number)) {
            $params['section'] = $section_number;
        }

        course_update_section(
            $section->course,
            $section,
            $params
        );

        return self::get_course_section($section_id);
    }

    public static function update_section_returns(): external_single_structure
    {
        return self::get_course_section_returns();
    }

    public static function flexible_section_create_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'course_id' => new external_value(PARAM_INT, 'course id needed', VALUE_REQUIRED),
                'topic_name' => new external_value(PARAM_RAW, 'topic_name', VALUE_REQUIRED),
                'parent' => new external_value(PARAM_INT, 'description', VALUE_DEFAULT, 0),
                'description' => new external_value(PARAM_RAW, 'description', VALUE_DEFAULT, ""),
                'availability_time' => new external_value(PARAM_INT, 'description', VALUE_DEFAULT, time()),
            ]
        );
    }

    /**
     * @throws moodle_exception
     * @throws Exception
     */
    public static function flexible_section_create($course_id, $topic, $parent = 0, $description = '', $availability_time = 0): array
    {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");

        $instance = course_get_format($course_id);
        if ($instance->get_format() === 'flexsections') {
            if (!empty($parent)) {
                $record_parent = $DB->get_record('course_sections', ['id' => $parent], '*', MUST_EXIST)->section;
            } else {
                $record_parent = 0;
            }

            $section = $instance->create_new_section($record_parent); // section number in course
            $data['summary'] = $description;
            $data['name'] = $topic;
            if ($availability_time !== 0)
                $data['availability'] = json_encode(
                    [
                        'op' => '&',
                        'c' => [condition::get_json(condition::DIRECTION_FROM, $availability_time)],
                        'showc' => [true]
                    ],
                    256
                );
            $course_section = $instance->get_section($section);
            course_update_section($course_id, $course_section, $data);

            return [
                'id' => $course_section->id,
                'course' => $course_id,
                'section' => $course_section->id,
                'number' => $section,
            ];

        }

        return [];
    }

    public static function flexible_section_create_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'id section', VALUE_REQUIRED),
                'course' => new external_value(PARAM_TEXT, 'course', VALUE_REQUIRED),
                'section' => new external_value(PARAM_TEXT, 'section', VALUE_REQUIRED),
                'number' => new external_value(PARAM_TEXT, 'section', VALUE_REQUIRED),
            ]
        );
    }

    public static function get_modules_in_section_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'section_id' => new external_value(PARAM_INT, 'section id needed', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * @throws dml_exception
     */
    public static function get_modules_in_section($section_id): array
    {
        global $DB;
        #$records = $DB->get_records('course_modules', ['section' => $section_id]);
        $records = $DB->get_records_sql(
            "SELECT cm.* FROM {course_modules} cm 
             INNER JOIN {modules} m ON (m.id = cm.module AND m.name IN ('assign', 'quiz'))  
             WHERE cm.section = ?",
            [$section_id]
        );
        $result = [];
        foreach ($records as $record) {
            $result[] = $record->id;
        }
        return $result;
    }

    public static function get_modules_in_section_returns(): external_multiple_structure
    {
        return new external_multiple_structure(
            new external_value(PARAM_INT, 'module id', VALUE_REQUIRED),
            '',
            VALUE_OPTIONAL
        );
    }

    public static function flexible_section_delete_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'section_id' => new external_value(PARAM_INT, 'section id needed', VALUE_REQUIRED),
                'delete_modules' => new external_value(PARAM_BOOL, 'delete modules inside section', VALUE_DEFAULT, true),
                'force_delete' => new external_value(PARAM_BOOL, 'force delete even if section contains modules', VALUE_DEFAULT, true),
            ]
        );
    }

    /**
     * @throws moodle_exception
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public static function flexible_section_delete($section_id, $delete_modules = true, $force_delete = true): array
    {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");

        $params = self::validate_parameters(self::flexible_section_delete_parameters(),
            [
                'section_id' => $section_id,
                'delete_modules' => $delete_modules,
                'force_delete' => $force_delete
            ]
        );

        // Получаем информацию о секции
        $section = $DB->get_record('course_sections', ['id' => $params['section_id']], '*', MUST_EXIST);
        
        // Получаем информацию о курсе
        $course = $DB->get_record('course', ['id' => $section->course], '*', MUST_EXIST);
        
        // Проверяем что формат курса - flexsections
        if ($course->format !== 'flexsections') {
            throw new cdo_config_exception(3009, "Course format is not flexsections");
        }

        // Получаем экземпляр формата курса
        $courseformat = course_get_format($course);
        
        // Получаем объект section_info вместо stdClass
        $modinfo = get_fast_modinfo($course->id);
        $section_info = $modinfo->get_section_info_by_id($section->id);
        
        // Проверяем что секция может быть удалена
        if (!$courseformat->can_delete_section($section_info)) {
            throw new cdo_config_exception(3010, "Section cannot be deleted");
        }

        // Проверяем права доступа
        $context = context_course::instance($course->id);
        //require_capability('moodle/course:update', $context);

        // Если не нужно удалять модули, перемещаем их в другую секцию
        if (!$params['delete_modules']) {
            // Получаем все модули в секции
            $modules = $DB->get_records('course_modules', ['section' => $section_info->id]);
            
            if (!empty($modules)) {
                // Находим главную секцию курса в формате flexsections
                $main_section = $DB->get_record_sql(
                    "SELECT cs.* 
                     FROM {course_sections} cs
                     LEFT JOIN {course_format_options} cfo ON (cfo.sectionid = cs.id AND cfo.name = 'parent')
                     WHERE cs.course = ? AND (cfo.value IS NULL OR cfo.value = 0)
                     ORDER BY cs.section ASC
                     LIMIT 1",
                    [$course->id]
                );
                
                if (!$main_section) {
                    // Если главная секция не найдена, используем секцию с наименьшим номером
                    $main_section = $DB->get_record('course_sections', 
                        ['course' => $course->id], '*', MUST_EXIST, 'section ASC');
                }
                
                // Перемещаем все модули в главную секцию
                foreach ($modules as $module) {
                    $module->section = $main_section->id;
                    $DB->update_record('course_modules', $module);
                }
                
                // Обновляем кэш модулей
                rebuild_course_cache($course->id, false);
            }
        }

        // Удаляем секцию и все дочерние элементы
        [$deleted_sections, $deleted_modules] = $courseformat->delete_section_with_children($section_info);

        return [
            'deleted_sections' => $deleted_sections,
            'deleted_modules' => $params['delete_modules'] ? $deleted_modules : [],
            'success' => true
        ];
    }

    public static function flexible_section_delete_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'deleted_sections' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'deleted section id', VALUE_REQUIRED),
                    'List of deleted section IDs',
                    VALUE_REQUIRED
                ),
                'deleted_modules' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'deleted module id', VALUE_REQUIRED),
                    'List of deleted module IDs',
                    VALUE_REQUIRED
                ),
                'success' => new external_value(PARAM_BOOL, 'operation success status', VALUE_REQUIRED),
            ]
        );
    }

    public static function section_delete_parameters(): external_function_parameters
    {
        return new external_function_parameters(
            [
                'section_id' => new external_value(PARAM_INT, 'section id needed', VALUE_REQUIRED),
                'delete_modules' => new external_value(PARAM_BOOL, 'delete modules inside section', VALUE_DEFAULT, true),
            ]
        );
    }

    /**
     * Удаляет обычную секцию из курса с форматом sections (topics/weekly)
     *
     * @param int $section_id ID секции для удаления
     * @param bool $delete_modules Удалять ли модули внутри секции (true) или перемещать в первую секцию (false)
     * @return array Результат удаления
     * @throws moodle_exception
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public static function section_delete($section_id, $delete_modules = true): array
    {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");

        $params = self::validate_parameters(self::section_delete_parameters(),
            [
                'section_id' => $section_id,
                'delete_modules' => $delete_modules
            ]
        );

        // Получаем информацию о секции
        $section = $DB->get_record('course_sections', ['id' => $params['section_id']], '*', MUST_EXIST);
        
        if (empty($section)) {
            throw new cdo_config_exception(3008, "section=" . $params['section_id']);
        }

        // Получаем информацию о курсе
        $course = $DB->get_record('course', ['id' => $section->course], '*', MUST_EXIST);
        
        // Проверяем что формат курса - sections (topics или weekly)
        if ($course->format !== 'topics' && $course->format !== 'weekly' && $course->format !== 'sections') {
            throw new cdo_config_exception(3011, "Course format is not sections/topics/weekly");
        }

        // Проверяем права доступа
        $context = context_course::instance($course->id);
        require_capability('moodle/course:update', $context);

        // Получаем объект section_info
        $modinfo = get_fast_modinfo($course->id);
        $section_info = $modinfo->get_section_info_by_id($section->id);

        // Проверяем, что это не нулевая секция (general section)
        if ($section_info->section == 0) {
            throw new cdo_config_exception(3012, "Cannot delete general section (section 0)");
        }

        $deleted_modules = [];

        // Обрабатываем модули в секции
        $modules = $DB->get_records('course_modules', ['section' => $section->id]);
        
        if (!empty($modules)) {
            if ($params['delete_modules']) {
                // Удаляем все модули
                foreach ($modules as $module) {
                    $deleted_modules[] = $module->id;
                    course_delete_module($module->id);
                }
            } else {
                // Перемещаем модули в первую секцию (general section)
                $general_section = $DB->get_record('course_sections', 
                    ['course' => $course->id, 'section' => 0], '*', MUST_EXIST);
                
                foreach ($modules as $module) {
                    $module->section = $general_section->id;
                    $DB->update_record('course_modules', $module);
                }
                
                // Обновляем кэш модулей
                rebuild_course_cache($course->id, false);
            }
        }

        // Удаляем секцию
        // В Moodle функция course_delete_section принимает только курс и section_info
        // Модули уже обработаны выше
        course_delete_section($course, $section_info);

        return [
            'deleted_section_id' => $section->id,
            'deleted_modules' => $deleted_modules,
            'success' => true
        ];
    }

    public static function section_delete_returns(): external_single_structure
    {
        return new external_single_structure(
            [
                'deleted_section_id' => new external_value(PARAM_INT, 'deleted section id', VALUE_REQUIRED),
                'deleted_modules' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'deleted module id', VALUE_REQUIRED),
                    'List of deleted module IDs',
                    VALUE_REQUIRED
                ),
                'success' => new external_value(PARAM_BOOL, 'operation success status', VALUE_REQUIRED),
            ]
        );
    }

}
