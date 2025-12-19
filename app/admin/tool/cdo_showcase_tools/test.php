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
 * Test page for CDO Showcase Tools letter grades API.
 *
 * @package     tool_cdo_showcase_tools
 * @copyright   2024 Your Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Ensure user is logged in and has proper permissions.
require_login();
$context = context_system::instance();
require_capability('tool/cdo_showcase_tools:view', $context);

// Set up the page.
$PAGE->set_url('/admin/tool/cdo_showcase_tools/test.php');
$PAGE->set_context($context);
$PAGE->set_title('Тестирование Letter Grades API');
$PAGE->set_heading('Тестирование Letter Grades API');
$PAGE->set_pagelayout('admin');

// Handle form submission.
$testresults = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseid = optional_param('courseid', 0, PARAM_INT);
    $userid = optional_param('userid', 0, PARAM_INT);
    
    if ($courseid > 0) {
        try {
            // Test the external API function.
            $result = \tool_cdo_showcase_tools\external\get_course_letter_grades::execute($courseid, $userid);
            $testresults = $result;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = 'Пожалуйста, введите корректный ID курса';
    }
}

// Get available courses for dropdown.
$courses = get_courses('all', 'c.sortorder ASC', 'c.id, c.fullname, c.shortname');

// Output page header.
echo $OUTPUT->header();

// Page heading.
echo $OUTPUT->heading('Тестирование Letter Grades API');

// Description.
echo html_writer::div(
    'Эта страница позволяет протестировать external API функцию получения буквенных оценок курса.',
    'alert alert-info'
);

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Тестирование API</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo $PAGE->url; ?>">
                        <div class="form-group mb-3">
                            <label for="courseid">Выберите курс:</label>
                            <select name="courseid" id="courseid" class="form-control" required>
                                <option value="">-- Выберите курс --</option>
                                <?php foreach ($courses as $course): ?>
                                    <?php if ($course->id > 1): // Skip site course ?>
                                        <option value="<?php echo $course->id; ?>" 
                                                <?php echo (optional_param('courseid', 0, PARAM_INT) == $course->id) ? 'selected' : ''; ?>>
                                            <?php echo format_string($course->fullname) . ' (' . format_string($course->shortname) . ')'; ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="userid">ID пользователя (опционально):</label>
                            <input type="number" name="userid" id="userid" class="form-control" 
                                   value="<?php echo optional_param('userid', 0, PARAM_INT); ?>"
                                   placeholder="0 для всех пользователей">
                            <small class="form-text text-muted">
                                Оставьте 0 для получения оценок всех пользователей курса
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-play"></i> Выполнить тест
                        </button>
                    </form>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger mt-3">
                    <strong>Ошибка:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Информация об API</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Функция:</dt>
                        <dd class="col-sm-8"><code>tool_cdo_showcase_tools_get_course_letter_grades</code></dd>
                        
                        <dt class="col-sm-4">Параметры:</dt>
                        <dd class="col-sm-8">
                            <code>courseid</code> (int, обязательный)<br>
                            <code>userid</code> (int, опциональный)
                        </dd>
                        
                        <dt class="col-sm-4">Права:</dt>
                        <dd class="col-sm-8"><code>tool/cdo_showcase_tools:view</code></dd>
                        
                        <dt class="col-sm-4">Тип:</dt>
                        <dd class="col-sm-8">read (только чтение)</dd>
                    </dl>

                    <h6>Формат ответа:</h6>
                    <pre><code>{
  "gradescales": [
    {
      "minimum": "90,00 %",
      "maximum": "100,00 %",
      "gradename": "Отлично",
      "lettercode": "A",
      "gradevalue": 95.0
    },
    {
      "minimum": "87,00 %",
      "maximum": "89,99 %",
      "gradename": "Хорошо",
      "lettercode": "B",
      "gradevalue": 88.5
    }
  ],
  "courseid": 2,
  "userid": 0,
  "warnings": []
}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <?php if ($testresults): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fa fa-check"></i> Результаты теста</h5>
                    </div>
                    <div class="card-body">
                        <h6>Параметры запроса:</h6>
                        <ul>
                            <li><strong>Course ID:</strong> <?php echo optional_param('courseid', 0, PARAM_INT); ?></li>
                            <li><strong>User ID:</strong> <?php echo optional_param('userid', 0, PARAM_INT) ?: 'Все пользователи'; ?></li>
                        </ul>

                        <h6>Шкалы оценок:</h6>
                        <?php if (!empty($testresults['gradescales'])): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Минимум</th>
                                            <th>Максимум</th>
                                            <th>Наименование оценки</th>
                                            <th>Буквенный код</th>
                                            <th>Числовое значение</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($testresults['gradescales'] as $scale): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($scale['minimum']); ?></td>
                                                <td><?php echo htmlspecialchars($scale['maximum']); ?></td>
                                                <td>
                                                    <span class="badge badge-success">
                                                        <?php echo htmlspecialchars($scale['gradename']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-primary">
                                                        <?php echo htmlspecialchars($scale['lettercode'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo isset($scale['gradevalue']) ? number_format($scale['gradevalue'], 2) : 'N/A'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Шкалы оценок не найдены для указанных параметров.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($testresults['warnings'])): ?>
                            <h6>Предупреждения:</h6>
                            <div class="alert alert-warning">
                                <ul class="mb-0">
                                    <?php foreach ($testresults['warnings'] as $warning): ?>
                                        <li>
                                            <strong><?php echo htmlspecialchars($warning['warningcode']); ?>:</strong>
                                            <?php echo htmlspecialchars($warning['message']); ?>
                                            (Item: <?php echo htmlspecialchars($warning['item']); ?>, 
                                             ID: <?php echo $warning['itemid']; ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <h6>Полный JSON ответ:</h6>
                        <pre class="bg-light p-3"><code><?php echo json_encode($testresults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></code></pre>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>JavaScript Test</h5>
                </div>
                <div class="card-body">
                    <p>Тест через AJAX (откройте консоль браузера для просмотра результатов):</p>
                    <button id="ajax-test-btn" class="btn btn-info">
                        <i class="fa fa-code"></i> Тест через AJAX
                    </button>
                    <div id="ajax-results" class="mt-3" style="display:none;">
                        <h6>AJAX результаты:</h6>
                        <pre id="ajax-output" class="bg-light p-3"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
require(['core/ajax'], function(Ajax) {
    document.getElementById('ajax-test-btn').addEventListener('click', function() {
        var courseid = document.getElementById('courseid').value;
        var userid = document.getElementById('userid').value || 0;
        
        if (!courseid) {
            alert('Пожалуйста, выберите курс');
            return;
        }
        
        console.log('Отправка AJAX запроса с параметрами:', {courseid: courseid, userid: userid});
        
        var request = Ajax.call([{
            methodname: 'tool_cdo_showcase_tools_get_course_letter_grades',
            args: {
                courseid: parseInt(courseid),
                userid: parseInt(userid)
            }
        }]);

        request[0].done(function(response) {
            console.log('AJAX ответ получен:', response);
            console.log('Шкалы оценок:', response.gradescales);
            document.getElementById('ajax-results').style.display = 'block';
            document.getElementById('ajax-output').textContent = JSON.stringify(response, null, 2);
        }).fail(function(error) {
            console.error('AJAX ошибка:', error);
            document.getElementById('ajax-results').style.display = 'block';
            document.getElementById('ajax-output').textContent = 'Ошибка: ' + JSON.stringify(error, null, 2);
        });
    });
});
</script>

<?php

// Output page footer.
echo $OUTPUT->footer(); 