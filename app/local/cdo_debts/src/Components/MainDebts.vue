<script>

import utility_debts from "@/utility_debts";
import _ from "lodash";
import {TYPE} from "vue-toastification";

export default {
  name: "MainDebts",
  components: {},
  data: () => ({
    debts: [],
    currentRetakeSheet: {
      file: [],
      subject: '',
      body: '',
      who: ''
    },
    errorMessage: '',
    allowedFileTypes: '.doc, .docx, .rtf, .jpg, .jpeg, .png, .pdf, .xls, .xlsx, .txt, .rar, .zip, .odt, .ods, .odp'
  }),
  async created() {
    let debts = await utility_debts.ajaxMoodleCall('local_cdo_debts_get_academic_debts', {});
    if (debts.hasOwnProperty('error_message')) {
      this.errorMessage = debts.error_message;
    } else {
      this.debts = debts.academic_debts;
    }
  },
  methods: {
    validateFileCount(files) {
      if (files.length > 10) {
        this.$toast(
            'Превышен лимит (10) прикрепления файлов', {
              timeout: 3000,
              type: TYPE.ERROR,
            });
        this.currentRetakeSheet.file = [];
      }
    },
    async prepareToSendRetakeRequest(item) {
      // По ТЗ, если вдруг с сервиса не вернулся ППС, добавить "..." в качестве выбора
      item.retake_sheet.teachers.push({
        'text': '...',
        'value': null
      });
      this.currentRetakeSheet = item.retake_sheet;
      this.currentRetakeSheet.document_id = item.document_source.id;
      this.currentRetakeSheet.gradebook = item.student_record_book;
    },
    async sendRetakeRequest() {
      this.currentRetakeSheet.filebs64 = {};
      let files = [];
      if (!!this.currentRetakeSheet.file.length) {
        let fileList = '';
        for (let i = 0; i < this.currentRetakeSheet.file.length; i++) {
          fileList = await utility_debts.uploadFile(this.currentRetakeSheet.file[i]);
          files.push(fileList);
        }
      }
      this.currentRetakeSheet.filebs64 = files;
      let DTO = this.currentRetakeSheet;
      delete DTO.file;
      delete DTO.teachers;
      let response = await utility_debts.ajaxMoodleCall('local_cdo_debts_send_request_retake', DTO);
      this.currentRetakeSheet.status = response.new_status;
      this.currentRetakeSheet.date = response.date;
    },
    showStatus(status) {
      switch (status) {
        case "0":
          return "text-primary";
        case "1":
          return "text-danger";
        case "2":
          return "text-success";
        default:
          return "";
      }
    }
  },
  computed: {
    isValidateModalRequestRetake() {
      if (_.isEmpty(this.currentRetakeSheet)) {
        return true;
      }
      return !!(this.currentRetakeSheet.subject.length &&
          this.currentRetakeSheet.body.length
          && (this.currentRetakeSheet.who === null || this.currentRetakeSheet.who.length)
      );
    }
  }
};
</script>

<template>
  <div>
    <div v-if="!!debts.length">
      <table class="table table-striped 1mt-20">
        <thead>
        <th scope="col" class="text-center">
          {{ this.$store.state.langStrings['semester'] }}
        </th>
        <th scope="col" class="text-center">
          {{ this.$store.state.langStrings['study_load_type'] }}
        </th>
        <th scope="col" class="text-center">
          {{ this.$store.state.langStrings['discipline'] }}
        </th>
        <th scope="col" class="text-center">
          {{ this.$store.state.langStrings['grade_short_name'] }}
        </th>
        </thead>
        <tbody>
        <tr v-for="debt in debts" :key="debt.document_source.id">
          <td class="text-center">{{ debt.semester }}</td>
          <td class="text-center">{{ debt.study_load_type.name }}</td>
          <td style="color: #0a78d1" class="text-center ">{{ debt.discipline.name }}</td>
          <td style="color: #8e662e" class="text-center">{{ debt.grade_short_name }}</td>
          <td class="text-center">
            <template v-if="!!debt.retake_sheet.status.id.length">
              <div :class="showStatus(debt.retake_sheet.status.id)">
                {{ debt.retake_sheet.status.name }} (от {{ debt.retake_sheet.date }})
              </div>
              <div v-if="debt.retake_sheet.commentary.length">
                Комментарий: {{ debt.retake_sheet.commentary }}
              </div>
              <div v-show="debt.retake_sheet.status.id==='1'" class="text-danger">
                Обратитесь в деканат к методисту своего факультета.
              </div>
              <div v-show="debt.retake_sheet.status.id==='2'" class="text-success">
                Пересдача: {{debt.retake_sheet.date_for_retake_convert}}
              </div>
            </template>
            <template v-else>
              <b-button v-b-modal.retake variant="outline-primary" @click="prepareToSendRetakeRequest(debt)">
                Подать заявку
              </b-button>
            </template>
          </td>
        </tr>
        </tbody>
      </table>
      <b-modal id="retake" :title="this.$store.state.langStrings['debts:request_retake']"
               cancel-title="Отмена"
               ok-title="Отправить"
               @ok="sendRetakeRequest"
               :ok-disabled="!isValidateModalRequestRetake"
      >
        <b-form-group
            id="input-group-1"
            :label="this.$store.state.langStrings['debts:request_who']"
            label-for="input-who"
            description=""
        >
          <b-form-select
              id="input-who"
              :options="currentRetakeSheet.teachers"
              v-model="currentRetakeSheet.who"
              type="text"
              placeholder=""
              required
          ></b-form-select>
        </b-form-group>
        <b-form-group
            id="input-group-2"
            label="Тема сообщения:"
            label-for="input-subject"
            description=""
        >
          <b-form-input
              id="input-subject"
              v-model="currentRetakeSheet.subject"
              type="text"
              placeholder=""
              required
          ></b-form-input>
        </b-form-group>
        <b-form-group
            id="input-group-3"
            label="Текст сообщения:"
            label-for="input-body"
            description=""
        >
          <b-form-textarea
              id="input-body"
              v-model="currentRetakeSheet.body"
              placeholder=""
              required
          ></b-form-textarea>
        </b-form-group>
        <b-form-group
            id="input-group-4"
            label="Прикрепите файл (если это требуется):"
            label-for="input-body"
            description=""
        >
          <b-form-file
              v-model="currentRetakeSheet.file"
              placeholder=""
              multiple
              accept=".doc, .docx, .rtf, .jpg, .jpeg, .png, .pdf, .xls, .xlsx, .txt, .rar, .zip, .odt, .ods, .odp"
              :state="Boolean(currentRetakeSheet.file)"
              browse-text="Выберите файл"
              @input="validateFileCount"
          ></b-form-file>
        </b-form-group>
      </b-modal>
    </div>
    <div v-else class="alert alert-primary">Задолженностей не найдено</div>
    <div v-if="!!errorMessage.length" class="alert alert-danger">{{ errorMessage }}</div>
  </div>
</template>

<style scoped>

</style>