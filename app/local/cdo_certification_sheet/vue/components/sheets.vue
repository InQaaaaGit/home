<script>
import {mapGetters, mapState} from "vuex";
import {ajax, store} from "../store";
import StudentsTable from "./students-table.vue";
import NotFoundOpenSheet from "./not-found-open-sheet.vue";
import SheetInfo from "./sheet-info.vue";
import Commission_table from "./commission_table.vue";
import {TYPE} from "vue-toastification";
import Notification from 'core/notification';
import SheetButtons from "./sheet-buttons.vue";

export default {
  name: "sheets",
  components: {SheetButtons, Commission_table, SheetInfo, NotFoundOpenSheet, StudentsTable},
  async created() {
    console.log(123123)
    await store.dispatch('loadListSheet');
  },
  computed: {
    ...mapState(['strings', 'sheets', 'userID', 'isAppLoading', 'showBRS', 'absenceGUID']),
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
    isSheetReachDateStart() {
      return this.isSheetReachDateStart(sheet.guid);
    },
    isAllGradeSet() {
      return this.isAllGradeSet(sheet.guid);
    },
    isThemedSheet() {
      return this.isThemedSheet(sheet.guid);
    },
    isCurrentUserSetAgreed() {
      return this.isCurrentUserSetAgreed(sheet.guid);
    },
    isAllAgreedSet() {
      return this.isAllAgreedSet(sheet.guid);
    },
    isAgreedAllow() {
      return this.isAgreedAllowInSheet(sheet.guid);
    },
    isChairmanInSheet() {
      return this.isChairmanInSheet(sheet.guid);
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
      if (result.closed) {
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
          async () => {
            await this.closedSheet(sheet);
          }
      );
    },
    isValidGUID(sheet) {
      return sheet.hasOwnProperty('guid');
    }
  }
};
</script>

<template>
  <div v-if="!isAppLoading" class="container-fluid">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
          integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <not-found-open-sheet/>
    <template v-if="haveSheets">
      <div
          class="nav nav-tabs mb-3"
          id="v-pills-tab"
          role="tablist"
      >
        <!--        <li class="nav-item" >-->
        <a
            v-for="(sheet, index) in sheets" :key="sheet.guid"
            class="nav-link" :class="firstSheet(index)"
            :id="sheet.guid"
            data-toggle="tab"
            :href="'#content-'+sheet.guid"
            role="tab"
            :aria-controls="'v-pills-'+sheet.guid"
            :aria-selected="index===0"
            v-on:click="currentSheet = sheet"
        >
          Ведомость {{ sheet.group }}
        </a>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="tab-content" id="v-pills-tabContent">
            <div v-for="(sheet, index) in sheets" :key="sheet.guid"
                 class="tab-pane fade " :class="firstSheet(index)"
                 :id="'content-'+sheet.guid" role="tabpanel"
                 :aria-labelledby="sheet.guid">
              <div class="row" v-if="isValidGUID(sheet)">
                <div class="col-7">
                  <template v-if="absenceGUID.length">
                    <students-table :sheet="sheet">
                    </students-table>
                  </template>
                  <template v-else>
                    <div class="alert alert-warning">{{ strings.guid_absence_not_set }}</div>
                  </template>
                </div>
                <div class="col-5">
                  <sheet-buttons :sheet="sheet">
                  </sheet-buttons>
                  <commission_table :sheet="sheet"/>
                  <sheet-info :sheet="sheet"></sheet-info>
                </div>
              </div>
              <div class="row" v-else>
                <div class="col-12">
                  <div class="alert alert-danger">{{ strings.sheet_guid_not_found }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
  <div v-else class="spinner-border text-dark" role="status">
    <span class="sr-only">Loading...</span>
  </div>
</template>

<style scoped>

</style>