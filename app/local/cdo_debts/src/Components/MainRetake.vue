<script>
import utility_debts from "@/utility_debts";
import {TYPE} from "vue-toastification";

export default {
  name: "MainRetake",
  data: () => ({
    errorMessage: '',
    upload: false,
    currentItem: {},
    filter: '',
    filterOn: ['student', 'date', 'discipline'],
    items: [],
    headers: [
      {key: 'show_details', label: '', thStyle: {width: "15%"},},
      {key: 'student', label: 'Студент'},
      {key: 'status', label: 'Статус'},
      {key: 'discipline', label: 'Дисциплина'},
      {key: 'date', label: 'Дата заявки'},
      {key: 'actions', label: ''},
      {key: 'date_for_retake', label: 'Дата пересдачи', thStyle: {width: "15%"}},
    ]
  }),
  methods: {
    getFile(fileStructure) {
      // if (!!item.item.file) {
      // let fileStructure = JSON.parse(file);
      this.convertBS64ToFile(fileStructure);
      //  }
    },
    convertBS64ToFile(fileStructure) {
      const url = "data:" + fileStructure.type + ";base64," + fileStructure.binaryString;
      fetch(url)
          .then(res => res.blob())
          .then(blob => {
            this.blobFile = new File([blob], fileStructure.filename, {type: fileStructure.type});
            this.downloadBlob(blob, fileStructure.filename);
          });
    },
    downloadBlob(blob, name = 'file.png') {
      // Convert your blob into a Blob URL (a special url that points to an object in the browser's memory)
      const blobUrl = URL.createObjectURL(blob);

      // Create a link element
      const link = document.createElement("a");

      // Set link's href to point to the Blob URL
      link.href = blobUrl;
      link.download = name;

      // Append link to the body
      document.body.appendChild(link);

      // Dispatch click event on the link
      // This is necessary as link.click() does not work on the latest firefox
      link.dispatchEvent(
          new MouseEvent('click', {
            bubbles: true,
            cancelable: true,
            view: window
          })
      );

      // Remove link from body
      document.body.removeChild(link);
    },
    async setRetakeItems() {
      let debts = await utility_debts.ajaxMoodleCall('local_cdo_debts_get_retake_list_by_user_id', {});
      if (debts.hasOwnProperty('error_message') && debts.error_message.length) {
        this.errorMessage = debts.error_message;
      } else {
        this.items = debts;
        this.items.forEach(item => {
          item.date_for_retake = this.convertDateFormService(item.date_for_retake);
          item.time = item.date_for_retake.toLocaleTimeString('ru-RU');
          item.upload = false;
          item.uploadDisagreed = false;
        });
      }
    },
    async updateRetakeStatusService(item, status) {
      this.upload = true;
      await this.updateRetakeStatus(item, status);
      this.upload = false;
    },
    async updateRetakeStatus(item, status) {
      let preparedDate = null;
      if (status === 2) {

        let chosenMonth = String(item.date_for_retake.getMonth() + 1).padStart(2, '0');
        let chosenDay = String(item.date_for_retake.getDate()).padStart(2, '0');
        preparedDate = item.date_for_retake.getFullYear().toString().concat(
            chosenMonth,
            chosenDay,
            item.time.replace(':', '').replace(':', '')
        );
      }
      let result = await utility_debts.ajaxMoodleCall(
          'local_cdo_debts_update_status_retake',
          {
            "document_id": item.document_source.id,
            "gradebook": item.gradebook,
            "status": status,
            "commentary": item.commentary,
            "date_for_retake": preparedDate,
            "user_id": item.student_id
          }
      );
      let content = 'Что-то пошло не так...';
      let type = TYPE.ERROR;
      if (result.hasOwnProperty('success')) {
        content = 'Успешно';
        type = TYPE.SUCCESS;
        item.status = result.new_status;
        item.date = result.date;
      }
      this.$toast(content, {
        timeout: 3000,
        type: type,
      });
    },
    convertDateFormService(dateToConvert) {
      return new Date(dateToConvert);
    },
    statusRowClass(item, type) {
      if (!item || type !== 'row') {
        return;
      }
      if (item.status.id === '1') {
        return 'table-danger';
      }
      if (item.status.id === '2') {
        return 'table-success';
      }
    }
  },
  computed: {
    setUploadStatus() {

    }
  },
  created() {
    this.setRetakeItems();
  }
};
</script>

