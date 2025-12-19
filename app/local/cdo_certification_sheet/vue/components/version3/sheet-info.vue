<script setup>
import { storeToRefs } from 'pinia';
import { useCertificationStore } from '../../stores/certification.js';

const props = defineProps({
  sheet: {
    type: Object,
    required: true
  }
});

const certificationStore = useCertificationStore();
const { strings } = storeToRefs(certificationStore);

const colorGrade = [
  '',
  'green',
  'yellow',
  'orange',
  'red'
];
</script>

<template>
  <div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_date }}</div>
      <div class="col-7"> {{ props.sheet.date }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_name_plan }}</div>
      <div class="col-7"> {{ props.sheet.name_plan }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_group }}</div>
      <div class="col-7"> {{ props.sheet.group }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_profile }}</div>
      <div class="col-7"> {{ props.sheet.profile }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_semester }}</div>
      <div class="col-7"> {{ props.sheet.semester }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_division }}</div>
      <div class="col-7"> {{ props.sheet.division }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_form_education }}</div>
      <div class="col-7"> {{ props.sheet.form_education }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_level_education }}</div>
      <div class="col-7"> {{ props.sheet.level_education }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_specialty }}</div>
      <div class="col-7"> {{ props.sheet.speacility }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_course }}</div>
      <div class="col-7"> {{ props.sheet.course }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_discipline }}</div>
      <div class="col-7"> {{ props.sheet.discipline }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_type_control }}</div>
      <div class="col-7"> {{ props.sheet.type_control }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_name_sheet }}</div>
      <div class="col-7"> {{ props.sheet.name_sheet }}</div>
    </div>

    <div class="row mt-2" v-for="legend in props.sheet.legend" :key="legend.name">
      <div class="col-8">
        <i class="fas fa-circle" :style="`color: ${colorGrade[legend.color]}`"></i> - {{ legend.name }}
      </div>
      <div class="col-4">
        {{ legend.points.minPoint }} - {{legend.points.maxPoint }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.row {
  font-size: 0.9rem;
}
</style>
