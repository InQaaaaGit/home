<script>
import {mapState} from "vuex";

export default {
  props: ['sheet'],
  name: "sheet-info",
  data: () => ({
    colorGrade: [
      '',
      'green',
      'yellow',
      'orange',
      'red'
    ]
  }),
  computed: mapState(['strings', 'sheets']),
  methods: {

  }
};
</script>

<template>
  <div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_date }}</div>
      <div class="col-7"> {{ sheet.date }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_name_plan }}</div>
      <div class="col-7"> {{ sheet.name_plan }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_group }}</div>
      <div class="col-7"> {{ sheet.group }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_profile }}</div>
      <div class="col-7"> {{ sheet.profile }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_semester }}</div>
      <div class="col-7"> {{ sheet.semester }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_division }}</div>
      <div class="col-7"> {{ sheet.division }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_form_education }}</div>
      <div class="col-7"> {{ sheet.form_education }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_level_education }}</div>
      <div class="col-7"> {{ sheet.level_education }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_specialty }}</div>
      <div class="col-7"> {{ sheet.speacility }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_course }}</div>
      <div class="col-7"> {{ sheet.course }}</div>
    </div>
    <!--    <div class="row">
          <div class="col-5 font-weight-bold">{{ strings.sheet_guid }}</div>
          <div class="col-7"> {{ sheet.guid }}</div>
        </div>-->
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_discipline }}</div>
      <div class="col-7"> {{ sheet.discipline }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_type_control }}</div>
      <div class="col-7"> {{ sheet.type_control }}</div>
    </div>
    <div class="row">
      <div class="col-5 font-weight-bold">{{ strings.sheet_name_sheet }}</div>
      <div class="col-7"> {{ sheet.name_sheet }}</div>
    </div>
    <!--    <div class="row">
          <div class="col-5 font-weight-bold">{{ strings.sheet_type }}</div>
          <div class="col-7"> {{ sheet.type }}</div>
        </div>-->
    <!--    <div class="row">
          <div class="col-5 font-weight-bold">{{ strings.sheet_type_code }}</div>
          <div class="col-7"> {{ sheet.type_code }}</div>
        </div>-->

    <div class="row mt-2" v-for="legend in sheet.legend">
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

</style>