<template>
    <div class="teacher-stats-page">
        <div class="page-header">
            <h1>Статистика сданных анкет</h1>
        </div>

        <div v-if="loading" class="loading-container">
            <div class="spinner"></div>
            <p>Загрузка статистики...</p>
        </div>

        <div v-else-if="error" class="error-container">
            <div class="error-message">
                <div class="error-icon">⚠️</div>
                <div class="error-content">
                    <h2 class="error-title">Ошибка загрузки</h2>
                    <p class="error-text">{{ error }}</p>
                </div>
            </div>
        </div>

        <div v-else class="stats-content">
            <!-- Информация о преподавателе -->
            <div class="teacher-info">
                <h2>{{ stats.teacher_name || 'Преподаватель не найден' }}</h2>
                <p class="stats-summary">
                    Всего записей: <strong>{{ stats.total_surveys }}</strong>
                </p>
            </div>

            <!-- Таблица со статистикой -->
            <div v-if="stats.surveys && stats.surveys.length > 0" class="stats-table-container">
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th class="col-num">№</th>
                            <th class="col-survey">Анкета</th>
                            <th class="col-discipline">Дисциплина</th>
                            <th class="col-count">Количество ответов</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(survey, index) in stats.surveys" :key="`${survey.surveyid}-${survey.discipline_id}`">
                            <td class="col-num">{{ index + 1 }}</td>
                            <td class="col-survey">{{ survey.survey_title }}</td>
                            <td class="col-discipline">
                                <span class="discipline-name">{{ survey.discipline_name || '—' }}</span>
                                <span v-if="survey.discipline_id" class="discipline-code">({{ survey.discipline_id }})</span>
                            </td>
                            <td class="col-count">
                                <span class="count-badge">{{ survey.completed_count }}</span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3" class="total-label">Итого ответов:</td>
                            <td class="col-count">
                                <span class="count-badge total">{{ totalResponses }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Пустой список -->
            <div v-else class="empty-state">
                <div class="empty-icon">📊</div>
                <h3>Нет данных</h3>
                <p>По данному преподавателю пока нет сданных анкет.</p>
            </div>
        </div>

        <div class="page-actions">
            <button @click="goBack" class="btn btn-secondary">
                ← Назад
            </button>
            <button @click="refresh" class="btn btn-primary" :disabled="loading">
                🔄 Обновить
            </button>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { ajax } from '../utils/ajax';

export default {
    name: 'TeacherStatsPage',
    setup() {
        const router = useRouter();
        const route = useRoute();
        
        const loading = ref(true);
        const error = ref(null);
        const stats = ref({
            teacher_id: null,
            teacher_name: '',
            total_surveys: 0,
            surveys: [],
        });

        const totalResponses = computed(() => {
            if (!stats.value.surveys || stats.value.surveys.length === 0) {
                return 0;
            }
            return stats.value.surveys.reduce((sum, s) => sum + s.completed_count, 0);
        });

        const loadStats = async () => {
            const teacherId = route.params.teacherId || route.query.teacher_id;
            
            if (!teacherId) {
                error.value = 'ID преподавателя не указан';
                loading.value = false;
                return;
            }

            loading.value = true;
            error.value = null;

            try {
                const result = await ajax('local_cdo_education_scoring_get_survey_stats', {
                    teacher_id: parseInt(teacherId, 10),
                });
                
                stats.value = result;
            } catch (e) {
                console.error('Ошибка загрузки статистики:', e);
                error.value = e?.message || e?.error || 'Не удалось загрузить статистику';
            } finally {
                loading.value = false;
            }
        };

        const goBack = () => {
            router.back();
        };

        const refresh = () => {
            loadStats();
        };

        onMounted(() => {
            loadStats();
        });

        return {
            loading,
            error,
            stats,
            totalResponses,
            goBack,
            refresh,
        };
    },
};
</script>

<style scoped>
.teacher-stats-page {
    padding: 20px;
    background-color: #f8f9fa;
    min-height: 400px;
}

.page-header {
    margin-bottom: 24px;
}

.page-header h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
    color: #333;
}

.loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e9ecef;
    border-top-color: #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-container p {
    margin-top: 16px;
    color: #6c757d;
}

.error-container {
    padding: 40px 20px;
}

.error-message {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    max-width: 500px;
    margin: 0 auto;
    padding: 24px;
    background: #fff;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
}

.error-icon {
    font-size: 32px;
}

.error-title {
    margin: 0 0 8px;
    font-size: 18px;
    color: #721c24;
}

.error-text {
    margin: 0;
    color: #856404;
}

.stats-content {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.teacher-info {
    padding: 20px 24px;
    border-bottom: 1px solid #e9ecef;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.teacher-info h2 {
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 600;
}

.stats-summary {
    margin: 0;
    opacity: 0.9;
}

.stats-summary strong {
    font-weight: 600;
}

.stats-table-container {
    overflow-x: auto;
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

.col-num {
    width: 50px;
    text-align: center !important;
}

.col-survey {
    min-width: 200px;
}

.col-discipline {
    min-width: 250px;
}

.col-count {
    width: 150px;
    text-align: center !important;
}

.discipline-name {
    display: block;
    font-weight: 500;
}

.discipline-code {
    font-size: 12px;
    color: #6c757d;
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
    background: #f8f9fa;
    font-weight: 600;
}

.total-label {
    text-align: right !important;
    padding-right: 24px !important;
}

.count-badge.total {
    background: #28a745;
    color: #fff;
}

.empty-state {
    padding: 60px 20px;
    text-align: center;
}

.empty-icon {
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
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    background: #007bff;
    color: #fff;
}

.btn-primary:hover:not(:disabled) {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: #fff;
}

.btn-secondary:hover:not(:disabled) {
    background: #545b62;
}
</style>

