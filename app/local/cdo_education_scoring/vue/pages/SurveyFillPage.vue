<template>
    <div class="survey-fill-page">
        <div class="page-header">
            <button @click="goBack" class="btn-back">
                ← Назад
            </button>
            <h1>{{ survey?.title || 'Загрузка...' }}</h1>
        </div>

        <div v-if="loading" class="loading-container">
            <p>Загрузка анкеты...</p>
        </div>

        <div v-else-if="!survey" class="error-container">
            <p>Анкета не найдена</p>
        </div>

        <div v-else-if="hasTeachersError" class="error-container">
            <div class="error-message">
                <div class="error-icon">⚠️</div>
                <div class="error-content">
                    <h2 class="error-title">Невозможно заполнить анкету</h2>
                    <p class="error-text">
                        Отсутствуют преподаватели для данной дисциплины.
                        Анкета не может быть заполнена, так как нет преподавателей, для которых она составлена.
                    </p>
                </div>
            </div>
        </div>

        <div v-else-if="allTeachersCompleted" class="error-container">
            <div class="error-message">
                <div class="error-icon">✓</div>
                <div class="error-content">
                    <h2 class="error-title">Анкета заполнена</h2>
                    <p class="error-text">
                        Вы уже заполнили эту анкету для всех доступных преподавателей.
                    </p>
                </div>
            </div>
        </div>

        <div v-else class="page-content">
            <div v-if="survey.description" class="survey-description">
                <p>{{ survey.description }}</p>
            </div>

            <form @submit.prevent="handleSubmit">
                <!-- Выбор преподавателя -->
                <div v-if="teachers.length > 0" class="teacher-selection-group">
                    <label class="teacher-label">
                        <span>Преподаватель</span>
                        <span class="required-indicator">*</span>
                    </label>
                    <select
                        v-model="selectedTeacherId"
                        @change="checkSurveyAvailability"
                        class="form-control teacher-select"
                        required
                    >
                        <option value="">Выберите преподавателя</option>
                        <option
                            v-for="teacher in availableTeachers"
                            :key="teacher.id"
                            :value="teacher.id"
                        >
                            {{ teacher.fullname }}
                        </option>
                    </select>
                    <div v-if="availabilityMessage" class="availability-message" :class="{'availability-error': !surveyAvailable}">
                        {{ availabilityMessage }}
                    </div>
                    <div v-if="teachersCompletedCount > 0" class="info-message">
                        <p>Заполнено анкет: {{ teachersCompletedCount }} из {{ teachersTotalCount }}</p>
                    </div>
                    <div v-if="errors.teacher" class="error-message">
                        {{ errors.teacher }}
                    </div>
                </div>

                <!-- Сообщение о необходимости выбора преподавателя -->
                <div v-if="teachers.length > 0 && !selectedTeacherId" class="info-box">
                    <p>Пожалуйста, выберите преподавателя, чтобы продолжить заполнение анкеты.</p>
                </div>

                <!-- Вопросы анкеты отображаются только после выбора преподавателя и успешной проверки доступности -->
                <div
                    v-if="(teachers.length === 0 || selectedTeacherId) && surveyAvailable"
                    v-for="(question, index) in survey.questions"
                    :key="question.id"
                    class="question-group"
                >
                    <label class="question-label">
                        <span class="question-number">Вопрос {{ index + 1 }}</span>
                        <span class="required-indicator">*</span>
                    </label>
                    <p class="question-text">{{ question.text }}</p>
                    <div v-if="question.description" class="question-description">
                        <span class="hint-icon">💡</span>
                        <span class="hint-text">{{ question.description }}</span>
                    </div>

                    <!-- Балльная шкала (1-5) -->
                    <div v-if="question.type === 'scale'" class="scale-options">
                        <label
                            v-for="value in [1, 2, 3, 4, 5]"
                            :key="value"
                            class="scale-option"
                            :class="{ 'scale-option-selected': responses[question.id] == value }"
                        >
                            <input
                                type="radio"
                                :name="`question_${question.id}`"
                                :value="value"
                                v-model.number="responses[question.id]"
                                required
                            />
                            <span class="scale-label">{{ value }}</span>
                        </label>
                    </div>

                    <!-- Свободный ответ -->
                    <div v-else-if="question.type === 'text'" class="text-input">
                        <textarea
                            :name="`question_${question.id}`"
                            v-model="responses[question.id]"
                            class="form-control"
                            rows="4"
                            required
                            placeholder="Введите ваш ответ..."
                        ></textarea>
                    </div>

                    <div v-if="errors[question.id]" class="error-message">
                        {{ errors[question.id] }}
                    </div>
                </div>

                <div v-if="(teachers.length === 0 || selectedTeacherId) && surveyAvailable" class="form-actions">
                    <button type="button" @click="goBack" class="btn btn-secondary">
                        Отмена
                    </button>
                    <button type="submit" class="btn btn-primary" :disabled="submitting || !isFormValid">
                        <span v-if="submitting">Отправка...</span>
                        <span v-else>Отправить ответы</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useStudentSurveysStore } from '../store/studentSurveys';
