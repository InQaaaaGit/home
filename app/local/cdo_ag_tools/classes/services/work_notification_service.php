<?php

namespace local_cdo_ag_tools\services;

use coding_exception;
use core_user;
use dml_exception;
use moodle_exception;
use stdClass;

/**
 * Сервис для управления уведомлениями о выполненных работах
 *
 * @package local_cdo_ag_tools
 * @author InQaaaa
 */
class work_notification_service
{
    /**
     * Отправляет уведомление о загрузке письменной работы
     *
     * @param int $userId ID пользователя
     * @param int $courseId ID курса
     * @param string $assignmentName Название работы
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function send_work_uploaded_notification(
        int $userId,
        int $courseId,
        string $assignmentName
    ): bool {
        global $DB;

        $user = core_user::get_user($userId);
        if (!$user) {
            throw new moodle_exception('usernotfound', 'local_cdo_ag_tools');
        }

        $course = $DB->get_record('course', ['id' => $courseId], '*', MUST_EXIST);
        $userCode = self::get_user_code($userId);

        $subject = get_string('work_uploaded_subject', 'local_cdo_ag_tools');
        $messageText = get_string('work_uploaded_message', 'local_cdo_ag_tools', [
            'usercode' => $userCode,
            'assignmentname' => $assignmentName,
            'coursename' => $course->fullname,
        ]);

        return self::send_notification(
            $user,
            $subject,
            $messageText,
            'work_uploaded'
        );
    }

    /**
     * Отправляет уведомление о проверке письменной работы и выставлении оценки
     *
     * @param int $userId ID пользователя
     * @param int $courseId ID курса
     * @param string $assignmentName Название работы
     * @param float $grade Оценка
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function send_work_graded_notification(
        int $userId,
        int $courseId,
        string $assignmentName,
        float $grade
    ): bool {
        global $DB;

        $user = core_user::get_user($userId);
        if (!$user) {
            throw new moodle_exception('usernotfound', 'local_cdo_ag_tools');
        }

        $course = $DB->get_record('course', ['id' => $courseId], '*', MUST_EXIST);
        $userCode = self::get_user_code($userId);

        $subject = get_string('work_graded_subject', 'local_cdo_ag_tools');
        $messageText = get_string('work_graded_message', 'local_cdo_ag_tools', [
            'usercode' => $userCode,
            'coursename' => $course->fullname,
            'assignmentname' => $assignmentName,
            'grade' => $grade,
        ]);

        return self::send_notification(
            $user,
            $subject,
            $messageText,
            'work_graded'
        );
    }

    /**
     * Формирует и отправляет еженедельный отчет о пройденных тестах
     *
     * @param int $userId ID пользователя
     * @param string $dateFrom Дата начала периода
     * @param string $dateTo Дата окончания периода
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function send_weekly_quiz_report(
        int $userId,
        string $dateFrom,
        string $dateTo
    ): bool {
        global $DB;

        $user = core_user::get_user($userId);
        if (!$user) {
            return false;
        }

        // Получаем список пройденных тестов за период
        $quizGrades = self::get_quiz_grades_for_period($userId, $dateFrom, $dateTo);

        if (empty($quizGrades)) {
            return false; // Нет оценок за период - не отправляем письмо
        }

        $userCode = self::get_user_code($userId);
        $subject = get_string('weekly_quiz_report_subject', 'local_cdo_ag_tools');

        // Формируем список работ в HTML формате
        $worksList = '';
        $counter = 1;
        foreach ($quizGrades as $grade) {
            $worksList .= sprintf(
                '<div class="work-item">
                    <strong>%d.</strong> 📚 <strong>%s</strong><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;📝 <em>%s</em><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="grade-badge" style="display: inline-block; font-size: 14px; padding: 4px 10px;">Оценка: %.1f</span>
                </div>',
                $counter++,
                htmlspecialchars($grade->coursename),
                htmlspecialchars($grade->itemname),
                $grade->finalgrade
            );
        }

        $messageText = get_string('weekly_quiz_report_message', 'local_cdo_ag_tools', [
            'usercode' => $userCode,
            'datefrom' => $dateFrom,
            'dateto' => $dateTo,
            'workslist' => $worksList,
        ]);

        return self::send_notification(
            $user,
            $subject,
            $messageText,
            'weekly_quiz_report'
        );
    }

    /**
     * Получает список оценок за тесты за указанный период
     *
     * @param int $userId ID пользователя
     * @param string $dateFrom Дата начала периода
     * @param string $dateTo Дата окончания периода
     * @return array
     * @throws dml_exception
     */
    private static function get_quiz_grades_for_period(
        int $userId,
        string $dateFrom,
        string $dateTo
    ): array {
        global $DB;

        $timeFrom = strtotime($dateFrom);
        $timeTo = strtotime($dateTo);

        $sql = "SELECT gg.id,
                       c.fullname as coursename,
                       gi.itemname,
                       gg.finalgrade,
                       gi.grademax,
                       gg.timemodified
                FROM {grade_grades} gg
                JOIN {grade_items} gi ON gi.id = gg.itemid
                JOIN {course} c ON c.id = gi.courseid
                WHERE gg.userid = :userid
                  AND gi.itemtype = 'mod'
                  AND gi.itemmodule = 'quiz'
                  AND gg.finalgrade IS NOT NULL
                  AND gg.timemodified >= :timefrom
                  AND gg.timemodified <= :timeto
                ORDER BY gg.timemodified DESC";

        $params = [
            'userid' => $userId,
            'timefrom' => $timeFrom,
            'timeto' => $timeTo,
        ];

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Получает код пользователя (username или другой идентификатор)
     *
     * @param int $userId ID пользователя
     * @return string
     * @throws dml_exception
     */
    private static function get_user_code(int $userId): string
    {
        global $DB;
        
        $user = $DB->get_record('user', ['id' => $userId], 'username, idnumber', MUST_EXIST);
        
        // Возвращаем idnumber если есть, иначе username
        return !empty($user->idnumber) ? $user->idnumber : $user->username;
    }

    /**
     * Базовый метод отправки уведомления
     *
     * @param stdClass $user Объект пользователя
     * @param string $subject Тема письма
     * @param string $messageText Текст сообщения
     * @param string $eventType Тип события
     * @return bool
     * @throws moodle_exception
     */
    private static function send_notification(
        stdClass $user,
        string $subject,
        string $messageText,
        string $eventType
    ): bool {
        if (empty($user->email)) {
            return false;
        }

        // Генерируем HTML версию сообщения
        $messageHtml = self::format_html_message($messageText, $subject);
        
        $message = new \core\message\message();
        $message->component = 'local_cdo_ag_tools';
        $message->name = $eventType;
        $message->userfrom = core_user::get_noreply_user();
        $message->userto = $user;
        $message->subject = $subject;
        $message->fullmessage = strip_tags($messageText);
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = $messageHtml;
        $message->smallmessage = $subject;
        $message->notification = 1;
        $message->contexturl = (new \moodle_url('/my/'))->out(false);
        $message->contexturlname = get_string('myhome');

        return (bool)message_send($message);
    }

    /**
     * Сохраняет информацию о загруженной работе в БД
     *
     * @param int $userId ID пользователя
     * @param int $courseId ID курса
     * @param int $assignmentId ID задания
     * @param int $submissionId ID submission
     * @return int ID созданной записи
     * @throws dml_exception
     */
    public static function log_work_upload(
        int $userId,
        int $courseId,
        int $assignmentId,
        int $submissionId
    ): int {
        global $DB;

        $record = new stdClass();
        $record->userid = $userId;
        $record->courseid = $courseId;
        $record->assignmentid = $assignmentId;
        $record->submissionid = $submissionId;
        $record->upload_notified = 1;
        $record->grade_notified = 0;
        $record->timecreated = time();
        $record->timemodified = time();

        return $DB->insert_record('local_cdo_ag_work_notif', $record);
    }

    /**
     * Обновляет статус уведомления о проверке работы
     *
     * @param int $userId ID пользователя
     * @param int $submissionId ID submission
     * @return bool
     * @throws dml_exception
     */
    public static function mark_work_as_graded_notified(
        int $userId,
        int $submissionId
    ): bool {
        global $DB;

        $record = $DB->get_record('local_cdo_ag_work_notif', [
            'userid' => $userId,
            'submissionid' => $submissionId,
        ]);

        if ($record) {
            $record->grade_notified = 1;
            $record->timemodified = time();
            return $DB->update_record('local_cdo_ag_work_notif', $record);
        }

        return false;
    }

    /**
     * Проверяет, было ли уже отправлено уведомление о проверке работы
     *
     * @param int $userId ID пользователя
     * @param int $submissionId ID submission
     * @return bool
     * @throws dml_exception
     */
    public static function is_graded_notification_sent(
        int $userId,
        int $submissionId
    ): bool {
        global $DB;

        $record = $DB->get_record('local_cdo_ag_work_notif', [
            'userid' => $userId,
            'submissionid' => $submissionId,
        ]);

        return $record && $record->grade_notified == 1;
    }

    /**
     * Форматирует сообщение в красивый HTML
     *
     * @param string $messageText Текст сообщения
     * @param string $subject Тема сообщения
     * @return string HTML-форматированное сообщение
     */
    private static function format_html_message(string $messageText, string $subject): string
    {
        // Стили для email
        $styles = "
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
            .email-container { max-width: 600px; margin: 0 auto; padding: 20px; background: #ffffff; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; border-radius: 10px 10px 0 0; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
            .content { background: #f8f9fa; padding: 30px 20px; border-left: 4px solid #667eea; }
            .info-badge { display: inline-block; background: #667eea; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: 600; margin-bottom: 15px; }
            .message-body { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            .work-list { background: white; padding: 20px; border-radius: 8px; margin: 15px 0; }
            .work-item { padding: 12px; margin: 8px 0; background: #f8f9fa; border-left: 3px solid #667eea; border-radius: 4px; }
            .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 12px; border-top: 1px solid #dee2e6; margin-top: 20px; }
            .highlight { color: #667eea; font-weight: 600; }
            .success { color: #28a745; font-weight: 600; }
            .grade-badge { display: inline-block; background: #28a745; color: white; padding: 8px 16px; border-radius: 6px; font-size: 18px; font-weight: bold; margin: 10px 0; }
        </style>
        ";

        // Заменяем переносы строк на HTML
        $messageText = nl2br($messageText);
        
        // Формируем HTML
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            {$styles}
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>📧 {$subject}</h1>
                </div>
                <div class='content'>
                    <div class='message-body'>
                        {$messageText}
                    </div>
                </div>
                <div class='footer'>
                    <p>Это автоматическое уведомление из системы LMS Moodle</p>
                    <p>© " . date('Y') . " Академическая гимназия</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $html;
    }
}

