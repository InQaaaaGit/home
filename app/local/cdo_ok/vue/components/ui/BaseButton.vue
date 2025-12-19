<template>
  <button
    :class="['btn', variantClass, sizeClass, { 'btn-disabled': disabled }]"
    :type="type"
    :disabled="disabled"
    @click="handleClick"
  >
    <span v-if="loading" class="spinner"></span>
    <slot v-else></slot>
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'primary', 'secondary', 'success', 'danger', 'warning'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  type: {
    type: String,
    default: 'button'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['click']);

const variantClass = computed(() => `btn-${props.variant}`);
const sizeClass = computed(() => `btn-${props.size}`);

const handleClick = (event) => {
  if (!props.disabled && !props.loading) {
    emit('click', event);
  }
};
</script>

<style scoped>
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  text-align: center;
  text-decoration: none;
  vertical-align: middle;
  cursor: pointer;
  user-select: none;
  border: 1px solid transparent;
  border-radius: 0.25rem;
  transition: all 0.15s ease-in-out;
  white-space: nowrap;
}

.btn:hover:not(.btn-disabled) {
  opacity: 0.9;
}

.btn:focus {
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.btn-default {
  color: #212529;
  background-color: #fff;
  border-color: #dee2e6;
}

.btn-primary {
  color: #fff;
  background-color: #007bff;
  border-color: #007bff;
}

.btn-secondary {
  color: #fff;
  background-color: #6c757d;
  border-color: #6c757d;
}

.btn-success {
  color: #fff;
  background-color: #28a745;
  border-color: #28a745;
}

.btn-danger {
  color: #fff;
  background-color: #dc3545;
  border-color: #dc3545;
}

.btn-warning {
  color: #212529;
  background-color: #ffc107;
  border-color: #ffc107;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.btn-lg {
  padding: 0.75rem 1.5rem;
  font-size: 1.25rem;
}

.spinner {
  width: 1rem;
  height: 1rem;
  border: 2px solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: spinner 0.75s linear infinite;
}

@keyframes spinner {
  to {
    transform: rotate(360deg);
  }
}
</style>







