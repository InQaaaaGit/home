<template>
  <div>
    <b-row>
      <b-col>
        <b-button-group>
          <b-button
              :variant="selectedFilter==='all' ? 'primary' : ''"
              @click="selectedFilter='all'">
            {{ this.$store.state.langStrings.all }}
          </b-button>
          <b-button
              :variant="selectedFilter==='active' ? 'primary' : ''"
              @click="selectedFilter='active'">
            {{ this.$store.state.langStrings.active }}
          </b-button>
          <b-button
              :variant="selectedFilter==='archive' ? 'primary' : ''"
              @click="selectedFilter='archive'">
            {{ this.$store.state.langStrings.archive }}
          </b-button>
          <b-button
              :variant=" surveyActive ? 'success' : 'danger'"
              @click="activateSurvey">
            <template v-if="!surveyActive">{{ this.$store.state.langStrings.active_survey }}</template>
            <template v-else>{{ this.$store.state.langStrings.survey_is_active }}</template>
          </b-button>
        </b-button-group>
      </b-col>
    </b-row>
    <b-row>
      <b-col>
        <b-table-simple class="table-item text-center"
                        striped sort-icon-left
        >
          <b-thead>
            <b-th v-for="(item) in fields" :key="item.key" :style="item.thStyle">
              {{ item.label }}
            </b-th>
          </b-thead>

          <draggable
              :class="{ [`cursor-grabbing`]: drag === true }"
              v-model="filteredQuestions"
              group="items"
              @start="drag = true"
              tag="tbody"
          >

            <!--            <b-tr v-for="(item, index) in filteredQuestions" :key="item.id"-->
            <b-tr v-for="(item, index) in filteredQuestions"
                  :key="item.id"
                  class="item-row ">
              <b-td class="align-middle">
                <b>{{ index + 1 }}. </b>
              </b-td>
              <b-td class="align-middle">
                <div>
                  <b-icon
                      icon="pencil"
                      :variant="!!item.editStatus ? 'primary' : 'secondary'"
                      v-model="item.editStatus"
                      @click="item.editStatus=!item.editStatus"
                  />
                  <b-icon
                      v-if="!item.answer.length"
                      icon="x-lg"
                      variant="danger"
                      class="ml-2"
                      @click="deleteQuestion(item)"

                  ></b-icon>
                </div>
              </b-td>
              <b-td class="align-middle">

                <b-form-checkbox
                    v-model="item.visible"
                    :disabled="(!isActiveScalesIsEqual(item) || surveyActive) || !item.editStatus"
                    @input="changeQuestionVisible(item)"
                ></b-form-checkbox>
              </b-td>
              <b-td>
                <b-form-input v-model="item.question"
                              @change="updateQuestionInput(item.question, item)"
                              :disabled="item.visible || !item.editStatus"
                ></b-form-input>
              </b-td>
              <b-td>
                <b-form-select :options="types"
                               v-model="item.type"
                               @change="updateQuestion(item)"
                               :disabled="item.visible || !item.editStatus"
                />
              </b-td>
              <b-td>
                <div v-if="item.type===1" class="d-inline-flex justify-content-center">
                  <div class="width-parameter-input">
                    <b-form-input
                        :disabled="(item.visible && item.type===1) || !item.editStatus"
                        @change="updateQuestionInput(item.first_value_of_type, item)"
                        v-model="item.first_value_of_type"
                        type="number"
                        min="-100"
                        max="100"
                    />
                  </div>
                  <div class="ml-3 d-inline-flex align-items-center">-</div>
                  <div class="width-parameter-input ml-3">
                    <b-form-input
                        :disabled="item.visible && item.type===1 || !item.editStatus"
                        @change="updateQuestionInput(item.second_value, item)"
                        v-model="item.second_value"
                        type="number"
                        min="-100"
                        max="100"
                    />
                  </div>
                </div>
                <div v-else class="d-inline-flex justify-content-center align-items-center">
                  <b-form-input
                      class="width-parameter-input"
                      :disabled="item.visible && item.type===2 || !!item.answer.length"
                      @change="updateQuestionInput(item.first_value_of_type, item)"
                      v-model="item.first_value_of_type"
                      type="number"
                      min="1"
                      max="100"
                  ></b-form-input>
                  <div class="ml-1">символов</div>
                </div>
              </b-td>
            </b-tr>
          </draggable>
        </b-table-simple>
      </b-col>
    </b-row>


  </div>
</template>

