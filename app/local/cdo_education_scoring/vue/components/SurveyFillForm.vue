<template>
    <Teleport to="body">
        <div class="modal-overlay" @click.self="close">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>{{ survey.title }}</h3>
                    <button @click="close" class="btn-close">&times;</button>
                </div>

                <div class="modal-body">
                <div v-if="survey.description" class="survey-description">
                    <p>{{ survey.description }}</p>
                </div>

                <form @submit.prevent="handleSubmit">
                    <div
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

                    <div class="form-actions">
                        <button type="button" @click="close" class="btn btn-secondary">
                            Отмена
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="loading || !isFormValid">
                            <span v-if="loading">Отправка...</span>
                            <span v-else>Отправить ответы</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </Teleport>
</template>

<script>
import { ref, reactive, computed } from 'vue';
import { useToast } from 'vue-toastification';
import { useStudentSurveysStore } from '../store/studentSurveys';

export default {
    name: 'SurveyFillForm',
    props: {
        survey: {
            type: Object,
            required: true,
        },
    },
    emits: ['close', 'submitted'],
    setup(props, { emit }) {
        const toast = useToast();
        const studentSurveysStore = useStudentSurveysStore();
        const loading = ref(false);
        const errors = reactive({});

        // Инициализируем объект ответов для всех вопросов
        const responses = reactive({});
        props.survey.questions.forEach(question => {
            responses[question.id] = question.type === 'scale' ? null : '';
        });

        const isFormValid = computed(() => {
            return props.survey.questions.every(question => {
                const response = responses[question.id];
                if (question.type === 'scale') {
                    return response !== null && response !== undefined;
                } else {
                    return response && response.trim() !== '';
                }
            });
        });

        const validateForm = () => {
            // Очищаем предыдущие ошибки
            Object.keys(errors).forEach(key => delete errors[key]);

            let isValid = true;

            props.survey.questions.forEach(question => {
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

            loading.value = true;

            try {
                // Преобразуем ответы в массив для отправки
                const answers = props.survey.questions.map(question => {
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

                await studentSurveysStore.submitSurveyResponse(props.survey.id, answers);

                emit('submitted');
                close();
            } catch (error) {
                const errorMessage = error?.message || error?.error || 'Ошибка при отправке ответов';
                toast.error(errorMessage);
                console.error(error);
            } finally {
                loading.value = false;
            }
        };

        const close = () => {
            emit('close');
        };

        return {
            responses,
            errors,
            loading,
            isFormValid,
            handleSubmit,
            close,
        };
    },
};
</script>

<style>
/* Глобальные стили для модального окна (Teleport в body) */
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
    overflow-y: auto;
    padding: 20px;
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    min-width: 600px;
    max-width: 900px;
    min-height: 400px;
    max-height: 90vh;
    margin: auto;
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
    position: sticky;
    top: 0;
    background: white;
    z-index: 10;
}

.modal-header h3 {
    margin: 0;
    color: #333;
    flex: 1;
}

.btn-close {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #666;
    padding: 0;
    width: 30px;
    height: 30px;
    line-height: 1;
}

.btn-close:hover {
    color: #000;
}

.modal-body {
    padding: 20px;
    flex: 1;
    overflow-y: auto;
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

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
    position: sticky;
    bottom: 0;
    background: white;
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
</style>

