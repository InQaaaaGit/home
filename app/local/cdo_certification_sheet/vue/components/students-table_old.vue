<script>
import {mapActions, mapState} from "vuex";
import {ajax, store} from "../store";
import {TYPE} from "vue-toastification";

export default {
  props: ['sheet'],
  data: () => ({
    inDev: false,
    isBFU: true,
    useColor: false,
    //absenceGUID: "b93acccf-cef9-4ed9-b1bc-f74e4a988d55",
    //absenceGUID: "2c28f956-4989-11eb-7d9a-005056970631",
    //absenceGUID: "b0379dd3-cc35-11e6-b94d-005056c00008",
    emptyGUID: "00000000-0000-0000-0000-000000000000",
  }),
  name: "students-table",
  computed: {
    ...mapState(['strings', 'sheets', 'userID', 'showBRS', 'absenceGUID',
      'typeControlCredit', 'typeControlExamine', 'typeControlDiffCredit',
      'divisionForBRS']
    ),
    isThemedSheet() {
      return store.getters.isThemedSheet(this.sheet.guid);
    },
    isAgreedAllow() {
      return store.getters.isAgreedAllowInSheet(this.sheet.guid);
    },
    isChairmanInSheet() {
      return store.getters.isChairmanInSheet(this.sheet.guid);
    },
    sheetIsOnBRS() {
      return !this.showBRS.includes(this.sheet.level_education.replaceAll(' ', ''));
    },
    sheetUseDivisionRules() {
      //return (this.divisionForBRS.includes(this.sheet.division.replaceAll(' ', ''))) && (this.isControlExamine || this.isControlDiffCredit);
      return this.sheet.useDivisionRules;
    },
    isControlCredit() {
      return this.sheet.type_control_code === this.typeControlCredit;
    },
    isControlExamine() {
      return this.sheet.type_control_code === this.typeControlExamine;
    },
    isControlDiffCredit() {
      return this.sheet.type_control_code === this.typeControlDiffCredit;
    },
    maximumGradeSheet() {
      return this.sheet.maximumGrade;
    }
  },
  methods: {
    ...mapActions(['updateGradesInSheet']),
    writeGradesFromNumbers(number) {
      if (number > 0 && number < 55) {
        return 'Неудовлетворительно';
      }
      if (number > 54 && number < 71) {
        return 'Удовлетворительно';
      }
      if (number > 70 && number < 86) {
        return 'Хорошо';
      }
      if (number > 85 && number < 101) {
        return 'Отлично';
      }
      return '';
    },
    convertGrade(grade, gradeType = "semester", gradeSemester = 0) { // 000000003 - zachet
      if (grade === "") {
        return 0;
      }

      grade = parseInt(grade);
      if (grade < 0) {
        return 0;
      }
      if (gradeType === "semester") {
        if (grade > this.maximumGradeSheet) {
          return this.maximumGradeSheet;
        }
      } else {
        /*if (gradeSemester === 100) {
          return 0;
        }*/
        if (100 - gradeSemester > 0 && grade > 100 - gradeSemester) {
          console.log(grade, gradeType, gradeSemester, '100 - gradeSemester > 0 && grade > 100 - gradeSemester')
          return 100 - gradeSemester;
        }
        if (grade > 30) {
          console.log(grade, gradeType, gradeSemester, 'grade > 30')
          return 30;
        }
        if (100 - gradeSemester === 0) {
          console.log(grade, gradeType, gradeSemester, '100 - gradeSemester === 0')
          return 0;
        }

      }
      return grade;
    },
    renderGrade(grade) {
      let grade_found = this.sheet.system_grades.find((element) => element.guid === grade);
      if (!grade_found) {
        return "";
      }
      return grade_found.short_name;
    },
    updateGradeAbsence(e, grade, student, point_semester = null, point_control_event = null, studentGrade = null) {
      const GUIDGrade = e.target.checked ? this.absenceGUID : this.emptyGUID;
      studentGrade.point_semester = e.target.checked ? -1 : 0;
      point_control_event = 0;
      point_semester = 0;
      this.updateGrade(
          GUIDGrade,
          student,
          this.sheet.guid,
          '',
          point_semester,
          point_control_event,
          'insert_absence'
      );
    },
    async updateGrade(grade, student, sheet, theme = '', point_semester = null, point_control_event = null, apiUsage = 'insert_grade') {

      let parameters = {grade: grade, student: student, sheet: sheet};
      if (theme.length) {
        Object.assign(parameters, {theme: theme});
      }
      if (point_semester != null) {
        if (point_semester !== '') {
          point_semester = this.convertGrade(point_semester, "semester");
        }

        this.$store.commit('updateSheet', this.sheet);
        Object.assign(parameters, {point_semester: point_semester});
      }
      if (point_control_event != null) {
        if (point_control_event !== '') {
          point_control_event = this.convertGrade(point_control_event, "point_control", point_semester);
        }
        this.$store.commit('updateSheet', this.sheet);
        Object.assign(parameters, {point_control_event: point_control_event});
      }
      Object.assign(parameters, {is_brs: apiUsage});
      let result = await ajax('insert_grade', parameters);
      if (result.success) {
        this.$toast(this.strings.toast_success, {
          timeout: 2000,
          type: TYPE.SUCCESS
        });
        this.sheet.students.forEach(element => {
          if (element.guid === student) {
            element.grade = result.grade;
            element.point_control_event= point_control_event;
          }
        });

        this.updateGradesInSheet(this.sheet);
      }
    },

  }
};
</script>

