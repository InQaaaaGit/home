<script setup >
import BoostrapMoodleSelect from "./boostrapMoodleSelect.vue";
import { useMainStore } from "../store/store";
import { storeToRefs } from "pinia";
import VueDadata from 'vue-dadata';
import 'vue-dadata/dist/style.css';
import { ref, onMounted, onUnmounted } from 'vue';
import AddressInput from "./AddressInput.vue";

const props = defineProps({
      'strings': {type: Object, required: false},
      'formData': {type: Object, required: false},
      'validationErrors': {type: Object, required: false},
      'isFieldRequired': {type: Function, required: false}
    }
);
const emit = defineEmits(['update:formData', 'file-selected']);
const stringsStore = useMainStore();
const { citizenships, documentTypes } = storeToRefs(stringsStore);
stringsStore.loadCitizenshipData();

const daDataApiKey = '7a47bae38496a511b54ca371b0b8ec12b40a457b'; // TODO: Замените на ваш API ключ Dadata
const suggestions = ref([]);
const isLoading = ref(false);
const showSuggestions = ref(false);
const errorMessage = ref('');
let debounceTimer = null;
const registrationAddress = ref('');
const urlAPISuggestion = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address';
// Функция для получения подсказок от DaData с дебаунсом
const fetchSuggestions = async (query) => {
  if (!query || query.length < 3) {
    suggestions.value = [];
    return;
  }

  // Очищаем предыдущий таймер
  if (debounceTimer) {
    clearTimeout(debounceTimer);
  }

  // Устанавливаем новый таймер
  debounceTimer = setTimeout(async () => {
    isLoading.value = true;
    errorMessage.value = '';
    
    try {
      const response = await fetch(urlAPISuggestion, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Token ${daDataApiKey}`
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
  }, 300); // Задержка 300мс
};

// Функция для выбора подсказки
const selectSuggestion = (suggestion) => {
  registrationAddress.value = suggestion.value;
  props.formData.individual.registrationAddress = suggestion.value;
  suggestions.value = [];
  showSuggestions.value = false;
  errorMessage.value = '';
};

// Функция для очистки поля
const clearAddress = () => {
  registrationAddress.value = '';
  props.formData.individual.registrationAddress = '';
  suggestions.value = [];
  showSuggestions.value = false;
  errorMessage.value = '';
};

// Очищаем таймер при размонтировании компонента
onUnmounted(() => {
  if (debounceTimer) {
    clearTimeout(debounceTimer);
  }
});

// Функция для обработки события blur
const handleBlur = () => {
  setTimeout(() => {
    showSuggestions.value = false;
  }, 200);
};

const daDataOptions = {
  token: daDataApiKey,
  type: "ADDRESS",
  count: 5,
  minChars: 3,
  delay: 300,
  placeholder: "Начните вводить адрес...",
  highlight: true,
  highlightClass: "highlight",
  selectFirst: false,
  showEmpty: false,
  showEmptyMessage: "Адрес не найден",
  showLoading: true,
  loadingMessage: "Поиск...",
  showError: true,
  errorMessage: "Ошибка поиска",
  showClear: true,
  clearMessage: "Очистить",
  showHint: true,
  hintMessage: "Выберите адрес из списка",
  showNoData: true,
  noDataMessage: "Нет данных",
  showSelected: true,
  selectedMessage: "Выбранный адрес",
  showSelectedHint: true,
  selectedHintMessage: "Нажмите для изменения",
  showSelectedClear: true,
  selectedClearMessage: "Очистить выбор",
  showSelectedError: true,
  selectedErrorMessage: "Ошибка выбора",
  showSelectedLoading: true,
  selectedLoadingMessage: "Загрузка...",
  showSelectedNoData: true,
  selectedNoDataMessage: "Нет выбранного адреса",
  showSelectedHintError: true,
  selectedHintErrorMessage: "Ошибка подсказки",
  showSelectedHintLoading: true,
  selectedHintLoadingMessage: "Загрузка подсказки...",
  showSelectedHintNoData: true,
  selectedHintNoDataMessage: "Нет подсказки"
};

// Функция для проверки обязательности поля
const isFieldRequired = (fieldName) => {
  // Поля, которые не являются обязательными
  const optionalFields = ['inn', 'snils', 'innScan', 'snilsScan'];
  return !optionalFields.includes(fieldName);
};

// Функция для обновления данных
const updateFormData = (field, value) => {
  const updatedFormData = { ...props.formData };
  // Добавляем префикс individual_ к полям, чтобы избежать конфликтов
  updatedFormData[`individual_${field}`] = value;
  emit('update:formData', updatedFormData);
};

// Функция для получения класса поля
const getFieldClass = (fieldName) => {
  return {
    'is-invalid': props.validationErrors && props.validationErrors[fieldName],
    'is-required': props.isFieldRequired && props.isFieldRequired(fieldName)
  };
};

</script>

<template>
  <div>
    <!-- ФИО -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_fullName')">
      <label for="fullName" class="col-sm-2 col-form-label required">{{ strings.fullName }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            :class="{'is-invalid': validationErrors && validationErrors.individual_fullName}"
            id="fullName"
            :placeholder="strings.placeholderFullName"
            v-model="formData.individual_fullName"
            required
        >
        <div v-if="validationErrors && validationErrors.individual_fullName" class="invalid-feedback d-block">
          {{ validationErrors.individual_fullName }}
        </div>
      </div>
    </div>

    <!-- Пол -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_sex')">
      <label class="col-sm-2 col-form-label required">{{ strings.sex }}</label>
      <div class="col-sm-10">
        <div class="form-check form-check-inline">
          <input
              class="form-check-input"
              :class="{'is-invalid': validationErrors && validationErrors.individual_sex}"
              type="radio"
              name="nameSex1"
              id="sex4"
              value="0"
              v-model="formData.individual_sex"
              required
          >
          <label class="form-check-label" for="sex4">{{ strings.sexF }}</label>
        </div>
        <div class="form-check form-check-inline">
          <input
              class="form-check-input"
              :class="{'is-invalid': validationErrors && validationErrors.individual_sex}"
              type="radio"
              name="nameSex1"
              id="sex5"
              value="1"
              v-model="formData.individual_sex"
              required
          >
          <label class="form-check-label" for="sex5">{{ strings.sexM }}</label>
        </div>
        <div v-if="validationErrors && validationErrors.individual_sex" class="invalid-feedback d-block">
          {{ validationErrors.individual_sex }}
        </div>
      </div>
    </div>

    <!-- Гражданство -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_citizenship')">
      <label for="citizenship" class="col-sm-2 col-form-label required">{{ strings.citizenship }}</label>
      <div class="col-sm-10">
        <boostrap-moodle-select
            v-if="citizenships && citizenships.length"
            :options="citizenships"
            id="citizenship"
            :label="strings.citizenship"
            v-model="formData.individual_citizenship"
            :class="{'is-invalid': validationErrors && validationErrors.individual_citizenship}"
            required
        />
        <div v-if="validationErrors && validationErrors.individual_citizenship" class="invalid-feedback d-block">
          {{ validationErrors.individual_citizenship }}
        </div>
      </div>
    </div>

    <!-- Дата рождения -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_birthday')">
      <label for="birthday" class="col-sm-2 col-form-label required">{{ strings.birthday }}</label>
      <div class="col-sm-10">
        <input
            type="date"
            class="form-control"
            :class="{'is-invalid': validationErrors && validationErrors.individual_birthday}"
            id="birthday"
            :value="formData.individual_birthday"
            @input="updateFormData('birthday', $event.target.value)"
            required
        >
        <div v-if="validationErrors && validationErrors.individual_birthday" class="invalid-feedback d-block">
          {{ validationErrors.individual_birthday }}
        </div>
      </div>
    </div>

    <!-- Тип Документа -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_document_type')">
      <label for="document_types" class="col-sm-2 col-form-label required">{{ strings.documentTypes }}</label>
      <div class="col-sm-10">
        <boostrap-moodle-select
            v-if="documentTypes && documentTypes.length"
            :options="documentTypes"
            id="document_types"
            :label="strings.documentTypes"
            v-model="formData.individual_document_type"
            :class="{'is-invalid': validationErrors && validationErrors.individual_document_type}"
            required
        />
        <div v-if="validationErrors && validationErrors.individual_document_type" class="invalid-feedback d-block">
          {{ validationErrors.individual_document_type }}
        </div>
      </div>
    </div>

    <!-- Скан страниц разворота паспорта с фотографией -->
    <div class="form-group row align-items-center">
      <label for="passportPhotoScan" class="col-sm-2 col-form-label required">{{ strings.passportFile }}</label>
      <div class="col-sm-10">
        <input
            style="background-color: white;"
            type="file"
            class="form-control-file"
            id="passportPhotoScan"
            @change="emit('file-selected', {fieldName: 'individual_passportPhotoScan', event: $event})"
            required
        >
      </div>
    </div>

    <!-- Серия паспорта -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_serial')">
      <label for="passportSeries" class="col-sm-2 col-form-label required">{{ strings.serial }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            :class="{'is-invalid': validationErrors && validationErrors.individual_serial}"
            id="passportSeries"
            :placeholder="strings.serial"
            v-model="formData.individual_serial"
            required
        >
        <div v-if="validationErrors && validationErrors.individual_serial" class="invalid-feedback d-block">
          {{ validationErrors.individual_serial }}
        </div>
      </div>
    </div>

    <!-- Номер паспорта -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_number')">
      <label for="passportNumber" class="col-sm-2 col-form-label required">{{ strings.number }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            :class="{'is-invalid': validationErrors && validationErrors.individual_number}"
            id="passportNumber"
            :placeholder="strings.number"
            v-model="formData.individual_number"
            required
        >
        <div v-if="validationErrors && validationErrors.individual_number" class="invalid-feedback d-block">
          {{ validationErrors.individual_number }}
        </div>
      </div>
    </div>

    <!-- Код подразделения -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_whoIssued')">
      <label for="divisionCodeIndividual" class="col-sm-2 col-form-label">{{ strings.divisionCode }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            :class="{'is-invalid': validationErrors.divisionCode}"
            id="divisionCodeIndividual"
            :placeholder="strings.placeholderDivisionCode"
            v-model="formData.individual_divisionCode"
            v-mask="'###-###'"
            required
        >
        <div v-if="validationErrors.divisionCode" class="invalid-feedback d-block">
          {{ validationErrors.divisionCode }}
        </div>
      </div>
    </div>

    <!-- Кем выдан паспорт -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_whoIssued')">
      <label for="passportIssuedBy" class="col-sm-2 col-form-label required">{{ strings.whoIssued }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            :class="{'is-invalid': validationErrors && validationErrors.individual_whoIssued}"
            id="passportIssuedBy"
            :placeholder="strings.whoIssued"
            :value="formData.individual_whoIssued"
            @input="updateFormData('whoIssued', $event.target.value)"
            required
        >
        <div v-if="validationErrors && validationErrors.individual_whoIssued" class="invalid-feedback d-block">
          {{ validationErrors.individual_whoIssued }}
        </div>
      </div>
    </div>

    <!-- Когда выдан паспорт -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_whenIssued')">
      <label for="passportIssuedDate" class="col-sm-2 col-form-label required">{{ strings.whenIssued }}</label>
      <div class="col-sm-10">
        <input
            type="date"
            class="form-control"
            :class="{'is-invalid': validationErrors && validationErrors.individual_whenIssued}"
            id="passportIssuedDate"
            v-model="formData.individual_whenIssued"
            required
        >
        <div v-if="validationErrors && validationErrors.individual_whenIssued" class="invalid-feedback d-block">
          {{ validationErrors.individual_whenIssued }}
        </div>
      </div>
    </div>

    <!-- Адрес по прописке -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_addressOfRegistration')">
      <label class="col-sm-2 col-form-label required">{{ strings.addressOfRegistration }}</label>
      <div class="col-sm-10">
        <address-input
            :model-value="formData.individual_addressOfRegistration"
            :label="strings.addressOfRegistration"
            :api-key="daDataApiKey"
            :class="{'is-invalid': validationErrors && validationErrors.individual_addressOfRegistration}"
            @update:model-value="(value) => updateFormData('addressOfRegistration', value)"
            @update:suggestion-data="(data) => updateFormData('addressOfRegistrationData', data)"
            required
        />
        <div v-if="validationErrors && validationErrors.individual_addressOfRegistration" class="invalid-feedback d-block">
          {{ validationErrors.individual_addressOfRegistration }}
        </div>
      </div>
    </div>

    <!-- Скан страницы паспорта с пропиской -->
    <div class="form-group row align-items-center">
      <label for="passportRegistrationScan" class="col-sm-2 col-form-label required">{{ strings.scanPassportAddress }}</label>
      <div class="col-sm-10">
        <input
            style="background-color: white;"
            type="file"
            class="form-control-file"
            id="passportRegistrationScan"
            @change="emit('file-selected', {fieldName: 'individual_passportRegistrationScan', event: $event})"
            required
        >
      </div>
    </div>

    <!-- Адрес местожительства -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_addressOfActualResidence')">
      <label class="col-sm-2 col-form-label required">{{ strings.addressOfActualResidence }}</label>
      <div class="col-sm-10">
        <address-input
            :model-value="formData.individual_addressOfActualResidence"
            :label="strings.addressOfActualResidence"
            :api-key="daDataApiKey"
            :class="{'is-invalid': validationErrors && validationErrors.individual_addressOfActualResidence}"
            @update:model-value="(value) => updateFormData('addressOfActualResidence', value)"
            @update:suggestion-data="(data) => updateFormData('addressOfActualResidenceData', data)"
            required
        />
        <div v-if="validationErrors && validationErrors.individual_addressOfActualResidence" class="invalid-feedback d-block">
          {{ validationErrors.individual_addressOfActualResidence }}
        </div>
      </div>
    </div>

    <!-- ИНН (необязательное поле) -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_inn')">
      <label for="inn" class="col-sm-2 col-form-label">{{ strings.inn }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            :class="{'is-invalid': validationErrors && validationErrors.individual_inn}"
            id="inn"
            :placeholder="strings.inn"
            v-model="formData.individual_inn"
        >
        <div v-if="validationErrors && validationErrors.individual_inn" class="invalid-feedback d-block">
          {{ validationErrors.individual_inn }}
        </div>
      </div>
    </div>

    <!-- Скан ИНН (необязательное поле) -->
    <div class="form-group row align-items-center">
      <label for="innScan" class="col-sm-2 col-form-label">{{ strings.innFile }}</label>
      <div class="col-sm-10">
        <input
            style="background-color: white;"
            type="file"
            class="form-control-file"
            id="innScan"
            @change="emit('file-selected', {fieldName: 'individual_innScan', event: $event})"
        >
      </div>
    </div>

    <!-- Телефон -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_telephone')">
      <label for="phoneNumber" class="col-sm-2 col-form-label required">{{ strings.telephone }}</label>
      <div class="col-sm-10">
        <input
            type="text"
            class="form-control"
            :class="{'is-invalid': validationErrors && validationErrors.individual_telephone}"
            id="phoneNumber"
            :placeholder="strings.telephone"
            v-model="formData.individual_telephone"
            required
        >
        <div v-if="validationErrors && validationErrors.individual_telephone" class="invalid-feedback d-block">
          {{ validationErrors.individual_telephone }}
        </div>
      </div>
    </div>

    <!-- Электронная почта -->
    <div class="form-group row align-items-center" :class="getFieldClass('individual_email')">
      <label for="email" class="col-sm-2 col-form-label required">{{ strings.email }}</label>
      <div class="col-sm-10">
        <input
            type="email"
            class="form-control"
            :class="{'is-invalid': validationErrors && validationErrors.individual_email}"
            id="email"
            :placeholder="strings.email"
            v-model="formData.individual_email"
            required
        >
        <div v-if="validationErrors && validationErrors.individual_email" class="invalid-feedback d-block">
          {{ validationErrors.individual_email }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.required::after {
  content: " *";
  color: red;
}

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
