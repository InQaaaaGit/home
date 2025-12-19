<template>
    <div class="survey-list">
        <div class="survey-list-header">
            <h2>Студенческая оценка преподавания</h2>
            <button @click="showCreateForm = true" class="btn btn-primary">
                + Создать анкету
            </button>
        </div>

        <div v-if="loading" class="loading">Загрузка...</div>

        <div v-else-if="surveys.length === 0" class="empty-state">
            <p>Анкет пока нет. Создайте первую анкету.</p>
        </div>

        <div v-else class="surveys-grid">
            <div
                v-for="survey in surveys"
                :key="survey.id"
                class="survey-card"
                :class="{ 'survey-active': survey.isActive }"
            >
                <div class="survey-card-header">
                    <h3>{{ survey.title || 'Без названия' }}</h3>
                    <div class="survey-status">
                        <span :class="survey.isActive ? 'status-active' : 'status-inactive'">
                            {{ survey.isActive ? 'Активна' : 'Неактивна' }}
                        </span>
                    </div>
                </div>

                <div class="survey-card-body">
                    <div class="survey-info">
                        <p><strong>Вопросов:</strong> {{ survey.questions?.length || 0 }}</p>
                        <p v-if="survey.durationDays">
                            <strong>Срок проведения:</strong> {{ survey.durationDays }} дней
                        </p>
                        <p v-if="survey.createdAt">
                            <strong>Создана:</strong> {{ formatDate(survey.createdAt) }}
                        </p>
                    </div>

                    <div class="survey-actions">
                        <button
                            v-if="!survey.isActive"
                            @click="activateSurvey(survey.id)"
                            class="btn btn-success btn-sm"
                        >
                            Активировать
                        </button>
                        <button
                            v-else
                            @click="deactivateSurvey(survey.id)"
                            class="btn btn-warning btn-sm"
                        >
                            Деактивировать
                        </button>
                        <button
                            @click="editSurvey(survey)"
                            class="btn btn-secondary btn-sm"
                        >
                            Редактировать
                        </button>
                        <button
                            @click="openReportView(survey.id)"
                            class="btn btn-info btn-sm"
                            title="Просмотреть отчет"
                        >
                            📊 Отчёт
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <SurveyForm
            v-if="showCreateForm"
            @close="showCreateForm = false"
            @saved="handleSurveySaved"
        />

        <SurveyForm
            v-if="editingSurvey"
            :survey="editingSurvey"
            @close="editingSurvey = null"
            @saved="handleSurveySaved"
        />

        <!-- Модальное окно выбора преподавателя -->
        <Teleport to="body">
            <div v-if="showTeacherModal" class="modal-overlay" @click.self="showTeacherModal = false">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Просмотр отчёта</h3>
                        <button @click="showTeacherModal = false" class="modal-close">&times;</button>
                    </div>
                <div class="modal-body">
                    <div v-if="loadingTeachers" class="loading">Загрузка преподавателей...</div>
                    <div v-else-if="teachers.length === 0" class="empty-state">
                        <p>Нет преподавателей с сданными анкетами для данной анкеты.</p>
                    </div>
                    <div v-else>
                        <label for="teacher-select">Преподаватель:</label>
                        <select
                            id="teacher-select"
                            v-model="selectedTeacherId"
                            class="form-select"
                        >
                            <option value="">-- Выберите преподавателя --</option>
                            <option
                                v-for="teacher in teachers"
                                :key="teacher.id"
                                :value="teacher.id"
                            >
                                {{ teacher.fullname }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        @click="showTeacherModal = false"
                        class="btn btn-secondary"
                    >
                        Отмена
                    </button>
                    <button
                        @click="confirmAction"
                        class="btn btn-primary"
                        :disabled="!selectedTeacherId || loadingTeachers"
                    >
                        📊 Открыть отчёт
                    </button>
                </div>
            </div>
        </div>
        </Teleport>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useSurveysStore } from '../store/surveys';
import SurveyForm from './SurveyForm.vue';

