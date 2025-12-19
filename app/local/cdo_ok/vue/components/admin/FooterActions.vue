<template>
  <div class="footer-actions">
    <base-select
      v-model="currentYear"
      :options="years"
      class="select-year"
    />
    <base-select
      v-model="currentReport"
      :options="reports"
      class="select-report"
    />
    <a
      :href="`print.php?report=${currentReport}`"
      class="btn-link"
    >
      <base-button variant="primary">
        {{ langStrings?.['buttons:construct'] || 'Сформировать' }}
      </base-button>
    </a>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useQuestionStore } from '../../stores/questionStore';
import BaseSelect from '../ui/BaseSelect.vue';
import BaseButton from '../ui/BaseButton.vue';

const questionStore = useQuestionStore();

const currentYear = ref(questionStore.years[0] || 2023);
const currentReport = ref('local_cdo_ok\\reports\\variants\\report1');

const years = computed(() => questionStore.years);
const reports = computed(() => questionStore.reports);
const langStrings = computed(() => questionStore.langStrings);
</script>

<style scoped>
.footer-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 1rem;
  flex-wrap: wrap;
  width: 100%;
  max-width: 100%;
}

.select-year,
.select-report {
  min-width: 120px;
  flex: 1 1 auto;
  max-width: 200px;
}

.btn-link {
  text-decoration: none;
  white-space: nowrap;
}
</style>

