<template>
  <div>

    <b-row class="py-0" v-if="getCommonFiles.length > 0">
      <b-col cols="12" md="4" class="mb-3" v-for="(file, index) in getCommonFiles" :key="index">
        <FileCard
            :file="file"
            @show-modal-file="showModalFile($event)"
            @show-confirm-delete="showConfirmDelete($event)"
            @show-modal-notes="showModalNotes($event)"
        ></FileCard>
      </b-col>
    </b-row>

    <b-row class="py-0" v-if="getEducationPlanSiteFiles.length > 0">
      <b-col cols="12" class="mb-1"><span class="font-weight-bold">Учебные планы на сайт:</span></b-col>
      <b-col cols="12" md="4" class="mb-3" v-for="(file, index) in getEducationPlanSiteFiles" :key="index">
        <FileCard
            :file="file"
            @show-modal-file="showModalFile($event)"
            @show-confirm-delete="showConfirmDelete($event)"
            @show-modal-notes="showModalNotes($event)"
        ></FileCard>
      </b-col>
    </b-row>

    <b-row class="py-0" v-if="getCalendarTrainingSiteFiles.length > 0">
      <b-col cols="12" class="mb-1"><span class="font-weight-bold">Календарные учебные графики на сайт:</span></b-col>
      <b-col cols="12" md="4" class="mb-3" v-for="(file, index) in getCalendarTrainingSiteFiles" :key="index">
        <FileCard
            :file="file"
            @show-modal-file="showModalFile($event)"
            @show-confirm-delete="showConfirmDelete($event)"
            @show-modal-notes="showModalNotes($event)"
        ></FileCard>
      </b-col>
    </b-row>

    <b-row class="py-0" v-if="getEducationPlanFiles.length > 0">
      <b-col cols="12" class="mb-1"><span class="font-weight-bold">Учебные планы:</span></b-col>
      <b-col cols="12" md="4" class="mb-3" v-for="(file, index) in getEducationPlanFiles" :key="index">
        <FileCard
            :file="file"
            @show-modal-file="showModalFile($event)"
            @show-confirm-delete="showConfirmDelete($event)"
            @show-modal-notes="showModalNotes($event)"
        ></FileCard>
      </b-col>
    </b-row>

    <b-row class="py-0" v-if="getCalendarTrainingFiles.length > 0">
      <b-col cols="12" class="mb-1"><span class="font-weight-bold">Календарные учебные графики:</span></b-col>
      <b-col cols="12" md="4" class="mb-3" v-for="(file, index) in getCalendarTrainingFiles" :key="index">
        <FileCard
            :file="file"
            @show-modal-file="showModalFile($event)"
            @show-confirm-delete="showConfirmDelete($event)"
            @show-modal-notes="showModalNotes($event)"
        ></FileCard>
      </b-col>
    </b-row>

    <b-button variant="primary"
              class="mt-3"
              @click="showModalFile('')"
              v-if="!getSettings.isAuditor"
    >
      Добавить файл
    </b-button>

    <b-modal v-model="showModalFiles"
             title="Загрузка файла"
             centered
             hide-footer
             modal-class="modal-custom"
             v-if="!getSettings.isAuditor"
    >
      <b-form ref="formFile" @submit.prevent="submitFile" validated>
        <b-form-group
            id="type-file-group"
            label="Категория:"
            label-for="type-file"
            invalid-feedback="Поле должно быть заполнено"
        >
          <b-form-select
              v-model="formFile.comment"
              id="type-file"
              :options="categories"
              required
              :disabled="formFile.mode === 'update_file'"
          ></b-form-select>
        </b-form-group>
        <b-form-group
            v-if="formFile.comment === 'Учебный план' || formFile.comment === 'Календарный учебный график'"
            id="edu-plan-group"
            label="Учебный план:"
            label-for="edu-plan"
            invalid-feedback="Поле должно быть заполнено"
        >
          <b-form-select
              v-model="formFile.edu_plan"
              id="edu-plan"
              :options="getProgramData.academic_plans"
              text-field="full_name"
              value-field="doc_number"
              required
              :disabled="formFile.mode === 'update_file'"
          ></b-form-select>
        </b-form-group>
        <b-form-group
            id="input-file-program-group"
            label="Файл:"
            label-for="input-file-program"
            invalid-feedback="Поле должно быть заполнено"
        >
          <b-form-file
              v-model="formFile.file"
              id="input-file-program"
              placeholder="Выберите файл"
              drop-placeholder="Перетащите файл сюда..."
              invalid-feedback="Поле должно быть заполнено"
              required
              accept="application/pdf"
          ></b-form-file>
        </b-form-group>
        <div class="w-100 text-center my-0">
          <b-button type="submit" variant="primary">
            <template v-if="getSavingProgram">
              <b-spinner small></b-spinner>
              <span>Сохранение...</span>
            </template>
            <template v-else>
              Сохранить
            </template>
          </b-button>
        </div>
      </b-form>
    </b-modal>

    <b-modal v-model="showModalNote" title="Изменить комментарий" centered hide-footer v-if="getSettings.isAuditor">
      <b-form ref="formNotes" @submit.prevent="submitNotes" validated>
        <b-form-group
            id="note-group"
            label="Комментарий:"
            label-for="note"
        >
          <b-form-textarea
              v-model="formNotes.description"
              id="note"
          ></b-form-textarea>
        </b-form-group>
        <div class="w-100 text-center my-0">
          <b-button type="submit" variant="primary" :disabled="getSavingProgram">
            <template v-if="getSavingProgram">
              <b-spinner small></b-spinner>
              <span>Сохранение...</span>
            </template>
            <template v-else>
              Сохранить
            </template>
          </b-button>
        </div>
      </b-form>
    </b-modal>

  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import FileCard from "./FileCard.vue";

