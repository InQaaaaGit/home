<?php

namespace local_cdo_unti2035bas\observer;

use core\event\base;
use core\event\grade_deleted;
use core\event\grade_item_created;
use core\event\grade_item_deleted;
use core\event\grade_item_updated;
use core\event\user_graded;
use local_cdo_unti2035bas\infrastructure\moodle\grade_data_extractor;
use local_cdo_unti2035bas\infrastructure\moodle\grade_validator;
use mod_quiz\event\attempt_submitted;

/**
 * Обсервер для отслеживания событий выставления оценок
 * Отвечает только за обработку событий, связанных с оценками
 */
class grade_observer
{
    public function get_max_attempts(): int
    {
        return 1000;
    }

    /**
     * Обработчик события выставления оценки пользователю
     *
     * @param user_graded $event
     */
    public static function user_graded(user_graded $event): void
    {
        self::handle_grade_event($event, 'graded');
    }

    /**
     * Обработчик события удаления оценки
     *
     * @param grade_deleted $event
     */
    public static function grade_deleted(grade_deleted $event): void
    {
        self::handle_grade_event($event, 'deleted');
    }

    /**
     * Обработчик события создания элемента оценки
     *
     * @param grade_item_created $event
     */
    public static function grade_item_created(grade_item_created $event): void
    {
        self::handle_grade_event($event, 'item_created');
    }

    /**
     * Обработчик события обновления элемента оценки
     *
     * @param grade_item_updated $event
     */
    public static function grade_item_updated(grade_item_updated $event): void
    {
        self::handle_grade_event($event, 'item_updated');
    }

    /**
     * Обработчик события удаления элемента оценки
     *
     * @param grade_item_deleted $event
     */
    public static function grade_item_deleted(grade_item_deleted $event): void
    {
        self::handle_grade_event($event, 'item_deleted');
    }

    /**
     * Обработчик события отправки попытки quiz
     *
     * @param attempt_submitted $event
     */
    public static function quiz_attempt_submitted(attempt_submitted $event): void
    {
        debugging("cdo_unti2035bas: Событие quiz_attempt_submitted сработало", DEBUG_DEVELOPER);
        self::handle_quiz_event($event, 'quiz_submitted');
    }

