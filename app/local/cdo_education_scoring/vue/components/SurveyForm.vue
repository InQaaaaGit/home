<template>
    <Teleport to="body">
        <div class="modal-overlay" @click.self="close">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>{{ survey ? 'Редактировать анкету' : 'Создать анкету' }}</h3>
                    <button @click="close" class="btn-close">&times;</button>
                </div>

            <div class="modal-body">
                <form @submit.prevent="handleSubmit">
                    <div class="form-group">
                        <label for="title">Название анкеты *</label>
                        <input
                            id="title"
                            v-model="formData.title"
                            type="text"
                            class="form-control"
                            required
                            placeholder="Введите название анкеты"
                        />
                    </div>

                    <div class="form-group">
                        <label for="description">Описание</label>
                        <textarea
                            id="description"
                            v-model="formData.description"
                            class="form-control"
                            rows="3"
                            placeholder="Введите описание анкеты"
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="durationDays">Срок проведения опроса (дней) *</label>
                        <input
                            id="durationDays"
                            v-model.number="formData.durationDays"
                            type="number"
                            class="form-control"
                            min="1"
                            required
                            placeholder="Введите количество дней"
                        />
                    </div>

                    <div class="questions-section">
                        <div class="section-header">
                            <h4>Вопросы анкеты</h4>
                            <button
                                type="button"
                                @click="showQuestionForm = true"
                                class="btn btn-primary btn-sm"
                            >
                                + Добавить вопрос
                            </button>
                        </div>

                        <div v-if="formData.questions.length === 0" class="empty-questions">
                            <p>Вопросов пока нет. Добавьте первый вопрос.</p>
                        </div>

                        <div v-else class="questions-list">
                            <div
                                v-for="(question, index) in formData.questions"
                                :key="index"
                                class="question-item"
                            >
                                <div class="question-header">
                                    <span class="question-number">Вопрос {{ index + 1 }}</span>
                                    <button
                                        type="button"
                                        @click="removeQuestion(index)"
                                        class="btn-remove"
                                    >
                                        Удалить
                                    </button>
                                </div>
                                <div class="question-content">
                                    <p><strong>{{ question.text }}</strong></p>
                                    <p v-if="question.description" class="question-description">
                                        {{ question.description }}
                                    </p>
                                    <p class="question-type">
                                        Тип: {{ question.type === 'scale' ? 'Балльная шкала (1-5)' : 'Свободный ответ' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <QuestionForm
                        v-if="showQuestionForm"
                        @close="showQuestionForm = false"
                        @add="addQuestion"
                    />

                    <div class="form-actions">
                        <button type="button" @click="close" class="btn btn-secondary">
                            Отмена
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="!isFormValid">
                            {{ survey ? 'Сохранить' : 'Создать' }}
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
import { useSurveysStore } from '../store/surveys';
import QuestionForm from './QuestionForm.vue';

export default {
    name: 'SurveyForm',
    components: {
        QuestionForm,
    },
    props: {
        survey: {
            type: Object,
            default: null,
        },
    },
    emits: ['close', 'saved'],
    setup(props, { emit }) {
        const surveysStore = useSurveysStore();
        const showQuestionForm = ref(false);

        const formData = reactive({
            title: props.survey?.title || '',
            description: props.survey?.description || '',
            durationDays: props.survey?.durationDays || 30,
            questions: props.survey?.questions ? [...props.survey.questions] : [],
        });

        const isFormValid = computed(() => {
            return formData.title.trim() !== '' &&
                   formData.durationDays > 0 &&
                   formData.questions.length > 0;
        });

        const addQuestion = (question) => {
            formData.questions.push(question);
            showQuestionForm.value = false;
        };

        const removeQuestion = (index) => {
            formData.questions.splice(index, 1);
        };

        const handleSubmit = async () => {
            if (!isFormValid.value) {
                return;
            }

            try {
                if (props.survey) {
                    await surveysStore.updateSurvey(props.survey.id, formData);
                } else {
                    await surveysStore.createSurvey({
                        ...formData,
                        isActive: false,
                    });
                }
                emit('saved');
            } catch (error) {
                console.error('Error saving survey:', error);
            }
        };

        const close = () => {
            emit('close');
        };

        return {
            formData,
            showQuestionForm,
            isFormValid,
            addQuestion,
            removeQuestion,
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
    max-height: calc(100vh - 40px);
    margin: auto;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
    background: white;
    flex-shrink: 0;
    border-radius: 8px 8px 0 0;
}

.modal-header h3 {
    margin: 0;
    color: #333;
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
    overflow-y: auto;
    flex: 1;
    min-height: 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.questions-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.section-header h4 {
    margin: 0;
    color: #333;
}

.empty-questions {
    text-align: center;
    padding: 30px;
    color: #666;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.questions-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.question-item {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 15px;
    background-color: #f8f9fa;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.question-number {
    font-weight: 600;
    color: #333;
}

.btn-remove {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
}

.btn-remove:hover {
    background-color: #bb2d3b;
}

.question-content p {
    margin: 5px 0;
}

.question-description {
    color: #666;
    font-size: 13px;
    font-style: italic;
    padding: 6px 10px;
    background-color: #ffffff;
    border-left: 3px solid #0d6efd;
    border-radius: 3px;
}

.question-type {
    color: #666;
    font-size: 13px;
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

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}
</style>

