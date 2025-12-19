<template>
  <b-container fluid>

    <Messages/>

    <b-card v-show="getLoading">
      <b-skeleton animation="wave" width="85%"></b-skeleton>
      <b-skeleton animation="wave" width="55%"></b-skeleton>
      <b-skeleton animation="wave" width="70%"></b-skeleton>
    </b-card>

    <b-row v-if="!getLoading && isAuthorized">
      <b-col v-if="getSettings.isAuditor" cols="12">
        <b-form-group id="secretary-group" label="Научный сотрудник:" label-for="secretary">
          <b-form-select
              v-model="selected"
              id="secretary"
              class="w-100"
              :options="getSettings.list_secretary"
              text-field="preview"
              value-field="id"
              @change="loadEduPrograms($event)"
          ></b-form-select>
        </b-form-group>
      </b-col>
      <b-col cols="12" md="6">
        <b-form-group id="year-group" label="Год начала реализации образовательной программы:" label-for="year">
          <b-form-select
              id="year"
              class="w-100"
              :options="getYears"
              v-model="filter.year"
          ></b-form-select>
        </b-form-group>
      </b-col>
      <!--
      <b-col cols="12" md="6">
        <b-form-group id="profile-group" label="Специальность/направление подготовки:" label-for="profile">
          <b-form-select
              id="profile"
              class="w-100"
              :options="getProfiles"
              text-field="name"
              value-field="id"
              v-model="filter.profile"
          ></b-form-select>
        </b-form-group>
      </b-col>
      -->
      <b-col cols="12" md="6">
        <b-form-group id="speciality-group" label="Специальность/направление подготовки:" label-for="speciality">
          <b-form-select
              id="speciality"
              class="w-100"
              :options="getSpecialities"
              text-field="name"
              value-field="id"
              v-model="filter.speciality"
          ></b-form-select>
        </b-form-group>
      </b-col>
      <b-col cols="12" md="6">
        <b-form-group id="education-type-group" label="Уровень образования:" label-for="education-type">
          <b-form-select
              id="education-type"
              class="w-100"
              :options="getEducationTypes"
              text-field="name"
              value-field="id"
              v-model="filter.type"
          ></b-form-select>
        </b-form-group>
      </b-col>
      <b-col cols="12" md="6">
        <b-form-group id="education-level-group" label="Уровень подготовки:" label-for="education-level">
          <b-form-select
              id="education-level"
              class="w-100"
              :options="getEducationLevels"
              text-field="name"
              value-field="id"
              v-model="filter.level"
          ></b-form-select>
        </b-form-group>
      </b-col>
      <b-col cols="12">
        <b-form-group id="edu-program-group" label="Образовательная программа:" label-for="edu-program">
          <b-form-select
              id="edu-program"
              class="w-100"
              :options="filteredEducationPrograms"
              text-field="preview"
              value-field="doc_number"
              @change="loadEduProgramData($event)"
          ></b-form-select>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row>
      <Program/>
    </b-row>
  </b-container>
</template>

<script>

import Messages from "./components/Messages.vue";
import Program from "./components/Program.vue";
import {mapGetters, mapActions} from "vuex";

export default {
  name: "App",
  data() {
    return {
      selected: null,
      filter:{
        profile: null,
        year: 'Не выбрано',
        level: null,
        type: null,
        speciality: null,
      }
    }
  },
  components: {Messages, Program},
  mounted() {
    this.$store.dispatch('APP/loadSettings');
  },
  computed: {
    ...mapGetters('APP', [
      'getLoading',
      'getSettings',
      'getEducationPrograms',
      'getYears',
      'getEducationTypes',
      'getEducationLevels',
      'getProfiles',
      'getSpecialities',
      'isAuthorized',
    ]),
    ...mapGetters('PROGRAM', [
        'getSelectedProgram'
    ]),
    filteredEducationPrograms() {
      let result = this.getEducationPrograms;
      if(this.filter.year !== null && this.filter.year !== 'Не выбрано')
        result = result.filter(item => item.year === this.filter.year)
      // if(this.filter.profile !== null)
      //   result = result.filter(item => item.profile_id === this.filter.profile)
      if(this.filter.type !== null)
        result = result.filter(item => item.education_type_id === this.filter.type)
      if(this.filter.level !== null)
        result = result.filter(item => item.education_level_id === this.filter.level)
      if(this.filter.speciality !== null)
        result = result.filter(item => item.specialty_id === this.filter.speciality)
      if(result.find(item => item.doc_number === this.getSelectedProgram) === undefined)
        this.$store.commit('PROGRAM/setSelectedProgram', null)
      return result;
    },
  },
  methods: {
    ...mapActions('APP', [
      'loadEducationPrograms',
    ]),
    ...mapActions('PROGRAM', [
      'loadProgramData'
    ]),
    loadEduPrograms(event){
      this.loadEducationPrograms(event)
    },
    loadEduProgramData(event){
      this.loadProgramData(event)
    },
  }
}
</script>

<style scoped>
  .custom-select {
    padding: 0.375rem 1.75rem 0.375rem 0.75rem !important;
  }
</style>