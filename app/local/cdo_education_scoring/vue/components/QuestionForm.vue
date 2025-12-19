<template>
    <Teleport to="body">
        <div class="modal-overlay" @click.self="close">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Добавить вопрос</h3>
                    <button @click="close" class="btn-close">&times;</button>
                </div>

                <div class="modal-body">
                <form @submit.prevent="handleSubmit">
                    <div class="form-group">
                        <label for="questionText">Текст вопроса *</label>
                        <textarea
                            id="questionText"
                            v-model="formData.text"
                            class="form-control"
                            rows="3"
                            required
                            placeholder="Введите текст вопроса"
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="questionDescription">Описание (подсказка)</label>
                        <textarea
                            id="questionDescription"
                            v-model="formData.description"
                            class="form-control"
                            rows="2"
                            placeholder="Введите дополнительное пояснение к вопросу (необязательно)"
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="questionType">Тип ответа *</label>
                        <select
                            id="questionType"
                            v-model="formData.type"
                            class="form-control"
                            required
                        >
                            <option value="scale">Балльная шкала (1-5)</option>
                            <option value="text">Свободный ответ</option>
                        </select>
                    </div>

                    <div v-if="formData.type === 'scale'" class="info-box">
                        <p>Респонденты смогут выбрать оценку от 1 до 5 баллов.</p>
                    </div>

                    <div v-if="formData.type === 'text'" class="info-box">
                        <p>Респонденты смогут ввести текстовый ответ.</p>
                    </div>

                    <div class="form-actions">
                        <button type="button" @click="close" class="btn btn-secondary">
                            Отмена
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="!isFormValid">
                            Добавить
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

export default {
    name: 'QuestionForm',
    emits: ['close', 'add'],
    setup(props, { emit }) {
        const formData = reactive({
            text: '',
            description: '',
            type: 'scale',
        });

        const isFormValid = computed(() => {
            return formData.text.trim() !== '';
        });

        const handleSubmit = () => {
            if (!isFormValid.value) {
                return;
            }

            emit('add', {
                text: formData.text.trim(),
                description: formData.description.trim(),
                type: formData.type,
            });

            // Сброс формы
            formData.text = '';
            formData.description = '';
            formData.type = 'scale';
        };

        const close = () => {
            emit('close');
        };

        return {
            formData,
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
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    min-width: 400px;
    max-width: 600px;
    min-height: 200px;
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
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

textarea.form-control {
    resize: vertical;
}

select.form-control {
    padding: 10px 35px 10px 12px;
    min-height: 42px;
    line-height: 1.5;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

select.form-control:hover {
    border-color: #adb5bd;
}

select.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

select.form-control option {
    padding: 10px 12px;
    line-height: 1.5;
    min-height: 40px;
    display: flex;
    align-items: center;
}

.info-box {
    background-color: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 4px;
    padding: 12px;
    margin-bottom: 20px;
}

.info-box p {
    margin: 0;
    color: #004085;
    font-size: 13px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
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

