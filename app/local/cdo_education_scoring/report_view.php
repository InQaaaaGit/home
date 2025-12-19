<?php
/**
 * Страница просмотра отчёта по анкете.
 *
 * @package    local_cdo_education_scoring
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_cdo_education_scoring\service\report_export_service;

$surveyid = required_param('surveyid', PARAM_INT);
$teacherid = required_param('teacher_id', PARAM_INT);
$disciplineid = optional_param('discipline_id', null, PARAM_TEXT);

// Проверка авторизации
require_login();
$context = context_system::instance();
require_capability('local/cdo_education_scoring:manage', $context);

// Настройка страницы
$PAGE->set_context($context);
$PAGE->set_url('/local/cdo_education_scoring/report_view.php', [
    'surveyid' => $surveyid,
    'teacher_id' => $teacherid,
    'discipline_id' => $disciplineid,
]);
$PAGE->set_title(get_string('pluginname', 'local_cdo_education_scoring') . ' - Просмотр отчёта');
$PAGE->set_heading(get_string('pluginname', 'local_cdo_education_scoring'));
$PAGE->set_pagelayout('base'); // Layout без ограничений по ширине

// Получаем данные отчёта
try {
    $exportService = new report_export_service();
    $data = $exportService->get_report_data($surveyid, $teacherid, $disciplineid);
} catch (\Exception $e) {
    throw new moodle_exception('Ошибка получения данных отчёта: ' . $e->getMessage());
}

// Получаем список дисциплин и групп
$disciplines = [];
$groups = [];
try {
    global $DB;
    $responseTable = \local_cdo_education_scoring_get_table_name(
        'local_cdo_edu_score_resp',
        'local_cdo_education_scoring_response'
    );
    
    // Получаем дисциплины
    $sql = "
        SELECT DISTINCT r.discipline_id, r.discipline_name
        FROM {" . $responseTable . "} r
        WHERE r.surveyid = :surveyid
        AND r.teacher_id = :teacher_id
        AND r.discipline_id IS NOT NULL
        AND r.discipline_name IS NOT NULL
        ORDER BY r.discipline_name
    ";
    
    $disciplines = $DB->get_records_sql($sql, [
        'surveyid' => $surveyid,
        'teacher_id' => $teacherid,
    ]);
    
    // Получаем группы из данных отчёта
    $groupsMap = [];
    foreach ($data['students'] as $student) {
        if (!empty($student['group'])) {
            $groupsMap[$student['group']] = $student['group'];
        }
    }
    ksort($groupsMap);
    $groups = array_values($groupsMap);
    
} catch (\Exception $e) {
    $disciplines = [];
    $groups = [];
}

// URL для скачивания Excel
$exportUrl = new moodle_url('/local/cdo_education_scoring/export.php', [
    'surveyid' => $surveyid,
    'teacher_id' => $teacherid,
    'discipline_id' => $disciplineid,
]);

// Вывод страницы
echo $OUTPUT->header();
?>

<style>
    /* Агрессивное расширение всех контейнеров Moodle на всю ширину */
    body,
    body #page,
    body #page.drawers,
    body.drawer-open-left #page,
    body.drawer-open-right #page,
    #page-wrapper,
    #page,
    .pagelayout-report #page {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
    }
    
    #page-content,
    #page-content > div,
    .page-content-wrapper {
        max-width: none !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    #region-main-box,
    #region-main-box > div,
    .region-main-box {
        max-width: none !important;
        width: 100% !important;
        margin: 0 !important;
    }
    
    #region-main,
    #region-main > div,
    .region_main,
    [role="main"] {
        max-width: none !important;
        width: 100% !important;
        padding: 10px 15px !important;
        margin: 0 !important;
    }
    
    /* Контейнер отчета */
    .report-container {
        max-width: none !important;
        width: calc(100% - 20px) !important;
        margin: 0 10px !important;
        padding: 20px !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        box-sizing: border-box !important;
    }
    
    /* Убираем все container классы Moodle */
    .container,
    .container-fluid {
        max-width: none !important;
        width: 100% !important;
    }

    .report-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
        color: #fff;
        padding: 24px 32px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(30, 58, 95, 0.3);
        width: 100%;
        box-sizing: border-box;
    }

    .filters-section {
        background: #fff;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 100%;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        color: #495057;
        font-size: 0.95rem;
        margin: 0;
        min-width: 220px;
        flex-shrink: 0;
    }

    .filter-icon {
        font-size: 1.2rem;
    }

    .filter-select {
        flex: 1;
        max-width: 400px;
        padding: 10px 16px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 0.95rem;
        background: #fff;
        color: #495057;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-select:hover {
        border-color: #adb5bd;
    }

    .filter-select:focus {
        outline: none;
        border-color: #1e3a5f;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
    }

    .filter-actions {
        display: flex;
        justify-content: flex-end;
        padding-top: 8px;
    }

    .btn-filter {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
        color: #fff;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(30, 58, 95, 0.3);
    }

    .btn-filter:hover {
        background: linear-gradient(135deg, #2d5a87 0%, #3d6a97 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 58, 95, 0.4);
    }

    .report-header h1 {
        margin: 0 0 20px 0;
        font-size: 1.75rem;
        font-weight: 600;
        color: #fff;
    }

    .report-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .report-meta-item {
        background: rgba(255, 255, 255, 0.1);
        padding: 12px 16px;
        border-radius: 8px;
        backdrop-filter: blur(10px);
    }

    .report-meta-label {
        font-size: 0.85rem;
        opacity: 0.8;
        margin-bottom: 4px;
    }

    .report-meta-value {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .report-actions {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        width: 100%;
    }

    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: #fff;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
    }

    .btn-export:hover {
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        color: #fff;
        text-decoration: none;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8f9fa;
        color: #495057;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 1px solid #dee2e6;
    }

    .btn-back:hover {
        background: #e9ecef;
        color: #212529;
        text-decoration: none;
    }

    .report-section {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
        overflow: visible;
        width: 100%;
    }

    .report-section-header {
        background: #f8f9fa;
        padding: 16px 24px;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        font-size: 1.1rem;
        color: #1e3a5f;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: auto;
    }

    .report-table th {
        background: #e7e6e6;
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }

    .report-table th.center {
        text-align: center;
    }

    .report-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.95rem;
        color: #212529;
    }

    .report-table td.center {
        text-align: center;
    }

    .report-table tbody tr:hover {
        background: #f8f9fa;
    }

    .report-table tbody tr:last-child td {
        border-bottom: none;
    }

    .completion-mark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        background: #d4edda;
        color: #155724;
        border-radius: 50%;
        font-weight: bold;
    }

    .completion-mark.empty {
        background: #f8d7da;
        color: #721c24;
    }

    .avg-score {
        font-weight: 600;
        color: #1e3a5f;
    }

    .overall-avg-row {
        background: #d4edda !important;
    }

    .overall-avg-row td {
        font-weight: 600;
        color: #155724;
        border-bottom: none !important;
    }

    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #6c757d;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .statistics-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
        width: 100%;
    }

    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 4px;
    }

    .stat-card-label {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .stat-card.success .stat-card-value {
        color: #28a745;
    }

    .stat-card.warning .stat-card-value {
        color: #ffc107;
    }

    /* Табы */
    .tabs-container {
        margin-bottom: 24px;
        width: 100%;
    }

    .tabs-nav {
        display: flex;
        gap: 4px;
        background: #f1f3f4;
        padding: 4px;
        border-radius: 12px;
        margin-bottom: 0;
    }

    .tab-btn {
        flex: 1;
        padding: 14px 24px;
        border: none;
        background: transparent;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 500;
        color: #5f6368;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .tab-btn:hover {
        background: rgba(255, 255, 255, 0.5);
        color: #1e3a5f;
    }

    .tab-btn.active {
        background: #fff;
        color: #1e3a5f;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .tab-btn .tab-icon {
        font-size: 1.1rem;
    }

    .tab-btn .tab-badge {
        background: #e8f0fe;
        color: #1a73e8;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .tab-btn.active .tab-badge {
        background: #1a73e8;
        color: #fff;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Пагинация */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        flex-wrap: wrap;
        gap: 16px;
        width: 100%;
        box-sizing: border-box;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .pagination-info strong {
        color: #1e3a5f;
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pagination-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border: 1px solid #dee2e6;
        background: #fff;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        color: #495057;
        transition: all 0.2s ease;
    }

    .pagination-btn:hover:not(:disabled) {
        background: #e9ecef;
        border-color: #adb5bd;
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-btn.active {
        background: #1e3a5f;
        color: #fff;
        border-color: #1e3a5f;
    }

    .pagination-pages {
        display: flex;
        gap: 4px;
    }

    .pagination-select {
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 0.9rem;
        background: #fff;
        color: #495057;
        cursor: pointer;
    }

    .pagination-select:focus {
        outline: none;
        border-color: #1e3a5f;
    }

    .page-size-selector {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .page-size-selector label {
        color: #6c757d;
        font-size: 0.9rem;
    }

    @media print {
        .report-actions, .btn-export, .btn-back, .tabs-nav, .pagination-container {
            display: none !important;
        }
        
        .report-container {
            max-width: 100%;
            padding: 0;
        }
        
        .report-section {
            box-shadow: none;
            border: 1px solid #dee2e6;
        }
        
        .tab-content {
            display: block !important;
            page-break-inside: avoid;
            margin-bottom: 24px;
        }
        
        .tabs-container {
            display: none;
        }

        .report-table tbody tr {
            display: table-row !important;
        }
    }
</style>

<div class="report-container">
    <!-- Шапка отчёта -->
    <div class="report-header">
        <h1>📊 <?php echo htmlspecialchars($data['survey']['title']); ?>
            <?php if ($disciplineid): ?>
                <span style="display: inline-block; background: rgba(255, 255, 255, 0.2); padding: 6px 12px; border-radius: 6px; font-size: 0.75em; margin-left: 10px;">
                    🔍 Фильтр активен
                </span>
            <?php endif; ?>
        </h1>
        <div class="report-meta">
            <div class="report-meta-item">
                <div class="report-meta-label">Дата выгрузки</div>
                <div class="report-meta-value"><?php echo htmlspecialchars($data['export_date']); ?></div>
            </div>
            <div class="report-meta-item">
                <div class="report-meta-label">Преподаватель</div>
                <div class="report-meta-value"><?php echo htmlspecialchars($data['teacher']['name']); ?></div>
            </div>
            <?php if ($disciplineid): ?>
            <div class="report-meta-item" style="background: rgba(255, 255, 255, 0.25);">
                <div class="report-meta-label">🔍 Дисциплина</div>
                <div class="report-meta-value">
                    <?php 
                    // Находим название дисциплины по ID
                    $selectedDisciplineName = '';
                    foreach ($disciplines as $disc) {
                        if ($disc->discipline_id === $disciplineid) {
                            $selectedDisciplineName = $disc->discipline_name;
                            break;
                        }
                    }
                    echo htmlspecialchars($selectedDisciplineName ?: $disciplineid);
                    ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="report-meta-item">
                <div class="report-meta-label">Всего студентов</div>
                <div class="report-meta-value"><?php echo $data['statistics']['total_students']; ?></div>
            </div>
            <div class="report-meta-item">
                <div class="report-meta-label">Получено ответов</div>
                <div class="report-meta-value"><?php echo $data['statistics']['completed_count']; ?> чел. / <?php echo $data['statistics']['completed_percent']; ?>%</div>
            </div>
        </div>
    </div>

    <!-- Список дисциплин и групп -->
    <?php if (!empty($disciplines) || !empty($groups)): ?>
    <div class="filters-section">
        <?php if (!empty($disciplines)): ?>
        <div class="filter-group">
            <label for="discipline-list" class="filter-label">
                <span class="filter-icon">📚</span>
                Дисциплины в отчёте (<?php echo count($disciplines); ?>):
            </label>
            <select id="discipline-list" class="filter-select" onchange="checkFilterSelection()">
                <option value="">-- Выберите дисциплину --</option>
                <?php foreach ($disciplines as $disc): ?>
                <option value="<?php echo htmlspecialchars($disc->discipline_id); ?>"
                    <?php echo ($disciplineid && $disciplineid === $disc->discipline_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($disc->discipline_name); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($groups)): ?>
        <div class="filter-group">
            <label for="group-list" class="filter-label">
                <span class="filter-icon">👥</span>
                Группы в отчёте (<?php echo count($groups); ?>):
            </label>
            <select id="group-list" class="filter-select" onchange="checkFilterSelection()">
                <option value="">-- Выберите группу --</option>
                <?php foreach ($groups as $group): ?>
                <option value="<?php echo htmlspecialchars($group); ?>">
                    <?php echo htmlspecialchars($group); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        
        <div class="filter-group">
            <label for="attendance-min" class="filter-label">
                <span class="filter-icon">📊</span>
                Минимальная посещаемость (%):
            </label>
            <div style="display: flex; flex-direction: column; gap: 4px; flex: 1; max-width: 400px;">
                <input 
                    type="number" 
                    id="attendance-min" 
                    class="filter-select" 
                    min="0" 
                    max="100" 
                    step="1" 
                    placeholder="Например: 70"
                    onchange="checkFilterSelection()"
                    oninput="checkFilterSelection()"
                    style="max-width: 200px; margin: 0;"
                />
                <small style="color: #6c757d; font-size: 0.85rem;">
                    💡 Будут отобраны только студенты с посещаемостью не ниже указанного значения
                </small>
            </div>
        </div>
        
        <div class="filter-actions" id="filter-actions" style="display: none;">
            <!--<button class="btn-filter" onclick="applyViewFilter()" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); margin-right: 10px;">
                🔍 Применить фильтры к отчету
            </button>-->
            <button class="btn-filter" onclick="applyFilter()">
                📥 Скачать отчет по фильтрам
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Кнопки действий -->
    <div class="report-actions">
        <a href="<?php echo $exportUrl; ?>" class="btn-export">
            📥 Скачать Excel
        </a>
        <?php if ($disciplineid): ?>
        <a href="<?php echo new moodle_url('/local/cdo_education_scoring/report_view.php', [
            'surveyid' => $surveyid,
            'teacher_id' => $teacherid
        ]); ?>" class="btn-back" style="background: #ffc107; color: #000; border-color: #ffc107;">
            ✖️ Сбросить фильтры
        </a>
        <?php endif; ?>
        <a href="<?php echo new moodle_url('/local/cdo_education_scoring/index.php'); ?>" class="btn-back">
            ← Назад
        </a>
        <button onclick="window.print()" class="btn-back">
            🖨️ Печать
        </button>
    </div>

    <!-- Статистика -->
    <div class="statistics-cards">
        <div class="stat-card">
            <div class="stat-card-value"><?php echo $data['statistics']['total_students']; ?></div>
            <div class="stat-card-label">Всего студентов</div>
        </div>
        <div class="stat-card success">
            <div class="stat-card-value"><?php echo $data['statistics']['completed_count']; ?></div>
            <div class="stat-card-label">Прошли опрос</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-value"><?php echo $data['statistics']['completed_percent']; ?>%</div>
            <div class="stat-card-label">Процент прохождения</div>
        </div>
        <?php if ($data['overall_avg'] !== null): ?>
        <div class="stat-card success">
            <div class="stat-card-value"><?php echo number_format($data['overall_avg'], 2); ?></div>
            <div class="stat-card-label">Общий средний балл</div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Табы -->
    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="students" onclick="switchTab('students')">
                <span class="tab-icon">👥</span>
                <span>Список студентов</span>
                <span class="tab-badge"><?php echo count($data['students']); ?></span>
            </button>
            <button class="tab-btn" data-tab="questions" onclick="switchTab('questions')">
                <span class="tab-icon">📊</span>
                <span>Средние баллы</span>
                <span class="tab-badge"><?php echo count($data['questions']); ?></span>
            </button>
        </div>
    </div>

    <!-- Таблица студентов -->
    <div id="tab-students" class="tab-content active">
        <div class="report-section">
            <div class="report-section-header">
                👥 Список студентов
            </div>
            <?php if (!empty($data['students'])): ?>
            <div style="overflow-x: auto; width: 100%;">
            <table class="report-table" id="students-table">
                <thead>
                    <tr>
                        <th class="center" style="width: 60px;">№ п/п</th>
                        <th>Направление подготовки/Специальность</th>
                        <th>Дисциплина</th>
                        <th>Группа</th>
                        <th>Студент (ФИО)</th>
                        <th class="center">Прохождение</th>
                        <th class="center">Посещаемость</th>
                    </tr>
                </thead>
                <tbody id="students-tbody">
                    <?php $rowNum = 1; foreach ($data['students'] as $student): ?>
                    <tr data-row="<?php echo $rowNum; ?>">
                        <td class="center"><?php echo $rowNum++; ?></td>
                        <td><?php echo htmlspecialchars($student['speciality']); ?></td>
                        <td><?php echo htmlspecialchars($student['discipline_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['group']); ?></td>
                        <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                        <td class="center">
                            <?php if ($student['completed']): ?>
                                <span class="completion-mark">✓</span>
                            <?php else: ?>
                                <span class="completion-mark empty">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="center"><?php echo htmlspecialchars($student['attendance']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <!-- Пагинация -->
            <div class="pagination-container" id="pagination-container">
                <div class="pagination-info">
                    Показано <strong><span id="showing-from">1</span>-<span id="showing-to">25</span></strong> из <strong><?php echo count($data['students']); ?></strong> студентов
                </div>
                <div class="pagination-controls">
                    <div class="page-size-selector">
                        <label for="page-size">На странице:</label>
                        <select id="page-size" class="pagination-select" onchange="changePageSize(this.value)">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Все</option>
                        </select>
                    </div>
                    <button class="pagination-btn" onclick="goToPage(1)" id="btn-first" title="Первая страница">⟪</button>
                    <button class="pagination-btn" onclick="goToPage(currentPage - 1)" id="btn-prev" title="Предыдущая">←</button>
                    <div class="pagination-pages" id="pagination-pages"></div>
                    <button class="pagination-btn" onclick="goToPage(currentPage + 1)" id="btn-next" title="Следующая">→</button>
                    <button class="pagination-btn" onclick="goToPage(totalPages)" id="btn-last" title="Последняя страница">⟫</button>
                </div>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <p>Нет данных о студентах</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Таблица вопросов -->
    <div id="tab-questions" class="tab-content">
        <div class="report-section">
            <div class="report-section-header">
                📊 Вопросы и средние баллы
            </div>
            <?php if (!empty($data['questions'])): ?>
            <div style="overflow-x: auto; width: 100%;">
            <table class="report-table">
                <thead>
                    <tr>
                        <th class="center" style="width: 60px;">№ п/п</th>
                        <th>Вопрос</th>
                        <th class="center" style="width: 140px;">Средний балл</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $questionNum = 1; foreach ($data['questions'] as $question): ?>
                    <tr>
                        <td class="center"><?php echo $questionNum++; ?></td>
                        <td><?php echo htmlspecialchars($question['text']); ?></td>
                        <td class="center">
                            <span class="avg-score">
                                <?php echo $question['avg_score'] !== null ? number_format($question['avg_score'], 2) : '—'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <!-- Общий средний балл -->
                    <tr class="overall-avg-row">
                        <td></td>
                        <td style="text-align: right;">Общий средний балл:</td>
                        <td class="center">
                            <span class="avg-score">
                                <?php echo $data['overall_avg'] !== null ? number_format($data['overall_avg'], 2) : '—'; ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📝</div>
                <p>Нет вопросов в анкете</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Проверка выбора фильтров
function checkFilterSelection() {
    var disciplineSelect = document.getElementById('discipline-list');
    var groupSelect = document.getElementById('group-list');
    var attendanceMin = document.getElementById('attendance-min');
    var filterActions = document.getElementById('filter-actions');
    
    if (!filterActions) return;
    
    var disciplineValue = disciplineSelect ? disciplineSelect.value : '';
    var groupValue = groupSelect ? groupSelect.value : '';
    var attendanceValue = attendanceMin ? attendanceMin.value : '';
    
    // Показываем кнопку если выбрана дисциплина ИЛИ группа ИЛИ посещаемость
    if (disciplineValue || groupValue || attendanceValue) {
        filterActions.style.display = 'flex';
    } else {
        filterActions.style.display = 'none';
    }
}

// Применить фильтры к отчету (перезагрузить страницу)
function applyViewFilter() {
    var disciplineSelect = document.getElementById('discipline-list');
    var groupSelect = document.getElementById('group-list');
    
    var disciplineValue = disciplineSelect ? disciplineSelect.value : '';
    var groupValue = groupSelect ? groupSelect.value : '';
    
    // Формируем URL для перезагрузки страницы с фильтрами
    var url = M.cfg.wwwroot + '/local/cdo_education_scoring/report_view.php' +
        '?surveyid=<?php echo $surveyid; ?>' +
        '&teacher_id=<?php echo $teacherid; ?>';
    
    if (disciplineValue) {
        url += '&discipline_id=' + encodeURIComponent(disciplineValue);
    }
    
    // Перезагружаем страницу с новыми параметрами
    window.location.href = url;
}

// Применение фильтра (скачать Excel)
function applyFilter() {
    var disciplineSelect = document.getElementById('discipline-list');
    var groupSelect = document.getElementById('group-list');
    var attendanceMin = document.getElementById('attendance-min');
    
    var disciplineValue = disciplineSelect ? disciplineSelect.value : '';
    var groupValue = groupSelect ? groupSelect.value : '';
    var attendanceValue = attendanceMin ? attendanceMin.value : '';
    
    // Проверяем что хотя бы что-то выбрано
    if ((disciplineValue && groupValue) || attendanceValue) {
        // Формируем URL для скачивания отчёта
        var url = M.cfg.wwwroot + '/local/cdo_education_scoring/export_filtered.php' +
            '?surveyid=<?php echo $surveyid; ?>' +
            '&teacher_id=<?php echo $teacherid; ?>';
        
        if (disciplineValue) {
            url += '&discipline_id=' + encodeURIComponent(disciplineValue);
        }
        if (groupValue) {
            url += '&group=' + encodeURIComponent(groupValue);
        }
        if (attendanceValue) {
            url += '&attendance_min=' + encodeURIComponent(attendanceValue);
        }
        
        // Открываем ссылку для скачивания
        window.open(url, '_blank');
    } else {
        alert('Пожалуйста, выберите дисциплину и группу, или укажите минимальную посещаемость');
    }
}

// Переключение табов
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(function(tab) {
        tab.classList.remove('active');
    });
    
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.classList.remove('active');
    });
    
    document.getElementById('tab-' + tabName).classList.add('active');
    document.querySelector('.tab-btn[data-tab="' + tabName + '"]').classList.add('active');
}

// Пагинация
var currentPage = 1;
var pageSize = 25;
var totalRows = 0;
var totalPages = 1;
var allRows = [];

function initPagination() {
    var tbody = document.getElementById('students-tbody');
    if (!tbody) return;
    
    allRows = Array.from(tbody.querySelectorAll('tr[data-row]'));
    totalRows = allRows.length;
    
    if (totalRows <= 25) {
        // Скрыть пагинацию если записей мало
        var paginationContainer = document.getElementById('pagination-container');
        if (paginationContainer && totalRows <= 10) {
            document.getElementById('page-size').value = 'all';
            pageSize = totalRows;
        }
    }
    
    updatePagination();
}

function changePageSize(size) {
    if (size === 'all') {
        pageSize = totalRows;
    } else {
        pageSize = parseInt(size);
    }
    currentPage = 1;
    updatePagination();
}

function goToPage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    updatePagination();
}

