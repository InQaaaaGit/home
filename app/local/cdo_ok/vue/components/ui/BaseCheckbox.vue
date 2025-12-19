<template>
  <div class="checkbox-wrapper">
    <label :class="['checkbox-label', { 'checkbox-disabled': disabled }]">
      <input
        type="checkbox"
        :checked="modelValue"
        :disabled="disabled"
        class="checkbox-input"
        @change="handleChange"
      />
      <span class="checkbox-custom"></span>
      <span v-if="label" class="checkbox-text">{{ label }}</span>
    </label>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  label: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const handleChange = (event) => {
  const checked = event.target.checked;
  emit('update:modelValue', checked);
  emit('change', checked);
};
</script>

<style scoped>
.checkbox-wrapper {
  display: inline-block;
}

.checkbox-label {
  display: inline-flex;
  align-items: center;
  cursor: pointer;
  user-select: none;
  padding: 0.25rem;
  border-radius: 0.375rem;
  transition: background-color 0.2s ease;
}

.checkbox-label:hover:not(.checkbox-disabled) {
  background-color: rgba(0, 123, 255, 0.08);
}

.checkbox-input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkbox-custom {
  position: relative;
  display: inline-block;
  width: 2.5rem;
  height: 2.5rem;
  background: linear-gradient(145deg, #ffffff, #f0f0f0);
  border: 4px solid #ff6b6b;
  border-radius: 0.5rem;
  transition: all 0.25s ease-in-out;
  box-shadow: 0 4px 8px rgba(255, 107, 107, 0.3), 0 0 0 4px rgba(255, 107, 107, 0.1);
}

.checkbox-label:hover:not(.checkbox-disabled) .checkbox-custom {
  border-color: #ff4757;
  box-shadow: 0 6px 12px rgba(255, 71, 87, 0.5), 0 0 0 6px rgba(255, 71, 87, 0.2);
  transform: scale(1.1);
}

.checkbox-input:checked ~ .checkbox-custom {
  background: linear-gradient(145deg, #2ecc71, #27ae60);
  border-color: #2ecc71;
  box-shadow: 0 6px 15px rgba(46, 204, 113, 0.6), 0 0 0 6px rgba(46, 204, 113, 0.2);
  animation: checkPulse 0.4s ease;
}

@keyframes checkPulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.15);
  }
  100% {
    transform: scale(1);
  }
}

.checkbox-input:checked ~ .checkbox-custom::after {
  content: '';
  position: absolute;
  left: 0.85rem;
  top: 0.35rem;
  width: 0.5rem;
  height: 1rem;
  border: solid white;
  border-width: 0 4px 4px 0;
  transform: rotate(45deg);
  filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.2));
}

.checkbox-input:focus ~ .checkbox-custom {
  border-color: #007bff;
  box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.35);
}

.checkbox-disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.checkbox-disabled .checkbox-custom {
  background-color: #f8f9fa;
  border-color: #dee2e6;
}

.checkbox-text {
  margin-left: 0.5rem;
  font-size: 1rem;
  color: #212529;
  font-weight: 500;
}
</style>