import { useAppStore } from '../store/app';
import { ajax } from '../utils/ajax';

export default {
    name: 'SurveyFillPage',
    setup() {
        const route = useRoute();
        const router = useRouter();
        const toast = useToast();
        const studentSurveysStore = useStudentSurveysStore();
        const appStore = useAppStore();
        
        const survey = ref(null);
        const loading = ref(true);
        const submitting = ref(false);
        const teachers = ref([]);
        const selectedTeacherId = ref(null);
        const errors = reactive({});
        const teachersLoaded = ref(false);
        const surveyAvailable = ref(true);
        const availabilityMessage = ref('');
        
        // Статистика преподавателей с бэкенда
        const teachersTotalCount = ref(0);
        const teachersCompletedCount = ref(0);
        const teachersAllCompleted = ref(false);

        // Инициализируем объект ответов для всех вопросов
        const responses = reactive({});

        // Доступные преподаватели (уже отфильтрованы на бэкенде)
        const availableTeachers = computed(() => teachers.value);

        // Проверка наличия преподавателей для заполнения анкеты
        // Ошибка только если общее количество = 0 (преподавателей нет вообще)
        const hasTeachersError = computed(() => {
            const disciplineId = appStore.disciplineId;
            return disciplineId && teachersLoaded.value && teachersTotalCount.value === 0;
        });

        // Проверка, заполнены ли все преподаватели (данные с бэкенда)
        const allTeachersCompleted = computed(() => {
            return teachersLoaded.value && teachersAllCompleted.value;
        });

        const isFormValid = computed(() => {
            if (!survey.value || !survey.value.questions) {
                return false;
            }
            
            // Проверяем ответы на вопросы
            const questionsValid = survey.value.questions.every(question => {
                const response = responses[question.id];
                if (question.type === 'scale') {
                    return response !== null && response !== undefined;
                } else {
                    return response && response.trim() !== '';
                }
            });
            
            // Если есть доступные преподаватели, проверяем выбор преподавателя
            const teacherValid = availableTeachers.value.length === 0 || selectedTeacherId.value !== null;
            
            // Проверяем доступность анкеты
            const availabilityValid = surveyAvailable.value;
            
            return questionsValid && teacherValid && availabilityValid;
        });

        const loadTeachers = async () => {
            const disciplineId = appStore.disciplineId;
            if (!disciplineId) {
                teachers.value = [];
                teachersTotalCount.value = 0;
                teachersCompletedCount.value = 0;
                teachersAllCompleted.value = false;
                teachersLoaded.value = true;
                return;
            }

            try {
                const requestData = {
                    discipline_id: disciplineId,
                };
                
                // Передаём surveyid для фильтрации уже заполненных преподавателей
                if (survey.value && survey.value.id) {
                    requestData.surveyid = survey.value.id;
                }
                
                const data = await ajax('local_cdo_education_scoring_get_teachers', requestData);
                
                // Обработка новой структуры ответа
                if (data && typeof data === 'object' && 'teachers' in data) {
                    teachers.value = Array.isArray(data.teachers) ? data.teachers : [];
                    teachersTotalCount.value = data.total_count || 0;
                    teachersCompletedCount.value = data.completed_count || 0;
                    teachersAllCompleted.value = data.all_completed || false;
                } else {
                    // Обратная совместимость со старым форматом
                    teachers.value = Array.isArray(data) ? data : [];
                    teachersTotalCount.value = teachers.value.length;
                    teachersCompletedCount.value = 0;
                    teachersAllCompleted.value = false;
                }
            } catch (error) {
                console.error('Ошибка при загрузке преподавателей:', error);
                teachers.value = [];
                teachersTotalCount.value = 0;
                teachersCompletedCount.value = 0;
                teachersAllCompleted.value = false;
            } finally {
                teachersLoaded.value = true;
            }
        };

        const checkSurveyAvailability = async () => {
            if (!selectedTeacherId.value || !survey.value) {
                availabilityMessage.value = '';
                surveyAvailable.value = true;
                return;
            }

            const disciplineId = appStore.disciplineId;

            try {
                const result = await ajax('local_cdo_education_scoring_check_survey_availability', {
                    surveyid: survey.value.id,
                    teacher_id: selectedTeacherId.value,
                    discipline_id: disciplineId || null,
                    duration_days: survey.value.durationDays || null,
                });

                surveyAvailable.value = result.status || false;

                // Если статус false, выводим причину из message
                if (!surveyAvailable.value) {
                    const reason = result.message || 'Анкета недоступна для заполнения';
                    availabilityMessage.value = reason;
                    toast.error(reason);
                } else {
                    // Если статус true, можно показать положительное сообщение (если есть)
                    availabilityMessage.value = result.message || '';
                    if (result.message) {
                        toast.success(result.message);
                    }
                }
            } catch (error) {
                console.error('Ошибка при проверке доступности анкеты:', error);
                const errorMessage = 'Не удалось проверить доступность анкеты';
                availabilityMessage.value = errorMessage;
                surveyAvailable.value = false;
                toast.error(errorMessage);
            }
        };

        const loadSurvey = async () => {
            loading.value = true;
            try {
                // Загружаем список анкет и находим нужную
                await studentSurveysStore.fetchSurveys();
                const surveyId = parseInt(route.params.id, 10);
                const foundSurvey = studentSurveysStore.surveys.find(s => s.id === surveyId);
                
                if (!foundSurvey) {
                    toast.error('Анкета не найдена');
                    router.push('/surveys');
                    return;
                }

                survey.value = foundSurvey;
                
                // Инициализируем ответы
                survey.value.questions.forEach(question => {
                    responses[question.id] = question.type === 'scale' ? null : '';
                });

                // Загружаем преподавателей
                await loadTeachers();
            } catch (error) {
                toast.error('Ошибка при загрузке анкеты');
                console.error(error);
                router.push('/surveys');
            } finally {
                loading.value = false;
            }
        };

        const validateForm = () => {
            // Очищаем предыдущие ошибки
            Object.keys(errors).forEach(key => delete errors[key]);

            let isValid = true;

            // Проверяем выбор преподавателя, если есть доступные преподаватели
            if (availableTeachers.value.length > 0 && !selectedTeacherId.value) {
                errors.teacher = 'Необходимо выбрать преподавателя';
                isValid = false;
            }

            survey.value.questions.forEach(question => {
                const response = responses[question.id];
                
                if (question.type === 'scale') {
                    if (response === null || response === undefined) {
                        errors[question.id] = 'Необходимо выбрать оценку';
                        isValid = false;
                    }
                } else {
                    if (!response || response.trim() === '') {
                        errors[question.id] = 'Необходимо заполнить ответ';
                        isValid = false;
                    }
                }
            });

            return isValid;
        };

        const handleSubmit = async () => {
            if (!validateForm()) {
                toast.error('Пожалуйста, заполните все обязательные поля');
                return;
            }

            if (!isFormValid.value) {
                toast.error('Необходимо заполнить все вопросы');
                return;
            }

            submitting.value = true;

            try {
                // Преобразуем ответы в массив для отправки
                const answers = survey.value.questions.map(question => {
                    let value = responses[question.id];
                    // Для балльной шкалы убеждаемся, что это число
                    if (question.type === 'scale') {
                        value = parseInt(value, 10);
                    }
                    return {
                        questionid: question.id,
                        value: String(value),
                    };
                });

                await studentSurveysStore.submitSurveyResponse(
                    survey.value.id, 
                    answers,
                    selectedTeacherId.value
                );
                
                // Возвращаемся к списку анкет
                router.push('/surveys');
            } catch (error) {
                const errorMessage = error?.message || error?.error || 'Ошибка при отправке ответов';
                toast.error(errorMessage);
                console.error(error);
            } finally {
                submitting.value = false;
            }
        };

        const goBack = () => {
            router.push('/surveys');
        };

        onMounted(() => {
            loadSurvey();
        });

        return {
            survey,
            loading,
            submitting,
            teachers,
            selectedTeacherId,
            responses,
            errors,
            isFormValid,
            hasTeachersError,
            allTeachersCompleted,
            availableTeachers,
            teachersTotalCount,
            teachersCompletedCount,
            surveyAvailable,
            availabilityMessage,
            handleSubmit,
            goBack,
            checkSurveyAvailability,
        };
    },
};
</script>