function updatePagination() {
    totalPages = Math.ceil(totalRows / pageSize);
    if (totalPages < 1) totalPages = 1;
    if (currentPage > totalPages) currentPage = totalPages;
    
    var startIndex = (currentPage - 1) * pageSize;
    var endIndex = Math.min(startIndex + pageSize, totalRows);
    
    // Показать/скрыть строки
    allRows.forEach(function(row, index) {
        if (index >= startIndex && index < endIndex) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Обновить информацию
    document.getElementById('showing-from').textContent = totalRows > 0 ? startIndex + 1 : 0;
    document.getElementById('showing-to').textContent = endIndex;
    
    // Обновить состояние кнопок
    document.getElementById('btn-first').disabled = currentPage === 1;
    document.getElementById('btn-prev').disabled = currentPage === 1;
    document.getElementById('btn-next').disabled = currentPage === totalPages;
    document.getElementById('btn-last').disabled = currentPage === totalPages;
    
    // Обновить номера страниц
    updatePageNumbers();
}

function updatePageNumbers() {
    var container = document.getElementById('pagination-pages');
    container.innerHTML = '';
    
    if (totalPages <= 1) return;
    
    var maxVisible = 5;
    var startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    var endPage = Math.min(totalPages, startPage + maxVisible - 1);
    
    if (endPage - startPage < maxVisible - 1) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }
    
    // Первая страница + многоточие
    if (startPage > 1) {
        container.appendChild(createPageButton(1));
        if (startPage > 2) {
            var dots = document.createElement('span');
            dots.textContent = '...';
            dots.style.padding = '0 8px';
            dots.style.color = '#6c757d';
            container.appendChild(dots);
        }
    }
    
    // Основные страницы
    for (var i = startPage; i <= endPage; i++) {
        container.appendChild(createPageButton(i));
    }
    
    // Многоточие + последняя страница
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            var dots = document.createElement('span');
            dots.textContent = '...';
            dots.style.padding = '0 8px';
            dots.style.color = '#6c757d';
            container.appendChild(dots);
        }
        container.appendChild(createPageButton(totalPages));
    }
}

