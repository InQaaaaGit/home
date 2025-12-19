<script setup>

import {useMainStore} from "../store/store";
import {storeToRefs} from "pinia";
import {useSelectsStore} from "../store/storeSelects";
import {useUserStore} from "../store/storeUser";
import BoostrapMoodleSelect from "./boostrapMoodleSelect.vue";
import {computed, onMounted, reactive, ref} from "vue";
import {mask} from 'vue-the-mask';
import LegalEntityFields from "./legalEntityFields.vue";
import * as moodleAjax from 'core/ajax';
import {useToast} from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import IndividualFields from "./individualFields.vue";
import {useLangStore} from "../store/storeLang";
import AddressInput from "./AddressInput.vue";


Object.defineProperty(mask, 'name', {value: 'mask'});
const directives = {mask};

const stringsStore = useMainStore();
const langStore = useLangStore();
const selectsStore = useSelectsStore();
const userStore = useUserStore();

const {strings} = storeToRefs(langStore);
const {citizenships, educationLevels, userGroups, courseSchedule, documentTypes} = storeToRefs(stringsStore);
const {lastname, firstname} = storeToRefs(userStore);

// Добавляем состояния загрузки
const isLoadingCitizenships = ref(true);
const isLoadingEducationLevels = ref(true);
const isLoadingUserGroups = ref(true);
const isLoadingCourseSchedule = ref(true);
const isLoadingDocumentTypes = ref(true);
const formSubmitted = ref(false); // Флаг успешной отправки формы

// Функции для загрузки данных
const loadData = async () => {
  try {
    isLoadingCitizenships.value = true;
    isLoadingEducationLevels.value = true;
    isLoadingUserGroups.value = true;
    isLoadingCourseSchedule.value = true;
    isLoadingDocumentTypes.value = true;

    await Promise.all([
      stringsStore.loadCitizenshipData(),
      stringsStore.loadEducationLevelData(),
      stringsStore.loadUserGroupsData(),
      stringsStore.loadCourseSchedule(),
      stringsStore.loadDocumentTypes()
    ]);
  } finally {
    isLoadingCitizenships.value = false;
    isLoadingEducationLevels.value = false;
    isLoadingUserGroups.value = false;
    isLoadingCourseSchedule.value = false;
    isLoadingDocumentTypes.value = false;
  }
};

// Функция для преобразования base64 в файл
const base64ToFile = (base64, filename) => {
  const arr = base64.split(',');
  // const mime = arr[0].match(/:(.*?);/)[1];
  const mime = 'image/jpeg';
  const bstr = atob(arr[0]);
  let n = bstr.length;
  const u8arr = new Uint8Array(n);
  while (n--) {
    u8arr[n] = bstr.charCodeAt(n);
  }
  return new File([u8arr], filename, { type: mime });
};

// Функция для установки файла в input
const setFileToInput = (file, inputId) => {
  const input = document.getElementById(inputId);
  if (input) {
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    input.files = dataTransfer.files;
  }
};

// Добавляем реактивные переменные для отображения имен файлов
const fileNames = reactive({
  snilsFile: '',
  innFile: '',
  passportFile: '',
  scanPassportAddress: '',
  eduFile: '',
  nameMismatchFile: ''
});

// Обновляем функцию handleFile
async function handleFile(event, targetProperty) {
  const file = event.target.files[0];
  if (targetProperty === 'passportFile') {
    formData.isNewPassport = true;
  }
  if (!file) {
    return;
  }

  const reader = new FileReader();

  // Когда чтение файла завершено
  reader.onload = function (e) {
    // Получаем base64 строку, убирая префикс data:image/jpeg;base64,
    const base64String = e.target.result.split(',')[1];
    // Сохраняем base64 строку в formData
    formData[targetProperty] = base64String;
    // Сохраняем имя файла
    formData[`${targetProperty}Name`] = file.name;
    // Обновляем имя файла в реактивном объекте
    fileNames[targetProperty] = file.name;
  };

  // Если произошла ошибка при чтении файла
  reader.onerror = function () {
    $toast.error('Ошибка при чтении файла');
  };

  // Начинаем чтение файла как Data URL (base64)
  reader.readAsDataURL(file);
}

// Обновляем функцию updateFormData
const updateFormData = (responseData) => {
  if (responseData && responseData.form_data) {
    Object.entries(responseData.form_data).forEach(([key, value]) => {
      if (key in formData) {
        if (key.endsWith('File') && value) {
          const fileNameKey = `${key}Name`;
          const fileName = responseData.form_data[fileNameKey] || 'file';
          fileNames[key] = fileName; // Обновляем имя файла в реактивном объекте
          formData[fileNameKey] = fileName; // Сохраняем имя файла в formData
          const file = base64ToFile(value, fileName);
          setFileToInput(file, key);
        }
        formData[key] = value;
      }
    });
  }
};

// Загружаем данные при монтировании компонента
onMounted(async () => {
  await loadData();

  // Загружаем данные анкеты, если они есть
  try {
    const responses = await moodleAjax.call([
      {
        methodname: 'block_cdo_survey_get_survey_data',
        args: {},
      }
    ]);

    const response = await responses[0];
    if (response && response.form_data) {
      updateFormData(response);
    }
  } catch (error) {
    console.error('Error loading survey data:', error);
  }
});

