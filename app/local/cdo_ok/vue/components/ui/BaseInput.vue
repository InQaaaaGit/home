<template>
  <div class="input-wrapper">
    <label v-if="label" :for="inputId" class="input-label">
      {{ label }}
    </label>
    <input
      :id="inputId"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :min="min"
      :max="max"
      :step="step"
      :class="['input', { 'input-disabled': disabled, 'input-error': error }]"
      @input="handleInput"
      @change="handleChange"
      @blur="handleBlur"
    />
    <span v-if="error" class="input-error-message">{{ error }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
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
  error: {
    type: String,
    default: ''
  },
  min: {
    type: [String, Number],
    default: undefined
  },
  max: {
    type: [String, Number],
    default: undefined
  },
  step: {
    type: [String, Number],
    default: undefined
  }
});

const emit = defineEmits(['update:modelValue', 'change', 'blur']);

const inputId = computed(() => `input-${Math.random().toString(36).substr(2, 9)}`);

const handleInput = (event) => {
  emit('update:modelValue', event.target.value);
};

const handleChange = (event) => {
  emit('change', event.target.value);
};

const handleBlur = (event) => {
  emit('blur', event.target.value);
};
</script>

<style scoped>
.input-wrapper {
  display: flex;
  flex-direction: column;
  width: 100%;
}

.input-label {
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #212529;
}

.input {
  display: block;
  width: 100%;
  padding: 0.375rem 0.75rem;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.input:focus {
  color: #495057;
  background-color: #fff;
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.input-disabled {
  background-color: #e9ecef;
  opacity: 1;
  cursor: not-allowed;
}

.input-error {
  border-color: #dc3545;
}

.input-error:focus {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.input-error-message {
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #dc3545;
}
</style>