<template>
  <div>
    <div v-if="items.length">
      <b-form-group
          label="Фильтр"
          label-for="filter-input"
          label-cols-sm="3"
          label-align-sm="right"
          label-size="sm"
          class="mb-0"
      >
        <b-input-group size="sm">
          <b-form-input
              id="filter-input"
              v-model="filter"
              type="search"
              placeholder="Введите для поиска"
          ></b-form-input>
          <b-input-group-append>
            <b-button :disabled="!filter" @click="filter = ''">Очистить</b-button>
          </b-input-group-append>
        </b-input-group>
      </b-form-group>
      <b-table striped
               class="mt-2"
               hover
               :items="items"
               :fields="headers"
               :filter="filter"
               :filter-included-fields="filterOn"
               :busy="upload"
               :tbody-tr-class="statusRowClass"
      >
        <template #table-busy>
          <div class="d-flex justify-content-center text-center text-danger my-2 mx-auto">
            <b-spinner class="align-middle"></b-spinner>
            <strong class="h3"> Обрабатываем ...</strong>
          </div>
        </template>
        <template #cell(show_details)="row">
          <b-button size="sm" @click="row.toggleDetails" class="mr-2">
            {{ row.detailsShowing ? 'Скрыть' : 'Показать сообщение' }}
          </b-button>
        </template>
        <template #row-details="row">
          <b-card>
            <b-row class="mb-2">
              <b-col sm="3" class="text-sm-right"><b>Заголовок:</b></b-col>
              <b-col>{{ row.item.subject }}</b-col>
            </b-row>
            <b-row class="mb-2">
              <b-col sm="3" class="text-sm-right"><b>Сообщение:</b></b-col>
              <b-col>{{ row.item.body }}</b-col>
            </b-row>
            <b-row class="mb-2">
              <b-col sm="3" class="text-sm-right"><b>Файл(ы):</b></b-col>
              <b-col>
                <template v-for="file in JSON.parse(row.item.file)">
                  <b-row class="mb-1">
                    <b-col>
                      <b-btn variant="primary" @click="getFile(file)">{{ file.filename }}</b-btn>
                    </b-col>
                  </b-row>
                </template>
              </b-col>
            </b-row>
            <b-button size="sm" @click="row.toggleDetails">Скрыть</b-button>
          </b-card>
        </template>
        <template #cell(file)="data">
          <b-btn variant="primary" @click="getFile(data)">Скачать</b-btn>
        </template>
        <template #cell(student)="data">
          {{ data.item.student }} ({{ data.item.gradebook }})
        </template>
        <template #cell(status)="data">
          {{ data.item.status.name }}
        </template>
        <template #cell(date_for_retake)="data">
          <div v-if="data.item.status.id!=='1'">
            <b-row>
              <b-col>
                <b-form-datepicker
                    label-no-date-selected=""
                    label-help=""
                    v-model="data.item.date_for_retake"
                    :value-as-date="true"
                    class="mb-2"
                    :date-format-options="{day:'2-digit', month:'2-digit',  year:'numeric'}"
                    :disabled="data.item.status.id==='0' ? false : true"
                >
                </b-form-datepicker>
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                <b-form-timepicker
                    :disabled="data.item.status.id==='0' ? false : true"
                    v-model="data.item.time"
                    label-no-time-selected=""
                    label-close-button="Закрыть"
                ></b-form-timepicker>
              </b-col>
            </b-row>
          </div>
        </template>
        <template #cell(actions)="data">
          <div v-if="data.item.status.id==='0'">
            <b-row>
              <b-col>
                <b-btn variant="success" class="w-100"
                       @click="updateRetakeStatusService(data.item,2)"
                >
                  Одобрить заявку
                </b-btn>
              </b-col>
            </b-row>
            <b-row class="mb-2">
              <b-col>
                <b-btn variant="danger" class="w-100 mt-2"
                       @click="updateRetakeStatusService(data.item, 1)"
                >
                  Отклонить заявку
                </b-btn>
              </b-col>
            </b-row>
            <b-row class="mb-2">
              <b-col>
                <b-textarea v-model="data.item.commentary" placeholder="Напишите комментарий">
                </b-textarea>
              </b-col>
            </b-row>
          </div>
        </template>
      </b-table>
    </div>
    <div v-else class="alert alert-primary"> Данных по пересдачам не найдено</div>
    <div v-if="!!errorMessage.length" class="alert alert-danger">{{ errorMessage }}</div>
  </div>
</template>

<style scoped>

</style>