// Список уровней образования, для которых нужно скрывать поля документа об образовании
const hideDocumentLevels = ['0', '1', '2', '3']; // ID уровней от дошкольного до среднего общего

// Функция для проверки должны ли быть скрыты поля документа об образовании
const shouldHideEducationDocFields = () => {
  return hideDocumentLevels.includes(formData.education_level);
};

// Обновляем функцию isFieldRequired
const isFieldRequired = (fieldName) => {
  // Поля, которые не являются обязательными
  const optionalFields = ['inn', 'snils', 'innFile', 'snilsFile'];
  
  // Если галочка не отмечена, то поле для файла обязательно
  if (fieldName === 'eduFile' && !formData.nameMatchesPassport) {
    return true;
  }
  
  return !optionalFields.includes(fieldName);
};

selectsStore.fillSelectsOptions();
const {allSelects} = storeToRefs(selectsStore);
const date = ref(new Date());
const inGroup = ref();
const legalEntity = 'legalEntity';
const anotherPayer = 'AnotherPayer';

const $toast = useToast();

const validationErrors = reactive({});
const formData = reactive({
  isNewPassport: false,
  course_schedule: "",
  document_type: "",
  telephone: "",
  citizenship: "",
  snils: "",
  inn: "",
  innFile: "",
  innFileName: "",
  snilsFile: "",
  snilsFileName: "",
  sex: "0", // По умолчанию выбран "Женский"
  birthday: "", // Изменяем null на пустую строку
  middleName: "",
  serial: "",
  number: "",
  whenIssued: "",
  whoIssued: "",
  serialEdu: "",
  numberEdu: "",
  whenIssuedEdu: "",
  whoIssuedEdu: "",
  inGroup: "",
  unemployed: false,
  returnToJob: false,
  imprisonment: false,
  insurance: false,
  parentalLeave: false,
  personalData: false,
  informationAboutPayer: null,
  limitedHealth: false,
  disabled: false,
  intellectualDisabilities: false,
  education_level: '',
  educationType: '',
  addressOfRegistration: '',
  addressOfActualResidence: '',
  scanPassportAddress: '',
  scanPassportAddressName: '',
  passportFile: '',
  passportFileName: '',
  eduFile: '',
  eduFileName: '',
  nameMatchesPassport: true,
  nameMismatchFile: '',
  nameMismatchFileName: '',
  individual: {},
  fullnameLE: '',
  shortnameLE: '',
  legalAddress: '',
  postalAddress: '',
  telephoneLE: '',
  innLE: '',
  kpp: '',
  bankDetails: '',
  divisionCode: "",
  addressOfRegistrationData: {},
  addressOfActualResidenceData: {},
  individual_inn: '',
  individual_innScan: '',
  individual_innScanName: '',
  individual_fullName: '',
  individual_sex: '',
  individual_citizenship: '',
  individual_birthday: '',
  individual_document_type: '',
  individual_passportPhotoScan: '',
  individual_passportPhotoScanName: '',
  individual_serial: '',
  individual_number: '',
  individual_whoIssued: '',
  individual_whenIssued: '',
  individual_addressOfRegistration: '',
  individual_addressOfRegistrationData: {},
  individual_addressOfActualResidence: '',
  individual_addressOfActualResidenceData: {},
  individual_telephone: '',
  individual_email: '',
  individual_passportRegistrationScan: '',
  individual_passportRegistrationScanName: '',
  individual_divisionCode: '',
  postalAddressData: {},
  legalAddressData: {}
});

// Добавляем поля для работы с ФИАС
const daDataApiKey = userStore.apiToken;

async function submitForm() {
  // Reset previous errors
  Object.keys(validationErrors).forEach(key => {
    validationErrors[key] = null;
  });

 /* const errors = validateSurveyForm(formData);
  console.log(errors, Object.keys(errors), Object.keys(errors).length)
  // If there are validation errors, update the errors object and return
  if (Object.keys(errors).length > 0) {
    Object.assign(validationErrors, errors);
    $toast.error('Пожалуйста, исправьте ошибки в форме.');
    return;
  }*/

  // Подготовка данных формы с именами файлов
  /*const formDataToSend = {
    ...formData,
    // Добавляем имена файлов в данные формы
    fileNames: {
      innFile: formData.innFileName,
      snilsFile: formData.snilsFileName,
      passportFile: formData.passportFileName,
      scanPassportAddress: formData.scanPassportAddressName,
      eduFile: formData.eduFileName
    }
  };*/

  try {
    const responses = await moodleAjax.call([
      {
        methodname: 'block_cdo_survey_submit_survey',
        args: {form_data: formData},
      }
    ]);

    const response = await responses[0];

    if (response && response.status === 1) {
      formSubmitted.value = true; // Устанавливаем флаг успешной отправки
      $toast.success('Опрос успешно отправлен!');
      // Optionally reset the form here
    } else {
      $toast.error('Ошибка при отправке опроса: ' + (response.data ? response.data.message : 'Неизвестная ошибка'));
    }
  } catch (error) {
    $toast.error('Произошла ошибка при отправке опроса.');
  }
}

