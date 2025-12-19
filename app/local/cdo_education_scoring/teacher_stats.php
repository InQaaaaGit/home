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
 * Страница статистики сданных анкет по преподавателю.
 *
 * @package     local_cdo_education_scoring
 * @copyright   2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

global $PAGE, $OUTPUT, $USER, $DB;

require_login();

$plugin = 'local_cdo_education_scoring';
$title = get_string('pluginname', $plugin) . ' - Статистика';
$url = new moodle_url('/local/cdo_education_scoring/teacher_stats.php');

// Получаем ID преподавателя из параметра или используем текущего пользователя
$teacher_id = optional_param('teacher_id', $USER->id, PARAM_INT);

$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url($url);

// Проверяем права доступа (преподаватель или администратор)
$context = context_system::instance();
require_capability('local/cdo_education_scoring:viewstats', $context);

// Получение актуальных имен таблиц
$surveyTable = local_cdo_education_scoring_get_table_name(
    'local_cdo_edu_score_survey',
    'local_cdo_education_scoring_survey'
);
$responseTable = local_cdo_education_scoring_get_table_name(
    'local_cdo_edu_score_resp',
    'local_cdo_education_scoring_response'
);

// Получаем информацию о преподавателе
$teacher = $DB->get_record('user', ['id' => $teacher_id]);
$teacherName = $teacher ? local_cdo_education_scoring_format_fullname($teacher) : 'Преподаватель не найден';

// Получаем статистику
$sql = "
    SELECT 
        s.id AS surveyid,
        s.title AS survey_title,
        r.discipline_id,
        r.discipline_name,
        COUNT(DISTINCT r.userid) AS completed_count
    FROM {" . $responseTable . "} r
    INNER JOIN {" . $surveyTable . "} s ON s.id = r.surveyid
    WHERE r.teacher_id = :teacher_id
    GROUP BY s.id, s.title, r.discipline_id, r.discipline_name
    ORDER BY s.title, r.discipline_name
";

$stats = $DB->get_records_sql($sql, ['teacher_id' => $teacher_id]);

// Подсчитываем общее количество ответов
$totalResponses = 0;
foreach ($stats as $row) {
    $totalResponses += (int)$row->completed_count;
}

echo $OUTPUT->header();
?>

<style>
/* Расширяем основной контейнер страницы Moodle */
#page-content {
    max-width: 100% !important;
    padding: 0 !important;
}

#region-main {
    max-width: 100% !important;
    width: 100% !important;
}

.teacher-stats-container {
    max-width: 98% !important;
    width: 98% !important;
    margin: 0 auto;
    padding: 20px;
}

@media (min-width: 1600px) {
    .teacher-stats-container {
        max-width: 1800px !important;
    }
}

.teacher-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 20px 24px;
    border-radius: 8px 8px 0 0;
    margin-bottom: 0;
}

.teacher-info h2 {
    margin: 0 0 8px;
    font-size: 22px;
    font-weight: 600;
}

.teacher-info p {
    margin: 0;
    opacity: 0.9;
}

.stats-table-container {
    background: #fff;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.stats-table {
    width: 100%;
    border-collapse: collapse;
}

.stats-table th,
.stats-table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.stats-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stats-table tbody tr:hover {
    background: #f8f9fa;
}

.stats-table .col-num {
    width: 60px;
    text-align: center;
}

.stats-table .col-count {
    width: 180px;
    text-align: center;
}

.discipline-name {
    font-weight: 500;
}

.discipline-code {
    font-size: 12px;
    color: #6c757d;
    display: block;
}

.count-badge {
    display: inline-block;
    min-width: 40px;
    padding: 4px 12px;
    background: #e7f3ff;
    color: #0056b3;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
}

.total-row {
    background: #f8f9fa !important;
    font-weight: 600;
}

.total-row td {
    border-top: 2px solid #dee2e6;
}

.count-badge.total {
    background: #28a745;
    color: #fff;
}

.empty-state {
    padding: 60px 20px;
    text-align: center;
    background: #fff;
    border-radius: 0 0 8px 8px;
}

.empty-state .icon {
    font-size: 48px;
    margin-bottom: 16px;
}

.empty-state h3 {
    margin: 0 0 8px;
    font-size: 18px;
    color: #495057;
}

.empty-state p {
    margin: 0;
    color: #6c757d;
}

.page-actions {
    margin-top: 20px;
}

.btn-back {
    display: inline-block;
    padding: 10px 20px;
    background: #6c757d;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
}

.btn-back:hover {
    background: #545b62;
    color: #fff;
    text-decoration: none;
}
</style>

<div class="teacher-stats-container">
    <div class="teacher-info">
        <h2><?php echo htmlspecialchars($teacherName); ?></h2>
        <p>Дата выгрузки: <strong><?php echo date('d.m.Y H:i:s'); ?></strong></p>
        <p>Всего записей: <strong><?php echo count($stats); ?></strong></p>
    </div>

    <?php if (!empty($stats)): ?>
    <div class="stats-table-container">
        <table class="stats-table">
            <thead>
                <tr>
                    <th class="col-num">№</th>
                    <th>Анкета</th>
                    <th>Дисциплина</th>
                    <th class="col-count">Количество ответов</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($stats as $row): ?>
                <tr>
                    <td class="col-num"><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row->survey_title); ?></td>
                    <td>
                        <span class="discipline-name"><?php echo htmlspecialchars($row->discipline_name ?: '—'); ?></span>
                        <?php if ($row->discipline_id): ?>
                        <span class="discipline-code">(<?php echo htmlspecialchars($row->discipline_id); ?>)</span>
                        <?php endif; ?>
                    </td>
                    <td class="col-count">
                        <span class="count-badge"><?php echo (int)$row->completed_count; ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right; padding-right: 24px;">Итого ответов:</td>
                    <td class="col-count">
                        <span class="count-badge total"><?php echo $totalResponses; ?></span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="icon">📊</div>
        <h3>Нет данных</h3>
        <p>По данному преподавателю пока нет сданных анкет.</p>
    </div>
    <?php endif; ?>

    <div class="page-actions">
        <a href="javascript:history.back()" class="btn-back">← Назад</a>
    </div>
</div>

<?php
echo $OUTPUT->footer();
