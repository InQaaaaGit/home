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
    <!-- Первый ряд - правая панель -->
    <div class="row">
      <div class="col-12">
        <SheetRightPanel :sheet="sheet" />
      </div>
    </div>
    <!-- Второй ряд - левая панель -->
    <div class="row">
      <div class="col-12">
        <SheetLeftPanel :sheet="sheet" />
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