function fillTestData() {

  formData.document_type = "1";
  formData.telephone = "+7 (999) 123-45-67";
  formData.citizenship = "1";
  formData.snils = "123-456-789 01";
  formData.inn = "123456789012";
  formData.sex = "1";
  formData.birthday = "1990-01-01";
  formData.serial = "1234";
  formData.number = "567890";
  formData.whenIssued = "2010-01-01";
  formData.whoIssued = "ОВД по району";
  formData.serialEdu = "1234";
  formData.numberEdu = "567890";
  formData.whenIssuedEdu = "2015-01-01";
  formData.whoIssuedEdu = "Университет";
  formData.inGroup = "1";
  formData.education_level = "1";
  formData.educationType = "Высшее образование";
  formData.addressOfRegistration = "г. Москва, ул. Примерная, д. 1";
  formData.addressOfActualResidence = "г. Москва, ул. Примерная, д. 1";
  /*  formData.informationAboutPayer = legalEntity;*/
  formData.personalData = true;
}

function validateSurveyForm(data) {
  const errors = {};
  console.log(data)
  // Общие проверки для всех типов плательщиков
  if (!data.divisionCode) {
    errors.divisionCode = strings.value.divisionCodeRequired || 'Пожалуйста, введите код подразделения';
  }
  if (!data.course_schedule) {
    errors.course_schedule = 'Пожалуйста, выберите расписание курсов';
  }
  if (!data.document_type) {
    errors.document_type = strings.value.documentTypeRequired || 'Пожалуйста, выберите тип документа';
  }
  if (!data.informationAboutPayer) {
    errors.informationAboutPayer = strings.value.informationAboutPayerRequired || 'Пожалуйста, выберите информацию о плательщике';
  }
  if (!data.personalData) {
    errors.personalData = strings.value.personalDataRequired || 'Пожалуйста, подтвердите согласие на обработку персональных данных';
  }
  if (!data.birthday) {
    console.log(data.birthday)
    errors.birthday = strings.value.birthdayRequired || 'Пожалуйста, введите дату рождения';
  }

  // Валидация полей юридического лица
  if (data.informationAboutPayer === legalEntity) {
    if (!data.fullnameLE) {
      errors.fullnameLE = 'Пожалуйста, введите полное наименование';
    }
    if (!data.shortnameLE) {
      errors.shortnameLE = 'Пожалуйста, введите сокращенное наименование';
    }
    if (!data.legalAddress) {
      errors.legalAddress = 'Пожалуйста, введите юридический адрес';
    }
    if (!data.postalAddress) {
      errors.postalAddress = 'Пожалуйста, введите почтовый адрес';
    }
    if (!data.telephoneLE) {
      errors.telephoneLE = 'Пожалуйста, введите номер телефона';
    }
    if (!data.innLE) {
      errors.innLE = 'Пожалуйста, введите ИНН';
    }
    if (!data.kpp) {
      errors.kpp = 'Пожалуйста, введите КПП';
    }
    if (!data.bankDetails) {
      errors.bankDetails = 'Пожалуйста, введите банковские реквизиты';
    }
  }

  // Валидация полей другого плательщика
  if (data.informationAboutPayer === anotherPayer) {
    if (!data.telephone) {
      errors.telephone = strings.value.telephoneRequired || 'Пожалуйста, введите номер телефона';
    }
    if (!data.citizenship) {
      errors.citizenship = strings.value.citizenshipRequired || 'Пожалуйста, выберите гражданство';
    }
    if (!data.snils) {
      errors.snils = strings.value.snilsRequired || 'Пожалуйста, введите СНИЛС';
    }
    if (!data.inn) {
      errors.inn = strings.value.innRequired || 'Пожалуйста, введите ИНН';
    }
    if (!data.sex) {
      errors.sex = strings.value.sexRequired || 'Пожалуйста, выберите пол';
    }
    if (!data.serial) {
      errors.serial = strings.value.serialRequired || 'Пожалуйста, введите серию документа';
    }
    if (!data.number) {
      errors.number = strings.value.numberRequired || 'Пожалуйста, введите номер документа';
    }
    if (!data.whenIssued) {
      errors.whenIssued = strings.value.whenIssuedRequired || 'Пожалуйста, введите дату выдачи';
    }
    if (!data.whoIssued) {
      errors.whoIssued = strings.value.whoIssuedRequired || 'Пожалуйста, введите кем выдан';
    }
    if (!data.inGroup) {
      errors.inGroup = strings.value.inGroupRequired || 'Пожалуйста, выберите группу';
    }
    if (!data.education_level) {
      errors.education_level = strings.value.educationLevelRequired || 'Пожалуйста, выберите уровень образования';
    }
    if (!data.educationType) {
      errors.educationType = strings.value.educationTypeRequired || 'Пожалуйста, введите тип образования';
    }
    if (!data.addressOfRegistration) {
      errors.addressOfRegistration = strings.value.addressOfRegistrationRequired || 'Пожалуйста, введите адрес регистрации';
    }
    if (!data.addressOfActualResidence) {
      errors.addressOfActualResidence = strings.value.addressOfActualResidenceRequired || 'Пожалуйста, введите адрес фактического проживания';
    }
  }

  return errors;
}

// Добавляем функцию для получения класса поля
const getFieldClass = (fieldName) => {
  return {
    'is-invalid': validationErrors[fieldName],
    'is-required': isFieldRequired(fieldName)
  };
};
const snilsRequired = computed( ()=> {
  if (formData.citizenship === '0') {
    return 'is-required';
  }
  return '';
});