<script>
import draggable from "vuedraggable";
import utility from "@/utility";
import _ from "lodash";
export default {
  name: "QuestionTableForDiscipline",
  props: {
    group: {
      type: Number,
      required: true
    }
  },
  created() {
    this.getActiveSurvey();
    this.types = this.$store.state.types;
    this.fields.push(
        {key: 'sort', label: '', thStyle: 'width: "5%"', tdClass: 'align-middle'},
        {key: 'actions', label: '', thStyle: 'width: "10%"', tdClass: 'align-middle'},
        {
          key: 'visible',
          label: this.$store.state.langStrings['fields:visible'],
          thStyle: 'width: "10%"',
          tdClass: 'align-middle'
        },
        {key: 'question', label: this.$store.state.langStrings['fields:question_name']},
        {key: 'type', label: this.$store.state.langStrings['fields:type']},
        {key: 'parameters', label: '', thStyle: 'width: "20%"', tdClass: 'align-middle'},
    );
  },
  computed: {
    questions: {
      get() {
        return  _.uniqBy(this.$store.state.questions, 'id');
       // return this.$store.state.questions;
      },
      set(data) {
        //this.$store.commit('updateDraggableItems', data);
        data.forEach((question, index) => {
          question.sort = index;
        });

        this.$store.dispatch('updateQuestionsSortOrder', data);
      },
    },

    filteredQuestions: {
      get() {
        const filterCondition = q => q.group_tab === this.group;

        switch (this.selectedFilter) {
          case 'all':
          default:
            return this.questions.filter(filterCondition);
          case 'archive':
            return this.questions.filter(q => filterCondition(q) && !q.visible);
          case 'active':
            return this.questions.filter(q => filterCondition(q) && q.visible);
        }
      },
      set(data) {
        const defaultData = this.$store.state.questions;
        data.forEach((question, index) => {
          question.sort = index;
        });
        let res = _.unionBy(data, defaultData, 'id');
        this.$store.dispatch('updateQuestionsSortOrder', res);
      }

    },
    isActiveScalesIsEqual1() {
      let reference = {}
      this.filteredQuestions.forEach(question => {
        if (question.type === 1 && question.visible) {
          reference = question;
        }
      });
      let result = this.filteredQuestions.every(question => {
        if (question.type === 1 && question.visible) {

          if (reference.first_value_of_type !== question.first_value_of_type || reference.second_value !== question.second_value) {
            return false;
          }
        }
      });

      return result;
    }
  },
  methods: {
    async getActiveSurvey() {
      this.surveyActive = await utility.ajaxMoodleCall(
          'local_cdo_ok_active_groups_get_active_group',
          {
            params: {
              group_tab: this.group
            }
          }
      );
    },
    async activateSurvey() {
      this.surveyActive = await utility.ajaxMoodleCall(
          'local_cdo_ok_active_groups_create_update',
          {
            data: {
              group_tab: this.group,
              active: Number(!this.surveyActive)
            }
          }
      );
    },
    handleChange(event) {
      const newIndex = event.newIndex;
      const oldIndex = event.oldIndex;
      const draggableItem = this.questions.splice(oldIndex, 1)[0];
      this.questions.splice(newIndex, 0, draggableItem);
    },
    handleEnd() {
      this.$store.commit('updateDraggableItems', this.questions);
    },
    updateQuestionInput(value, item) {

      if (Number(value) > 100) {
        item.first_value_of_type = 100;
        value = 100;
      }
      if (Number(value) < -100) {
        item.first_value_of_type = -100;
        value = -100;
      }
      if (item.type === 2) {
        if (Number(value) < 1) {
          item.first_value_of_type = 1;
          value = 1;
        }
      }

      this.updateQuestion(item);
    },
    changeQuestionVisible(item) {

      this.updateQuestion(item);

    },
    isActiveScalesIsEqual(reference) {
      let result = true;
      if (reference.type === 1) {
        result = this.filteredQuestions.every(question => {
          if (question.type === 1 && question.visible) {
            if (reference.first_value_of_type !== question.first_value_of_type || reference.second_value !== question.second_value) {
              return false;
            }
          }
          return true;
        });
      }
      return (result && reference.question.length) ;
    },
    updateQuestion(item) {
      this.$store.dispatch('updateQuestionAPI', item);
      /*this.$bvToast.toast(`This is toast number`, {
        title: 'BootstrapVue Toast',
        autoHideDelay: 5000,
        toaster: 'b-toaster-bottom-right',
        variant: 'success',
        appendToast: true
      })*/
    },
    deleteQuestion(item) {
      this.$store.dispatch('deleteQuestionAPI', item);
    },
  }
  ,
  components: {
    draggable,
  }
  ,
  data: () => ({
    surveyActive: false,
    selectedTable: 0,
    selectedFilter: 'all',
    drag: false,
    fields: [],
    types: [],
  }),
}
</script>

<style scoped>
.width-parameter-input {
  width: 75px;
}
</style>