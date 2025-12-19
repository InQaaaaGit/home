<template>
  <div id="cdo-app" class="cdo-quality-assessment">
    <router-view />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useAppStore } from '../stores/app';

const props = defineProps({
  appType: {
    type: String,
    required: true,
    validator: (value) => ['admin', 'survey'].includes(value)
  }
});

const appStore = useAppStore();

// Устанавливаем тип приложения
onMounted(() => {
  appStore.setAppType(props.appType);
});
</script>

<style>
/* Глобальные стили для приложения */
#cdo-app {
  width: 100%;
  min-height: 400px;
}

.cdo-quality-assessment {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

/* Импорт глобальных стилей */
@import '../styles/reset.css';
@import '../styles/main.css';
</style>









