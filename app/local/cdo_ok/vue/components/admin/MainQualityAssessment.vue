<template>
  <div class="main-quality-assessment">
    <div class="container">
      <div class="tabs-section">
        <base-tabs
          v-model="selectedTab"
          :tabs="tabList"
          @change="handleTabChange"
        >
          <template #tab-0>
            <question-table :group="0" />
          </template>
          <template #tab-1>
            <question-table :group="1" />
          </template>
        </base-tabs>
      </div>

      <div class="actions-section">
        <div class="actions-left">
          <base-button
            variant="primary"
            :loading="isCreateLoaderOn"
            @click="handleCreateQuestion"
          >
            {{ langStrings?.['buttons:add'] || 'Добавить' }}
          </base-button>
        </div>
        <div class="actions-right">
          <footer-actions />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useQuestionStore } from '../../stores/questionStore';
import BaseTabs from '../ui/BaseTabs.vue';
import BaseButton from '../ui/BaseButton.vue';
import QuestionTable from './QuestionTable.vue';
import FooterActions from './FooterActions.vue';

const questionStore = useQuestionStore();

// Загружаем вопросы при монтировании компонента
onMounted(async () => {
  try {
    await questionStore.loadQuestions();
  } catch (error) {
    console.error('Ошибка загрузки вопросов:', error);
  }
});

const selectedTab = computed({
  get: () => questionStore.selectedTab,
  set: (value) => questionStore.selectTab(value)
});

const isCreateLoaderOn = computed(() => questionStore.isCreateLoaderOn);
const langStrings = computed(() => questionStore.langStrings);

const tabList = computed(() => [
  { title: langStrings.value?.['tabs:question_for_discipline'] || 'Перечень вопросов по отдельным дисциплинам' },
  { title: langStrings.value?.['tabs:question_for_education_program'] || 'Перечень вопросов по образовательной программе' }
]);

const handleTabChange = (tabIndex) => {
  questionStore.selectTab(tabIndex);
};

const handleCreateQuestion = () => {
  questionStore.createQuestionAPI();
};
</script>

<style scoped>
.main-quality-assessment {
  width: 100%;
  padding: 1rem 0;
}

.container {
  width: 100%;
  max-width: 100%;
}

.tabs-section {
  margin-bottom: 2rem;
}

.actions-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  border-top: 1px solid #dee2e6;
}

.actions-left {
  flex: 0 0 auto;
}

.actions-right {
  flex: 1;
  display: flex;
  justify-content: flex-end;
}
</style>

