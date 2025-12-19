<template>
  <div class="select-wrapper">
    <label v-if="label" :for="selectId" class="select-label">
      {{ label }}
    </label>
    <select
      :id="selectId"
      :value="modelValue"
      :disabled="disabled"
      :class="['select', { 'select-disabled': disabled }]"
      @change="handleChange"
    >
      <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
      <option
        v-for="option in options"
        :key="getOptionValue(option)"
        :value="getOptionValue(option)"
      >
        {{ getOptionText(option) }}
      </option>
    </select>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  options: {
    type: Array,
    required: true,
    default: () => []
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  valueKey: {
    type: String,
    default: 'value'
  },
  textKey: {
    type: String,
    default: 'text'
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const selectId = computed(() => `select-${Math.random().toString(36).substr(2, 9)}`);

const getOptionValue = (option) => {
  return typeof option === 'object' ? option[props.valueKey] : option;
};

const getOptionText = (option) => {
  return typeof option === 'object' ? option[props.textKey] : option;
};

const handleChange = (event) => {
  const value = event.target.value;
  emit('update:modelValue', value);
  emit('change', value);
};
</script>

<style scoped>
.select-wrapper {
  display: flex;
  flex-direction: column;
  width: 100%;
}

.select-label {
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #212529;
}

.select {
  display: block;
  width: 100%;
  padding: 0.375rem 2rem 0.375rem 0.75rem;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 0.75rem center;
  background-size: 16px 12px;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  appearance: none;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  cursor: pointer;
}

.select:focus {
  color: #495057;
  background-color: #fff;
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.select-disabled {
  background-color: #e9ecef;
  opacity: 1;
  cursor: not-allowed;
}
</style>









