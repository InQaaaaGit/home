<?php
/**
 * PHPUnit тесты для класса grade_digest
 *
 * @package    local_cdo_ag_tools
 * @category   test
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_cdo_ag_tools\digest;

use advanced_testcase;
use local_cdo_ag_tools\controllers\grade_notification_controller;

/**
 * Тесты для класса grade_digest
 *
 * @covers \local_cdo_ag_tools\digest\grade_digest
 */
class grade_digest_test extends advanced_testcase {

    /**
     * Создает тестовые данные
     *
     * @return object Объект с тестовыми данными
     */
    private function setup_test_data(): object {
        $this->resetAfterTest(true);

        // Создаем пользователя
        $user = $this->getDataGenerator()->create_user([
            'username' => 'testuser',
            'firstname' => 'Test',
            'lastname' => 'User',
            'alternatename' => 'Test Alt Name',
        ]);

        // Создаем курсы
        $course1 = $this->getDataGenerator()->create_course(['fullname' => 'Test Course 1']);
        $course2 = $this->getDataGenerator()->create_course(['fullname' => 'Test Course 2']);

        // Создаем уведомления об оценках
        $now = time();
        $grade1id = grade_notification_controller::create_notification(
            $user->id,
            $course1->id,
            85.5,
            'Test Assignment 1',
            'assign'
        );

        $grade2id = grade_notification_controller::create_notification(
            $user->id,
            $course1->id,
            90.0,
            'Test Quiz 1',
            'quiz'
        );

        $grade3id = grade_notification_controller::create_notification(
            $user->id,
            $course2->id,
            75.0,
            'Test Assignment 2',
            'assign'
        );

        // Создаем оценку с датой в прошлом (60 дней назад)
        $grade4id = grade_notification_controller::create_notification(
            $user->id,
            $course2->id,
            60.0,
            'Old Assignment',
            'assign'
        );

        global $DB;
        $DB->set_field('local_cdo_ag_grade_notifications', 'timecreated', $now - (60 * 24 * 60 * 60), ['id' => $grade4id]);

        return (object)[
            'user' => $user,
            'course1' => $course1,
            'course2' => $course2,
            'grade1id' => $grade1id,
            'grade2id' => $grade2id,
            'grade3id' => $grade3id,
            'grade4id' => $grade4id,
        ];
    }

    /**
     * Тест создания экземпляра класса
     */
    public function test_create_instance(): void {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $digest = new grade_digest($user->id);

        $this->assertInstanceOf(grade_digest::class, $digest);
    }

    /**
     * Тест генерации HTML дайджеста для пользователя без оценок
     */
    public function test_generate_html_digest_no_grades(): void {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $digest = new grade_digest($user->id);

        $html = $digest->generate_html_digest();

        $this->assertStringContainsString('grade-digest-container', $html);
        $this->assertStringContainsString(get_string('no_grades_found', 'local_cdo_ag_tools'), $html);
    }

    /**
     * Тест генерации HTML дайджеста для пользователя с оценками
     */
    public function test_generate_html_digest_with_grades(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);

        $html = $digest->generate_html_digest();

        // Проверяем наличие основных элементов
        $this->assertStringContainsString('grade-digest-container', $html);
        $this->assertStringContainsString('grade-digest-statistics', $html);
        $this->assertStringContainsString('grade-digest-courses', $html);

        // Проверяем наличие данных курсов
        $this->assertStringContainsString('Test Course 1', $html);
        $this->assertStringContainsString('Test Course 2', $html);

