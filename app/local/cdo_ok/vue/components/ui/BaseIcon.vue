<template>
  <svg
    :class="['icon', `icon-${name}`, variantClass]"
    :width="size"
    :height="size"
    viewBox="0 0 16 16"
    fill="currentColor"
    @click="handleClick"
  >
    <path :d="iconPath" />
  </svg>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  size: {
    type: [String, Number],
    default: 16
  },
  variant: {
    type: String,
    default: 'default'
  }
});

const emit = defineEmits(['click']);

const variantClass = computed(() => `icon-${props.variant}`);

const iconPaths = {
  'pencil': 'M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z',
  'trash': 'M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z',
  'x-lg': 'M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z'
};

const iconPath = computed(() => iconPaths[props.name] || '');

const handleClick = (event) => {
  emit('click', event);
};
</script>

<style scoped>
.icon {
  display: inline-block;
  cursor: pointer;
  transition: color 0.15s ease-in-out;
}

.icon-default {
  color: #6c757d;
}

.icon-primary {
  color: #007bff;
}

.icon-secondary {
  color: #6c757d;
}

.icon-success {
  color: #28a745;
}

.icon-danger {
  color: #dc3545;
}

.icon-warning {
  color: #ffc107;
}

.icon:hover {
  opacity: 0.8;
}
</style>









