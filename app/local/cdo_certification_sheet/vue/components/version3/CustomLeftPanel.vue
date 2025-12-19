<script setup>
import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useCertificationStore } from '../../stores/certification.js';
import StudentsTable from './students-table.vue';

const certificationStore = useCertificationStore();
const { strings, absenceGUID } = storeToRefs(certificationStore);

defineProps({
  sheet: {
    type: Object,
    required: true
  }
});

const hasAbsenceGUID = computed(() => absenceGUID.value.length > 0);

// Дополнительные функции для кастомной панели
const customFunction = () => {
  console.log('Кастомная функция левой панели');
};
</script>

<template>
  <div class="col-7">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
          <i class="fas fa-users mr-2"></i>
          Кастомная панель студентов
        </h5>
      </div>
      <div class="card-body">
        <template v-if="hasAbsenceGUID">
          <StudentsTable :sheet="sheet" />
        </template>
        <template v-else>
          <div class="alert alert-warning">{{ strings.guid_absence_not_set }}</div>
        </template>
        
        <!-- Дополнительные элементы кастомной панели -->
        <div class="mt-3 p-2 bg-light rounded">
          <small class="text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            Это кастомная версия левой панели
          </small>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.card {
  border: 2px solid #007bff;
}
.card-header {
  border-bottom: 2px solid #0056b3;
}
</style>