export default {
    name: 'SurveyList',
    components: {
        SurveyForm,
    },
    setup() {
        const surveysStore = useSurveysStore();
        const showCreateForm = ref(false);
        const editingSurvey = ref(null);

        const surveys = computed(() => surveysStore.surveys);
        const loading = computed(() => surveysStore.loading);

        onMounted(() => {
            surveysStore.fetchSurveys();
        });

        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        };

        const activateSurvey = async (surveyId) => {
            await surveysStore.activateSurvey(surveyId);
        };

        const deactivateSurvey = async (surveyId) => {
            await surveysStore.deactivateSurvey(surveyId);
        };

        const editSurvey = (survey) => {
            editingSurvey.value = { ...survey };
        };

        const handleSurveySaved = () => {
            showCreateForm.value = false;
            editingSurvey.value = null;
            surveysStore.fetchSurveys();
        };

        const showTeacherModal = ref(false);
        const selectedSurveyId = ref(null);
        const teachers = ref([]);
        const selectedTeacherId = ref(null);
        const loadingTeachers = ref(false);

        const openReportView = async (surveyId) => {
            selectedSurveyId.value = surveyId;
            selectedTeacherId.value = null;
            showTeacherModal.value = true;
            await loadTeachers(surveyId);
        };

        const loadTeachers = async (surveyId) => {
            loadingTeachers.value = true;
            try {
                const { ajax } = await import('../utils/ajax.js');
                const result = await ajax('local_cdo_education_scoring_get_teachers_for_report', {
                    surveyid: surveyId,
                });
                teachers.value = result || [];
            } catch (error) {
                console.error('Ошибка при загрузке преподавателей:', error);
                teachers.value = [];
            } finally {
                loadingTeachers.value = false;
            }
        };

        const confirmAction = () => {
            if (!selectedTeacherId.value) {
                return;
            }
            
            // Открываем страницу просмотра отчёта
            const url = `${M.cfg.wwwroot}/local/cdo_education_scoring/report_view.php?surveyid=${selectedSurveyId.value}&teacher_id=${selectedTeacherId.value}`;
            window.open(url, '_blank');
            
            showTeacherModal.value = false;
        };

        return {
            surveys,
            loading,
            showCreateForm,
            editingSurvey,
            formatDate,
            activateSurvey,
            deactivateSurvey,
            editSurvey,
            handleSurveySaved,
            openReportView,
            showTeacherModal,
            teachers,
            selectedTeacherId,
            loadingTeachers,
            confirmAction,
        };
    },
};
</script>

<style>
/* Стили для SurveyList (без scoped для поддержки Teleport) */
.survey-list {
    padding: 20px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

.survey-list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.survey-list-header h2 {
    margin: 0;
    color: #333;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #0d6efd;
    color: white;
}

.btn-primary:hover {
    background-color: #0b5ed7;
}

.btn-success {
    background-color: #198754;
    color: white;
}

.btn-success:hover {
    background-color: #157347;
}

.btn-warning {
    background-color: #ffc107;
    color: #000;
}

.btn-warning:hover {
    background-color: #ffca2c;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5c636a;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
    margin: 0 5px;
}

.loading {
    text-align: center;
    padding: 40px;
    color: #666;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.surveys-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    width: 100%;
    box-sizing: border-box;
}

.survey-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    background: white;
    transition: box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.survey-card:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.survey-card.survey-active {
    border-color: #198754;
    border-width: 2px;
}

.survey-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    height: 80px;
    flex-shrink: 0;
}

.survey-card-header h3 {
    margin: 0;
    color: #333;
    flex: 1;
    word-wrap: break-word;
    overflow-wrap: break-word;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    max-height: 70px;
}

.survey-status {
    margin-left: 10px;
}

.status-active,
.status-inactive {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.status-active {
    background-color: #d1e7dd;
    color: #0f5132;
}

.status-inactive {
    background-color: #f8d7da;
    color: #842029;
}

.survey-card-body {
    display: flex;
    flex-direction: column;
    gap: 15px;
    flex: 1;
}

.survey-info {
    flex: 1;
}

.survey-info p {
    margin: 5px 0;
    color: #666;
    font-size: 14px;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.survey-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: auto;
    padding-top: 15px;
}

/* Модальное окно */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    min-width: 400px;
    max-width: 600px;
    min-height: 200px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
}

.modal-header h3 {
    margin: 0;
    color: #333;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    color: #000;
}

.modal-body {
    padding: 20px;
    flex: 1;
    overflow-y: auto;
}

.modal-body label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.form-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 14px;
    background-color: white;
}

.form-select:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 20px;
    border-top: 1px solid #dee2e6;
}

.btn-info {
    background-color: #0dcaf0;
    color: white;
}

.btn-info:hover {
    background-color: #0aa2c0;
}

.btn-outline-info {
    background-color: transparent;
    color: #0dcaf0;
    border: 1px solid #0dcaf0;
}

.btn-outline-info:hover {
    background-color: #0dcaf0;
    color: white;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

