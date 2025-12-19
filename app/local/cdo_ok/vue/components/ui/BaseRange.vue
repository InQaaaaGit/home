<template>
  <div class="range-wrapper">
    <label v-if="label" :for="rangeId" class="range-label">
      {{ label }}
    </label>
    <div class="range-container">
      <span v-if="showMinMax" class="range-value range-min">{{ min }}</span>
      <input
        :id="rangeId"
        type="range"
        :value="modelValue"
        :min="min"
        :max="max"
        :step="step"
        :disabled="disabled"
        :class="['range-input', { 'range-disabled': disabled }]"
        @input="handleInput"
        @change="handleChange"
      />
      <span v-if="showMinMax" class="range-value range-max">{{ max }}</span>
    </div>
    <div v-if="showValue" class="range-current">{{ modelValue }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: 0
  },
  min: {
    type: [String, Number],
    default: 0
  },
  max: {
    type: [String, Number],
    default: 100
  },
  step: {
    type: [String, Number],
    default: 1
  },
  label: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  showValue: {
    type: Boolean,
    default: true
  },
  showMinMax: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const rangeId = computed(() => `range-${Math.random().toString(36).substr(2, 9)}`);

const handleInput = (event) => {
  emit('update:modelValue', event.target.value);
};

const handleChange = (event) => {
  emit('change', event.target.value);
};
</script>

<style scoped>
.range-wrapper {
  display: flex;
  flex-direction: column;
  width: 100%;
}

.range-label {
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #212529;
}

.range-container {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.range-value {
  font-size: 0.875rem;
  font-weight: 500;
  min-width: 2rem;
  text-align: center;
}

.range-min {
  color: #dc3545;
}

.range-max {
  color: #28a745;
}

.range-input {
  flex: 1;
  height: 0.5rem;
  background: #dee2e6;
  border-radius: 0.25rem;
  outline: none;
  appearance: none;
  cursor: pointer;
}

.range-input::-webkit-slider-thumb {
  appearance: none;
  width: 1.25rem;
  height: 1.25rem;
  background: #007bff;
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.15s ease-in-out;
}

.range-input::-webkit-slider-thumb:hover {
  background: #0056b3;
}

.range-input::-moz-range-thumb {
  width: 1.25rem;
  height: 1.25rem;
  background: #007bff;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.15s ease-in-out;
}

.range-input::-moz-range-thumb:hover {
  background: #0056b3;
}

.range-input:focus {
  outline: none;
}

.range-input:focus::-webkit-slider-thumb {
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.range-input:focus::-moz-range-thumb {
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.range-disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.range-current {
  margin-top: 0.5rem;
  text-align: center;
  font-size: 1.125rem;
  font-weight: 500;
  color: #007bff;
}
</style>









