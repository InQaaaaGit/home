<script setup>
import { ref, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useToast } from 'vue-toastification';
import { useCertificationStore } from '../../stores/certification.js';

const props = defineProps({
  sheet: {
    type: Object,
    required: true,
  },
});

const certificationStore = useCertificationStore();
const { strings, userID, showDownloadButton } = storeToRefs(certificationStore);
const toast = useToast();

const isLoading = ref(false);
const isLoadingClosed = ref(false);

const isSheetReachDateStartButton = computed(() => certificationStore.isSheetReachDateStart(props.sheet.guid));
const isAllGradeSetButton = computed(() => certificationStore.isAllGradeSet(props.sheet.guid));
const isThemedSheetButton = computed(() => certificationStore.isThemedSheet(props.sheet.guid));
const isCurrentUserSetAgreedButton = computed(() => certificationStore.isCurrentUserSetAgreed(props.sheet.guid));
const isAllAgreedSetButton = computed(() => certificationStore.isAllAgreedSet(props.sheet.guid));
const isAgreedAllowInSheetButton = computed(() => certificationStore.isAgreedAllowInSheet(props.sheet.guid));
const isChairmanInSheetButton = computed(() => certificationStore.isChairmanInSheet(props.sheet.guid));

const closeButtonDisable = computed(() => {
  if (isAgreedAllowInSheetButton.value) {
    return isAllAgreedSetButton.value && (isAllGradeSetButton.value && isSheetReachDateStartButton.value);
  } else {
    return (isAllGradeSetButton.value && isSheetReachDateStartButton.value);
  }
});

const preparedGrades = (sheet) => {
  let grades = [];
  sheet.students.forEach(student => {
    grades.push({
      save_grade: student.grade,
      sheet_guid: sheet.guid,
      student_guid: student.guid
    });
  });
  return grades;
};

const changeAgreed = async (sheet) => {
  isLoading.value = true;
  const grades = preparedGrades(sheet);
  try {
    await certificationStore.commissionAgreed({
      grades: grades,
      sheet_guid: sheet.guid,
      user_id: userID.value,
      uid_request: sheet.uid_request
    });
    await certificationStore.getListSheet();
  } catch (error) {
    console.error('Error changing agreed status:', error);
  } finally {
    isLoading.value = false;
  }
};

const closedSheetNotify = (sheetGuid) => {
  require(['core/notification'], (Notification) => {
    Notification.confirm(
        strings.value.grades_confirm_change_close_title,
        strings.value.grades_confirm_change_close_message,
        strings.value.grades_confirm_change_close_yes,
        strings.value.grades_confirm_change_close_no,
        async () => {
          isLoadingClosed.value = true;
          try {
            await certificationStore.closeSheet(sheetGuid);
            await certificationStore.getListSheet();
          } catch (error) {
            console.error('Error closing sheet:', error);
          } finally {
            isLoadingClosed.value = false;
          }
        }
    );
  });
};
</script>

<template>
  <div class="row mb-1">
    <div class="col-12 d-flex">
      <button
          v-if="isAgreedAllowInSheetButton && !isCurrentUserSetAgreedButton"
          class="btn btn-primary d-flex align-items-center"
          @click="changeAgreed(sheet)"
          :disabled="!isAllGradeSetButton && !isSheetReachDateStartButton"
      >
        <div v-show="isLoading" class="spinner-border"></div>
        {{ strings.commission_sheet_agreed }}
      </button>
      <button v-if="(!isAgreedAllowInSheetButton || isChairmanInSheetButton)"
              class="btn btn-primary ml-2 d-flex align-items-center"
              @click="closedSheetNotify(sheet.guid)"
              :disabled="!closeButtonDisable"
      >
        <div v-show="isLoadingClosed" class="spinner-border"></div>
        {{ strings.close_sheet_close_button }}
      </button>
      <a v-if="showDownloadButton"
         class="btn btn-primary ml-2 d-flex align-items-center"
         target="_blank"
         :href="'download.php?guid='+sheet.guid">{{ strings.sheet_download }}
      </a>
    </div>
  </div>
</template>

<style scoped>
</style>
