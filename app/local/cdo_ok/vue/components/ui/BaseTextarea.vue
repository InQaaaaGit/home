<template>
  <div class="textarea-wrapper">
    <label v-if="label" :for="textareaId" class="textarea-label">
      {{ label }}
    </label>
    <textarea
      :id="textareaId"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :rows="rows"
      :maxlength="maxlength"
      :class="['textarea', { 'textarea-disabled': disabled, 'textarea-error': error }]"
      @input="handleInput"
      @change="handleChange"
      @blur="handleBlur"
    ></textarea>
    <div v-if="maxlength" class="textarea-counter">
      {{ modelValue?.length || 0 }} / {{ maxlength }}
    </div>
    <span v-if="error" class="textarea-error-message">{{ error }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
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
  rows: {
    type: Number,
    default: 3
  },
  maxlength: {
    type: Number,
    default: undefined
  }
});

const emit = defineEmits(['update:modelValue', 'change', 'blur']);

const textareaId = computed(() => `textarea-${Math.random().toString(36).substr(2, 9)}`);

const handleInput = (event) => {
  let value = event.target.value;
  if (props.maxlength && value.length > props.maxlength) {
    value = value.substring(0, props.maxlength);
  }
  emit('update:modelValue', value);
};

const handleChange = (event) => {
  emit('change', event.target.value);
};

const handleBlur = (event) => {
  emit('blur', event.target.value);
};
</script>

<style scoped>
.textarea-wrapper {
  display: flex;
  flex-direction: column;
  width: 100%;
}

.textarea-label {
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #212529;
}

.textarea {
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
  resize: vertical;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.textarea:focus {
  color: #495057;
  background-color: #fff;
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.textarea-disabled {
  background-color: #e9ecef;
  opacity: 1;
  cursor: not-allowed;
}

.textarea-error {
  border-color: #dc3545;
}

.textarea-error:focus {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.textarea-counter {
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #6c757d;
  text-align: right;
}

.textarea-error-message {
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #dc3545;
}
</style>