        // Проверяем наличие названий заданий
        $this->assertStringContainsString('Test Assignment 1', $html);
        $this->assertStringContainsString('Test Quiz 1', $html);
        $this->assertStringContainsString('Test Assignment 2', $html);
    }

    /**
     * Тест фильтрации по последним N дням
     */
    public function test_set_last_days(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);
        $digest->set_last_days(30);

        $html = $digest->generate_html_digest();

        // Проверяем, что есть новые оценки
        $this->assertStringContainsString('Test Assignment 1', $html);
        $this->assertStringContainsString('Test Quiz 1', $html);
        $this->assertStringContainsString('Test Assignment 2', $html);

        // Проверяем, что старая оценка НЕ включена
        $this->assertStringNotContainsString('Old Assignment', $html);
    }

    /**
     * Тест установки текущего месяца
     */
    public function test_set_current_month(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);
        $digest->set_current_month();

        $html = $digest->generate_html_digest();

        // Проверяем, что дайджест сгенерирован
        $this->assertStringContainsString('grade-digest-container', $html);
        $this->assertStringContainsString('grade-digest-statistics', $html);
    }

    /**
     * Тест установки текущего года
     */
    public function test_set_current_year(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);
        $digest->set_current_year();

        $html = $digest->generate_html_digest();

        // Проверяем, что дайджест сгенерирован
        $this->assertStringContainsString('grade-digest-container', $html);
        $this->assertStringContainsString('grade-digest-statistics', $html);
    }

    /**
     * Тест установки произвольного периода
     */
    public function test_set_period(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);

        $dateFrom = strtotime('-90 days');
        $dateTo = time();
        $digest->set_period($dateFrom, $dateTo);

        $html = $digest->generate_html_digest();

        // Проверяем, что есть все оценки (включая старую)
        $this->assertStringContainsString('Test Assignment 1', $html);
        $this->assertStringContainsString('Old Assignment', $html);

        // Проверяем наличие информации о периоде
        $this->assertStringContainsString(get_string('from', 'local_cdo_ag_tools'), $html);
        $this->assertStringContainsString(get_string('to', 'local_cdo_ag_tools'), $html);
    }

    /**
     * Тест цепочки вызовов методов
     */
    public function test_method_chaining(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);

        $result = $digest->set_last_days(30);

        $this->assertInstanceOf(grade_digest::class, $result);
        $this->assertSame($digest, $result);
    }

    /**
     * Тест корректности статистики
     */
    public function test_statistics_calculation(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);

        $html = $digest->generate_html_digest();

        // Проверяем наличие статистических элементов
        $this->assertStringContainsString('stat-item', $html);
        $this->assertStringContainsString('stat-label', $html);
        $this->assertStringContainsString('stat-value', $html);

        // Всего должно быть 4 оценки
        $this->assertStringContainsString('>4<', $html);
    }

    /**
     * Тест форматирования оценок с цветовой индикацией
     */
    public function test_grade_formatting(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);

        $html = $digest->generate_html_digest();

        // Проверяем наличие классов для разных уровней оценок
        $this->assertStringContainsString('grade-excellent', $html); // 85.5, 90.0
        $this->assertStringContainsString('grade-good', $html); // 75.0
        $this->assertStringContainsString('grade-satisfactory', $html); // 60.0
    }

    /**
     * Тест группировки оценок по курсам
     */
    public function test_grades_grouping_by_course(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);

        $html = $digest->generate_html_digest();

        // Проверяем наличие разделов для каждого курса
        $this->assertStringContainsString('course-section', $html);
        $this->assertStringContainsString('data-courseid="' . $data->course1->id . '"', $html);
        $this->assertStringContainsString('data-courseid="' . $data->course2->id . '"', $html);
    }

    /**
     * Тест обработки несуществующего пользователя
     */
    public function test_invalid_user(): void {
        $this->resetAfterTest(true);

        $digest = new grade_digest(99999); // Несуществующий пользователь

        $this->expectException(\dml_exception::class);
        $digest->generate_html_digest();
    }

    /**
     * Тест HTML структуры дайджеста
     */
    public function test_html_structure(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);

        $html = $digest->generate_html_digest();

        // Проверяем правильность HTML структуры
        $this->assertStringContainsString('<div class="grade-digest-container">', $html);
        $this->assertStringContainsString('<h2>', $html);
        $this->assertStringContainsString('</div>', $html);

        // Проверяем наличие таблицы
        $this->assertStringContainsString('<table', $html);
        $this->assertStringContainsString('</table>', $html);
        $this->assertStringContainsString('<thead>', $html);
        $this->assertStringContainsString('<tbody>', $html);
    }

    /**
     * Тест отображения информации о пользователе
     */
    public function test_user_info_display(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);

        $html = $digest->generate_html_digest();

        // Проверяем наличие информации о пользователе
        $this->assertStringContainsString('digest-user-info', $html);
        $this->assertStringContainsString(fullname($data->user), $html);
        $this->assertStringContainsString('Test Alt Name', $html);
    }

    /**
     * Тест ссылок на курсы
     */
    public function test_course_links(): void {
        $data = $this->setup_test_data();
        $digest = new grade_digest($data->user->id);

        $html = $digest->generate_html_digest();

        // Проверяем наличие ссылок на курсы
        $this->assertStringContainsString('/course/view.php?id=' . $data->course1->id, $html);
        $this->assertStringContainsString('/course/view.php?id=' . $data->course2->id, $html);
    }
}

