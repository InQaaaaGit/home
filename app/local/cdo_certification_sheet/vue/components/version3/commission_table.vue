<script setup>
import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useCertificationStore } from '../../stores/certification.js';

const props = defineProps({
  sheet: {
    type: Object,
    required: true
  }
});

const certificationStore = useCertificationStore();
const { strings, userID, isAgreedAllowInSheet } = storeToRefs(certificationStore);

const isAgreedAllow = computed(() => {
  return isAgreedAllowInSheet.value(props.sheet.guid);
});

const currentTeacher = computed(() => {
  if (isAgreedAllow.value && props.sheet.teachers) {
    return !!props.sheet.teachers.find(teacher => {
      return parseInt(teacher.user_id) === parseInt(userID.value);
    });
  }
  return false;
});
</script>

<template>
  <div v-if="isAgreedAllow">
    <div class="row">
      <div class="col-12">
        <h3>{{ strings.commission_sheet_title }}</h3>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">{{ strings.commission_sheet_user_full_name }}</th>
              <th scope="col">{{ strings.commission_sheet_activity }}</th>
              <th scope="col">{{ strings.commission_sheet_chairman }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="teacher in sheet.teachers" :key="teacher.guid"
                :class="parseInt(userID)===parseInt(teacher.user_id) ? 'table-success' : ''">
              <th scope="row">{{ teacher.full_name }}</th>
              <td>
                {{ teacher.agreed ? strings.commission_sheet_agreed_message_yes : strings.commission_sheet_agreed_message_no }}
              </td>
              <td><i v-if="teacher.chairman" class="fas fa-check"></i></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div v-else></div>
</template>

<style scoped>
</style>
