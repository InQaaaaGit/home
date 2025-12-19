<template>
    <div class="student-survey-list">
        <div class="survey-list-header">
            <h2>Студенческая оценка преподавания</h2>
        </div>

        <div v-if="loading" class="loading">Загрузка...</div>

        <div v-else-if="surveys.length === 0" class="empty-state">
            <p>Активных анкет пока нет.</p>
        </div>

        <div v-else class="surveys-grid">
            <div
                v-for="survey in surveys"
                :key="survey.id"
                class="survey-card"
                :class="{ 'survey-completed': survey.isCompleted }"
            >
                <div class="survey-card-header">
                    <h3>{{ survey.title }}</h3>
                    <div class="survey-status">
                        <span v-if="survey.isCompleted" class="status-completed">
                            Завершена
                        </span>
                        <span v-else class="status-active">
                            Доступна
                        </span>
                    </div>
                </div>

                <div class="survey-card-body">
                    <div class="survey-info">
                        <p v-if="survey.description" class="survey-description">
                            {{ survey.description }}
                        </p>
                        <p><strong>Вопросов:</strong> {{ survey.questions?.length || 0 }}</p>
                        <p v-if="survey.endDate">
                            <strong>Срок сдачи:</strong> {{ formatDate(survey.endDate) }}
                        </p>
                    </div>

                    <div class="survey-actions">
                        <button
                            v-if="!survey.isCompleted"
                            @click="openSurvey(survey)"
                            class="btn btn-primary"
                        >
                            Заполнить анкету
                        </button>
                        <button
                            v-else
                            @click="viewSurvey(survey)"
                            class="btn btn-secondary"
                        >
                            Просмотреть
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useStudentSurveysStore } from '../store/studentSurveys';

export default {
    name: 'StudentSurveyList',
    setup() {
        const router = useRouter();
        const studentSurveysStore = useStudentSurveysStore();

        const surveys = computed(() => studentSurveysStore.surveys);
        const loading = computed(() => studentSurveysStore.loading);
        
        // Получаем только доступные (незавершенные) анкеты
        const availableSurveys = computed(() => {
            return surveys.value.filter(survey => !survey.isCompleted);
        });

        onMounted(() => {
            studentSurveysStore.fetchSurveys();
        });

        // Автоматически открываем анкету, если доступна только одна
        let autoRedirectExecuted = false;
        watch(
            [surveys, loading],
            ([newSurveys, isLoading]) => {
                // Ждем завершения загрузки
                if (isLoading) {
                    return;
                }

                // Проверяем, что мы еще не выполнили автоматическое перенаправление
                // и что мы находимся на странице списка анкет
                if (autoRedirectExecuted || router.currentRoute.value.name !== 'student-surveys') {
                    return;
                }

                // Получаем доступные анкеты
                const available = newSurveys.filter(survey => !survey.isCompleted);
                
                // Если доступна только одна анкета, автоматически открываем её
                if (available.length === 1) {
                    autoRedirectExecuted = true;
                    // Небольшая задержка для плавности перехода
                    setTimeout(() => {
                        router.push({ 
                            name: 'survey-fill', 
                            params: { id: available[0].id } 
                        });
                    }, 300);
                }
            },
            { immediate: false }
        );

        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        };

        const openSurvey = (survey) => {
            router.push({ name: 'survey-fill', params: { id: survey.id } });
        };

        const viewSurvey = (survey) => {
            // TODO: Просмотр заполненной анкеты
            console.log('Просмотреть анкету:', survey);
        };

        return {
            surveys,
            loading,
            availableSurveys,
            formatDate,
            openSurvey,
            viewSurvey,
        };
    },
};
</script>

<style scoped>
.student-survey-list {
    padding: 20px;
}

.survey-list-header {
    margin-bottom: 30px;
}

.survey-list-header h2 {
    margin: 0;
    color: #333;
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

.survey-card.survey-completed {
    background-color: #f8f9fa;
    opacity: 0.8;
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
.status-completed {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.status-active {
    background-color: #d1e7dd;
    color: #0f5132;
}

.status-completed {
    background-color: #cfe2ff;
    color: #084298;
}

.survey-card-body {
    display: flex;
    flex-direction: column;
    gap: 15px;
    flex: 1;
}

.survey-description {
    color: #666;
    margin-bottom: 10px;
    font-style: italic;
    word-wrap: break-word;
    overflow-wrap: break-word;
    line-height: 1.4;
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
    margin-top: auto;
    padding-top: 15px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
    width: 100%;
}

.btn-primary {
    background-color: #0d6efd;
    color: white;
}

.btn-primary:hover {
    background-color: #0b5ed7;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5c636a;
}
</style>

