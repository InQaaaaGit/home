<template>
  <div class="button-group">
    <button
      v-for="(button, index) in buttons"
      :key="index"
      :class="[
        'button-group-item',
        { 'button-group-active': modelValue === button.value }
      ]"
      @click="selectButton(button.value)"
    >
      {{ button.text }}
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  buttons: {
    type: Array,
    required: true,
    default: () => []
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const selectButton = (value) => {
  emit('update:modelValue', value);
  emit('change', value);
};
</script>

<style scoped>
.button-group {
  display: inline-flex;
  border-radius: 0.25rem;
  overflow: hidden;
}

.button-group-item {
  padding: 0.5rem 1rem;
  font-size: 1rem;
  font-weight: 400;
  color: #495057;
  background-color: #fff;
  border: 1px solid #dee2e6;
  border-right: none;
  cursor: pointer;
  transition: all 0.15s ease-in-out;
}

.button-group-item:last-child {
  border-right: 1px solid #dee2e6;
}

.button-group-item:hover {
  background-color: #f8f9fa;
}

.button-group-active {
  color: #fff;
  background-color: #007bff;
  border-color: #007bff;
}

.button-group-active:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}
</style>









