<?php
/**
 * CLI скрипт для сброса статуса "переопределено" у оценок во всех курсах указанной категории и вложенных категорий
 * 
 * Использование:
 * php admin/cli/reset_overridden_grades.php --categoryid=123
 * php admin/cli/reset_overridden_grades.php --categoryid=123 --verbose
 * php admin/cli/reset_overridden_grades.php --categoryid=123 --dry-run
 * php admin/cli/reset_overridden_grades.php --categoryid=123 --itemtypes=course,category
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/grade_grade.php');
require_once($CFG->libdir . '/clilib.php');

// Получаем параметры командной строки
list($options, $unrecognized) = cli_get_params([
    'categoryid' => '',
    'itemtypes' => 'course,category',
    'verbose' => false,
    'dry-run' => false,
    'help' => false,
], [
    'c' => 'categoryid',
    't' => 'itemtypes',
    'v' => 'verbose',
    'd' => 'dry-run',
    'h' => 'help',
]);

if ($options['help'] || empty($options['categoryid'])) {
    $help = "
CLI скрипт для сброса статуса 'переопределено' у оценок во всех курсах указанной категории и вложенных категорий

Опции:
--categoryid, -c     ID категории курса (обязательный параметр)
--itemtypes, -t      Типы элементов оценок для обработки (по умолчанию: course,category)
--verbose, -v        Подробный вывод
--dry-run, -d        Тестовый режим (без реальных изменений)
--help, -h           Показать эту справку

Примеры использования:
php admin/cli/reset_overridden_grades.php --categoryid=123
php admin/cli/reset_overridden_grades.php --categoryid=123 --verbose
php admin/cli/reset_overridden_grades.php --categoryid=123 --dry-run
php admin/cli/reset_overridden_grades.php --categoryid=123 --itemtypes=course,category
";

    cli_writeln($help);
    exit(0);
}

$category_id = (int)$options['categoryid'];
$item_types = explode(',', $options['itemtypes']);
$verbose = $options['verbose'];
$dry_run = $options['dry-run'];

cli_writeln("=== Запуск сброса переопределенных оценок для категории ID: {$category_id} ===");
cli_writeln("Режим: " . ($dry_run ? "ТЕСТОВЫЙ (без изменений)" : "РАБОЧИЙ"));
cli_writeln("Типы элементов: " . implode(', ', $item_types));
cli_writeln("");

// Проверяем существование категории
$category = $DB->get_record('course_categories', ['id' => $category_id]);
if (!$category) {
    cli_error("Ошибка: Категория с ID {$category_id} не найдена");
}

cli_writeln("Категория: {$category->name} (ID: {$category->id})");
cli_writeln("");

try {
    // Получаем все курсы в категории и вложенных категориях
    $courses = get_courses_in_category_tree($category_id);
    
    if (empty($courses)) {
        cli_writeln("Предупреждение: Не найдены курсы в категории ID {$category_id} и вложенных категориях");
        exit(0);
    }
    
    $total_courses = count($courses);
    $processed_courses = 0;
    $successful_courses = 0;
    $total_grades_reset = 0;
    $errors = [];
    
    cli_writeln("Найдено курсов для обработки: {$total_courses}");
    cli_writeln("");
    
    // Обрабатываем каждый курс
    foreach ($courses as $course) {
        $processed_courses++;
        
        if ($verbose) {
            cli_writeln("Обработка курса: {$course->fullname} (ID: {$course->id})");
        }
        
        try {
            // Получаем все элементы оценок для курса с указанными типами
            list($in_sql, $params) = $DB->get_in_or_equal($item_types);
            $params['courseid'] = $course->id;
            
            $sql = "SELECT gi.* 
                    FROM {grade_items} gi
                    WHERE gi.courseid = :courseid 
                    AND gi.itemtype $in_sql
                    AND gi.hidden = 0";
            
            $grade_items = $DB->get_records_sql($sql, $params);
            
            if (!$grade_items) {
                if ($verbose) {
                    cli_writeln("  - Не найдены элементы оценок указанных типов для курса");
                }
                continue;
            }
            
            $total_items = count($grade_items);
            $processed_items = 0;
            $course_grades_reset = 0;
            $course_errors = [];
            
            if ($verbose) {
                cli_writeln("  Найдено элементов оценок: {$total_items}");
            }
            
            // Обрабатываем каждый элемент оценки
            foreach ($grade_items as $grade_item) {
                $item_type = $grade_item->itemtype;
                $item_name = $grade_item->itemname ?: "ID {$grade_item->id}";
                
                if ($verbose) {
                    cli_writeln("    Обработка элемента: {$item_name} (тип: {$item_type})");
                }
                
                // Получаем все переопределенные оценки для этого элемента
                $grade_grades = grade_grade::fetch_all([
                    'itemid' => $grade_item->id,
                    'overridden' => 1
                ]);
                
                if (!$grade_grades) {
                    if ($verbose) {
                        cli_writeln("      - Нет переопределенных оценок");
                    }
                    continue;
                }
                
                $total_grades = count($grade_grades);
                $item_grades_reset = 0;
                
                if ($verbose) {
                    cli_writeln("      Найдено переопределенных оценок: {$total_grades}");
                }
                
                // Сбрасываем статус переопределения для каждой оценки
                foreach ($grade_grades as $grade_grade) {
                    try {
                        if (!$dry_run) {
                            // Сбрасываем статус переопределения
                            $grade_grade->set_overridden(0);
                            $grade_grade->update();
                        }
                        
                        $item_grades_reset++;
                        $course_grades_reset++;
                        $total_grades_reset++;
                        
                        if ($verbose) {
                            $user = $DB->get_record('user', ['id' => $grade_grade->userid]);
                            $user_name = $user ? "{$user->firstname} {$user->lastname}" : "ID {$grade_grade->userid}";
                            cli_writeln("      ✓ Сброшен статус для пользователя: {$user_name}");
                        }
                        
                    } catch (Exception $e) {
                        $course_errors[] = "Оценка элемента {$item_name} (пользователь ID: {$grade_grade->userid}): " . $e->getMessage();
                        if ($verbose) {
                            cli_writeln("      ✗ Ошибка: " . $e->getMessage());
                        }
                    }
                }
                
                $processed_items++;
                
                if ($verbose) {
                    cli_writeln("      Результат: {$item_grades_reset} оценок сброшено");
                }
            }
            
            // Если есть обработанные элементы и не тестовый режим, запускаем пересчет оценок
            if ($processed_items > 0 && !$dry_run) {
                try {
                    $regrade_result = grade_regrade_final_grades($course->id);
                    
                    if ($regrade_result === true) {
                        $successful_courses++;
                        if ($verbose) {
                            cli_writeln("  ✓ Пересчет оценок успешно завершен для курса");
                        }
                    } else {
                        $course_errors[] = "Ошибка при пересчете оценок: " . json_encode($regrade_result);
                        if ($verbose) {
                            cli_writeln("  ✗ Ошибка при пересчете оценок");
                        }
                    }
                    
                } catch (Exception $e) {
                    $course_errors[] = "Ошибка при запуске пересчета оценок: " . $e->getMessage();
                    if ($verbose) {
                        cli_writeln("  ✗ Ошибка при запуске пересчета оценок: " . $e->getMessage());
                    }
                }
            } elseif ($dry_run && $processed_items > 0) {
                if ($verbose) {
                    cli_writeln("  - Тестовый режим: пересчет оценок НЕ запущен");
                }
            }
            
            // Добавляем ошибки курса в общий список
            if (!empty($course_errors)) {
                $errors[] = "Курс {$course->fullname} (ID: {$course->id}):";
                $errors = array_merge($errors, $course_errors);
            }
            
            if ($verbose) {
                cli_writeln("  Результаты: {$processed_items} элементов обработано, {$course_grades_reset} оценок сброшено");
                cli_writeln("");
            }
            
        } catch (Exception $e) {
            $errors[] = "Курс {$course->fullname} (ID: {$course->id}): " . $e->getMessage();
            if ($verbose) {
                cli_writeln("  ✗ Ошибка при обработке курса: " . $e->getMessage());
                cli_writeln("");
            }
        }
    }
    
    cli_writeln("");
    cli_writeln("=== Результаты обработки ===");
    cli_writeln("Всего курсов: {$total_courses}");
    cli_writeln("Обработано: {$processed_courses}");
    cli_writeln("Успешно: {$successful_courses}");
    cli_writeln("Всего оценок сброшено: {$total_grades_reset}");
    cli_writeln("Ошибок: " . count($errors));
    
    // Выводим ошибки, если они есть
    if (!empty($errors)) {
        cli_writeln("");
        cli_writeln("=== Детали ошибок ===");
        foreach ($errors as $error) {
            cli_writeln("✗ " . $error);
        }
    }
    
    cli_writeln("");
    cli_writeln("=== Операция завершена ===");
    
    // Возвращаем код выхода в зависимости от наличия ошибок
    exit(empty($errors) ? 0 : 1);
    
} catch (Exception $e) {
    cli_error("Критическая ошибка: " . $e->getMessage());
}

/**
 * Получает все курсы в указанной категории и вложенных категориях
 * 
 * @param int $category_id ID категории
 * @return array Массив объектов курсов
 */