<style scoped>
.survey-fill-page {
    background-color: #f5f5f5;
    padding: 20px;
}

.page-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 30px;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-back {
    padding: 8px 16px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background-color: white;
    color: #333;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-back:hover {
    background-color: #f8f9fa;
    border-color: #adb5bd;
}

.page-header h1 {
    margin: 0;
    color: #333;
    flex: 1;
}

.loading-container {
    text-align: center;
    padding: 60px 20px;
    color: #666;
    background: white;
    border-radius: 8px;
}

.error-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    padding: 20px;
}

.error-container .error-message {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    max-width: 600px;
    padding: 30px;
    background-color: #fff;
    border: 2px solid #dc3545;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);
}

.error-container .error-icon {
    font-size: 48px;
    flex-shrink: 0;
}

.error-container .error-content {
    flex: 1;
}

.error-container .error-title {
    margin: 0 0 12px 0;
    font-size: 24px;
    font-weight: 600;
    color: #dc3545;
}

.error-container .error-text {
    margin: 0;
    font-size: 16px;
    line-height: 1.6;
    color: #333;
}

.info-message {
    margin-top: 8px;
    padding: 10px 12px;
    background-color: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 4px;
    font-size: 13px;
    color: #004085;
}

.info-message p {
    margin: 0;
    line-height: 1.5;
}

