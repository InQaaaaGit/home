<script setup>
import {ref} from "vue";
import {useMainStore} from '../store/store';
import {useLangStore} from "../store/storeLang";
import {computed} from "vue";

const message = ref('Hello from cdo_schedule!');
const store = useMainStore();
const lang = useLangStore();

const formattedDate = computed(() => {
  if (store.attendances.attendance_data && store.attendances.attendance_data.date1c) {
    const date = new Date(store.attendances.attendance_data.date1c);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Month is 0-indexed
    const year = date.getFullYear();

    return `${day}.${month}.${year}`;
  }
  return store.attendances.attendance_data.date1c; // Return original if not in expected format
});

function getAttendance() {
  store.getSetAttendance();
}
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-8">
        <div v-if="!store.loading && store.attendances.attendance.length > 0" class="table-responsive">
          <h3>{{ store.attendances.name }}</h3>

          <table class="table table-striped table-bordered">
            <thead class="thead-light">
            <tr>
              <th scope="col">{{ lang.strings.student }}</th>
              <th scope="col">{{ lang.strings.attendance }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(attendance, index) in store.attendances.attendance" :key="index">
              <td>{{ attendance.student_fio }}</td>
              <template v-if="!attendance.loading">
                <td>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" :checked="attendance.attendance_status==='Посетил'"
                           @click="store.toggleAttendance(index, attendance.student)"/>
                  </div>
                </td>
              </template>
              <template v-else>
                <td style="text-align: center;">
                  <div class="spinner-border text-primary" role="status">
                    <span class="sr-only"></span>
                  </div>
                </td>
              </template>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-4">
        <h2>{{ store.attendances.attendance_data.name }}</h2>
        <div v-if="store.attendances">
          <h4>{{ lang.strings.attendanceDetails }}</h4>
          <ul>
            <li v-for="(value, key) in store.attendances.attendance_data" :key="key"
                v-if="(key) !== 'name'">
<!--              <template v-if="(key) !== 'name'">-->
                <template v-if="key === 'date1c'">
                  <strong>{{ lang.strings[key] }}</strong> {{ formattedDate }} {{ store.time_start }} :
                  {{ store.time_end }}
                </template>
                <template v-else><strong>{{ lang.strings[key] }}</strong> {{ value }}</template>
<!--              </template>-->
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.container {
  padding: 20px;
}
</style>