function get_courses_in_category_tree($category_id) {
    global $DB;
    
    // Получаем все ID категорий в дереве (включая указанную категорию)
    $category_ids = get_category_tree_ids($category_id);
    
    if (empty($category_ids)) {
        return [];
    }
    
    list($in_sql, $params) = $DB->get_in_or_equal($category_ids);
    
    $sql = "SELECT c.id, c.fullname, c.category
            FROM {course} c
            WHERE c.category $in_sql
            AND c.id != 1  -- исключаем системный курс
            ORDER BY c.fullname";
    
    return $DB->get_records_sql($sql, $params);
}

/**
 * Получает все ID категорий в дереве (включая указанную категорию)
 * 
 * @param int $category_id ID корневой категории
 * @return array Массив ID категорий
 */
function get_category_tree_ids($category_id) {
    global $DB;
    
    $category_ids = [$category_id];
    
    // Получаем корневую категорию
    $root_category = $DB->get_record('course_categories', ['id' => $category_id]);
    if (!$root_category) {
        return $category_ids;
    }
    
    // Ищем все категории, которые находятся в пути корневой категории
    $sql = "SELECT id 
            FROM {course_categories} 
            WHERE path LIKE ? 
            ORDER BY depth";
    
    $params = ["%/{$category_id}/%"];
    $child_categories = $DB->get_records_sql($sql, $params);
    
    foreach ($child_categories as $child_category) {
        $category_ids[] = $child_category->id;
    }
    
    return array_unique($category_ids);
}
