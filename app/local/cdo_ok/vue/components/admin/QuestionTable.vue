<template>
  <div class="question-table">
    <div class="filter-buttons">
      <base-button-group
        v-model="selectedFilter"
        :buttons="filterButtons"
        @change="handleFilterChange"
      />
      <base-button
        :variant="surveyActive ? 'success' : 'danger'"
        @click="handleActivateSurvey"
        class="ml-2"
      >
        {{ surveyActive ? (langStrings?.survey_is_active || 'Анкета активна') : (langStrings?.active_survey || 'Активировать анкету') }}
      </base-button>
    </div>

    <div class="table-container">
      <base-table :fields="fields">
        <tr
          v-for="(item, index) in filteredQuestions"
          :key="item.id"
          :draggable="true"
          :class="['table-row', { 'dragging': isDragging && draggedIndex === index }]"
          @dragstart="handleDragStart(index, $event)"
          @dragend="handleDragEnd"
          @dragover.prevent
          @drop="handleDrop(index)"
        >
          <td class="table-cell text-center">
            <strong>{{ index + 1 }}.</strong>
          </td>
          <td class="table-cell text-center">
            <base-icon
              name="pencil"
              :variant="item.editStatus ? 'primary' : 'secondary'"
              @click="toggleEdit(item)"
            />
            <base-icon
              v-if="!item.answer || !item.answer.length"
              name="x-lg"
              variant="danger"
              class="ml-2"
              @click="handleDeleteQuestion(item)"
            />
          </td>
          <td class="table-cell text-center">
            <base-checkbox
              v-model="item.visible"
              :disabled="(!isActiveScalesEqual(item) || surveyActive) || !item.editStatus"
              @change="handleVisibilityChange(item)"
            />
          </td>
          <td class="table-cell">
            <base-input
              v-model="item.question"
              :disabled="item.visible || !item.editStatus"
              @change="handleQuestionUpdate(item)"
            />
          </td>
          <td class="table-cell">
            <base-select
              v-model="item.type"
              :options="types"
              :disabled="item.visible || !item.editStatus"
              value-key="value"
              text-key="text"
              @change="handleQuestionUpdate(item)"
            />
          </td>
          <td class="table-cell">
            <div v-if="item.type === 1" class="parameter-input-scale">
              <base-input
                v-model.number="item.first_value_of_type"
                type="number"
                min="-100"
                max="100"
                :disabled="item.visible || !item.editStatus"
                @change="handleParameterChange(item)"
              />
              <span class="separator">-</span>
              <base-input
                v-model.number="item.second_value"
                type="number"
                min="-100"
                max="100"
                :disabled="item.visible || !item.editStatus"
                @change="handleParameterChange(item)"
              />
            </div>
            <div v-else class="parameter-input-string">
              <base-input
                v-model.number="item.first_value_of_type"
                type="number"
                min="1"
                max="100"
                :disabled="item.visible || !!item.answer?.length"
                @change="handleParameterChange(item)"
              />
              <span class="text-muted">символов</span>
            </div>
          </td>
        </tr>
      </base-table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useQuestionStore } from '../../stores/questionStore';
import { useActiveSurveyStore } from '../../stores/activeSurveyStore';
import { useDraggable } from '../../composables/useDraggable';
import BaseButton from '../ui/BaseButton.vue';
import BaseButtonGroup from '../ui/BaseButtonGroup.vue';
import BaseTable from '../ui/BaseTable.vue';
import BaseInput from '../ui/BaseInput.vue';
import BaseSelect from '../ui/BaseSelect.vue';
import BaseCheckbox from '../ui/BaseCheckbox.vue';
import BaseIcon from '../ui/BaseIcon.vue';

const props = defineProps({
  group: {
    type: Number,
    required: true
  }
});

const questionStore = useQuestionStore();
const activeSurveyStore = useActiveSurveyStore();
const { isDragging, draggedIndex, handleDragStart, handleDragEnd } = useDraggable();

const handleDrop = (targetIndex) => {
  if (draggedIndex.value === null || draggedIndex.value === targetIndex) {
    handleDragEnd();
    return;
  }

  const newItems = [...filteredQuestions.value];
  const draggedItem = newItems[draggedIndex.value];
  newItems.splice(draggedIndex.value, 1);
  newItems.splice(targetIndex, 0, draggedItem);

  // Обновляем сортировку
  newItems.forEach((item, index) => {
    item.sort = index;
  });

  questionStore.updateQuestionsSortOrder(newItems);
  handleDragEnd();
};

const selectedFilter = ref('all');
const surveyActive = ref(false);