    /**
     * Общий обработчик событий выставления оценок
     *
     * @param base $event
     * @param string $action
     */
    private static function handle_grade_event(base $event, string $action): void
    {
        try {
            debugging("cdo_unti2035bas: Обработка события {$action} оценки", DEBUG_DEVELOPER);

            // Получаем данные из события
            $event_data = $event->get_data();
            $grade_data = grade_data_extractor::extract($event_data);

            // Проверяем наличие courseid
            if (empty($grade_data['courseid']) || $grade_data['courseid'] < 2) {
                debugging("cdo_unti2035bas: Не указан courseid для события {$action} оценки", DEBUG_DEVELOPER);
                return;
            }

            // Проверяем, что курс привязан к потоку (имеет flow_id)
            $dependencies = new dependencies();
            $streamRepo = $dependencies->get_stream_repo();
            $streams = $streamRepo->read_by_courseid($grade_data['courseid']);
            if (empty($streams)) {
                debugging("cdo_unti2035bas: Курс {$grade_data['courseid']} не привязан к потоку, пропускаем обработку события {$action} оценки", DEBUG_DEVELOPER);
                return;
            }

            // Проверяем, нужно ли отправлять xAPI statement
            if (!grade_validator::should_send_xapi_statement($grade_data)) {
                debugging("cdo_unti2035bas: Пропуск отправки xAPI для оценки", DEBUG_DEVELOPER);
                return;
            }

            // Отправляем xAPI statement
            $grade_use_case = $dependencies->get_grade_activity_use_case();
            $result = $grade_use_case->send_grade_statement($grade_data, $action, $grade_data['grade_info']['itemid'], 2);

            if ($result['success']) {
                debugging("cdo_unti2035bas: Успешно отправлен xAPI statement для оценки {$action} пользователя {$grade_data['userid']}, элемент: {$grade_data['itemid']}", DEBUG_DEVELOPER);
            } else {
                debugging("cdo_unti2035bas: Ошибка отправки xAPI statement для оценки {$action}: " . ($result['error'] ?? 'Unknown error'), DEBUG_DEVELOPER);
            }

        } catch (\Exception $e) {
            debugging("cdo_unti2035bas: Ошибка при обработке события {$action} оценки: " . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Общий обработчик событий quiz
     *
     * @param attempt_submitted $event
     * @param string $action
     */
    private static function handle_quiz_event(attempt_submitted $event, string $action): void
    {
        try {
            debugging("cdo_unti2035bas: Обработка события {$action}", DEBUG_DEVELOPER);

            // Получаем данные из события
            $event_data = $event->get_data();
            $quiz_data = self::extract_quiz_data($event_data);
            $quiz_snapshot = $event->get_record_snapshot('quiz', $quiz_data['quizid']);
            $user_attempts = quiz_get_user_attempts($quiz_data['quizid'], $quiz_data['userid']);
            $grade = array_values(quiz_get_user_grades($quiz_snapshot, $quiz_data['userid']))[0]->rawgrade;
            if (!$grade) {
                return;
            }
            $quiz_max_attempts = (int)$quiz_snapshot->attempts === 0 ? (new grade_observer)->get_max_attempts() : (int)$quiz_snapshot->attempts;

            // Проверяем базовые условия
            if (!$quiz_data['userid'] || !$quiz_data['courseid'] || !$quiz_data['cmid']) {
                debugging("cdo_unti2035bas: Недостаточно данных для обработки события quiz", DEBUG_DEVELOPER);
                return;
            }

            // Проверяем, что курс привязан к потоку (имеет flow_id)
            $dependencies = new dependencies();
            $streamRepo = $dependencies->get_stream_repo();
            $streams = $streamRepo->read_by_courseid($quiz_data['courseid']);
            if (empty($streams)) {
                debugging("cdo_unti2035bas: Курс {$quiz_data['courseid']} не привязан к потоку, пропускаем обработку события quiz", DEBUG_DEVELOPER);
                return;
            }

            // Отправляем xAPI statement
            $grade_use_case = $dependencies->get_grade_activity_use_case();

            $result = $grade_use_case->send_grade_statement(
                $quiz_data,
                $action,
                $quiz_data['itemid'],
                $quiz_max_attempts,
                $grade,
                count($user_attempts)
            );

            if ($result['success']) {
                debugging("cdo_unti2035bas: Успешно отправлен xAPI statement для quiz {$action} пользователя {$quiz_data['userid']}, cmid: {$quiz_data['cmid']}", DEBUG_DEVELOPER);
            } else {
                debugging("cdo_unti2035bas: Ошибка отправки xAPI statement для quiz {$action}: " . ($result['error'] ?? 'Unknown error'), DEBUG_DEVELOPER);
            }

        } catch (\Exception $e) {
            debugging("cdo_unti2035bas: Ошибка при обработке события {$action}: " . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Извлекает данные из события quiz
     *
     * @param array $event_data
     * @return array
     */
    private static function extract_quiz_data(array $event_data): array
    {
        global $DB;

        $quiz_data = [
            'userid' => $event_data['userid'] ?? 0,
            'courseid' => $event_data['courseid'] ?? 0,
            'cmid' => $event_data['contextinstanceid'] ?? 0,
            'attemptid' => $event_data['objectid'] ?? 0,
            'timecreated' => $event_data['timecreated'] ?? time(),
            'grade' => null,
            'maxgrade' => null,
            'itemname' => null,
            'itemid' => null
        ];

        // Получаем информацию о попытке
        if ($quiz_data['attemptid']) {
            $attempt = $DB->get_record('quiz_attempts', ['id' => $quiz_data['attemptid']], 'quiz, sumgrades, timestart, timefinish', IGNORE_MISSING);
            if ($attempt) {
                $quiz_data['quizid'] = $attempt->quiz;
                $quiz_data['sumgrades'] = $attempt->sumgrades;
                $quiz_data['timestart'] = $attempt->timestart;
                $quiz_data['timefinish'] = $attempt->timefinish;

                // Получаем информацию о quiz
                $quiz = $DB->get_record('quiz', ['id' => $attempt->quiz], 'name, grade, sumgrades', IGNORE_MISSING);
                if ($quiz) {
                    $quiz_data['itemname'] = $quiz->name;
                    $quiz_data['maxgrade'] = $quiz->grade;
                    $quiz_data['grade'] = $attempt->sumgrades;

                    // Получаем grade item id
                    $grade_item = $DB->get_record('grade_items', [
                        'itemtype' => 'mod',
                        'itemmodule' => 'quiz',
                        'iteminstance' => $attempt->quiz,
                        'courseid' => $quiz_data['courseid']
                    ], 'id', IGNORE_MISSING);

                    if ($grade_item) {
                        $quiz_data['itemid'] = $grade_item->id;
                    }
                }
            }
        }

        return $quiz_data;
    }
} 