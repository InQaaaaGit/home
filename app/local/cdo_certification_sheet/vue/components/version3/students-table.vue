<script setup>
import {ref, computed} from 'vue';
import {storeToRefs} from 'pinia';
import {useToast} from 'vue-toastification';
import {useCertificationStore} from '../../stores/certification.js';

const props = defineProps({
  sheet: {
    type: Object,
    required: true,
  },
});

const certificationStore = useCertificationStore();
const {
  strings,
  userID,
  showBRS,
  absenceGUID,
  typeControlCredit,
  typeControlExamine,
  typeControlDiffCredit,
  divisionForBRS
} = storeToRefs(certificationStore);

const toast = useToast();

const inDev = ref(false);
const isBFU = ref(true);
const useColor = ref(false);
const emptyGUID = ref("00000000-0000-0000-0000-000000000000");

// Состояния видимости столбцов
const columnVisibility = ref({
  gradebook: false,
  averageRating: true,
  intermediateRating: true,
  finalRating: true,
  ysc: true,
  grade: true,
  teacher: false,
  note: false
});

const isThemedSheet = computed(() => certificationStore.isThemedSheet(props.sheet.guid));
const isAgreedAllow = computed(() => certificationStore.isAgreedAllowInSheet(props.sheet.guid));
const isChairmanInSheet = computed(() => certificationStore.isChairmanInSheet(props.sheet.guid));
const sheetIsOnBRS = computed(() => !showBRS.value.includes(props.sheet.level_education.replaceAll(' ', '')));
const sheetUseDivisionRules = computed(() => props.sheet.useDivisionRules);
const isControlCredit = computed(() => props.sheet.type_control_code === typeControlCredit.value);
const isControlExamine = computed(() => props.sheet.type_control_code === typeControlExamine.value);
const isControlDiffCredit = computed(() => props.sheet.type_control_code === typeControlDiffCredit.value);
const maximumGradeSheet = computed(() => props.sheet.maximumGrade);

const clampGrade = (grade) => {
  const num = parseInt(grade, 10);
  if (isNaN(num)) return 0;
  if (num < 0) return 0;
  if (num > 100) return 100;
  return num;
};

const writeGradesFromNumbers = (number1, number2) => {
  const number = parseInt(number1) + parseInt(number2);
  if (number > 0 && number < 55) return strings.value.grade_unsatisfactory;
  if (number > 54 && number < 71) return strings.value.grade_satisfactory;
  if (number > 70 && number < 86) return strings.value.grade_good;
  if (number > 85 && number < 101) return strings.value.grade_excellent;
  return '';
};

const convertGrade = (grade, gradeType = "semester", gradeSemester = 0) => {
  if (grade === "") return 0;
  grade = parseInt(grade);
  if (grade < 0) return 0;
  if (gradeType === "semester") {
    if (grade > maximumGradeSheet.value) return maximumGradeSheet.value;
  } else {
    if (gradeSemester === 100) return 0;
    if (100 - gradeSemester > 0 && grade > 100 - gradeSemester) return 100 - gradeSemester;
    if (grade > 30) return 30;
    if (100 - gradeSemester === 0) return 0;
  }
  return grade;
};

const handleRatingChange = (studentGrade, ratingType) => {
  studentGrade[ratingType] = clampGrade(studentGrade[ratingType]);

  updateGrade(
      studentGrade.grade,
      studentGrade.guid,
      props.sheet.guid,
      studentGrade.theme || '',
      studentGrade.point_semester,
      studentGrade.point_control_event,
      'insert_grade',
      ratingType,
      studentGrade.note || '',
      studentGrade[ratingType]
  );
};

const getGradeShortName = (gradeGuid) => {
  if (!gradeGuid || !props.sheet.system_grades) {
    return '';
  }
  const grade = props.sheet.system_grades.find(g => g.guid === gradeGuid);
  return grade ? grade.short_name : '';
};

const isAbsent = (studentGrade) => {
    return studentGrade.grade === absenceGUID.value;
}

const updateGradeAbsence = async (e, studentGrade) => {
  const GUIDGrade = e.target.checked ? absenceGUID.value : emptyGUID.value;
  studentGrade.grade = GUIDGrade; // Optimistic update for instant UI feedback

  let parameters = {
      grade: GUIDGrade,
      student: studentGrade.guid,
      sheet: props.sheet.guid,
      theme: studentGrade.theme || '',
      is_brs: 'insert_absence'
  };

  if (studentGrade.note) {
      parameters.note = studentGrade.note;
  }

  if (props.sheet.useBRS) {
      studentGrade.point_semester = e.target.checked ? -1 : 0;
      studentGrade.point_control_event = 0;
      parameters.point_semester = studentGrade.point_semester;
      parameters.point_control_event = studentGrade.point_control_event;
  }

  try {
      await certificationStore.insertGrade(parameters);
  } catch (error) {
      console.error('Error updating absence:', error);
  }
};