const educationPlan = 'Учебный план';
const calendarTraining = 'Календарный учебный график';

export default {
  name: "ProgramFiles",
  components: {FileCard},
  data() {
    return {
      showModalFiles: false,
      showModalNote: false,
      categories: [
        'Характеристики образовательной программы',
        'Матрица компетенций',
        'Сведения о реализации образовательной программы',
        'Учебный план на сайт (очная форма)',
        'Учебный план на сайт (заочная форма)',
        'Учебный план на сайт (очно-заочная форма)',
        'Календарный учебный график на сайт (очная форма)',
        'Календарный учебный график на сайт (заочная форма)',
        'Календарный учебный график на сайт (очно-заочная форма)',
        educationPlan,
        calendarTraining,
      ],
      showEduPlan: false,
      formFile: {},
      formNotes: {
        learning_program: '',
        description: '',
        update_mode: 'update_description',
        id: '',
      },
    }
  },
  created() {
    this.resetFormFile();
  },
  computed: {
    ...mapGetters('PROGRAM', [
      'getLoadingProgram',
      'getProgramData',
      'getSelectedProgram',
      'getSavingProgram',
    ]),
    ...mapGetters('APP', [
      'getSettings'
    ]),
    getCommonFiles() {
      return this.getProgramData.files.filter(item =>
          item.edu_plan === null
          && !item.comment.match(/^Учебный план на сайт/g)
          && !item.comment.match(/^Календарный учебный график на сайт/g)
      )
    },
    getEducationPlanFiles() {
      return this.getProgramData.files.filter(item => item.edu_plan !== null && item.comment === educationPlan)
    },
    getCalendarTrainingFiles() {
      return this.getProgramData.files.filter(item => item.edu_plan !== null && item.comment === calendarTraining)
    },
    getEducationPlanSiteFiles() {
      return this.getProgramData.files.filter(item => item.comment.match(/^Учебный план на сайт/g))
    },
    getCalendarTrainingSiteFiles() {
      return this.getProgramData.files.filter(item => item.comment.match(/^Календарный учебный график на сайт/g))
    }
  },
  methods: {
    ...mapActions('PROGRAM', [
      'putFileProgram',
      'deleteFileProgram',
      'putFileProgramDescription',
    ]),
    async submitFile() {
      this.formFile.learning_program = this.getSelectedProgram;
      if (this.formFile.mode === 'new_file') {
        const find = this.getProgramData.files.find(item =>
            item.comment === this.formFile.comment
            && (this.formFile.edu_plan === null ? true : item.edu_plan === this.formFile.edu_plan)
        )
        if (find !== undefined) {
          this.$store.commit('MESSAGES/IS_SHOW', {
            message: 'В данной категории уже загружен файл.',
            type: 'danger'
          });
          return;
        }
      }
      await this.putFileProgram(this.formFile);
      this.showModalFiles = false;
    },
    async submitNotes() {
      this.formNotes.learning_program = this.getSelectedProgram;
      await this.putFileProgramDescription(this.formNotes);
      this.showModalNote = false;
    },
    showModalFile(id) {
      this.showModalFiles = true;
      if (id === '') {
        this.resetFormFile();
      } else {
        this.formFile = {
          ...this.getProgramData.files.find(item => item.id === id),
          mode: 'update_file',
          update_mode: 'update_program_file'
        }
      }
    },
    showConfirmDelete(id) {
      this.$bvModal.msgBoxConfirm('Вы действительно хотите удалить файл?', {
        title: 'Подтвердите действие',
        okVariant: 'danger',
        okTitle: 'Да',
        cancelTitle: 'Нет',
        footerClass: 'p-2',
        hideHeaderClose: false,
        centered: true
      })
          .then(value => {
            if (value)
              this.deleteFileProgram({file_id: id, doc_id: this.getSelectedProgram});
          })
    },
    showModalNotes(id) {
      const find = this.getProgramData.files.find(item => item.id === id);
      this.showModalNote = true;
      this.formNotes.description = find !== undefined ? find.description : '';
      this.formNotes.id = id;
    },
    resetFormFile() {
      this.formFile = {
        update_mode: 'update_program_file',
        mode: 'new_file',
        comment: null,
        edu_plan: null,
        file: null,
        learning_program: null,
      }
    }
  }
}
</script>

<style scoped>

</style>