<?php
/**
 * CLI скрипт для пересчета оценок во всех курсах указанной категории и вложенных категорий
 * 
 * Использование:
 * php admin/cli/regrade_category_courses.php --categoryid=123
 * php admin/cli/regrade_category_courses.php --categoryid=123 --verbose
 * php admin/cli/regrade_category_courses.php --categoryid=123 --dry-run
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/weblib.php');

// Получаем параметры командной строки
list($options, $unrecognized) = cli_get_params([
    'categoryid' => '',
    'verbose' => false,
    'dry-run' => false,
    'help' => false,
], [
    'c' => 'categoryid',
    'v' => 'verbose',
    'd' => 'dry-run',
    'h' => 'help',
]);

if ($options['help'] || empty($options['categoryid'])) {
    $help = "
CLI скрипт для пересчета оценок во всех курсах указанной категории и вложенных категорий

Опции:
--categoryid, -c     ID категории курса (обязательный параметр)
--verbose, -v        Подробный вывод
--dry-run, -d        Тестовый режим (без реальных изменений)
--help, -h           Показать эту справку

Примеры использования:
php admin/cli/regrade_category_courses.php --categoryid=123
php admin/cli/regrade_category_courses.php --categoryid=123 --verbose
php admin/cli/regrade_category_courses.php --categoryid=123 --dry-run
";

    cli_writeln($help);
    exit(0);
}

$category_id = (int)$options['categoryid'];
$verbose = $options['verbose'];
$dry_run = $options['dry-run'];

cli_writeln(str_repeat("=", 80));
cli_writeln("  ЗАПУСК ПЕРЕСЧЕТА ОЦЕНОК ДЛЯ КАТЕГОРИИ ID: {$category_id}");
cli_writeln(str_repeat("=", 80));
cli_writeln("Режим: " . ($dry_run ? "ТЕСТОВЫЙ (без изменений)" : "РАБОЧИЙ"));
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
    $errors = [];
    
    cli_writeln("Найдено курсов для обработки: {$total_courses}");
    cli_writeln("");
    
    // Запоминаем время начала
    $start_time = time();
    cli_writeln("Время начала: " . date('Y-m-d H:i:s', $start_time));
    cli_writeln("");
    
    // Инициализируем прогресс-бар
    $progress_bar = new progress_bar('regrading', 500, true);
    $progress_bar->create();
    
    // Обрабатываем каждый курс
    foreach ($courses as $course) {
        $processed_courses++;
        
        // Обновляем прогресс-бар с расчетом оставшегося времени
        $progress_percent = ($processed_courses / $total_courses) * 100;
        $elapsed_time = time() - $start_time;
        $avg_time_per_course = $elapsed_time / $processed_courses;
        $remaining_courses = $total_courses - $processed_courses;
        $estimated_time_remaining = (int)($remaining_courses * $avg_time_per_course);
        
        $time_info = sprintf(
            "[%d%%] Прошло: %s | Осталось: ~%s",
            (int)$progress_percent,
            format_time_duration($elapsed_time),
            format_time_duration($estimated_time_remaining)
        );
        
        $progress_bar->update($processed_courses, $total_courses, 
            "Курс {$processed_courses}/{$total_courses}: {$course->fullname} | {$time_info}");
        
        if ($verbose) {
            $progress_bar_text = get_progress_bar_text($processed_courses, $total_courses);
            cli_writeln("\n{$progress_bar_text} Обработка курса: {$course->fullname} (ID: {$course->id})");
        }
        
        try {
            // Получаем все элементы оценок для курса
            $grade_items = grade_item::fetch_all(['courseid' => $course->id]);
            
            if (!$grade_items) {
                if ($verbose) {
                    cli_writeln("  - Не найдены элементы оценок для курса");
                }
                continue;
            }
            
            $total_items = count($grade_items);
            $processed_items = 0;
            $category_items = 0;
            $course_items = 0;
            $course_errors = [];
            
            if ($verbose) {
                cli_writeln("  Найдено элементов оценок: {$total_items}");
            }
            
            // Обрабатываем каждый элемент оценки
            foreach ($grade_items as $grade_item) {
                $item_type = $grade_item->itemtype;
                $item_name = $grade_item->itemname ?: "ID {$grade_item->id}";
                
                // Применяем force_regrading() только для категорий и итоговых оценок
                if ($grade_item->is_category_item() || $grade_item->is_course_item()) {
                    try {
                        if (!$dry_run) {
                            $grade_item->force_regrading();
                        }
                        
                        $processed_items++;
                        
                        if ($grade_item->is_category_item()) {
                            $category_items++;
                            if ($verbose) {
                                cli_writeln("    ✓ Применен force_regrading() для категории: {$item_name}");
                            }
                        } elseif ($grade_item->is_course_item()) {
                            $course_items++;
                            if ($verbose) {
                                cli_writeln("    ✓ Применен force_regrading() для итоговой оценки курса");
                            }
                        }
                        
                    } catch (Exception $e) {
                        $course_errors[] = "Элемент {$item_name} (ID: {$grade_item->id}): " . $e->getMessage();
                        if ($verbose) {
                            cli_writeln("    ✗ Ошибка: " . $e->getMessage());
                        }
                    }
                } else {
                    if ($verbose) {
                        cli_writeln("    - Пропущен (не категория и не итоговая оценка): {$item_name}");
                    }
                }
            }
            
            // Если есть обработанные элементы и не тестовый режим, запускаем полный пересчет
            if ($processed_items > 0 && !$dry_run) {
                try {
                    $regrade_result = grade_regrade_final_grades($course->id);
                    
                    if ($regrade_result === true) {
                        $successful_courses++;
                        if ($verbose) {
                            cli_writeln("  ✓ Пересчет оценок успешно завершен для курса");
                        }
                        // Обновляем прогресс-бар с информацией об успехе
                        $progress_bar->update($processed_courses, $total_courses, 
                            "Курс {$processed_courses}/{$total_courses}: ✓ {$course->fullname} (Успешно: {$successful_courses})");
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
                cli_writeln("  Результаты: {$processed_items} элементов обработано ({$category_items} категорий, {$course_items} итоговых оценок)");
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
    
    // Завершаем прогресс-бар
    $progress_bar->update_full(100, "Обработка завершена");
    
    // Рассчитываем общее время выполнения
    $end_time = time();
    $total_time = $end_time - $start_time;
    
    cli_writeln("");
    cli_writeln("");
    cli_writeln(str_repeat("=", 80));
    cli_writeln("  РЕЗУЛЬТАТЫ ОБРАБОТКИ");
    cli_writeln(str_repeat("=", 80));
    cli_writeln("");
    cli_writeln("Статистика курсов:");
    cli_writeln("  • Всего курсов: {$total_courses}");
    cli_writeln("  • Обработано: {$processed_courses}");
    cli_writeln("  • Успешно: {$successful_courses}");
    cli_writeln("  • Ошибок: " . count($errors));
    
    if ($total_courses > 0) {
        $success_rate = ($successful_courses / $total_courses) * 100;
        cli_writeln(sprintf("  • Процент успеха: %.1f%%", $success_rate));
    }
    
    cli_writeln("");
    cli_writeln("Временные показатели:");
    cli_writeln("  • Время начала: " . date('Y-m-d H:i:s', $start_time));
    cli_writeln("  • Время окончания: " . date('Y-m-d H:i:s', $end_time));
    cli_writeln("  • Общее время выполнения: " . format_time_duration($total_time));
    
    if ($total_courses > 0) {
        $avg_time = $total_time / $total_courses;
        cli_writeln("  • Среднее время на курс: " . format_time_duration($avg_time));
    }
    
    // Выводим ошибки, если они есть
    if (!empty($errors)) {
        cli_writeln("");
        cli_writeln(str_repeat("-", 80));
        cli_writeln("  ДЕТАЛИ ОШИБОК");
        cli_writeln(str_repeat("-", 80));
        foreach ($errors as $error) {
            cli_writeln("  ✗ " . $error);
        }
    }
    
    cli_writeln("");
    cli_writeln(str_repeat("=", 80));
    $status = empty($errors) ? "УСПЕШНО ЗАВЕРШЕНА" : "ЗАВЕРШЕНА С ОШИБКАМИ";
    cli_writeln("  ОПЕРАЦИЯ {$status}");
    cli_writeln(str_repeat("=", 80));
    
    // Возвращаем код выхода в зависимости от наличия ошибок
    exit(empty($errors) ? 0 : 1);
    
} catch (Exception $e) {
    cli_error("Критическая ошибка: " . $e->getMessage());
}

/**
 * Форматирует продолжительность времени в удобочитаемый вид
 * 
 * @param int|float $seconds Количество секунд
 * @return string Отформатированная строка времени
 */
function format_time_duration($seconds) {
    $seconds = (int)$seconds;
    
    if ($seconds < 60) {
        return "{$seconds} сек";
    }
    
    $minutes = floor($seconds / 60);
    $remaining_seconds = $seconds % 60;
    
    if ($minutes < 60) {
        return sprintf("%d мин %d сек", $minutes, $remaining_seconds);
    }
    
    $hours = floor($minutes / 60);
    $remaining_minutes = $minutes % 60;
    
    return sprintf("%d ч %d мин", $hours, $remaining_minutes);
}

/**
 * Создает текстовый индикатор прогресса
 * 
 * @param int $current Текущее значение
 * @param int $total Общее количество
 * @param int $width Ширина прогресс-бара (по умолчанию 30)
 * @return string Отформатированный прогресс-бар
 */
function get_progress_bar_text($current, $total, $width = 30) {
    $percent = ($current / $total) * 100;
    $filled = (int)(($current / $total) * $width);
    $empty = $width - $filled;
    
    $bar = '[' . str_repeat('█', $filled) . str_repeat('░', $empty) . ']';
    $text = sprintf("%s %3d%% (%d/%d)", $bar, (int)$percent, $current, $total);
    
    return $text;
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
