<script setup>
import { ref, onUnmounted } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    required: true
  },
  placeholder: {
    type: String,
    default: ''
  },
  apiKey: {
    type: String,
    required: true
  },
  suggestionData: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['update:modelValue', 'update:suggestionData']);

const suggestions = ref([]);
const isLoading = ref(false);
const showSuggestions = ref(false);
const errorMessage = ref('');
let debounceTimer = null;
const inputValue = ref(props.modelValue);
const urlAPISuggestion = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address';

// Функция для получения подсказок от DaData с дебаунсом
const fetchSuggestions = async (query) => {
  if (!query || query.length < 3) {
    suggestions.value = [];
    return;
  }

  if (debounceTimer) {
    clearTimeout(debounceTimer);
  }

  debounceTimer = setTimeout(async () => {
    isLoading.value = true;
    errorMessage.value = '';
    
    try {
      const response = await fetch(urlAPISuggestion, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Token ${props.apiKey}`
        },
        body: JSON.stringify({
          query: query,
          count: 5,
          locations: [
            {
              country: "*"
            }
          ]
        })
      });

      if (!response.ok) {
        throw new Error(`Ошибка API: ${response.status}`);
      }

      const data = await response.json();
      suggestions.value = data.suggestions || [];
      
      if (suggestions.value.length === 0) {
        errorMessage.value = 'Адрес не найден';
      }
    } catch (error) {
      console.error('Ошибка при получении подсказок:', error);
      errorMessage.value = 'Ошибка при поиске адреса. Попробуйте позже.';
      suggestions.value = [];
    } finally {
      isLoading.value = false;
    }
  }, 300);
};

// Функция для выбора подсказки
const selectSuggestion = (suggestion) => {
  inputValue.value = suggestion.value;
  emit('update:modelValue', suggestion.value);
  emit('update:suggestionData', suggestion);
  suggestions.value = [];
  showSuggestions.value = false;
  errorMessage.value = '';
};

// Функция для очистки поля
const clearAddress = () => {
  inputValue.value = '';
  emit('update:modelValue', '');
  emit('update:suggestionData', null);
  suggestions.value = [];
  showSuggestions.value = false;
  errorMessage.value = '';
};

// Функция для обработки события blur
const handleBlur = () => {
  setTimeout(() => {
    showSuggestions.value = false;
  }, 200);
};

// Очищаем таймер при размонтировании компонента
onUnmounted(() => {
  if (debounceTimer) {
    clearTimeout(debounceTimer);
  }
});
</script>

<template>
  <div class="form-group row align-items-center">
<!--    <label :for="'address-' + label" class="col-sm-2 col-form-label">{{ label }}</label>-->
    <div class="col-sm-12 position-relative">
      <div class="input-group">
        
        <input
            type="text"
            class="form-control"
            :id="'address-' + label"
            :placeholder="placeholder || label"
            :value="modelValue"
            @input="(e) => {
              emit('update:modelValue', e.target.value);
              fetchSuggestions(e.target.value);
              showSuggestions = true;
            }"
            @focus="showSuggestions = true"
            @blur="handleBlur"
        >
        <button 
            v-if="inputValue"
            class="btn btn-outline-secondary" 
            type="button"
            @click="clearAddress"
        >
          <i class="bi bi-x"></i>
        </button>
      </div>
      
      <!-- Индикатор загрузки -->
      <div v-if="isLoading" class="position-absolute end-0 top-50 translate-middle-y me-3">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
          <span class="visually-hidden"></span>
        </div>
      </div>

      <!-- Сообщение об ошибке -->
      <div v-if="errorMessage" class="text-danger small mt-1">
        {{ errorMessage }}
      </div>

      <!-- Список подсказок -->
      <div v-if="showSuggestions && suggestions.length > 0" class="suggestions-list">
        <div
            v-for="suggestion in suggestions"
            :key="suggestion.value"
            class="suggestion-item"
            @mousedown="selectSuggestion(suggestion)"
        >
          {{ suggestion.value }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.suggestions-list {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  z-index: 1000;
  max-height: 200px;
  overflow-y: auto;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.suggestion-item {
  padding: 8px 12px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.suggestion-item:hover {
  background-color: #f8f9fa;
}

.suggestion-item:not(:last-child) {
  border-bottom: 1px solid #e9ecef;
}

.input-group {
  position: relative;
}

.input-group .btn {
  position: absolute;
  right: 0;
  top: 0;
  height: 100%;
  z-index: 2;
  border: none;
  background: transparent;
  color: #6c757d;
}

.input-group .btn:hover {
  color: #dc3545;
}
</style> 