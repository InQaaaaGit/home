<template>
  <b-container v-if="questions.length && !confirmed">
    <h2 class="mb-5">{{ this.$store.state.langStrings['title_survey_full'] }}: {{ this.$store.state.discipline }} </h2>
    <b-row v-for="question in questions" :key="question.id"
           class="d-flex align-items-center mt-2 mb-2  ">
      <b-col>
        {{ question.question }}
      </b-col>
      <b-col>
        <template v-if="question.type===1">
          <div class="d-flex">
            <div class="mr-1 text-danger">{{ question.first_value_of_type }}</div>
            <div class="w-100">
              <b-form-input type="range" v-model="question.answer"
                            :min="question.first_value_of_type"
                            :max="question.second_value"
                            step="1"
                            @input="sendAnswer(question.id, question.answer)"
              ></b-form-input>
            </div>
            <div class="ml-1 text-success">{{ question.second_value }}</div>

          </div>
          <div class="text-center">{{ question.answer }}</div>
        </template>
        <template v-else>
          <b-form-textarea type="text"
                           @input="currentLimit=question.first_value_of_type"
                           :formatter="validateStringAnswer"
                           v-model="question.answer"
                           @change="sendAnswer(question.id, question.answer)"
          ></b-form-textarea>
        </template>
      </b-col>
      <div class="w-100"><hr></div>
    </b-row>
    <b-row>
      <b-col>
        <b-btn @click="confirmAnswers" variant="primary" :disabled="!answeredAll">
          {{ this.$store.state.langStrings['send_answers'] }}
        </b-btn>
      </b-col>
    </b-row>
  </b-container>
  <div v-else>
    <div class="alert alert-success" v-if="confirmed">
      {{ this.$store.state.langStrings['survey_is_confirmed'] }}
    </div>
    <div class="alert alert-warning" v-else>
      {{ this.$store.state.langStrings['title_not_active_survey'] }}
    </div>
  </div>
</template>

<script>
import utility from "@/utility";

export default {
  name: "surveyMainApp",
  data: () => ({
    questions: [],
    active: false,
    currentLimit: 0,
    confirmed: false,
  }),
  async created() {
    await this.getConfirmStatus();
    if (!this.confirmed)
      this.questions = await utility.ajaxMoodleCall(
          'local_cdo_ok_get_question_with_answers',
          {
            params: {
              group_tab: this.$store.state.group_tab,
              visible: true,
              integration: this.$store.state.discipline_code
            }
          }
      );
  },
  methods: {
    async getConfirmStatus() {
      let result = await utility.ajaxMoodleCall(
          'local_cdo_ok_confirm_answers_get_confirm_answer',
          {
            params: {
              integration: this.$store.state.discipline_code
            }
          }
      );
      let status = false;
      if (result.length) {
        status = result[0].status;
      }
      this.confirmed = status;
    },
    confirmAnswers() {

      utility.ajaxMoodleCall(
          'local_cdo_ok_confirm_answers_create_update',
          {
            data: {
              status: 1,
              integration: this.$store.state.discipline_code
            }
          }
      );
      window.location.href = "/local/cdo_academic_progress";

      this.confirmed = true;
    },
    validateStringAnswer(text) {
      return text.length > this.currentLimit ? text.substring(0, this.currentLimit) : text;
    },
    sendAnswer(questionID, answer, limit = 0) {
      utility.ajaxMoodleCall('local_cdo_ok_create_answer',
          {
            data: {
              answer: answer,
              question_id: questionID,
              integration: this.$store.state.discipline_code,
              discipline: this.$store.state.discipline
            }
          }
      );

    }
  },
  computed: {
    answeredAll() {
      return this.questions.every(item => {
        if (item.answer==null) {
          return false;
        }
        return !!item.answer.length;
      })
    }
  }
}
</script>

<style scoped>

</style>