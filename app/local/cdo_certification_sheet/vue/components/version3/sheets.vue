<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useCertificationStore } from '../../stores/certification.js';

// Импорт компонентов
import NotFoundOpenSheet from './not-found-open-sheet.vue';
import LayoutManagerDispatcher from './LayoutManagerDispatcher.vue';

const certificationStore = useCertificationStore();
const { 
  strings, 
  sheets, 
  userID, 
  isAppLoading, 
  showBRS, 
  absenceGUID,
  haveSheets,
  isSheetReachDateStart,
  isAllGradeSet,
  isThemedSheet,
  isCurrentUserSetAgreed,
  isAllAgreedSet,
  isAgreedAllowInSheet,
  isChairmanInSheet
} = storeToRefs(certificationStore);

const currentSheet = ref(null);
const isLoading = ref(false);
const isLoadingClosed = ref(false);

// Lifecycle
onMounted(async () => {
  await certificationStore.getListSheet();
});

// Отслеживаем изменения в sheets
watch(sheets, (newSheets) => {
  if (newSheets && newSheets.length > 0) {
    // Если есть текущая ведомость, пытаемся найти её в обновленном списке
    if (currentSheet.value && currentSheet.value.guid) {
      const foundSheet = newSheets.find(sheet => sheet.guid === currentSheet.value.guid);
      if (foundSheet) {
        // Обновляем данные текущей ведомости, сохраняя её выбранной
        currentSheet.value = foundSheet;
      } else {
        // Если текущая ведомость больше не существует, выбираем первую
        currentSheet.value = newSheets[0];
      }
    } else {
      // Если ведомость не выбрана, выбираем первую
      currentSheet.value = newSheets[0];
    }
  }
}, { deep: true });

// Computed properties
const isSheetReachDateStartComputed = computed(() => (sheetGuid) => {
  return isSheetReachDateStart.value(sheetGuid);
});

const isAllGradeSetComputed = computed(() => (sheetGuid) => {
  return isAllGradeSet.value(sheetGuid);
});

const isThemedSheetComputed = computed(() => (sheetGuid) => {
  return isThemedSheet.value(sheetGuid);
});

const isCurrentUserSetAgreedComputed = computed(() => (sheetGuid) => {
  return isCurrentUserSetAgreed.value(sheetGuid);
});

const isAllAgreedSetComputed = computed(() => (sheetGuid) => {
  return isAllAgreedSet.value(sheetGuid);
});

const isAgreedAllowComputed = computed(() => (sheetGuid) => {
  return isAgreedAllowInSheet.value(sheetGuid);
});

const isChairmanInSheetComputed = computed(() => (sheetGuid) => {
  return isChairmanInSheet.value(sheetGuid);
});

// Methods
const selectSheet = (sheetGuid) => {
  const selectedSheet = sheets.value.find(sheet => sheet.guid === sheetGuid);
  if (selectedSheet) {
    currentSheet.value = selectedSheet;
  }
};

const preparedGrades = (sheet) => {
  let preparedGrades = [];
  sheet.students.forEach(student => {
    preparedGrades.push({
      save_grade: student.grade,
      sheet_guid: sheet.guid,
      student_guid: student.guid
    });
  });
  return preparedGrades;
};

const changeAgreed = async (sheet) => {
  isLoading.value = true;
  try {
    const grades = preparedGrades(sheet);
    const result = await certificationStore.commissionAgreed({
      grades: grades,
      sheet_guid: sheet.guid,
      user_id: userID.value,
      uid_request: sheet.uid_request
    });
    
    if (result.success) {
      await certificationStore.getListSheet();
    }
  } catch (error) {
    console.error('Ошибка согласования:', error);
  } finally {
    isLoading.value = false;
  }
};

const closedSheet = async (sheet) => {
  isLoadingClosed.value = true;
  try {
    const result = await certificationStore.closeSheet(sheet);
    if (result.closed) {
      await certificationStore.getListSheet();
    }
  } catch (error) {
    console.error('Ошибка закрытия ведомости:', error);
  } finally {
    isLoadingClosed.value = false;
  }
};

const closedSheetNotify = (sheet) => {
  require(['core/notification'], (Notification) => {
    Notification.confirm(
      strings.value.grades_confirm_change_close_title,
      strings.value.grades_confirm_change_close_message,
      strings.value.grades_confirm_change_close_yes,
      strings.value.grades_confirm_change_close_no,
      async () => {
        await closedSheet(sheet);
      }
    );
  });
};

const isValidGUID = (sheet) => {
  return sheet && sheet.hasOwnProperty('guid');
};
</script>

<template>
  <div v-if="!isAppLoading">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    
    <NotFoundOpenSheet v-if="!haveSheets" />
    <template v-if="haveSheets">
      <div class="form-group mb-3">
        <label :for="'sheet-select'" class="form-label font-weight-bold">
          {{ strings.sheet_tab_name || 'Ведомость' }}
        </label>
        <select
            :id="'sheet-select'"
            class="form-control"
            :value="currentSheet?.guid || ''"
            @change="selectSheet($event.target.value)"
        >
          <option
              v-for="sheet in sheets"
              :key="sheet.guid"
              :value="sheet.guid"
          >
            {{ strings.sheet_tab_name }} {{ sheet.group }} {{sheet.discipline}}
          </option>
        </select>
      </div>
      <div class="row">
        <div class="col-12">
          <div v-if="currentSheet">
            <div v-if="isValidGUID(currentSheet)">
              <LayoutManagerDispatcher :sheet="currentSheet" />
            </div>
            <div class="row" v-else>
              <div class="col-12">
                <div class="alert alert-danger">{{ strings.sheet_guid_not_found }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
  <div v-else class="spinner-border text-dark" role="status">
    <span class="sr-only">{{ strings.loading }}</span>
  </div>
</template>

<style scoped>
/* Используем стандартные стили Bootstrap из Moodle */
</style>
