<?php
/**
 * CLI скрипт для запуска форс-регрейдинга оценок по ID курса
 * 
 * Использование:
 * php admin/cli/regrade_course_grades.php --courseid=123
 * php admin/cli/regrade_course_grades.php --courseid=123 --verbose
 * php admin/cli/regrade_course_grades.php --courseid=123 --dry-run
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/clilib.php');

// Получаем параметры командной строки
list($options, $unrecognized) = cli_get_params([
    'courseid' => '',
    'verbose' => false,
    'dry-run' => false,
    'help' => false,
], [
    'c' => 'courseid',
    'v' => 'verbose',
    'd' => 'dry-run',
    'h' => 'help',
]);

if ($options['help'] || empty($options['courseid'])) {
    $help = "
CLI скрипт для запуска форс-регрейдинга оценок по ID курса

Опции:
--courseid, -c     ID курса (обязательный параметр)
--verbose, -v      Подробный вывод
--dry-run, -d      Тестовый режим (без реальных изменений)
--help, -h         Показать эту справку

Примеры использования:
php admin/cli/regrade_course_grades.php --courseid=123
php admin/cli/regrade_course_grades.php --courseid=123 --verbose
php admin/cli/regrade_course_grades.php --courseid=123 --dry-run
";

    cli_writeln($help);
    exit(0);
}

$course_id = (int)$options['courseid'];
$verbose = $options['verbose'];
$dry_run = $options['dry-run'];

cli_writeln("=== Запуск форс-регрейдинга оценок для курса ID: {$course_id} ===");
cli_writeln("Режим: " . ($dry_run ? "ТЕСТОВЫЙ (без изменений)" : "РАБОЧИЙ"));
cli_writeln("");

// Проверяем существование курса
$course = $DB->get_record('course', ['id' => $course_id]);
if (!$course) {
    cli_error("Ошибка: Курс с ID {$course_id} не найден");
}

cli_writeln("Курс: {$course->fullname} (ID: {$course->id})");
cli_writeln("");

try {
    // Получаем все элементы оценок для курса
    $grade_items = grade_item::fetch_all(['courseid' => $course_id]);
    
    if (!$grade_items) {
        cli_writeln("Предупреждение: Не найдены элементы оценок для курса ID {$course_id}");
        exit(0);
    }
    
    $total_items = count($grade_items);
    $processed_items = 0;
    $category_items = 0;
    $course_items = 0;
    $other_items = 0;
    $errors = [];
    
    cli_writeln("Найдено элементов оценок: {$total_items}");
    cli_writeln("");
    
    // Обрабатываем каждый элемент оценки
    foreach ($grade_items as $grade_item) {
        $item_type = $grade_item->itemtype;
        $item_name = $grade_item->itemname ?: "ID {$grade_item->id}";
        
        if ($verbose) {
            cli_writeln("Обработка элемента: {$item_name} (тип: {$item_type})");
        }
        
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
                        cli_writeln("  ✓ Применен force_regrading() для категории");
                    }
                } elseif ($grade_item->is_course_item()) {
                    $course_items++;
                    if ($verbose) {
                        cli_writeln("  ✓ Применен force_regrading() для итоговой оценки курса");
                    }
                }
                
            } catch (Exception $e) {
                $errors[] = "Элемент {$item_name} (ID: {$grade_item->id}): " . $e->getMessage();
                if ($verbose) {
                    cli_writeln("  ✗ Ошибка: " . $e->getMessage());
                }
            }
        } else {
            $other_items++;
            if ($verbose) {
                cli_writeln("  - Пропущен (не категория и не итоговая оценка)");
            }
        }
    }
    
    cli_writeln("");
    cli_writeln("=== Результаты обработки элементов оценок ===");
    cli_writeln("Всего элементов: {$total_items}");
    cli_writeln("Обработано: {$processed_items}");
    cli_writeln("  - Категории: {$category_items}");
    cli_writeln("  - Итоговые оценки курса: {$course_items}");
    cli_writeln("  - Пропущено: {$other_items}");
    cli_writeln("Ошибок: " . count($errors));
    
    // Если есть обработанные элементы и не тестовый режим, запускаем полный пересчет
    if ($processed_items > 0 && !$dry_run) {
        cli_writeln("");
        cli_writeln("=== Запуск полного пересчета оценок ===");
        
        try {
            $regrade_result = grade_regrade_final_grades($course_id);
            
            if ($regrade_result === true) {
                cli_writeln("✓ Пересчет оценок успешно завершен");
            } else {
                cli_writeln("✗ Ошибка при пересчете оценок: " . json_encode($regrade_result));
                $errors[] = "Ошибка при grade_regrade_final_grades(): " . json_encode($regrade_result);
            }
            
        } catch (Exception $e) {
            cli_writeln("✗ Ошибка при запуске пересчета оценок: " . $e->getMessage());
            $errors[] = "Ошибка при grade_regrade_final_grades(): " . $e->getMessage();
        }
    } elseif ($dry_run && $processed_items > 0) {
        cli_writeln("");
        cli_writeln("=== Тестовый режим: пересчет оценок НЕ запущен ===");
        cli_writeln("В рабочем режиме был бы запущен полный пересчет оценок");
    }
    
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
