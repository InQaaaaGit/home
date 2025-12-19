<template>
  <div class="survey-main-app">
    <div v-if="questions.length && !confirmed" class="survey-container">
      <h2 class="survey-title">
        {{ langStrings?.['title_survey_full'] || 'Оцените условия, содержание, организацию и качество образовательного процесса' }}: {{ discipline }}
      </h2>

      <div class="questions-list">
        <div
          v-for="question in questions"
          :key="question.id"
          class="question-item"
        >
          <div class="question-label">
            {{ question.question }}
          </div>
          
          <div class="question-answer">
            <template v-if="question.type === 1">
              <div class="range-container">
                <base-range
                  v-model="question.answer"
                  :min="question.first_value_of_type"
                  :max="question.second_value"
                  :show-value="true"
                  :show-min-max="true"
                  @change="handleAnswerChange(question.id, question.answer)"
                />
              </div>
            </template>
            
            <template v-else>
              <base-textarea
                v-model="question.answer"
                :maxlength="question.first_value_of_type"
                :rows="3"
                placeholder="Введите ваш ответ..."
                @change="handleAnswerChange(question.id, question.answer)"
              />
            </template>
          </div>
          
          <hr class="divider" />
        </div>
      </div>

      <div class="submit-section">
        <base-button
          variant="primary"
          :disabled="!answeredAll"
          @click="handleConfirmAnswers"
        >
          {{ langStrings?.['send_answers'] || 'Отправить' }}
        </base-button>
      </div>
    </div>

    <div v-else class="alert-container">
      <div v-if="confirmed" class="alert alert-success">
        {{ langStrings?.['survey_is_confirmed'] || 'Анкета уже отправлена' }}
      </div>
      <div v-else class="alert alert-warning">
        {{ langStrings?.['title_not_active_survey'] || 'Возможно, анкета еще не активирована' }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useSurveyStore } from '../../stores/surveyStore';
import { useToast } from 'vue-toastification';
import BaseRange from '../ui/BaseRange.vue';
import BaseTextarea from '../ui/BaseTextarea.vue';
import BaseButton from '../ui/BaseButton.vue';

const surveyStore = useSurveyStore();
const toast = useToast();

const questions = computed(() => surveyStore.questions);
const confirmed = computed(() => surveyStore.confirmed);
const answeredAll = computed(() => surveyStore.answeredAll);
const langStrings = computed(() => surveyStore.langStrings);
const discipline = computed(() => surveyStore.discipline);

const handleAnswerChange = (questionId, answer) => {
  surveyStore.updateAnswer(questionId, answer);
  surveyStore.sendAnswer(questionId, answer);
};

const handleConfirmAnswers = async () => {
  if (confirm('Вы уверены, что хотите отправить ответы? После отправки изменение ответов будет невозможно.')) {
    try {
      await surveyStore.confirmAnswers();
      toast.success('Ответы успешно отправлены!');
    } catch (error) {
      toast.error('Ошибка при отправке ответов');
    }
  }
};

onMounted(async () => {
  try {
    await surveyStore.loadQuestions();
  } catch (error) {
    console.error('Ошибка загрузки вопросов опроса:', error);
    toast.error('Не удалось загрузить вопросы опроса');
  }
});
</script>

<style scoped>
.survey-main-app {
  width: 100%;
  padding: 2rem;
}

.survey-container {
  max-width: 900px;
  margin: 0 auto;
}

.survey-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #212529;
  margin-bottom: 2rem;
  text-align: center;
}

.questions-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.question-item {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.question-label {
  font-size: 1.125rem;
  font-weight: 500;
  color: #495057;
}

.question-answer {
  width: 100%;
}

.range-container {
  padding: 1rem 0;
}

.divider {
  width: 100%;
  border: none;
  border-top: 1px solid #dee2e6;
  margin: 1rem 0;
}

.submit-section {
  margin-top: 2rem;
  text-align: center;
}

.alert-container {
  max-width: 600px;
  margin: 2rem auto;
}

.alert {
  padding: 1rem 1.5rem;
  border-radius: 0.25rem;
  font-size: 1rem;
  text-align: center;
}

.alert-success {
  color: #155724;
  background-color: #d4edda;
  border: 1px solid #c3e6cb;
}

.alert-warning {
  color: #856404;
  background-color: #fff3cd;
  border: 1px solid #ffeaa7;
}
</style>

