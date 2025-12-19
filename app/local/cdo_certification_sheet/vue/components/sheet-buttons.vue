<script>
import {mapGetters, mapState} from "vuex";
import {ajax, store} from "../store";
import {TYPE} from "vue-toastification";
import Notification from 'core/notification';

export default {
  /* props: {
    sheet: {
      type: Object,
      required: true,
      default: function() {
        return {guid: '00000-00000-00000-00000'};
      }
    }
  }, */
  props: ['sheet'],
  name: "sheet-buttons",
  data: () => ({
    isLoading: false,
    isLoadingClosed: false
  }),
  computed: {
    ...mapState(['strings', 'sheets', 'userID']),
    ...mapGetters([
      'haveSheets',
      'isSheetReachDateStart',
      'isAllGradeSet',
      'isThemedSheet',
      'isCurrentUserSetAgreed',
      'isAllAgreedSet',
      'isAgreedAllowInSheet',
      'isChairmanInSheet',

    ]),
    isSheetReachDateStartButton() {
      return this.isSheetReachDateStart(this.sheet.guid);
    },
    isAllGradeSetButton() {
      console.log(this.isAllGradeSet(this.sheet.guid))
      return this.isAllGradeSet(this.sheet.guid);
    },
    isThemedSheetButton() {
      return this.isThemedSheet(this.sheet.guid);
    },
    isCurrentUserSetAgreedButton() {
      return this.isCurrentUserSetAgreed(this.sheet.guid);
    },
    isAllAgreedSetButton() {
      return this.isAllAgreedSet(this.sheet.guid);
    },
    isAgreedAllowInSheetButton() {
      return this.isAgreedAllowInSheet(this.sheet.guid);
    },
    isChairmanInSheetButton() {
      return this.isChairmanInSheet(this.sheet.guid);
    },
    closeButtonDisable() {
      // Если это ведомость с согласованием смотрим, стоят ли все согласия
      if (this.isAgreedAllowInSheetButton) {
        return this.isAllAgreedSetButton && (this.isAllGradeSetButton && this.isSheetReachDateStartButton);
      } else {
        return (this.isAllGradeSetButton && this.isSheetReachDateStartButton);
      }
    },
  },
  methods: {
    firstSheet(index) {
      if (index === 0) {
        return 'active show';
      }
    },
    preparedGrades(sheet) {
      let preparedGrades = [];
      sheet.students.forEach(student => {
        preparedGrades.push(
            {
              save_grade: student.grade,
              sheet_guid: sheet.guid,
              student_guid: student.guid
            }
        );
      });
      return preparedGrades;
    },
    async changeAgreed(sheet) {
      this.isLoading = true;
      const grades = this.preparedGrades(sheet);
      let result = await ajax('commission_agreed',
          {
            grades: grades,
            sheet_guid: sheet.guid,
            user_id: this.userID,
            uid_request: sheet.uid_request
          }
      );
      if (result.success) {
        await store.dispatch('loadListSheet');
        this.$toast(this.strings.toast_success, {
          timeout: 2000,
          type: TYPE.SUCCESS
        });
      }
      this.isLoading = false;
    },
    async closedSheet(sheet) {
      this.isLoadingClosed = true;
      let result = await ajax('close_sheet', {sheet_guid: sheet});
      if (result.closed || result.success) {
        await store.dispatch('loadListSheet');
        this.$toast(this.strings.toast_success, {
          timeout: 2000,
          type: TYPE.SUCCESS
        });
      }
      this.isLoadingClosed = false;
    },
    closedSheetNotify(sheet) {
      Notification.confirm(
          this.strings.grades_confirm_change_close_title,
          this.strings.grades_confirm_change_close_message,
          this.strings.grades_confirm_change_close_yes,
          this.strings.grades_confirm_change_close_no,
          async() => {
            await this.closedSheet(sheet);
          }
      );
    },
  }
};
</script>

<template>
  <div class="row mb-1">
    <div class="col-12 d-flex">
      <button
          v-if="isAgreedAllowInSheetButton && !isCurrentUserSetAgreedButton"
          class="btn btn-primary d-flex align-items-center"
          @click="changeAgreed(sheet) "
          :disabled="!isAllGradeSetButton && !this.isSheetReachDateStart"
      >
        <div v-show="isLoading" class="spinner-border"></div>
        {{ strings.commission_sheet_agreed }}
      </button>
      <button v-if="(!isAgreedAllowInSheetButton || isChairmanInSheetButton) "
              class="btn btn-primary ml-2 d-flex align-items-center"
              @click="closedSheetNotify(sheet.guid)"
              :disabled="!closeButtonDisable"
      >
        <div v-show="isLoadingClosed" class="spinner-border"></div>
        {{ strings.close_sheet_close_button }}
      </button>
      <a class="btn btn-primary ml-2 d-flex align-items-center"
         target="_blank"
         :href="'print.php?guid='+sheet.guid">{{ strings.sheet_download }}
      </a>
    </div>
  </div>
</template>

<style scoped>

</style>