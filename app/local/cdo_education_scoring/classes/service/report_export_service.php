<?php

namespace local_cdo_education_scoring\service;

use tool_cdo_config\di;

defined('MOODLE_INTERNAL') || die();

global $CFG;
// PhpSpreadsheet доступен через composer в Moodle
require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');
require_once(__DIR__ . '/../../lib.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class report_export_service {

    /**
     * Получение данных для отчёта (используется для Excel и HTML).
     *
     * @param int $surveyid ID анкеты
     * @param int $teacher_id ID преподавателя (обязательный)
     * @param string|null $discipline_id Код дисциплины
     * @return array Данные отчёта
     */
    public function get_report_data(int $surveyid, int $teacher_id, ?string $discipline_id = null): array {
        global $DB;

        // Получение актуальных имен таблиц.
        $surveyTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_survey',
            'local_cdo_education_scoring_survey'
        );
        $responseTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_resp',
            'local_cdo_education_scoring_response'
        );
        $questionTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_quest',
            'local_cdo_education_scoring_question'
        );

        // Получаем данные анкеты
        $survey = $DB->get_record($surveyTable, ['id' => $surveyid]);
        if (!$survey) {
            throw new \moodle_exception('Анкета не найдена');
        }

        // Получаем данные студентов через external API
        $studentsData = [];
        
        try {
            $requestParams = [];
            if ($discipline_id) {
                $requestParams['discipline_id'] = $discipline_id;
            }
            $requestParams['surveyid'] = $surveyid;
            $requestParams['teacher_id'] = $teacher_id;

            $options = di::get_instance()->get_request_options();
            $options->set_properties($requestParams);

            $requestResult = di::get_instance()
                ->get_request('get_students_for_report')
                ->request($options)
                ->get_request_result();

            if (method_exists($requestResult, 'to_array')) {
                $studentsData = $requestResult->to_array();
            } elseif (is_array($requestResult)) {
                $studentsData = $requestResult;
            } else {
                $studentsData = [];
            }
            
            if (!is_array($studentsData)) {
                $studentsData = [];
            }
            
            $validatedData = [];
            foreach ($studentsData as $student) {
                if (is_array($student) && isset($student['id'])) {
                    $validatedData[] = $student;
                }
            }
            $studentsData = $validatedData;
            
        } catch (\Exception $e) {
            error_log('Ошибка при получении данных студентов для отчета: ' . $e->getMessage());
            error_log('Трассировка: ' . $e->getTraceAsString());
            $studentsData = [];
        }

        // Получаем данные о прохождении опроса из локальной БД
        $sql = "
            SELECT DISTINCT
                r.userid,
                r.teacher_id,
                r.discipline_id,
                r.discipline_name,
                MIN(r.timecreated) AS first_response_time
            FROM {" . $responseTable . "} r
            WHERE r.surveyid = :surveyid
        ";
        
        $params = [
            'surveyid' => $surveyid,
            'teacher_id' => $teacher_id,
        ];
        $sql .= " AND r.teacher_id = :teacher_id";
        if ($discipline_id) {
            $sql .= " AND r.discipline_id = :discipline_id";
            $params['discipline_id'] = $discipline_id;
        }
        
        $sql .= " GROUP BY r.userid, r.teacher_id, r.discipline_id, r.discipline_name";
        
        $responses = $DB->get_records_sql($sql, $params);
        
        $completedByExternalId = [];
        $completedByUserId = [];
        
        foreach ($responses as $response) {
            $externalId = (string)$response->userid;
            $userId = (int)$response->userid;
            
            if (!isset($completedByExternalId[$externalId])) {
                $completedByExternalId[$externalId] = [];
            }
            $completedByExternalId[$externalId][] = [
                'user_id' => $response->userid,
                'discipline_id' => $response->discipline_id,
                'discipline_name' => $response->discipline_name ?? '',
                'teacher_id' => $response->teacher_id,
                'timecreated' => $response->first_response_time,
            ];
            
            if (!isset($completedByUserId[$userId])) {
                $completedByUserId[$userId] = [];
            }
            $completedByUserId[$userId][] = [
                'user_id' => $response->userid,
                'discipline_id' => $response->discipline_id,
                'discipline_name' => $response->discipline_name ?? '',
                'teacher_id' => $response->teacher_id,
                'timecreated' => $response->first_response_time,
            ];
        }

        // Получаем информацию о преподавателе
        $teacher = $DB->get_record('user', ['id' => $teacher_id]);
        if (!$teacher) {
            throw new \moodle_exception('Преподаватель не найден');
        }
        $teacherName = fullname($teacher);

        // Сортируем данные студентов: сначала по группе, потом по ФИО
        usort($studentsData, function($a, $b) {
            $groupA = $a['group'] ?? '';
            $groupB = $b['group'] ?? '';
            $fullnameA = $a['fullname'] ?? '';
            $fullnameB = $b['fullname'] ?? '';
            
            $groupCompare = strnatcasecmp($groupA, $groupB);
            if ($groupCompare !== 0) {
                return $groupCompare;
            }
            
            return strnatcasecmp($fullnameA, $fullnameB);
        });

        // Подсчитываем статистику
        $totalStudents = count($studentsData);
        $completedCount = 0;
        foreach ($studentsData as $student) {
            $studentExternalId = (string)($student['id'] ?? '');
            if ($studentExternalId && isset($completedByExternalId[$studentExternalId])) {
                $completedCount++;
            }
        }
        $completedPercent = $totalStudents > 0 ? round(($completedCount / $totalStudents) * 100, 1) : 0;

        // Получаем вопросы анкеты и подсчитываем средние баллы
        $questions = $DB->get_records(
            $questionTable,
            ['surveyid' => $surveyid],
            'sortorder ASC'
        );
        
        if (!$questions) {
            $questions = [];
        }

        // Подсчитываем средние баллы для каждого вопроса
        $questionAverages = [];
        $totalScoreSum = 0;
        $totalScoreCount = 0;

        foreach ($questions as $question) {
            if ($question->questiontype === 'scale') {
                $sql = "
                    SELECT r.responsevalue
                    FROM {" . $responseTable . "} r
                    WHERE r.surveyid = :surveyid
                    AND r.questionid = :questionid
                    AND r.teacher_id = :teacher_id
                ";

                $params = [
                    'surveyid' => $surveyid,
                    'questionid' => $question->id,
                    'teacher_id' => $teacher_id,
                ];

                if ($discipline_id) {
                    $sql .= " AND r.discipline_id = :discipline_id";
                    $params['discipline_id'] = $discipline_id;
                }

                try {
                    $responsesQ = $DB->get_records_sql($sql, $params);
                    $responseCount = count($responsesQ);
                    
                    if ($responseCount > 0) {
                        $sum = 0;
                        $validCount = 0;
                        foreach ($responsesQ as $resp) {
                            $value = (float)$resp->responsevalue;
                            if ($value > 0) {
                                $sum += $value;
                                $validCount++;
                            }
                        }
                        $avgScore = $validCount > 0 ? round($sum / $validCount, 2) : null;
                    } else {
                        $avgScore = null;
                    }
                } catch (\Exception $e) {
                    error_log('Ошибка при подсчете среднего балла для вопроса ' . $question->id . ': ' . $e->getMessage());
                    $avgScore = null;
                    $responseCount = 0;
                }

                if ($avgScore !== null) {
                    $questionAverages[$question->id] = [
                        'avg_score' => $avgScore,
                        'response_count' => $responseCount,
                    ];
                    $totalScoreSum += $avgScore;
                    $totalScoreCount++;
                } else {
                    $questionAverages[$question->id] = [
                        'avg_score' => null,
                        'response_count' => 0,
                    ];
                }
            } else {
                $questionAverages[$question->id] = [
                    'avg_score' => null,
                    'response_count' => 0,
                ];
            }
        }

        // Формируем данные о студентах с метками прохождения
        $studentsWithCompletion = [];
        foreach ($studentsData as $student) {
            $studentExternalId = (string)($student['id'] ?? '');
            $hasCompleted = isset($completedByExternalId[$studentExternalId]);
            
            $studentDisciplineName = '';
            $studentDisciplineId = $discipline_id;
            $studentUserId = null; // user_id из таблицы ответов
            
            if ($hasCompleted && !empty($completedByExternalId[$studentExternalId])) {
                $completionData = $completedByExternalId[$studentExternalId];
                if (!empty($completionData[0]['discipline_name'])) {
                    $studentDisciplineName = $completionData[0]['discipline_name'];
                }
                if (!empty($completionData[0]['discipline_id'])) {
                    $studentDisciplineId = $completionData[0]['discipline_id'];
                }
                if (!empty($completionData[0]['user_id'])) {
                    $studentUserId = $completionData[0]['user_id'];
                }
            }
            
            // Получаем посещаемость если есть user_id из таблицы ответов и discipline_id
            $attendance = '';
            $this->debugLog('[Report] Студент: ' . ($student['fullname'] ?? 'N/A') . 
                ', hasCompleted=' . ($hasCompleted ? 'true' : 'false') . 
                ', studentUserId=' . ($studentUserId ?? 'null') . 
                ', studentDisciplineId=' . ($studentDisciplineId ?? 'null'));
            
            if ($studentUserId && $studentDisciplineId) {
                $attendance = $this->getStudentAttendance((string)$studentUserId, (string)$studentDisciplineId);
                $this->debugLog('[Report] Получена посещаемость: ' . ($attendance ?: 'пусто'));
            }
            
            $studentsWithCompletion[] = [
                'id' => $student['id'] ?? '',
                'user_id' => $studentUserId, // user_id из таблицы ответов для фильтрации
                'fullname' => $student['fullname'] ?? '',
                'group' => $student['group'] ?? '',
                'group_id' => $student['group_id'] ?? '',
                'speciality' => $student['speciality'] ?? '',
                'discipline_name' => $studentDisciplineName,
                'completed' => $hasCompleted,
                'attendance' => $attendance,
            ];
        }

        // Формируем данные о вопросах
        $questionsData = [];
        foreach ($questions as $question) {
            $avgData = $questionAverages[$question->id] ?? ['avg_score' => null, 'response_count' => 0];
            $questionsData[] = [
                'id' => $question->id,
                'text' => $question->questiontext,
                'type' => $question->questiontype,
                'avg_score' => $avgData['avg_score'],
                'response_count' => $avgData['response_count'],
            ];
        }

        $overallAvg = $totalScoreCount > 0 ? round($totalScoreSum / $totalScoreCount, 2) : null;

        return [
            'survey' => [
                'id' => $survey->id,
                'title' => $survey->title,
            ],
            'teacher' => [
                'id' => $teacher_id,
                'name' => $teacherName,
            ],
            'discipline_id' => $discipline_id,
            'export_date' => date('d.m.Y H:i:s'),
            'statistics' => [
                'total_students' => $totalStudents,
                'completed_count' => $completedCount,
                'completed_percent' => $completedPercent,
            ],
            'students' => $studentsWithCompletion,
            'questions' => $questionsData,
            'overall_avg' => $overallAvg,
        ];
    }

    /**
     * Экспорт отчета по анкете в Excel.
     *
     * @param int $surveyid ID анкеты
     * @param int $teacher_id ID преподавателя (обязательный)
     * @param string|null $discipline_id Код дисциплины
     * @return void
     */
    public function export_survey_report(int $surveyid, int $teacher_id, ?string $discipline_id = null): void {
        // Получаем данные отчёта
        $data = $this->get_report_data($surveyid, $teacher_id, $discipline_id);

        // Создаем Excel файл
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Устанавливаем шапку отчета
        $row = 1;
        
        // Дата выгрузки
        $sheet->setCellValue('A' . $row, 'Дата выгрузки:');
        $sheet->setCellValue('B' . $row, $data['export_date']);
        $row++;

        // Преподаватель
        $sheet->setCellValue('A' . $row, 'Преподаватель:');
        $sheet->setCellValue('B' . $row, $data['teacher']['name']);
        $row++;

        // Всего студентов
        $sheet->setCellValue('A' . $row, 'Всего студентов:');
        $sheet->setCellValue('B' . $row, $data['statistics']['total_students']);
        $row++;

        // Получено ответов
        $sheet->setCellValue('A' . $row, 'Получено ответов:');
        $sheet->setCellValue('B' . $row, $data['statistics']['completed_count'] . ' чел. / ' . $data['statistics']['completed_percent'] . '%');
        $row++;

        // Пустая строка
        $row++;

        // Заголовки основной таблицы
        $headers = [
            'A' => '№ п/п',
            'B' => 'Направление подготовки/Специальность',
            'C' => 'Дисциплина',
            'D' => 'Группа',
            'E' => 'Студент (ФИО)',
            'F' => 'Метка о прохождении опроса',
            'G' => 'Посещаемость',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $row, $header);
        }

        // Стили для заголовков основной таблицы
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7E6E6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($headerStyle);
        
        $row++;

        // Заполняем данные основной таблицы со студентами
        $rowNumber = 1;
        foreach ($data['students'] as $student) {
            $completionMark = $student['completed'] ? '✓' : '';

            $sheet->setCellValue('A' . $row, $rowNumber);
            $sheet->setCellValue('B' . $row, $student['speciality']);
            $sheet->setCellValue('C' . $row, $student['discipline_name']);
            $sheet->setCellValue('D' . $row, $student['group']);
            $sheet->setCellValue('E' . $row, $student['fullname']);
            $sheet->setCellValue('F' . $row, $completionMark);
            $sheet->setCellValue('G' . $row, $student['attendance']);

            // Стили для строк данных
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($dataStyle);
            
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $rowNumber++;
        }

        // Добавляем пустую строку перед таблицей с вопросами
        $row += 2;

        // Заголовки таблицы с вопросами
        $sheet->setCellValue('A' . $row, '№ п/п');
        $sheet->setCellValue('B' . $row, 'Вопрос');
        $sheet->setCellValue('C' . $row, 'Средний балл');
        
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($headerStyle);
        
        $row++;

        // Заполняем таблицу с вопросами
        $questionRowNumber = 1;
        foreach ($data['questions'] as $question) {
            $avgScore = $question['avg_score'] !== null ? number_format($question['avg_score'], 2) : '—';

            $sheet->setCellValue('A' . $row, $questionRowNumber);
            $sheet->setCellValue('B' . $row, $question['text']);
            $sheet->setCellValue('C' . $row, $avgScore);

            $questionDataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($questionDataStyle);
            
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $questionRowNumber++;
        }

        // Добавляем строку с общим средним баллом
        $overallAvgDisplay = $data['overall_avg'] !== null ? number_format($data['overall_avg'], 2) : '—';

        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'Общий средний балл');
        $sheet->setCellValue('C' . $row, $overallAvgDisplay);

        $overallAvgStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D4EDDA'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($overallAvgStyle);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Автоматическая ширина колонок
        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Закрываем сессию перед отправкой файла
        \core\session\manager::write_close();

        // Очищаем буфер вывода
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Отправляем файл
        $filename = 'survey_report_' . $surveyid . '_' . date('YmdHi') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Экспорт упрощённого отчета по группе и дисциплине с фильтрацией.
     *
     * @param int $surveyid ID анкеты
     * @param int $teacher_id ID преподавателя
     * @param string $discipline_id Код дисциплины (обязательный для фильтра)
     * @param string $group Название группы (обязательный для фильтра)
     * @param int|null $attendanceMin Минимальная посещаемость (опциональный)
     * @return void
     */
    public function export_filtered_report(int $surveyid, int $teacher_id, string $discipline_id = '', string $group = '', ?int $attendanceMin = null): void {
        // Получаем полные данные отчёта с фильтром по дисциплине
        $data = $this->get_report_data($surveyid, $teacher_id, $discipline_id ?: null);

        // Дополнительная фильтрация студентов по группе и посещаемости
        $filteredStudents = [];
        foreach ($data['students'] as $student) {
            // Фильтр по группе
            if ($group && $student['group'] !== $group) {
                continue;
            }
            
            // Фильтр по посещаемости
            if ($attendanceMin !== null) {
                $attendanceValue = null;
                if (!empty($student['attendance'])) {
                    // Извлекаем число из строки типа "85.5%"
                    $attendanceStr = str_replace('%', '', $student['attendance']);
                    if (is_numeric($attendanceStr)) {
                        $attendanceValue = (float)$attendanceStr;
                    }
                }
                
                // Пропускаем студента если посещаемость ниже минимума или не указана
                if ($attendanceValue === null || $attendanceValue < $attendanceMin) {
                    continue;
                }
            }
            
            $filteredStudents[] = $student;
        }
        
        // Обновляем данные отфильтрованными студентами
        $data['students'] = $filteredStudents;
        
        // Пересчитываем статистику
        $data['statistics']['total_students'] = count($filteredStudents);
        $completedCount = 0;
        foreach ($filteredStudents as $student) {
            if ($student['completed']) {
                $completedCount++;
            }
        }
        $data['statistics']['completed_count'] = $completedCount;
        $data['statistics']['completed_percent'] = $data['statistics']['total_students'] > 0 
            ? round(($completedCount / $data['statistics']['total_students']) * 100, 1) 
            : 0;

        // ВАЖНО: Пересчитываем средние баллы только для отфильтрованных студентов
        $data = $this->recalculateQuestionAverages($data, $surveyid, $teacher_id, $discipline_id);

        // Создаем Excel файл
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Устанавливаем шапку отчета
        $row = 1;
        
        // Дата выгрузки
        $sheet->setCellValue('A' . $row, 'Дата выгрузки:');
        $sheet->setCellValue('B' . $row, $data['export_date']);
        $row++;

        // Преподаватель
        $sheet->setCellValue('A' . $row, 'Преподаватель:');
        $sheet->setCellValue('B' . $row, $data['teacher']['name']);
        $row++;

        // Дисциплина
        if ($discipline_id) {
            $sheet->setCellValue('A' . $row, 'Дисциплина:');
            $disciplineName = '';
            if (!empty($filteredStudents[0]['discipline_name'])) {
                $disciplineName = $filteredStudents[0]['discipline_name'];
            }
            $sheet->setCellValue('B' . $row, $disciplineName ?: $discipline_id);
            $row++;
        }

        // Группа
        if ($group) {
            $sheet->setCellValue('A' . $row, 'Группа:');
            $sheet->setCellValue('B' . $row, $group);
            $row++;
        }
        
        // Минимальная посещаемость
        if ($attendanceMin !== null) {
            $sheet->setCellValue('A' . $row, 'Мин. посещаемость:');
            $sheet->setCellValue('B' . $row, $attendanceMin . '%');
            $row++;
        }

        // Всего студентов
        $sheet->setCellValue('A' . $row, 'Всего студентов:');
        $sheet->setCellValue('B' . $row, $data['statistics']['total_students']);
        $row++;

        // Получено ответов
        $sheet->setCellValue('A' . $row, 'Получено ответов:');
        $sheet->setCellValue('B' . $row, $data['statistics']['completed_count'] . ' чел. / ' . $data['statistics']['completed_percent'] . '%');
        $row++;

        // Пустая строка
        $row++;

        // Заголовки основной таблицы
        $headers = [
            'A' => '№ п/п',
            'B' => 'Направление подготовки/Специальность',
            'C' => 'Дисциплина',
            'D' => 'Группа',
            'E' => 'Студент (ФИО)',
            'F' => 'Метка о прохождении опроса',
            'G' => 'Посещаемость',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $row, $header);
        }

        // Стили для заголовков основной таблицы
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7E6E6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($headerStyle);
        
        $row++;

        // Заполняем данные основной таблицы со студентами
        $rowNumber = 1;
        foreach ($filteredStudents as $student) {
            $completionMark = $student['completed'] ? '✓' : '';

            $sheet->setCellValue('A' . $row, $rowNumber);
            $sheet->setCellValue('B' . $row, $student['speciality']);
            $sheet->setCellValue('C' . $row, $student['discipline_name']);
            $sheet->setCellValue('D' . $row, $student['group']);
            $sheet->setCellValue('E' . $row, $student['fullname']);
            $sheet->setCellValue('F' . $row, $completionMark);
            $sheet->setCellValue('G' . $row, $student['attendance']);

            // Стили для строк данных
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($dataStyle);
            
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $rowNumber++;
        }

        // Добавляем пустую строку перед таблицей с вопросами
        $row += 2;

        // Заголовки таблицы с вопросами
        $sheet->setCellValue('A' . $row, '№ п/п');
        $sheet->setCellValue('B' . $row, 'Вопрос');
        $sheet->setCellValue('C' . $row, 'Средний балл');
        
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($headerStyle);
        
        $row++;

        // Заполняем таблицу с вопросами
        $questionRowNumber = 1;
        foreach ($data['questions'] as $question) {
            $avgScore = $question['avg_score'] !== null ? number_format($question['avg_score'], 2) : '—';

            $sheet->setCellValue('A' . $row, $questionRowNumber);
            $sheet->setCellValue('B' . $row, $question['text']);
            $sheet->setCellValue('C' . $row, $avgScore);

            $questionDataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($questionDataStyle);
            
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $questionRowNumber++;
        }

        // Добавляем строку с общим средним баллом
        $overallAvgDisplay = $data['overall_avg'] !== null ? number_format($data['overall_avg'], 2) : '—';

        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'Общий средний балл');
        $sheet->setCellValue('C' . $row, $overallAvgDisplay);

        $overallAvgStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D4EDDA'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($overallAvgStyle);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Автоматическая ширина колонок
        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Закрываем сессию перед отправкой файла
        \core\session\manager::write_close();

        // Очищаем буфер вывода
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Отправляем файл
        $filename = 'survey_report_filtered_' . $surveyid . '_' . date('YmdHi') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Пересчёт средних баллов для отфильтрованных студентов.
     *
     * @param array $data Данные отчёта
     * @param int $surveyid ID анкеты
     * @param int $teacher_id ID преподавателя
     * @param string $discipline_id Код дисциплины
     * @return array Обновлённые данные с пересчитанными средними баллами
     */
    private function recalculateQuestionAverages(array $data, int $surveyid, int $teacher_id, string $discipline_id): array {
        global $DB;
        
        $responseTable = \local_cdo_education_scoring_get_table_name(
            'local_cdo_edu_score_resp',
            'local_cdo_education_scoring_response'
        );
        
        // Получаем ID отфильтрованных студентов (используем user_id из таблицы ответов)
        $filteredUserIds = [];
        foreach ($data['students'] as $student) {
            if ($student['completed'] && !empty($student['user_id'])) {
                $filteredUserIds[] = $student['user_id'];
            }
        }
        
        error_log('=== RECALCULATE DEBUG ===');
        error_log('Filtered students count: ' . count($data['students']));
        error_log('Filtered user IDs: ' . print_r($filteredUserIds, true));
        
        // Если нет студентов для расчёта, обнуляем средние баллы
        if (empty($filteredUserIds)) {
            foreach ($data['questions'] as &$question) {
                $question['avg_score'] = null;
                $question['response_count'] = 0;
            }
            $data['overall_avg'] = null;
            return $data;
        }
        
        // Пересчитываем средние баллы для каждого вопроса
        $totalScoreSum = 0;
        $totalScoreCount = 0;
        
        foreach ($data['questions'] as &$question) {
            if ($question['type'] !== 'scale') {
                $question['avg_score'] = null;
                $question['response_count'] = 0;
                continue;
            }
            
            // Формируем SQL-запрос с фильтрацией по userid
            list($inSql, $params) = $DB->get_in_or_equal($filteredUserIds, SQL_PARAMS_NAMED);
            
            $sql = "
                SELECT r.responsevalue
                FROM {" . $responseTable . "} r
                WHERE r.surveyid = :surveyid
                AND r.questionid = :questionid
                AND r.teacher_id = :teacher_id
                AND r.userid $inSql
            ";
            
            $params['surveyid'] = $surveyid;
            $params['questionid'] = $question['id'];
            $params['teacher_id'] = $teacher_id;
            
            if ($discipline_id) {
                $sql .= " AND r.discipline_id = :discipline_id";
                $params['discipline_id'] = $discipline_id;
            }
            
            error_log('SQL для вопроса ' . $question['id'] . ': ' . $sql);
            error_log('Params: ' . print_r($params, true));
            
            try {
                $responses = $DB->get_records_sql($sql, $params);
                $responseCount = count($responses);
                
                error_log('Вопрос ' . $question['id'] . ': найдено ответов - ' . $responseCount);
                
                if ($responseCount > 0) {
                    $sum = 0;
                    $validCount = 0;
                    foreach ($responses as $resp) {
                        $value = (float)$resp->responsevalue;
                        if ($value > 0) {
                            $sum += $value;
                            $validCount++;
                        }
                    }
                    $avgScore = $validCount > 0 ? round($sum / $validCount, 2) : null;
                } else {
                    $avgScore = null;
                }
                
                $question['avg_score'] = $avgScore;
                $question['response_count'] = $responseCount;
                
                if ($avgScore !== null) {
                    $totalScoreSum += $avgScore;
                    $totalScoreCount++;
                }
                
            } catch (\Exception $e) {
                error_log('Ошибка при пересчете среднего балла для вопроса ' . $question['id'] . ': ' . $e->getMessage());
                $question['avg_score'] = null;
                $question['response_count'] = 0;
            }
        }
        
        // Пересчитываем общий средний балл
        $data['overall_avg'] = $totalScoreCount > 0 ? round($totalScoreSum / $totalScoreCount, 2) : null;
        
        error_log('=== RECALCULATE RESULT ===');
        error_log('Overall avg: ' . var_export($data['overall_avg'], true));
        error_log('Total score sum: ' . $totalScoreSum . ', count: ' . $totalScoreCount);
        
        return $data;
    }

    /**
     * Получение посещаемости студента по дисциплине через DI сервис.
     *
     * @param string $userId ID студента
     * @param string $disciplineId Код дисциплины
     * @return string Процент посещаемости или пустая строка при ошибке
     */
    private function getStudentAttendance(string $userId, string $disciplineId): string {
        try {
            $requestParams = [
                'user_id' => $userId,
                'discipline_id' => $disciplineId,
            ];

            $this->debugLog('[Attendance] Запрос: user_id=' . $userId . ', discipline_id=' . $disciplineId);

            $options = di::get_instance()->get_request_options();
            $options->set_properties($requestParams);

            $requestResult = di::get_instance()
                ->get_request('get_percent_attendance')
                ->request($options)
                ->get_request_result()
                ->to_array();


            if ($requestResult['percent'] !== null) {
                return round($requestResult['percent'], 1) . '%';
            }

            return '';
        } catch (\Exception $e) {
            $this->debugLog('[Attendance] Ошибка: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Запись отладочной информации в файл.
     *
     * @param string $message Сообщение для записи
     */
    private function debugLog(string $message): void {
        global $CFG;
        $logFile = $CFG->dataroot . '/attendance_debug.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }
}