function createPageButton(page) {
    var btn = document.createElement('button');
    btn.className = 'pagination-btn' + (page === currentPage ? ' active' : '');
    btn.textContent = page;
    btn.onclick = function() { goToPage(page); };
    return btn;
}

// Принудительное расширение контейнеров
function forceFullWidth() {
    const selectors = [
        'body',
        '#page',
        '#page-wrapper',
        '#page-content',
        '#region-main-box',
        '#region-main',
        '.container',
        '.container-fluid'
    ];
    
    selectors.forEach(function(selector) {
        const elements = document.querySelectorAll(selector);
        elements.forEach(function(el) {
            el.style.width = '100%';
            el.style.maxWidth = 'none';
            el.style.margin = '0';
        });
    });
    
    // Особая обработка для region-main
    const regionMain = document.getElementById('region-main');
    if (regionMain) {
        regionMain.style.width = '100%';
        regionMain.style.maxWidth = 'none';
        regionMain.style.padding = '10px 15px';
    }
    
    console.log('Full width applied to all containers');
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    forceFullWidth();
    initPagination();
    checkFilterSelection(); // Проверяем фильтры при загрузке
    
    // Повторяем через небольшую задержку на случай если Moodle что-то изменил
    setTimeout(forceFullWidth, 100);
    setTimeout(forceFullWidth, 500);
});
</script>

<?php
echo $OUTPUT->footer();

