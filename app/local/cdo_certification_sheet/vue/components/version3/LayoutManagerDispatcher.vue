<script setup>
import SheetLayoutManagerTwoRows from './SheetLayoutManagerTwoRows.vue';

defineProps({
  sheet: {
    type: Object,
    required: true
  }
});
</script>

<template>
  <div>
    <!-- Статический рендеринг компонента компоновки в две строки -->
    <SheetLayoutManagerTwoRows :sheet="sheet" />
  </div>
</template>

<style scoped>
</style>