// Computed для полей таблицы, чтобы подтягивать переводы динамически
const fields = computed(() => [
  { key: 'sort', label: '', thStyle: 'width: 5%' },
  { key: 'actions', label: '', thStyle: 'width: 10%' },
  { key: 'visible', label: langStrings.value['fields:visible'] || '', thStyle: 'width: 10%' },
  { key: 'question', label: langStrings.value['fields:question_name'] || '' },
  { key: 'type', label: langStrings.value['fields:type'] || '' },
  { key: 'parameters', label: '', thStyle: 'width: 20%' }
]);

const filterButtons = computed(() => [
  { value: 'all', text: langStrings.value?.all || 'Все' },
  { value: 'active', text: langStrings.value?.active || 'Активные' },
  { value: 'archive', text: langStrings.value?.archive || 'Архив' }
]);

const types = computed(() => questionStore.types);
const langStrings = computed(() => questionStore.langStrings);

const questions = computed(() => {
  return questionStore.questions.filter(q => q.group_tab === props.group);
});

const filteredQuestions = computed(() => {
  switch (selectedFilter.value) {
    case 'active':
      return questions.value.filter(q => q.visible);
    case 'archive':
      return questions.value.filter(q => !q.visible);
    case 'all':
    default:
      return questions.value;
  }
});

const toggleEdit = (item) => {
  item.editStatus = !item.editStatus;
};

const handleFilterChange = (filter) => {
  selectedFilter.value = filter;
};

const handleVisibilityChange = (item) => {
  questionStore.updateQuestionAPI(item);
};

const handleQuestionUpdate = (item) => {
  questionStore.updateQuestionAPI(item);
};

const handleParameterChange = (item) => {
  // Валидация значений
  if (item.type === 1) {
    if (item.first_value_of_type > 100) item.first_value_of_type = 100;
    if (item.first_value_of_type < -100) item.first_value_of_type = -100;
    if (item.second_value > 100) item.second_value = 100;
    if (item.second_value < -100) item.second_value = -100;
  } else if (item.type === 2) {
    if (item.first_value_of_type < 1) item.first_value_of_type = 1;
    if (item.first_value_of_type > 100) item.first_value_of_type = 100;
  }
  
  questionStore.updateQuestionAPI(item);
};

const handleDeleteQuestion = (item) => {
  if (confirm('Вы уверены, что хотите удалить этот вопрос?')) {
    questionStore.deleteQuestionAPI(item);
  }
};

const handleActivateSurvey = async () => {
  surveyActive.value = await activeSurveyStore.toggleActiveSurvey(props.group);
};

const isActiveScalesEqual = (reference) => {
  if (reference.type !== 1) return true;
  
  const activeScales = questions.value.filter(q => q.type === 1 && q.visible);
  
  if (activeScales.length === 0) return true;
  
  return activeScales.every(q => 
    q.first_value_of_type === reference.first_value_of_type &&
    q.second_value === reference.second_value
  ) && reference.question.length > 0;
};

onMounted(async () => {
  try {
    surveyActive.value = await activeSurveyStore.getActiveSurvey(props.group);
  } catch (error) {
    console.error('Ошибка загрузки статуса активности опроса:', error);
    surveyActive.value = false;
  }
});
</script>

<style scoped>
.question-table {
  width: 100%;
}

.filter-buttons {
  display: flex;
  align-items: center;
  margin-bottom: 1.5rem;
}

.ml-2 {
  margin-left: 0.5rem;
}

.table-container {
  width: 100%;
  overflow-x: auto;
}

.table-row {
  transition: opacity 0.2s ease;
  cursor: move;
}

.table-row.dragging {
  opacity: 0.5;
}

.table-row:hover {
  background-color: #f8f9fa;
}

.table-cell {
  padding: 0.75rem;
  vertical-align: middle;
}

/* Выделение колонки видимости */
:deep(.table-header:nth-child(3)) {
  background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
  color: #000;
  font-weight: 700;
  font-size: 1rem;
  text-align: center;
  border-left: 3px solid #ff9800;
  border-right: 3px solid #ff9800;
  box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
}

.table-row td:nth-child(3) {
  background-color: #fff8e1;
  border-left: 3px solid #ffc107;
  border-right: 3px solid #ffc107;
}

.table-row:hover td:nth-child(3) {
  background-color: #fff3cd;
  box-shadow: inset 0 0 8px rgba(255, 193, 7, 0.3);
}

.text-center {
  text-align: center;
}

.text-muted {
  color: #6c757d;
  margin-left: 0.5rem;
}

.parameter-input-scale {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  justify-content: center;
}

.parameter-input-scale > * {
  max-width: 80px;
}

.parameter-input-string {
  display: flex;
  align-items: center;
  justify-content: center;
}

.parameter-input-string > :first-child {
  max-width: 80px;
}

.separator {
  font-weight: bold;
}
</style>