.page-content {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    max-width: 900px;
    margin: 0 auto;
}

.survey-description {
    background-color: #f8f9fa;
    border-left: 4px solid #0d6efd;
    padding: 15px;
    margin-bottom: 25px;
    border-radius: 4px;
}

.survey-description p {
    margin: 0;
    color: #666;
    font-style: italic;
}

.question-group {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e9ecef;
}

.question-group:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.question-label {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 10px;
    font-weight: 600;
    color: #333;
}

.question-number {
    color: #0d6efd;
}

.required-indicator {
    color: #dc3545;
    font-weight: bold;
}

.question-text {
    font-size: 16px;
    color: #333;
    margin-bottom: 8px;
    line-height: 1.5;
}

.question-description {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 14px;
    color: #495057;
    margin-bottom: 15px;
    margin-top: 8px;
    padding: 12px 15px;
    background-color: #e7f3ff;
    border-left: 4px solid #0d6efd;
    border-radius: 4px;
    line-height: 1.6;
}

.question-description .hint-icon {
    font-size: 18px;
    flex-shrink: 0;
    line-height: 1;
}

.question-description .hint-text {
    flex: 1;
}

.scale-options {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.scale-option {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 10px 15px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    transition: all 0.3s;
    background: white;
}

.scale-option:hover {
    border-color: #0d6efd;
    background-color: #f0f7ff;
}

.scale-option input[type="radio"] {
    margin: 0;
    margin-right: 8px;
    cursor: pointer;
}

.scale-option input[type="radio"]:checked + .scale-label {
    font-weight: bold;
    color: #0d6efd;
}

.scale-option input[type="radio"]:checked {
    accent-color: #0d6efd;
}

.scale-option-selected {
    border-color: #0d6efd !important;
    background-color: #e7f3ff !important;
}

.scale-label {
    font-size: 18px;
    font-weight: 500;
    color: #333;
}

.text-input {
    margin-top: 10px;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.error-message {
    color: #dc3545;
    font-size: 13px;
    margin-top: 5px;
}

.availability-message {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    border-radius: 4px;
    padding: 10px 12px;
    margin-top: 10px;
    font-size: 13px;
    color: #0c5460;
}

.availability-message.availability-error {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.info-box {
    background-color: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 4px;
    padding: 15px 20px;
    margin-bottom: 25px;
}

.info-box p {
    margin: 0;
    color: #004085;
    font-size: 14px;
    line-height: 1.6;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
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

.btn-primary:hover:not(:disabled) {
    background-color: #0b5ed7;
}

.btn-primary:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5c636a;
}

.teacher-selection-group {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e9ecef;
}

.teacher-label {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 10px;
    font-weight: 600;
    color: #333;
    font-size: 16px;
}

.teacher-select {
    width: 100%;
    padding: 12px 35px 12px 12px;
    min-height: 44px;
    line-height: 1.5;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
    background-color: white;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 12px;
    cursor: pointer;
    appearance: none;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

.teacher-select:hover {
    border-color: #adb5bd;
}

.teacher-select:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.teacher-select option {
    padding: 12px;
    line-height: 1.6;
    min-height: 44px;
    display: flex;
    align-items: center;
}
</style>

