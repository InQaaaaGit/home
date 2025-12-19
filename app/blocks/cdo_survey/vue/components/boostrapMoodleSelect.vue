<script setup>
import {defineProps, defineEmits, ref, watch} from 'vue';

// Определение props
const props = defineProps({
  id: {type: String, required: true}, // ID элемента
  label: {type: String, required: false}, // Надпись над полем
  options: {type: Array, required: true}, // Массив опций
  requiredAttr: {type: Boolean, default: false}, // Массив опций
  modelValue: {type: [String, Number], required: false, default: ''} // Значение для v-model
});

// Определение emits
const emit = defineEmits(['update:modelValue']); // Эмит события для обновления v-model

// Реактивная переменная для хранения текущего значения
const selectedValue = ref(props.modelValue);

// Обновление значения при изменении props.modelValue
watch(
    () => props.modelValue,
    (newValue) => {
      selectedValue.value = newValue;
    }
);

// Обработчик изменения select
function handleSelectChange(event) {
  const newValue = event.target.value;
  selectedValue.value = newValue;
  emit('update:modelValue', newValue); // Эмит нового значения
}


</script>

<template>
  <div class="form-group row align-items-center">
    <label v-if="props.label" :for="props.id" class="col-sm-2 col-form-label">{{ props.label }}</label>
    <div class="col-sm-12">
      <select
          :required="props.requiredAttr"
          class="form-control"
          :id="props.id"
          :value="selectedValue"
          @change="handleSelectChange"
      >
        <option
            v-for="option in props.options"
            :key="option.value"
            :value="option.value"
        >
          {{ option.name }}
        </option>
      </select>
    </div>
  </div>
</template>

<style scoped>

</style>
