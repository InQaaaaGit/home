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
</script>

<template>
    <template v-if="hasAbsenceGUID">
      <StudentsTable :sheet="sheet" />
    </template>
    <template v-else>
      <div class="alert alert-warning">{{ strings.guid_absence_not_set }}</div>
    </template>
</template>

<style scoped>
</style>
