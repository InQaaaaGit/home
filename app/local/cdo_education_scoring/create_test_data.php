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
 * Веб-скрипт для создания тестовых анкет.
 * 
 * ⚠️ ВНИМАНИЕ: Этот файл предназначен только для разработки!
 * Удалите его перед развертыванием в продакшене.
 *
 * @package     local_cdo_education_scoring
 * @category    admin
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

global $PAGE, $OUTPUT, $USER, $DB, $CFG;

require_login();
require_capability('local/cdo_education_scoring:manage', context_system::instance());

$plugin = 'local_cdo_education_scoring';
$title = 'Создание тестовых данных - ' . get_string('pluginname', $plugin);
$url = new moodle_url('/local/cdo_education_scoring/create_test_data.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url($url);

$created = 0;
$errors = [];

if (optional_param('create', false, PARAM_BOOL)) {
    require_sesskey();
    
    // Тестовые данные
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
    
    $timecreated = time();
    
    foreach ($testSurveys as $surveyData) {
        try {
            // Проверяем, не существует ли уже такая анкета
            $existing = $DB->get_record('local_cdo_edu_score_survey', ['title' => $surveyData['title']]);
            if ($existing) {
                continue; // Пропускаем, если уже существует
            }
            
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
            
        } catch (Exception $e) {
            $errors[] = "Ошибка при создании анкеты '{$surveyData['title']}': " . $e->getMessage();
        }
    }
    
    if ($created > 0) {
        redirect($url, "Успешно создано анкет: {$created}", null, \core\output\notification::NOTIFY_SUCCESS);
    } elseif (empty($errors)) {
        redirect($url, "Все анкеты уже существуют", null, \core\output\notification::NOTIFY_INFO);
    } else {
        redirect($url, "Ошибки: " . implode(', ', $errors), null, \core\output\notification::NOTIFY_ERROR);
    }
}

echo $OUTPUT->header();

echo $OUTPUT->heading('Создание тестовых анкет');

echo html_writer::start_div('alert alert-info');
echo html_writer::tag('p', 'Этот скрипт создаст 7 тестовых анкет с различными вопросами.');
echo html_writer::tag('p', 'Если анкета с таким названием уже существует, она будет пропущена.');
echo html_writer::end_div();

echo html_writer::start_div('mt-3');
echo html_writer::tag('h4', 'Анкеты, которые будут созданы:');
echo html_writer::start_tag('ul');
echo html_writer::tag('li', 'Оценка качества преподавания (активна, 4 вопроса)');
echo html_writer::tag('li', 'Удовлетворенность образовательным процессом (активна, 3 вопроса)');
echo html_writer::tag('li', 'Оценка качества учебных материалов (активна, 4 вопроса)');
echo html_writer::tag('li', 'Оценка работы деканата (неактивна, 3 вопроса)');
echo html_writer::tag('li', 'Оценка библиотечного обслуживания (активна, 3 вопроса)');
echo html_writer::tag('li', 'Оценка IT-инфраструктуры (активна, 4 вопроса)');
echo html_writer::tag('li', 'Общая оценка качества образования (активна, 5 вопросов)');
echo html_writer::end_tag('ul');
echo html_writer::end_div();

echo html_writer::start_tag('form', ['method' => 'post', 'class' => 'mt-4']);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'create', 'value' => '1']);
echo html_writer::tag('button', 'Создать тестовые анкеты', [
    'type' => 'submit',
    'class' => 'btn btn-primary btn-lg',
]);
echo html_writer::end_tag('form');

echo html_writer::start_div('mt-4');
echo html_writer::link(
    new moodle_url('/local/cdo_education_scoring/index.php'),
    '← Вернуться к списку анкет',
    ['class' => 'btn btn-secondary']
);
echo html_writer::end_div();

echo $OUTPUT->footer();

