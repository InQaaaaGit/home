<template>
  <div class="tabs">
    <div class="tabs-header">
      <button
        v-for="(tab, index) in tabs"
        :key="index"
        :class="['tab-button', { 'tab-active': modelValue === index }]"
        @click="selectTab(index)"
      >
        {{ tab.title }}
      </button>
    </div>
    <div class="tabs-content">
      <slot :name="`tab-${modelValue}`"></slot>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Number,
    default: 0
  },
  tabs: {
    type: Array,
    required: true,
    default: () => []
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const selectTab = (index) => {
  emit('update:modelValue', index);
  emit('change', index);
};
</script>

<style scoped>
.tabs {
  display: flex;
  flex-direction: column;
  width: 100%;
}

.tabs-header {
  display: flex;
  gap: 0.25rem;
  border-bottom: 2px solid #dee2e6;
  margin-bottom: 1rem;
}

.tab-button {
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  font-weight: 400;
  color: #495057;
  background-color: transparent;
  border: none;
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
  cursor: pointer;
  transition: all 0.15s ease-in-out;
}

.tab-button:hover {
  color: #007bff;
  border-bottom-color: #007bff;
}

.tab-active {
  color: #007bff;
  font-weight: 500;
  border-bottom-color: #007bff;
}

.tabs-content {
  padding: 1rem 0;
}
</style>









