<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * CLI скрипт для создания тестовых анкет.
 *
 * @package     local_cdo_education_scoring
 * @category    cli
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// Теперь получаем cli опции.
list($options, $unrecognized) = cli_get_params(
    [
        'help' => false,
        'count' => 7,
    ],
    [
        'h' => 'help',
        'c' => 'count',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help = "Создает тестовые анкеты для плагина CDO Education Scoring.

Options:
-h, --help            Вывести эту справку
-c, --count=NUMBER    Количество анкет для создания (по умолчанию: 7)

Example:
\$ sudo -u www-data /usr/bin/php local/cdo_education_scoring/cli/create_test_surveys.php --count=7
";
    echo $help;
    exit(0);
}

// Убеждаемся, что скрипт запущен из командной строки.
if (!CLI_SCRIPT) {
    cli_error('Этот скрипт должен запускаться из командной строки.');
}

cli_heading('Создание тестовых анкет');

$count = (int)$options['count'];
if ($count < 1 || $count > 50) {
    cli_error('Количество анкет должно быть от 1 до 50.');
}

global $DB, $USER;

// Создаем тестовые данные
$testSurveys = [
    [
        'title' => 'Оценка качества преподавания',
        'description' => 'Анкета для оценки качества преподавания учебных дисциплин',
        'durationdays' => 30,
        'isactive' => 1,
        'questions' => [
            ['text' => 'Насколько понятно преподаватель объясняет материал?', 'type' => 'scale'],
            ['text' => 'Использует ли преподаватель современные методы обучения?', 'type' => 'scale'],
            ['text' => 'Доступность материалов курса?', 'type' => 'scale'],
            ['text' => 'Какие аспекты преподавания вы хотели бы улучшить?', 'type' => 'text'],
        ],
    ],
    [
        'title' => 'Удовлетворенность образовательным процессом',
        'description' => 'Оценка удовлетворенности студентов образовательным процессом',
        'durationdays' => 45,
        'isactive' => 1,
        'questions' => [
            ['text' => 'Как вы оцениваете общую организацию учебного процесса?', 'type' => 'scale'],
            ['text' => 'Удобство расписания занятий?', 'type' => 'scale'],
            ['text' => 'Что бы вы хотели изменить в организации учебного процесса?', 'type' => 'text'],
        ],
    ],
    [
        'title' => 'Оценка качества учебных материалов',
        'description' => 'Анкета для оценки качества учебных материалов и ресурсов',
        'durationdays' => 20,
        'isactive' => 1,
        'questions' => [
            ['text' => 'Актуальность учебных материалов?', 'type' => 'scale'],
            ['text' => 'Полнота предоставленных материалов?', 'type' => 'scale'],
            ['text' => 'Удобство использования электронных ресурсов?', 'type' => 'scale'],
            ['text' => 'Ваши предложения по улучшению учебных материалов?', 'type' => 'text'],
        ],
    ],
    [
        'title' => 'Оценка работы деканата',
        'description' => 'Анкета для оценки работы деканата факультета',
        'durationdays' => 60,
        'isactive' => 0,
        'questions' => [
            ['text' => 'Скорость обработки заявлений и документов?', 'type' => 'scale'],
            ['text' => 'Внимательность сотрудников деканата?', 'type' => 'scale'],
            ['text' => 'Какие проблемы вы испытывали при обращении в деканат?', 'type' => 'text'],
        ],
    ],
    [
        'title' => 'Оценка библиотечного обслуживания',
        'description' => 'Анкета для оценки качества библиотечного обслуживания',
        'durationdays' => 25,
        'isactive' => 1,
        'questions' => [
            ['text' => 'Доступность необходимых книг и материалов?', 'type' => 'scale'],
            ['text' => 'Качество работы библиотечного персонала?', 'type' => 'scale'],
            ['text' => 'Удобство работы с электронным каталогом?', 'type' => 'scale'],
        ],
    ],
    [
        'title' => 'Оценка IT-инфраструктуры',
        'description' => 'Анкета для оценки IT-инфраструктуры и технической поддержки',
        'durationdays' => 40,
        'isactive' => 1,
        'questions' => [
            ['text' => 'Качество Wi-Fi в учебных корпусах?', 'type' => 'scale'],
            ['text' => 'Доступность компьютерных классов?', 'type' => 'scale'],
            ['text' => 'Скорость работы электронных систем?', 'type' => 'scale'],
            ['text' => 'Ваши замечания и предложения по IT-инфраструктуре?', 'type' => 'text'],
        ],
    ],
    [
        'title' => 'Общая оценка качества образования',
        'description' => 'Комплексная оценка качества образования в университете',
        'durationdays' => 90,
        'isactive' => 1,
        'questions' => [
            ['text' => 'Соответствие программы современным требованиям?', 'type' => 'scale'],
            ['text' => 'Возможности для практического применения знаний?', 'type' => 'scale'],
            ['text' => 'Подготовка к будущей карьере?', 'type' => 'scale'],
            ['text' => 'Общая оценка качества образования?', 'type' => 'scale'],
            ['text' => 'Ваши рекомендации по улучшению качества образования?', 'type' => 'text'],
        ],
    ],
];

$created = 0;
$timecreated = time();

cli_writeln("Создание {$count} тестовых анкет...\n");

for ($i = 0; $i < min($count, count($testSurveys)); $i++) {
    $surveyData = $testSurveys[$i];
    
    try {
        // Создаем анкету
        $survey = new stdClass();
        $survey->title = $surveyData['title'];
        $survey->description = $surveyData['description'];
        $survey->durationdays = $surveyData['durationdays'];
        $survey->isactive = $surveyData['isactive'];
        $survey->usercreated = $USER->id;
        $survey->timecreated = $timecreated;
        $survey->timemodified = $timecreated;
        
        $surveyid = $DB->insert_record('local_cdo_edu_score_survey', $survey);
        
        // Создаем вопросы
        $sortorder = 0;
        foreach ($surveyData['questions'] as $questionData) {
            $question = new stdClass();
            $question->surveyid = $surveyid;
            $question->questiontext = $questionData['text'];
            $question->questiontype = $questionData['type'];
            $question->sortorder = $sortorder++;
            $question->timecreated = $timecreated;
            $question->timemodified = $timecreated;
            
            $DB->insert_record('local_cdo_edu_score_quest', $question);
        }
        
        $created++;
        $status = $surveyData['isactive'] ? 'АКТИВНА' : 'НЕАКТИВНА';
        cli_writeln("  ✓ Создана анкета #{$surveyid}: \"{$surveyData['title']}\" ({$status}, вопросов: {$sortorder})");
        
    } catch (Exception $e) {
        cli_error("Ошибка при создании анкеты: " . $e->getMessage());
    }
}

cli_writeln("\n✓ Успешно создано анкет: {$created}");
cli_writeln("Для просмотра анкет перейдите на страницу плагина.");

exit(0);