const otherTypePassport = computed(()=>{
  if (formData.document_type === userStore.guidPassportRF) {
    return 'is-required';
  }
  return '';
});
// Регистрируем компоненты
const components = {
  BoostrapMoodleSelect,
  LegalEntityFields,
  IndividualFields,
  AddressInput
};
</script>

<template>
  <div class="container">
    <div class="form-container">
      <!-- Сообщение об успешной отправке формы -->
      <div v-if="formSubmitted" class="alert alert-success text-center my-4" role="alert">
        <h4>{{ strings.formSubmittedSuccess || 'Ваша заявка отправлена' }}</h4>
        <p>{{
            strings.formSubmittedDescription || 'Благодарим за заполнение анкеты! Ваша заявка принята и будет обработана в ближайшее время.'
          }}</p>
      </div>

      <form v-if="!formSubmitted" @submit.prevent="submitForm">
        <!-- Расписание курсов -->
        <div class="form-group row" :class="getFieldClass('course_schedule')">
          <label for="course_schedule" class="col-sm-2 col-form-label">{{ strings.courseSchedule }}</label>
          <div class="col-sm-10">
            <div v-if="isLoadingCourseSchedule" class="spinner-border text-primary" role="status">
              <span class="visually-hidden"></span>
            </div>
            <boostrap-moodle-select
                v-else-if="courseSchedule && courseSchedule.length"
                :options="courseSchedule"
                id="course_schedule"
                :label="strings.courseSchedule"
                v-model="formData.course_schedule"
                :class="{'is-invalid': validationErrors.course_schedule}"
                :requiredAttr="true"
            />
            <div v-if="validationErrors.course_schedule" class="invalid-feedback d-block">
              {{ validationErrors.course_schedule }}
            </div>
          </div>
        </div>
        <hr/>

        <!-- Фамилия -->
        <div class="form-group row align-items-center" :class="getFieldClass('lastname')">
          <label class="col-sm-2 col-form-label">{{ strings.lastname }}</label>
          <div class="col-sm-10">
            <input
                type="text"
                class="form-control"
                :value="lastname"
                disabled
                required
            >
          </div>
        </div>

        <!-- Имя -->
        <div class="form-group row align-items-center" :class="getFieldClass('firstname')">
          <label class="col-sm-2 col-form-label">{{ strings.firstname }}</label>
          <div class="col-sm-10">
            <input
                type="text"
                class="form-control"
                :value="firstname"
                disabled
                required
            >
          </div>
        </div>

        <!-- Отчество -->
        <div class="form-group row align-items-center">
          <label for="middleName" class="col-sm-2 col-form-label">{{ strings.middleName }}</label>
          <div class="col-sm-10">
            <input
                type="text"
                class="form-control"
                :class="{'is-invalid': validationErrors.middleName}"
                id="middleName"
                :placeholder="strings.placeholderMiddleName"
                v-model="formData.middleName"

            >
            <div v-if="validationErrors.middleName" class="invalid-feedback d-block">
              {{ validationErrors.middleName }}
            </div>
          </div>
        </div>

        <!-- Телефон -->
        <div class="form-group row align-items-center" :class="getFieldClass('telephone')">
          <label for="telephone" class="col-sm-2 col-form-label">{{ strings.telephone }}</label>
          <div class="col-sm-10">
            <input
                type="text"
                class="form-control"
                :class="{'is-invalid': validationErrors.telephone}"
                id="telephone"
                aria-describedby="telephoneHelp"
                :placeholder="strings.placeholderTelephone"
                v-model="formData.telephone"
                v-mask="'+7 (###) ###-##-##'"
                required
            >
            <small id="telephoneHelp" class="form-text text-muted">{{ strings.helperTelephone }}</small>
            <div v-if="validationErrors.telephone" class="invalid-feedback d-block">
              {{ validationErrors.telephone }}
            </div>
          </div>
        </div>

        <!-- Гражданство -->
        <div class="form-group row" :class="getFieldClass('citizenship')">
          <label for="citizenship" class="col-sm-2 col-form-label">{{ strings.citizenship }}</label>
          <div class="col-sm-10">
            <div v-if="isLoadingCitizenships" class="spinner-border text-primary" role="status">
              <span class="visually-hidden"></span>
            </div>
            <boostrap-moodle-select
                v-else-if="citizenships && citizenships.length"
                :options="citizenships"
                id="citizenship"
                :label="strings.citizenship"
                v-model="formData.citizenship"
                :class="{'is-invalid': validationErrors.citizenship}"
                required
            />
            <div v-if="validationErrors.citizenship" class="invalid-feedback d-block">
              {{ validationErrors.citizenship }}
            </div>
          </div>
        </div>

        <!-- СНИЛС -->
        <div class="form-group row align-items-center"
             :class="snilsRequired">
          <label for="snils" class="col-sm-2 col-form-label">{{ strings.snils }}</label>
          <div class="col-sm-10">
            <input
                type="text"
                :required="formData.citizenship === '0'"
                class="form-control"
                id="snils"
                :placeholder="strings.placeholderSnils"
                v-model="formData.snils"
                v-mask="'###-###-### ##'"
            >
            <div v-if="validationErrors.snils" class="invalid-feedback d-block">
              {{ validationErrors.snils }}
            </div>
          </div>
        </div>

        <!-- Файл СНИЛС -->
        <div class="form-group row" :class="snilsRequired">
          <label for="snilsFile" class="col-sm-2 col-form-label">{{ strings.snilsFile }}</label>
          <div class="col-sm-10">
            <input
                :required="formData.citizenship === '0'"
                style="background-color: white;"
                type="file"
                class="form-control-file"
                id="snilsFile"
                :placeholder="strings.placeholderSnilsFile"
                @change="handleFile($event, 'snilsFile')"
            >
          </div>
        </div>

        <!-- ИНН -->
        <div class="form-group row" :class="getFieldClass('inn')">
          <label for="inn" class="col-sm-2 col-form-label">{{ strings.inn }}</label>
          <div class="col-sm-10">
            <input
                type="text"
                class="form-control"
                :class="{'is-invalid': validationErrors.inn}"
                id="inn"
                :placeholder="strings.placeholderInn"
                v-model="formData.inn"
                v-mask="'############'"
            >
            <div v-if="validationErrors.inn" class="invalid-feedback d-block">
              {{ validationErrors.inn }}
            </div>
          </div>
        </div>

        <!-- ИНН Файл -->
        <div class="form-group row" :class="getFieldClass('innFile')">
          <label for="innFile" class="col-sm-2 col-form-label">{{ strings.innFile }}</label>
          <div class="col-sm-10">
            <input
                style="background-color: white;"
                type="file"
                class="form-control-file"
                id="innFile"
                :placeholder="strings.placeholderInnFile"
                @change="handleFile($event, 'innFile')"
            >
          </div>
        </div>

        <!-- Пол -->
        <div class="form-group row align-items-center" :class="getFieldClass('sex')">
          <label class="col-sm-2 col-form-label">{{ strings.sex }}</label>
          <div class="col-sm-10">
            <div class="form-check form-check-inline">
              <input
                  class="form-check-input"
                  type="radio"
                  name="nameSex"
                  id="sex1"
                  value="0"
                  v-model="formData.sex"
                  required
              >
              <label class="form-check-label" for="sex1">{{ strings.sexF }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input
                  class="form-check-input"
                  type="radio"
                  name="nameSex"
                  id="sex2"
                  value="1"
                  v-model="formData.sex"
                  required
              >
              <label class="form-check-label" for="sex2">{{ strings.sexM }}</label>
            </div>
          </div>
        </div>

        <!-- Дата рождения -->
        <div class="form-group row align-items-center" :class="getFieldClass('birthday')">
          <label for="birthday" class="col-sm-2 col-form-label">{{ strings.birthday }}</label>
          <div class="col-sm-10">
            <input 
                type="date" 
                class="form-control"
                :class="{'is-invalid': validationErrors.birthday}"
                v-model="formData.birthday" 
                id="birthday"
                required
            >
            <div v-if="validationErrors.birthday" class="invalid-feedback d-block">
              {{ validationErrors.birthday }}
            </div>
          </div>
        </div>

        <hr/>
        <h2>{{ strings.passportData }}</h2>
        <!-- Тип Документа -->
        <div class="form-group row" :class="getFieldClass('document_type')">
          <label for="document_type" class="col-sm-2 col-form-label">{{ strings.documentTypes }}</label>
          <div class="col-sm-10">
            <div v-if="isLoadingDocumentTypes" class="spinner-border text-primary" role="status">
              <span class="visually-hidden"></span>
            </div>
            <boostrap-moodle-select
                v-else-if="documentTypes && documentTypes.length"
                :options="documentTypes"
                id="document_type"
                :label="strings.documentTypes"
                v-model="formData.document_type"
                :class="{'is-invalid': validationErrors.document_type}"
                required
            />
            <div v-if="validationErrors.document_type" class="invalid-feedback d-block">
              {{ validationErrors.document_type }}
            </div>
          </div>
        </div>

        <!-- Файл паспорта -->
        <div class="form-group row" :class="getFieldClass('passportFile')">
          <label for="passportFile" class="col-sm-2 col-form-label">{{ strings.passportFile }}</label>
          <div class="col-sm-10">
            <input
                style="background-color: white;"
                type="file"
                class="form-control-file"
                id="passportFile"
                :placeholder="strings.placeholderPassportFile"
                @change="handleFile($event, 'passportFile')"
                required
            >
          </div>
<!--          <div class="col-sm-2">
            <button
                v-if="formData.passportFile"
                class="btn btn-primary" @click.prevent="formData.uploadPassportAvailable = true">
              Прикрепить новый файл паспорта
            </button>
          </div>-->
        </div>

        <!-- Серия паспорта -->
        <div class="form-group row align-items-center" :class="otherTypePassport" >
          <label for="serial" class="col-sm-2 col-form-label">{{ strings.serial }}</label>
          <div class="col-sm-10">
            <input
                type="text"
                class="form-control"
                :class="{'is-invalid': validationErrors.serial}"
                id="serial"
                :placeholder="strings.placeholderSerial"
                v-model="formData.serial"
                :required="formData.document_type === userStore.guidPassportRF"
            >
            <div v-if="validationErrors.serial" class="invalid-feedback d-block">
              {{ validationErrors.serial }}
            </div>
          </div>
        </div>

        <!-- Номер паспорта -->
        <div class="form-group row" :class="getFieldClass('number')">
          <label for="number" class="col-sm-2 col-form-label">{{ strings.number }}</label>
          <div class="col-sm-10">
            <input
                type="text"
                class="form-control"
                :class="{'is-invalid': validationErrors.number}"
                id="number"
                :placeholder="strings.placeholderNumber"
                v-model="formData.number"
                required
            >
            <div v-if="validationErrors.number" class="invalid-feedback d-block">
              {{ validationErrors.number }}
            </div>
          </div>
        </div>

        <!-- Дата выдачи паспорта -->
        <div class="form-group row" :class="getFieldClass('whenIssued')">
          <label for="whenIssued" class="col-sm-2 col-form-label">{{ strings.whenIssued }}</label>
          <div class="col-sm-10">
            <input
                type="date"
                class="form-control"
                :class="{'is-invalid': validationErrors.whenIssued}"
                id="whenIssued"
                :placeholder="strings.placeholderWhenIssued"
                v-model="formData.whenIssued"
                required
            >
            <div v-if="validationErrors.whenIssued" class="invalid-feedback d-block">
              {{ validationErrors.whenIssued }}
            </div>
          </div>
        </div>

        <!-- Кем выдан паспорт -->
        <div class="form-group row" :class="otherTypePassport" >
          <label for="whoIssued" class="col-sm-2 col-form-label">{{ strings.whoIssued }}</label>
          <div class="col-sm-10">
            <input
                type="text"
                class="form-control"
                :class="{'is-invalid': validationErrors.whoIssued}"
                id="whoIssued"
                :placeholder="strings.placeholderWhoIssued"
                v-model="formData.whoIssued"
                :required="formData.document_type === userStore.guidPassportRF"
            >
            <div v-if="validationErrors.whoIssued" class="invalid-feedback d-block">
              {{ validationErrors.whoIssued }}
            </div>
          </div>
        </div>

        <!-- Код подразделения -->
        <div class="form-group row align-items-center" :class="otherTypePassport">
          <label for="divisionCode" class="col-sm-2 col-form-label">{{ strings.divisionCode }}</label>
          <div class="col-sm-10">
            <input
                type="text"
                class="form-control"
                :class="{'is-invalid': validationErrors.divisionCode}"
                id="divisionCode"
                :placeholder="strings.placeholderDivisionCode"
                v-model="formData.divisionCode"
                v-mask="'###-###'"
                :required="formData.document_type === userStore.guidPassportRF"
            >
            <div v-if="validationErrors.divisionCode" class="invalid-feedback d-block">
              {{ validationErrors.divisionCode }}
            </div>
          </div>
        </div>

        <!-- Адрес регистрации -->
        <div class="form-group row align-items-center" :class="getFieldClass('addressOfRegistration')">
          <label for="addressOfRegistration" class="col-sm-2 col-form-label">{{ strings.addressOfRegistration }}</label>
          <div class="col-sm-10">
            <address-input
                id="addressOfRegistration"
                :class="{'is-invalid': validationErrors.addressOfRegistration}"
                :placeholder="strings.placeholderAddressOfRegistration"
                v-model="formData.addressOfRegistration"
                :api-key=daDataApiKey 
                label="w"
                @update:suggestion-data="(data) => formData.addressOfRegistrationData = data"
                required
            />
            <div v-if="validationErrors.addressOfRegistration" class="invalid-feedback d-block">
              {{ validationErrors.addressOfRegistration }}
            </div>
          </div>
        </div>

        <!-- Скан страницы паспорта с пропиской -->
        <div class="form-group row" :class="getFieldClass('scanPassportAddress')">
          <label for="scanPassportAddress" class="col-sm-2 col-form-label">{{ strings.scanPassportAddress }}</label>
          <div class="col-sm-10">
            <input
                style="background-color: white;"
                type="file"
                class="form-control-file"
                id="scanPassportAddress"
                :placeholder="strings.placeholderScanPassportAddress"
                @change="handleFile($event, 'scanPassportAddress')"
                required
            >
          </div>
        </div>

        <!-- Адрес фактического проживания -->
        <div class="form-group row align-items-center" :class="getFieldClass('addressOfActualResidence')">
          <label for="addressOfActualResidence" class="col-sm-2 col-form-label">{{
              strings.addressOfActualResidence
            }}</label>
          <div class="col-sm-10">
            <address-input
                id="addressOfActualResidence"
                :class="{'is-invalid': validationErrors.addressOfActualResidence}"
                :placeholder="strings.placeholderAddressOfActualResidence"
                v-model="formData.addressOfActualResidence"
                :api-key=daDataApiKey 
                label="q"
                @update:suggestion-data="(data) => formData.addressOfActualResidenceData = data"
                required
            />
            <div v-if="validationErrors.addressOfActualResidence" class="invalid-feedback d-block">
              {{ validationErrors.addressOfActualResidence }}
            </div>
          </div>
        </div>

        <hr/>
        <h2>{{ strings.eduDocument }}</h2>

        <!-- Уровень образования -->
        <div class="form-group row" :class="getFieldClass('education_level')">
          <label for="education_level" class="col-sm-2 col-form-label">{{ strings.educationLevel }}</label>
          <div class="col-sm-10">
            <div v-if="isLoadingEducationLevels" class="spinner-border text-primary" role="status">
              <span class="visually-hidden"></span>
            </div>
            <boostrap-moodle-select
                v-else-if="educationLevels && educationLevels.length"
                :options="educationLevels"
                id="education_level"
                :label="strings.education_level"
                v-model="formData.education_level"
                :class="{'is-invalid': validationErrors.education_level}"
                required
            />
            <div v-if="validationErrors.education_level" class="invalid-feedback d-block">
              {{ validationErrors.education_level }}
            </div>
          </div>
        </div>

        <!-- Скрываем поля документа об образовании при определенных уровнях образования -->
        <template v-if="!shouldHideEducationDocFields()">
          <!-- Тип образования -->
<!--          <div class="form-group row align-items-center" :class="getFieldClass('educationType')">
            <label for="educationType" class="col-sm-2 col-form-label">{{ strings.educationType }}</label>
            <div class="col-sm-10">
              <input
                  type="text"
                  class="form-control"
                  id="educationType"
                  :placeholder="strings.placeholderEducationType"
                  v-model="formData.educationType"
                  required
              >
              <div v-if="validationErrors.educationType" class="invalid-feedback d-block">
                {{ validationErrors.educationType }}
              </div>
            </div>
          </div>-->

          <!-- Серия документа об образовании -->
          <div class="form-group row align-items-center" :class="getFieldClass('serialEdu')">
            <label for="serialEdu" class="col-sm-2 col-form-label">{{ strings.serialEdu }}</label>
            <div class="col-sm-10">
              <input
                  type="text"
                  class="form-control"
                  id="serialEdu"
                  :placeholder="strings.placeholderSerialEdu"
                  v-model="formData.serialEdu"
                  required
              >
              <div v-if="validationErrors.serialEdu" class="invalid-feedback d-block">
                {{ validationErrors.serialEdu }}
              </div>
            </div>
          </div>

          <!-- Номер документа об образовании -->
          <div class="form-group row align-items-center" :class="getFieldClass('numberEdu')">
            <label for="numberEdu" class="col-sm-2 col-form-label">{{ strings.numberEdu }}</label>
            <div class="col-sm-10">
              <input
                  type="text"
                  class="form-control"
                  id="numberEdu"
                  :placeholder="strings.placeholderNumberEdu"
                  v-model="formData.numberEdu"
                  required
              >
              <div v-if="validationErrors.numberEdu" class="invalid-feedback d-block">
                {{ validationErrors.numberEdu }}
              </div>
            </div>
          </div>

          <!-- Дата выдачи документа об образовании -->
          <div class="form-group row align-items-center" :class="getFieldClass('whenIssuedEdu')">
            <label for="whenIssuedEdu" class="col-sm-2 col-form-label">{{ strings.whenIssuedEdu }}</label>
            <div class="col-sm-10">
              <input
                  type="date"
                  class="form-control"
                  id="whenIssuedEdu"
                  :placeholder="strings.placeholderWhenIssuedEdu"
                  v-model="formData.whenIssuedEdu"
                  required
              >
              <div v-if="validationErrors.whenIssuedEdu" class="invalid-feedback d-block">
                {{ validationErrors.whenIssuedEdu }}
              </div>
            </div>
          </div>

          <!-- Кем выдан документ об образовании -->
          <div class="form-group row align-items-center" :class="getFieldClass('whoIssuedEdu')">
            <label for="whoIssuedEdu" class="col-sm-2 col-form-label">{{ strings.whoIssuedEdu }}</label>
            <div class="col-sm-10">
              <input
                  type="text"
                  class="form-control"
                  id="whoIssuedEdu"
                  :placeholder="strings.placeholderWhoIssuedEdu"
                  v-model="formData.whoIssuedEdu"
                  required
              >
              <div v-if="validationErrors.whoIssuedEdu" class="invalid-feedback d-block">
                {{ validationErrors.whoIssuedEdu }}
              </div>
            </div>
          </div>

          <div class="mb-3" v-if="!shouldHideEducationDocFields()">
            <div class="form-check mb-3">
              <input
                  class="form-check-input"
                  type="checkbox"
                  id="nameMatchesPassport"
                  v-model="formData.nameMatchesPassport"
              >
              <label class="form-check-label" for="nameMatchesPassport">
                ФИО в дипломе соответствует данным в паспорте
              </label>
            </div>
          </div>

          <div v-if="!formData.nameMatchesPassport"
               class="form-group row" :class="getFieldClass('nameMismatchFile')">
            <label for="nameMismatchFile" class="col-sm-2 col-form-label">Документ о смене ФИО</label>
            <div class="col-sm-10">
              <input
                  style="background-color: white;"
                  type="file"
                  class="form-control-file"
                  id="nameMismatchFile"
                  @change="handleFile($event, 'nameMismatchFile')"
                  required
              >
            </div>
          </div>
          <!-- Файл документа об образовании -->
          <div class="form-group row" :class="getFieldClass('eduFile')">
            <label for="eduFile" class="col-sm-2 col-form-label">{{ strings.eduFile }}</label>
            <div class="col-sm-10">
              <input
                  style="background-color: white;"
                  type="file"
                  class="form-control-file"
                  id="eduFile"
                  :placeholder="strings.placeholderEduFile"
                  @change="handleFile($event, 'eduFile')"
                  required
              >
            </div>
          </div>


        </template>

        <hr/>
        <!-- Группа -->
        <div class="form-group row" :class="getFieldClass('inGroup')">
          <label for="user_group" class="col-sm-2 col-form-label">{{ strings.inGroup }}</label>
          <div class="col-sm-10">
            <div v-if="isLoadingUserGroups" class="spinner-border text-primary" role="status">
              <span class="visually-hidden"></span>
            </div>
            <boostrap-moodle-select
                v-else-if="userGroups && userGroups.length"
                :options="userGroups"
                id="user_group"
                :label="strings.inGroup"
                v-model="formData.inGroup"
                :class="{'is-invalid': validationErrors.inGroup}"
                required
            />
            <div v-if="validationErrors.inGroup" class="invalid-feedback d-block">
              {{ validationErrors.inGroup }}
            </div>
          </div>
        </div>
        <hr/>
        <!-- Чекбоксы -->
        <div class="form-group row">
          <div class="col-sm-2"></div>
          <div class="col-sm-10">
            <div class="form-check form-check-inline">
              <input
                  class="form-check-input"
                  type="checkbox"
                  id="unemployed"
                  v-model="formData.unemployed"
              >
              <label class="form-check-label" for="unemployed">{{ strings.unemployed }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input
                  class="form-check-input"
                  type="checkbox"
                  id="returnToJob"
                  v-model="formData.returnToJob"
              >
              <label class="form-check-label" for="returnToJob">{{ strings.returnToJob }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input
                  class="form-check-input"
                  type="checkbox"
                  id="imprisonment"
                  v-model="formData.imprisonment"
              >
              <label class="form-check-label" for="imprisonment">{{ strings.imprisonment }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input
                  class="form-check-input"
                  type="checkbox"
                  id="insurance"
                  v-model="formData.insurance"
              >
              <label class="form-check-label" for="insurance">{{ strings.insurance }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input
                  class="form-check-input"
                  type="checkbox"
                  id="parentalLeave"
                  v-model="formData.parentalLeave"
              >
              <label class="form-check-label" for="parentalLeave">{{ strings.parentalLeave }}</label>
            </div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-sm-2"></div>
          <div class="col-sm-10">
            <div class="form-check form-check-inline">
              <input
                  class="form-check-input"
                  type="checkbox"
                  id="limitedHealth"
                  v-model="formData.limitedHealth"
              >
              <label class="form-check-label" for="limitedHealth">{{ strings.limitedHealth }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input
                  class="form-check-input"
                  type="checkbox"
                  id="disabled"
                  v-model="formData.disabled"
              >
              <label class="form-check-label" for="disabled">{{ strings.disabled }}</label>
            </div>
            <div class="form-check form-check-inline">
              <input
                  class="form-check-input"
                  type="checkbox"
                  id="intellectualDisabilities"
                  v-model="formData.intellectualDisabilities"
              >
              <label class="form-check-label" for="intellectualDisabilities">{{
                  strings.intellectualDisabilities
                }}</label>
            </div>
          </div>
        </div>

        <!-- Выпадающий список -->
        <boostrap-moodle-select
            v-if="allSelects && allSelects['informationAboutPayer'] && allSelects['informationAboutPayer'].length"
            :options="allSelects['informationAboutPayer']"
            :id="'informationAboutPayer'"
            :label="strings.informationAboutPayer"
            v-model="formData.informationAboutPayer"
        />
        <div v-if="validationErrors.informationAboutPayer" class="invalid-feedback d-block">
          {{ validationErrors.informationAboutPayer }}
        </div>
        <hr/>
        <template v-if="formData.informationAboutPayer === 'legalEntity'">
          <legal-entity-fields
              :form-data="formData"
              :strings="strings"
              :da-data-api-key="daDataApiKey"
              :errors="validationErrors">
          </legal-entity-fields>
        </template>
        <template v-if="formData.informationAboutPayer === 'AnotherPayer'">
          <individualFields
              :strings="strings"
              :form-data="formData"
              :validation-errors="validationErrors"
              :is-field-required="isFieldRequired"
              @update:form-data="(newData) => Object.assign(formData, newData)"
              @file-selected="handleFile($event.event, $event.fieldName)"
          />
        </template>

        <!-- Согласие на обработку данных -->
        <div class="form-group">
          <div class="col-sm-10">
            <div class="form-check">
              <input
                  class="form-check-input"
                  :class="{'is-invalid': validationErrors.personalData}"
                  type="checkbox"
                  id="personalData"
                  v-model="formData.personalData"
              >
              <label class="form-check-label" for="personalData">
                Согласие на <a target="_blank" href="https://www.chuvsu.ru/politika-konfidenczialnosti/">обработку персональных данных</a>

              </label>
              <div v-if="validationErrors.personalData" class="invalid-feedback d-block">
                {{ validationErrors.personalData }}
              </div>
            </div>
          </div>
        </div>
        <hr/>

        <!-- Кнопки -->
        <div class="form-group row">
          <div class="col-sm-12 text-center">
<!--            <button type="button" class="btn btn-secondary mr-2" @click="fillTestData">Заполнить тестовыми данными
            </button>-->
            <button type="submit" class="btn btn-primary">{{ strings.submitSurvey }}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.is-required label::after {
  content: " *";
  color: red;
}

.is-invalid {
  border-color: #dc3545;
}

.is-invalid + .invalid-feedback {
  display: block;
}

.form-group {
  margin-bottom: 1rem;
}

.required-field {
  border-left: 3px solid #dc3545;
  padding-left: 0.5rem;
}
</style>