const updateGrade = async (grade, studentGuid, sheetGuid, theme = '', point_semester = null,
                           point_control_event = null, apiUsage = 'insert_grade', ratingType = '', note = '', ratingValue = null) => {
  let point_semester1 = 0;
  let point_control_event1 = 0;
  let parameters = {grade: grade, student: studentGuid, sheet: sheetGuid};

  if (theme.length) {
    parameters.theme = theme;
  }
  if (note.length) {
    parameters.note = note;
  }
  if (point_semester !== null) {
    if (point_semester !== '') {
      point_semester1 = convertGrade(point_semester, "semester");
    }
    parameters.point_semester = point_semester1;
  }
  if (point_control_event !== null) {
    if (point_control_event !== '') {
      point_control_event1 = convertGrade(point_control_event, "point_control", point_semester);
    }
    parameters.point_control_event = point_control_event1;
  }

  parameters.is_brs = apiUsage;
  parameters.rating_type = ratingType;
  
  if (ratingType && ratingValue !== null) {
    parameters.rating_value = ratingValue;
  }

  try {
    await certificationStore.insertGrade(parameters);
  } catch (error) {
    console.error('Error updating grade:', error);
  }
};

const updateNote = async (studentGrade) => {
  await updateGrade(
    studentGrade.grade,
    studentGrade.guid,
    props.sheet.guid,
    studentGrade.theme || '',
    studentGrade.point_semester,
    studentGrade.point_control_event,
    'insert_grade',
    '',
    studentGrade.note || ''
  );
};
</script>