<template>
  <div>
    <table class="table">
      <thead>
      <tr>
        <th scope="col" class="col-5">{{ strings.table_sheet_user_full_name }}</th>
        <th scope="col" class="col-4">{{ strings.table_sheet_grade_book }}</th>
        <th scope="col" class="col-2">{{ strings.table_sheet_grade }}</th>
        <th scope="col" class="col-1">{{ strings.table_sheet_teacher_grade }}</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="studentGrade in sheet.students" :key="studentGrade.guid">
        <td class="col-5">
          <div class="row">
            <div class="col-12">
              {{ studentGrade.full_name }}
            </div>
          </div>
        </td>
        <td class="col-4">
          <div class="row">
            <div class="col-12">
              {{ studentGrade.grade_book }}
            </div>
          </div>
        </td>
        <td class="col-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">

                <div class="row" v-if="!sheet.useBRS">
                  <!--                                <div class="row">-->
                  <div class="col-12">
                    <select class="custom-select w-100"
                            :disabled="isAgreedAllow && !isChairmanInSheet"
                            v-model="studentGrade.grade"
                            @change="updateGrade(
                                                studentGrade.grade,
                                                studentGrade.guid,
                                                sheet.guid)"
                    >
                      <option :value="grade.guid" v-for="grade in sheet.system_grades" :key="grade.id">
                        {{ grade.short_name }}
                      </option>
                    </select>
                  </div>
                </div>
                <template v-else>
                  <template>
                    <!--                  <template v-if="absenceGUID !== studentGrade.grade">-->
                    <!--                  <template v-if="studentGrade.point_semester !== -1">-->
                    <div class="row">
                      <div class="col-12">
                        <label>{{ strings.point_semester }}:</label>
                        <input v-model="studentGrade.point_semester"
                               class="form-control"
                               type="number"
                               min="0" max="100"
                               @change="updateGrade(studentGrade.grade,
                                                  studentGrade.guid,
                                                  sheet.guid,
                                                  studentGrade.theme,
                                                  studentGrade.point_semester,
                                                  studentGrade.point_control_event,
                                                  'Insert_grade_brs'
                                                  )"
                        />
                      </div>
                    </div>
                    <div class="row" v-if="isBFU">
                      <div class="col-12">
                        <label>{{ strings.point_control_event }}:</label>
                        <input v-model="studentGrade.point_control_event"
                               class="form-control"
                               type="number"
                               :disabled="studentGrade.grade === absenceGUID || sheetUseDivisionRules"
                               min="0" max="30"
                               @change="updateGrade(studentGrade.grade,
                                                  studentGrade.guid,
                                                  sheet.guid,
                                                  studentGrade.theme,
                                                  studentGrade.point_semester,
                                                  studentGrade.point_control_event,
                                                  'Insert_grade_brs'
                                                  )"
                        />
                      </div>
                    </div>
                  </template>
                  <div class="row pt-2">
                    <div class="col-6">
                      <label>{{ strings.absence }}</label>
                      <input type="checkbox" class=""
                             :checked="absenceGUID === studentGrade.grade"
                             @change="updateGradeAbsence(
                                 $event,
                                 studentGrade.grade,
                                 studentGrade.guid,
                                 studentGrade.point_semester,
                                 studentGrade.point_control_event,
                                 studentGrade
                             ); "
                      />
                    </div>
                  </div>
                  <template v-if="studentGrade.point_semester !== -1">
                    <div class="row pt-2">
                      <div class="col-12 d-flex align-items-center">
                        Текущая отметка:
                      </div>
                    </div>
                    <div class="row pt-2">
                      <div class="col-12 d-flex align-items-center">
                        <b class="ml-2" v-if="studentGrade.grade===absenceGUID"> Неявка </b>
                        <b class="ml-2" v-else>{{ writeGradesFromNumbers(studentGrade.point_semester) }}</b>
                      </div>
                    </div>
                  </template>
                </template>
              </div>
              <div class="form-group" v-if="isThemedSheet">
                <label :for="'theme'+studentGrade.guid">{{ strings.sheet_theme }}</label>
                <textarea
                    :id="'theme'+studentGrade.guid"
                    v-model="studentGrade.theme"
                    :disabled="isAgreedAllow && !isChairmanInSheet"
                    class="form-control mt-1 w-100"
                    :placeholder="strings.sheet_theme_placeholder"
                    @change="updateGrade(studentGrade.grade, studentGrade.guid, sheet.guid, studentGrade.theme)"
                >
                </textarea>
              </div>
              <template v-if="inDev">
                <div class="form-group">
                  <label class="" :for="'points'+studentGrade.guid">{{ strings.sheet_points_semester }}</label>
                  <input :id="'points'+studentGrade.guid" type="number" class="form-control " max="100" min="0"/>
                </div>
                <div class="form-group">
                  <label class="" :for="studentGrade.guid">{{ strings.sheet_control_event }}</label>
                  <input :id="studentGrade.guid" type="number" class="form-control " max="100" min="0"/>
                </div>
              </template>
            </div>
          </div>
        </td>
        <td class="col-1">{{ studentGrade.teacher_full_name }}</td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.table th, .table td {
  padding: 0.3rem;
  vertical-align: middle;
}

.col-2 .form-group {
  margin-bottom: 0.3rem;
}

.col-2 .form-control {
  padding: 0.2rem 0.4rem;
  font-size: 0.85rem;
  height: calc(1.5em + 0.4rem);
}

.col-2 .row {
  margin-left: -0.2rem;
  margin-right: -0.2rem;
}

.col-2 .col-12 {
  padding-left: 0.2rem;
  padding-right: 0.2rem;
}

.col-2 label {
  margin-bottom: 0.1rem;
  font-size: 0.8rem;
  font-weight: normal;
}

.col-2 .custom-select {
  padding: 0.2rem 1.5rem 0.2rem 0.4rem;
  font-size: 0.85rem;
  height: calc(1.5em + 0.4rem);
}

.col-2 input[type="checkbox"] {
  transform: scale(0.8);
  margin: 0;
}

.col-2 .pt-2 {
  padding-top: 0.3rem !important;
}

.col-2 .ml-2 {
  margin-left: 0.3rem !important;
}

.col-1 {
  font-size: 0.85rem;
}
</style>
