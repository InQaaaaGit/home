<script setup>
import SheetLeftPanel from './SheetLeftPanel.vue';
import SheetRightPanel from './SheetRightPanel.vue';

defineProps({
  sheet: {
    type: Object,
    required: true
  }
});
</script>

<template>
  <div v-if="sheet">
    <!-- Левая панель - полная ширина -->
    <div class="row">
      <div class="col-12 mb-4">
        <div class="card">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">
              <i class="fas fa-users mr-2"></i>
              Список студентов
            </h5>
          </div>
          <div class="card-body">
            <SheetLeftPanel :sheet="sheet" />
          </div>
        </div>
      </div>
    </div>
    <!-- Правая панель - полная ширина -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0">
              <i class="fas fa-cogs mr-2"></i>
              Управление ведомостью
            </h5>
          </div>
          <div class="card-body">
            <SheetRightPanel :sheet="sheet" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.card {
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
}
.card-header {
  border-bottom: 1px solid rgba(0,0,0,.125);
}
</style>
