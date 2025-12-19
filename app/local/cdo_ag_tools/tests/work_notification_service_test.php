<?php

namespace local_cdo_ag_tools;

use advanced_testcase;
use coding_exception;
use dml_exception;
use local_cdo_ag_tools\services\work_notification_service;
use moodle_exception;

/**
 * Unit тесты для work_notification_service
 *
 * @package local_cdo_ag_tools
 * @covers \local_cdo_ag_tools\services\work_notification_service
 */
class work_notification_service_test extends advanced_testcase
{
    /**
     * Тест отправки уведомления о загрузке работы
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_send_work_uploaded_notification(): void
    {
        global $DB;
        
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        // Создаем тестовые данные
        $user = $this->getDataGenerator()->create_user(['email' => 'test@example.com']);
        $course = $this->getDataGenerator()->create_course();
        $assignment = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Отправляем уведомление
        $result = work_notification_service::send_work_uploaded_notification(
            $user->id,
            $course->id,
            $assignment->name
        );

        // Проверяем результат
        $this->assertTrue($result);
    }

    /**
     * Тест отправки уведомления о проверке работы
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_send_work_graded_notification(): void
    {
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        // Создаем тестовые данные
        $user = $this->getDataGenerator()->create_user(['email' => 'test@example.com']);
        $course = $this->getDataGenerator()->create_course();
        $assignment = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Отправляем уведомление
        $result = work_notification_service::send_work_graded_notification(
            $user->id,
            $course->id,
            $assignment->name,
            85.5
        );

        // Проверяем результат
        $this->assertTrue($result);
    }

    /**
     * Тест логирования загрузки работы
     *
     * @throws dml_exception
     */
    public function test_log_work_upload(): void
    {
        global $DB;
        
        $this->resetAfterTest(true);

        // Создаем тестовые данные
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $assignment = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Создаем фиктивный submission ID
        $submissionId = 123;

        // Логируем загрузку работы
        $recordId = work_notification_service::log_work_upload(
            $user->id,
            $course->id,
            $assignment->id,
            $submissionId
        );

        // Проверяем, что запись создана
        $this->assertGreaterThan(0, $recordId);

        // Проверяем данные в БД
        $record = $DB->get_record('local_cdo_ag_work_notif', ['id' => $recordId]);
        $this->assertNotFalse($record);
        $this->assertEquals($user->id, $record->userid);
        $this->assertEquals($course->id, $record->courseid);
        $this->assertEquals($assignment->id, $record->assignmentid);
        $this->assertEquals($submissionId, $record->submissionid);
        $this->assertEquals(1, $record->upload_notified);
        $this->assertEquals(0, $record->grade_notified);
    }

    /**
     * Тест отметки о проверке работы
     *
     * @throws dml_exception
     */
    public function test_mark_work_as_graded_notified(): void
    {
        global $DB;
        
        $this->resetAfterTest(true);

        // Создаем тестовые данные
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $assignment = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $submissionId = 123;

        // Создаем запись
        $recordId = work_notification_service::log_work_upload(
            $user->id,
            $course->id,
            $assignment->id,
            $submissionId
        );

        // Отмечаем, что уведомление о проверке отправлено
        $result = work_notification_service::mark_work_as_graded_notified(
            $user->id,
            $submissionId
        );

        // Проверяем результат
        $this->assertTrue($result);

        // Проверяем данные в БД
        $record = $DB->get_record('local_cdo_ag_work_notif', ['id' => $recordId]);
        $this->assertEquals(1, $record->grade_notified);
    }

    /**
     * Тест проверки, было ли отправлено уведомление о проверке
     *
     * @throws dml_exception
     */
    public function test_is_graded_notification_sent(): void
    {
        $this->resetAfterTest(true);

        // Создаем тестовые данные
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $assignment = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $submissionId = 123;

        // Создаем запись
        work_notification_service::log_work_upload(
            $user->id,
            $course->id,
            $assignment->id,
            $submissionId
        );

        // Проверяем, что уведомление еще не отправлено
        $result = work_notification_service::is_graded_notification_sent(
            $user->id,
            $submissionId
        );
        $this->assertFalse($result);

        // Отмечаем, что уведомление отправлено
        work_notification_service::mark_work_as_graded_notified(
            $user->id,
            $submissionId
        );

        // Проверяем, что теперь уведомление отправлено
        $result = work_notification_service::is_graded_notification_sent(
            $user->id,
            $submissionId
        );
        $this->assertTrue($result);
    }

    /**
     * Тест еженедельного отчета с оценками
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_send_weekly_quiz_report_with_grades(): void
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

        // Отправляем отчет
        $result = work_notification_service::send_weekly_quiz_report(
            $user->id,
            date('Y-m-d', strtotime('-7 days')),
            date('Y-m-d')
        );

        // Проверяем результат
        $this->assertTrue($result);
    }

    /**
     * Тест еженедельного отчета без оценок
     *
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_send_weekly_quiz_report_without_grades(): void
    {
        $this->resetAfterTest(true);
        $this->preventResetByRollback();

        // Создаем пользователя без оценок
        $user = $this->getDataGenerator()->create_user(['email' => 'test@example.com']);

        // Отправляем отчет
        $result = work_notification_service::send_weekly_quiz_report(
            $user->id,
            date('Y-m-d', strtotime('-7 days')),
            date('Y-m-d')
        );

        // Проверяем, что уведомление не отправлено (нет оценок)
        $this->assertFalse($result);
    }

    /**
     * Тест с несуществующим пользователем
     */
    public function test_send_notification_with_invalid_user(): void
    {
        $this->resetAfterTest(true);

        // Ожидаем исключение при попытке отправить уведомление несуществующему пользователю
        $this->expectException(moodle_exception::class);

        work_notification_service::send_work_uploaded_notification(
            99999, // Несуществующий ID
            1,
            'Test Assignment'
        );
    }
}

