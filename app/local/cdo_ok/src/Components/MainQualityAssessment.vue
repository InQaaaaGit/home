<template>
  <b-container fluid>
    <b-row>
      <b-col>
        <b-tabs pills card v-model="selectedTab">
          <b-tab :title="this.$store.state.langStrings['tabs:question_for_discipline']">
            <question-table-for-discipline :group="0" />
          </b-tab>
          <b-tab :title="this.$store.state.langStrings['tabs:question_for_education_program']" >
            <question-table-for-discipline :group="1" />
          </b-tab>
        </b-tabs>
      </b-col>
    </b-row>
    <b-row>
      <b-col md="4">
        <b-btn
            @click="createQuestion"
            variant="primary"

        >
          <template v-if="!this.$store.state.isCreateLoaderOn">
            {{this.$store.state.langStrings['buttons:add']}}
          </template>
          <template v-else>
            <b-spinner small type="grow"></b-spinner>
          </template>
        </b-btn>
      </b-col>
      <b-col md="8" class="d-flex justify-content-end">
       <footer-actions-quality-assessment />
      </b-col>
    </b-row>
  </b-container>
</template>

<script>

import QuestionTableForDiscipline from "@/Components/QuestionTableForDiscipline.vue";
import FooterActionsQualityAssessment from "@/Components/FooterActionsQualityAssessment.vue";
import utility from "@/utility";

export default {
  name: "MainQualityAssessment",
  components: {FooterActionsQualityAssessment, QuestionTableForDiscipline},
  data: () => ({
  }),
  created() {
  },
  methods: {
    createQuestion() {
      this.$store.dispatch('createQuestionAPI');
    },
  },
  computed: {
    selectedTab: {
      get() {
        return this.$store.state.selectedTab;
      },
      set(newTabIndex) {
        this.$store.commit('selectTab', newTabIndex);
      }
    }
  }
}
</script>

<style scoped>
.create-button-width {
  width: 100px;
  height:36px;
}
</style>