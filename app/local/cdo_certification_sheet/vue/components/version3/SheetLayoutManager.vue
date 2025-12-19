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
  <div class="row" v-if="sheet">
    <!-- Левая панель -->
    <div class="col-md-7">
      <SheetLeftPanel :sheet="sheet" />
    </div>
    <!-- Правая панель -->
    <div class="col-md-5">
      <SheetRightPanel :sheet="sheet" />
    </div>
  </div>
</template>

<style scoped>
</style>
