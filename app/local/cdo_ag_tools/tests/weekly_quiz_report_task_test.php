<?php

namespace local_cdo_ag_tools;

use advanced_testcase;
use coding_exception;
use dml_exception;
use local_cdo_ag_tools\tasks\weekly_quiz_report_task;
use moodle_exception;

/**
 * Unit тесты для weekly_quiz_report_task
 *
 * @package local_cdo_ag_tools
 * @covers \local_cdo_ag_tools\tasks\weekly_quiz_report_task
 */
class weekly_quiz_report_task_test extends advanced_testcase
{
    /**
     * Тест выполнения задачи с оценками
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_execute_with_grades(): void
    {
        global $DB;
        
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        // Создаем тестовые данные
        $user = $this->getDataGenerator()->create_user(['email' => 'test@example.com']);
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        // Создаем grade item
        $gradeItem = new \stdClass();
        $gradeItem->courseid = $course->id;
        $gradeItem->itemtype = 'mod';
        $gradeItem->itemmodule = 'quiz';
        $gradeItem->iteminstance = $quiz->id;
        $gradeItem->itemname = 'Test Quiz';
        $gradeItem->grademax = 100;
        $gradeItem->grademin = 0;
        $gradeItemId = $DB->insert_record('grade_items', $gradeItem);

        // Создаем оценку
        $gradeGrade = new \stdClass();
        $gradeGrade->itemid = $gradeItemId;
        $gradeGrade->userid = $user->id;
        $gradeGrade->finalgrade = 85;
        $gradeGrade->timemodified = time();
        $DB->insert_record('grade_grades', $gradeGrade);

        // Выполняем задачу
        $task = new weekly_quiz_report_task();
        
        // Должна выполниться без ошибок
        ob_start();
        $task->execute();
        $output = ob_get_clean();

        // Проверяем, что задача выполнилась
        $this->assertStringContainsString('Начало отправки еженедельных отчетов', $output);
    }

    /**
     * Тест выполнения задачи без оценок
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_execute_without_grades(): void
    {
        $this->resetAfterTest(true);

        // Выполняем задачу
        $task = new weekly_quiz_report_task();

        ob_start();
        $task->execute();
        $output = ob_get_clean();

        // Проверяем, что задача выполнилась и не нашла оценок
        $this->assertStringContainsString('Нет пользователей с оценками', $output);
    }

    /**
     * Тест получения имени задачи
     *
     * @throws coding_exception
     */
    public function test_get_name(): void
    {
        $task = new weekly_quiz_report_task();
        $name = $task->get_name();

        $this->assertNotEmpty($name);
        $this->assertIsString($name);
    }

    /**
     * Тест выполнения задачи с несколькими пользователями
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_execute_with_multiple_users(): void
    {
        global $DB;
        
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        // Создаем тестовые данные
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        // Создаем grade item
        $gradeItem = new \stdClass();
        $gradeItem->courseid = $course->id;
        $gradeItem->itemtype = 'mod';
        $gradeItem->itemmodule = 'quiz';
        $gradeItem->iteminstance = $quiz->id;
        $gradeItem->itemname = 'Test Quiz';
        $gradeItem->grademax = 100;
        $gradeItem->grademin = 0;
        $gradeItemId = $DB->insert_record('grade_items', $gradeItem);

        // Создаем несколько пользователей с оценками
        for ($i = 0; $i < 3; $i++) {
            $user = $this->getDataGenerator()->create_user([
                'email' => "test{$i}@example.com",
            ]);

            $gradeGrade = new \stdClass();
            $gradeGrade->itemid = $gradeItemId;
            $gradeGrade->userid = $user->id;
            $gradeGrade->finalgrade = 80 + $i;
            $gradeGrade->timemodified = time();
            $DB->insert_record('grade_grades', $gradeGrade);
        }

        // Выполняем задачу
        $task = new weekly_quiz_report_task();

        ob_start();
        $task->execute();
        $output = ob_get_clean();

        // Проверяем, что задача нашла 3 пользователей
        $this->assertStringContainsString('Найдено пользователей с оценками: 3', $output);
        $this->assertStringContainsString('Успешно: 3', $output);
    }
}

