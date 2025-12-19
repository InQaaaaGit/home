<?php

require_once(__DIR__ . "/../../config.php");
require_login();

// Проверяем права доступа
require_capability('moodle/grade:edit', context_system::instance());

echo "<h1>Тест функции set_category_and_course_grades</h1>";

// Пример вызова функции
try {
    // Получаем первый курс для теста
    $courses = $DB->get_records('course', [], 'id ASC', 'id,fullname', 0, 1);
    if (!$courses) {
        echo "Нет доступных курсов для теста";
        exit;
    }
    
    $course = reset($courses);
    echo "<p>Тестируем на курсе: {$course->fullname} (ID: {$course->id})</p>";
    
    // Получаем первую категорию оценок для теста
    $categories = $DB->get_records('grade_categories', ['courseid' => $course->id], 'id ASC', 'id,fullname', 0, 1);
    if (!$categories) {
        echo "Нет категорий оценок в курсе";
        exit;
    }
    
    $category = reset($categories);
    echo "<p>Категория оценок: {$category->fullname} (ID: {$category->id})</p>";
    
    // Получаем текущего пользователя
    $userid = $USER->id;
    echo "<p>Пользователь: {$USER->firstname} {$USER->lastname} (ID: {$userid})</p>";
    
    // Тестируем функцию
    echo "<h2>Тест установки оценки для категории:</h2>";
    
    $result = \local_cdo_ag_tools\external\grades::set_category_and_course_grades(
        $course->id,
        'category',
        $category->fullname,
        $userid,
        85.5
    );
    
    echo "<pre>" . print_r($result, true) . "</pre>";
    
    echo "<h2>Тест установки итоговой оценки за курс:</h2>";
    
    $result = \local_cdo_ag_tools\external\grades::set_category_and_course_grades(
        $course->id,
        'course',
        $category->fullname,
        $userid,
        90.0
    );
    
    echo "<pre>" . print_r($result, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Ошибка: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<p><strong>Тест завершен</strong></p>";