<template>
  <div class="mt-2">
    <!-- Чекбоксы для управления видимостью столбцов -->
    <div class="row mb-2">
      <div class="col-12">
        <div class="d-flex flex-wrap align-items-center p-2 border rounded column-controls">
          <div class="form-check">
            <input 
              class="form-check-input" 
              type="checkbox" 
              :id="'col-gradebook-' + sheet.guid"
              v-model="columnVisibility.gradebook"
            >
            <label class="form-check-label" :for="'col-gradebook-' + sheet.guid">
              {{ strings.table_sheet_grade_book }}
            </label>
          </div>
          <div class="form-check">
            <input 
              class="form-check-input" 
              type="checkbox" 
              :id="'col-averageRating-' + sheet.guid"
              v-model="columnVisibility.averageRating"
            >
            <label class="form-check-label" :for="'col-averageRating-' + sheet.guid">
              {{ strings.average_discipline_rating }}
            </label>
          </div>
          <div class="form-check">
            <input 
              class="form-check-input" 
              type="checkbox" 
              :id="'col-intermediateRating-' + sheet.guid"
              v-model="columnVisibility.intermediateRating"
            >
            <label class="form-check-label" :for="'col-intermediateRating-' + sheet.guid">
              {{ strings.rating_intermediate_certification_discipline }}
            </label>
          </div>
          <div class="form-check">
            <input 
              class="form-check-input" 
              type="checkbox" 
              :id="'col-finalRating-' + sheet.guid"
              v-model="columnVisibility.finalRating"
            >
            <label class="form-check-label" :for="'col-finalRating-' + sheet.guid">
              {{ strings.final_rating_discipline }}
            </label>
          </div>
          <div class="form-check">
            <input 
              class="form-check-input" 
              type="checkbox" 
              :id="'col-ysc-' + sheet.guid"
              v-model="columnVisibility.ysc"
            >
            <label class="form-check-label" :for="'col-ysc-' + sheet.guid">
              {{ strings.ysc_competence_level }}
            </label>
          </div>
          <div class="form-check">
            <input 
              class="form-check-input" 
              type="checkbox" 
              :id="'col-grade-' + sheet.guid"
              v-model="columnVisibility.grade"
            >
            <label class="form-check-label" :for="'col-grade-' + sheet.guid">
              {{ strings.table_sheet_grade }}
            </label>
          </div>
          <div class="form-check">
            <input 
              class="form-check-input" 
              type="checkbox" 
              :id="'col-teacher-' + sheet.guid"
              v-model="columnVisibility.teacher"
            >
            <label class="form-check-label" :for="'col-teacher-' + sheet.guid">
              {{ strings.table_sheet_teacher_grade }}
            </label>
          </div>
          <div class="form-check">
            <input 
              class="form-check-input" 
              type="checkbox" 
              :id="'col-note-' + sheet.guid"
              v-model="columnVisibility.note"
            >
            <label class="form-check-label" :for="'col-note-' + sheet.guid">
              Примечание
            </label>
          </div>
        </div>
      </div>
    </div>
    
    <table class="table">
      <thead>
      <tr>
        <th scope="col" class="col-name">{{ strings.table_sheet_user_full_name }}</th>
        <th scope="col" class="col-gradebook" v-show="columnVisibility.gradebook">{{ strings.table_sheet_grade_book }}</th>
        <th scope="col" class="col-rating" v-show="columnVisibility.averageRating">{{ strings.average_discipline_rating }}</th>
        <th scope="col" class="col-rating" v-show="columnVisibility.intermediateRating">{{ strings.rating_intermediate_certification_discipline }}</th>
        <th scope="col" class="col-rating" v-show="columnVisibility.finalRating">{{ strings.final_rating_discipline }}</th>
        <th scope="col" class="col-rating" v-show="columnVisibility.ysc">{{ strings.ysc_competence_level }}</th>
        <th scope="col" class="col-grade" v-show="columnVisibility.grade">{{ strings.table_sheet_grade }}</th>
        <th scope="col" class="col-teacher" v-show="columnVisibility.teacher">{{ strings.table_sheet_teacher_grade }}</th>
        <th scope="col" class="col-note" v-show="columnVisibility.note">Примечание</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="studentGrade in sheet.students" :key="studentGrade.guid">
        <td class="col-name">
          <div class="row">
            <div class="col-12">
              {{ studentGrade.full_name }}
            </div>
          </div>
        </td>
        <td class="col-gradebook" v-show="columnVisibility.gradebook">
          <div class="row">
            <div class="col-12">
              {{ studentGrade.grade_book }}
            </div>
          </div>
        </td>
        <td class="col-rating" v-show="columnVisibility.averageRating">
          <template v-if="!isAbsent(studentGrade)">
            <input type="number" class="form-control" min="0" max="100" v-model="studentGrade.adr"
                   @change="handleRatingChange(studentGrade, 'adr')"
            >
          </template>
        </td>
        <td class="col-rating" v-show="columnVisibility.intermediateRating">
          <template v-if="!isAbsent(studentGrade)">
            <input type="number" class="form-control" min="0" max="100" v-model="studentGrade.ricd"
                   @change="handleRatingChange(studentGrade, 'ricd')"
            >
          </template>
        </td>
        <td class="col-rating" v-show="columnVisibility.finalRating">
<!--                    <input type="number" class="form-control" min="0" max="100" v-model="studentGrade.frd"
                           @change="updateGrade(
                               studentGrade.grade,
                               studentGrade.guid,
                               sheet.guid,
                               studentGrade.theme,
                               studentGrade.point_semester,
                               studentGrade.point_control_event,
                               'insert_grade',
                               studentGrade.adr,
                               studentGrade.ricd,
                               studentGrade.frd
                           )"
                    >-->
          {{studentGrade.frd}}
        </td>
        <td class="col-rating" v-show="columnVisibility.ysc">
          {{ studentGrade.ysc || '' }}
        </td>
        <td class="col-grade" v-show="columnVisibility.grade">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
