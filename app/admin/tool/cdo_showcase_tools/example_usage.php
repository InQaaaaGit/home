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
 * Example usage of the letter grades external API.
 *
 * This file demonstrates how to use the external API function both
 * from within Moodle and via web service calls.
 *
 * @package     tool_cdo_showcase_tools
 * @copyright   2024 Your Organization
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

// Ensure user is logged in.
require_login();

$PAGE->set_url('/admin/tool/cdo_showcase_tools/example_usage.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Letter Grades API Example');
$PAGE->set_heading('Letter Grades API Usage Examples');

echo $OUTPUT->header();
echo $OUTPUT->heading('CDO Showcase Tools - Letter Grades API Examples');

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h3>1. Internal API Usage (PHP)</h3>
            <p>Пример использования API изнутри Moodle:</p>
            <pre><code class="language-php"><?php echo htmlspecialchars('<?php
// Использование external API изнутри Moodle
use tool_cdo_showcase_tools\external\get_course_letter_grades;

$courseid = 2; // ID курса
$userid = 0;   // 0 для всех пользователей

try {
    $result = get_course_letter_grades::execute($courseid, $userid);
    
    echo "Course ID: {$result[\'courseid\']}, User ID: {$result[\'userid\']}\n";
    
    foreach ($result[\'gradescales\'] as $scale) {
        echo "Grade: {$scale[\'gradename\']} ({$scale[\'lettercode\']})\n";
        echo "Range: {$scale[\'minimum\']} - {$scale[\'maximum\']}\n";
        echo "Value: {$scale[\'gradevalue\']}\n\n";
    }
    
    if (!empty($result[\'warnings\'])) {
        foreach ($result[\'warnings\'] as $warning) {
            echo "Warning: {$warning[\'message\']}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>'); ?></code></pre>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h3>2. Web Service Call (JavaScript/AJAX)</h3>
            <p>Пример вызова через AJAX:</p>
            <pre><code class="language-javascript"><?php echo htmlspecialchars('// JavaScript example using Moodle AJAX
require([\'core/ajax\'], function(Ajax) {
    var request = Ajax.call([{
        methodname: \'tool_cdo_showcase_tools_get_course_letter_grades\',
        args: {
            courseid: 2,
            userid: 0
        }
    }]);

    request[0].done(function(response) {
        console.log(\'Grade scales:\', response.gradescales);
        console.log(`Course ID: ${response.courseid}, User ID: ${response.userid}`);
        
        response.gradescales.forEach(function(scale) {
            console.log(`${scale.gradename} (${scale.lettercode}): ${scale.minimum} - ${scale.maximum}`);
        });
        
        if (response.warnings.length > 0) {
            console.log(\'Warnings:\', response.warnings);
        }
    }).fail(function(error) {
        console.error(\'Error:\', error);
    });
});'); ?></code></pre>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h3>3. REST API Call (cURL)</h3>
            <p>Пример вызова через REST API:</p>
            <pre><code class="language-bash"><?php echo htmlspecialchars('# Сначала получите токен авторизации
curl -X POST "https://your-moodle-site.com/login/token.php" \
     -d "username=your_username" \
     -d "password=your_password" \
     -d "service=cdo_showcase_tools_service"

# Затем используйте токен для вызова API
curl -X POST "https://your-moodle-site.com/webservice/rest/server.php" \
     -d "wstoken=YOUR_TOKEN_HERE" \
     -d "wsfunction=tool_cdo_showcase_tools_get_course_letter_grades" \
     -d "moodlewsrestformat=json" \
     -d "courseid=2" \
     -d "userid=0"'); ?></code></pre>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h3>4. Response Format</h3>
            <p>Пример ответа API:</p>
            <pre><code class="language-json"><?php echo htmlspecialchars('{
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
        },
        {
            "minimum": "70,00 %",
            "maximum": "86,99 %",
            "gradename": "УДВ",
            "lettercode": "C",
            "gradevalue": 78.5
        }
    ],
    "courseid": 2,
    "userid": 0,
    "warnings": []
}'); ?></code></pre>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h3>5. Manager Class Usage</h3>
            <p>Использование через manager class:</p>
            <pre><code class="language-php"><?php echo htmlspecialchars('<?php
// Использование через manager class
use tool_cdo_showcase_tools\manager;

$manager = manager::get_instance();
$courseid = 2;
$userid = 0;

try {
    $gradescales = $manager->get_course_letter_grades($courseid, $userid);
    
    foreach ($gradescales as $scale) {
        echo "Grade: {$scale[\'gradename\']} ({$scale[\'lettercode\']})\n";
        echo "Range: {$scale[\'minimum\']} - {$scale[\'maximum\']}\n\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>'); ?></code></pre>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <h4>Настройка Web Services</h4>
        <p>Для использования через REST API необходимо:</p>
        <ol>
            <li>Включить Web Services: <strong>Администрирование → Дополнительные возможности → Web services</strong></li>
            <li>Создать пользователя для API</li>
            <li>Назначить роль с правами <code>tool/cdo_showcase_tools:view</code></li>
            <li>Активировать сервис <code>cdo_showcase_tools_service</code></li>
            <li>Создать токен для пользователя</li>
        </ol>
    </div>
</div>

<?php

echo $OUTPUT->footer(); 