<!--                <div class="row pt-2">
                  <div class="col-12">
                    <label class="mr-2">{{ strings.absence }}</label>
                    <input type="checkbox"
                           :checked="isAbsent(studentGrade)"
                           @change="updateGradeAbsence($event, studentGrade);"
                    />
                  </div>
                </div>-->

                <template v-if="!isAbsent(studentGrade)">
                  <div class="row" v-if="!sheet.useBRS || isAgreedAllow">
                    <div class="col-12">
                      <template v-if="isAgreedAllow && !isChairmanInSheet">
                        <span>{{ getGradeShortName(studentGrade.grade) }}</span>
                      </template>
                      <template v-else>
                        <select class="custom-select w-100"
                                v-model="studentGrade.grade"
                                @change="updateGrade(
                                                          studentGrade.grade,
                                                          studentGrade.guid,
                                                          sheet.guid,
                                                          studentGrade.theme || '',
                                                          null,
                                                          null,
                                                          'insert_grade',
                                                          '',
                                                          studentGrade.note || '')"
                        >
                          <option :value="grade.guid" v-for="grade in sheet.system_grades" :key="grade.id">
                            {{ grade.short_name }}
                          </option>
                        </select>
                      </template>
                    </div>
                  </div>
                  <template v-else>
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
                                                    studentGrade.theme || '',
                                                    studentGrade.point_semester,
                                                    studentGrade.point_control_event,
                                                    'Insert_grade_brs',
                                                    '',
                                                    studentGrade.note || '')"
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
                                                    studentGrade.theme || '',
                                                    studentGrade.point_semester,
                                                    studentGrade.point_control_event,
                                                    'Insert_grade_brs',
                                                    '',
                                                    studentGrade.note || '')"
                          />
                        </div>
                      </div>
                      <div class="row pt-2">
                        <div class="col-12 d-flex align-items-center">
                          {{ strings.current_grade }}:
                        </div>
                      </div>
                      <div class="row pt-2">
                        <div class="col-12 d-flex align-items-center">
                          <b class="ml-2" v-if="studentGrade.grade===absenceGUID"> {{ strings.absence_grade }} </b>
                          <b class="ml-2" v-else>{{
                              writeGradesFromNumbers(studentGrade.point_semester, studentGrade.point_control_event)
                            }}</b>
                        </div>
                      </div>
                  </template>
                </template>
              </div>
              <div class="form-group" v-if="isThemedSheet && !isAbsent(studentGrade)">
                <label :for="'theme'+studentGrade.guid">{{ strings.sheet_theme }}</label>
                <textarea
                    :id="'theme'+studentGrade.guid"
                    v-model="studentGrade.theme"
                    :disabled="isAgreedAllow && !isChairmanInSheet"
                    class="form-control mt-1 w-100"
                    :placeholder="strings.sheet_theme_placeholder"
                    @change="updateGrade(studentGrade.grade, studentGrade.guid, sheet.guid, studentGrade.theme, null, null, 'insert_grade', '', studentGrade.note || '')"
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
        <td class="col-teacher" v-show="columnVisibility.teacher">{{ studentGrade.teacher_full_name }}</td>
        <td class="col-note" v-show="columnVisibility.note">
          <textarea
            class="form-control note-input"
            v-model="studentGrade.note"
            @blur="updateNote(studentGrade)"
            :placeholder="'Введите примечание'"
            rows="2"
          ></textarea>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.table {
  table-layout: fixed;
  width: 100%;
}

.table th, .table td {
  padding: 0.3rem;
  vertical-align: middle;
}

/* Распределение ширины колонок */
.col-name {
  width: 20%;
}

.col-gradebook {
  width: 10%;
}

.col-rating {
  width: 8%;
}

.col-grade {
  width: 30%;
}

.col-teacher {
  width: 16%;
}

.col-note {
  width: 15%;
}

.table td .form-group {
  margin-bottom: 0.3rem;
}

.table td .form-control {
  padding: 0.2rem 0.4rem;
  font-size: 0.85rem;
  height: calc(1.5em + 0.4rem);
}

.table td .row {
  margin-left: -0.2rem;
  margin-right: -0.2rem;
}

.table td .col-12 {
  padding-left: 0.2rem;
  padding-right: 0.2rem;
}

.table td label {
  margin-bottom: 0.1rem;
  font-size: 0.8rem;
  font-weight: normal;
}

.table td .custom-select {
  padding: 0.2rem 1.5rem 0.2rem 0.4rem;
  font-size: 0.85rem;
  height: calc(1.5em + 0.4rem);
}

.table td input[type="checkbox"] {
  transform: scale(0.8);
  margin: 0;
}

.table td .pt-2 {
  padding-top: 0.3rem !important;
}

.table td .ml-2 {
  margin-left: 0.3rem !important;
}

.table td {
  font-size: 0.85rem;
}

.note-input {
  font-size: 0.85rem;
  padding: 0.2rem 0.4rem;
  resize: vertical;
  min-height: 2.5rem;
}

/* Стили для чекбоксов управления столбцами */
.form-check {
  margin-bottom: 0;
}

.form-check-input {
  margin-top: 0.25rem;
  cursor: pointer;
}

.form-check-label {
  cursor: pointer;
  font-size: 0.9rem;
  margin-left: 0.25rem;
}

.column-controls .form-check {
  margin-right: 1rem;
  margin-bottom: 0.5rem;
}

.column-controls .form-check:last-child {
  margin-right: 0;
}
</style>
