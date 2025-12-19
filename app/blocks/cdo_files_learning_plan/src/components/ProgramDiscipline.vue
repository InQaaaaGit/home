<template>
  <div>

    <b-row class="py-0">
      <b-col cols="12">
        <b-form-group label="Категория файлов:">
          <b-form-radio-group
              id="btn-radios-2"
              v-model="selectedSection"
              :options="sections"
              buttons
              button-variant="outline-primary"
          ></b-form-radio-group>
        </b-form-group>
      </b-col>
      <b-col cols="12">
        <b-form-group label="Блок дисциплин:">
          <b-form-radio-group
              id="btn-radios-2"
              v-model="selectedBlock"
              :options="blocks"
              buttons
              button-variant="outline-primary"
          ></b-form-radio-group>
        </b-form-group>
      </b-col>
      <b-col cols="12">
        <b-form-group label="Дисциплина:">
          <b-input
              v-model="filterDiscipline"
          ></b-input>
        </b-form-group>
      </b-col>
    </b-row>

    <div v-if="selectedSection !== null && selectedBlock !== null">
      <b-row>
        <b-card
            v-for="(discipline, index) in filteredDiscipline"
            :key="index"
            :title="discipline.discipline_name"
            :sub-title="discipline.description_text"
            class="w-100"
        >

          <b-row class="py-0">
            <b-col cols="12" md="6">
              <div class="d-flex flex-column">
                <div v-for="(disc_file, index2) in discipline.filteredFiles"
                     :key="index2">
                  <b-link :href="disc_file.path" target="_blank">
                    {{ disc_file.filename }}
                  </b-link>
                  &nbsp;&nbsp;
                  <b-link @click="showConfirmDelete(disc_file.guidfile, discipline.discipline_id,
                  discipline.discipline_index, discipline.module_id, discipline.type)"
                          v-if="!getSettings.isAuditor"
                  >
                    <b-icon icon="trash-fill"></b-icon>
                  </b-link>
                </div>

                <div v-for="(link, index3) in discipline.filteredLinks"
                     :key="index3">
                  <b-link :href="link.link_URL" target="_blank">
                    {{ link.link_name }}
                  </b-link>
                  &nbsp;&nbsp;
                  <b-link @click="showModalLink(link.link_guid, discipline)" v-if="!getSettings.isAuditor">
                    <b-icon icon="pencil-square"></b-icon>
                  </b-link>
                  &nbsp;&nbsp;
                  <b-link @click="showConfirmDeleteLink(link.link_guid, link.discipline_id)"
                          v-if="!getSettings.isAuditor"
                  >
                    <b-icon icon="trash-fill"></b-icon>
                  </b-link>
                </div>

                <div class="mt-3">
                  <b-button @click="showModalDiscFiles(discipline.discipline_id, discipline.discipline_index,
                  discipline.module_id, discipline.type)"
                            variant="primary"
                            v-if="!getSettings.isAuditor && (!discipline.filteredFiles.length || selectedSection === 3)"
                  >
                    Загрузить файл
                  </b-button>
                  <b-button @click="showModalLink('', discipline)"
                            variant="primary"
                            v-if="!getSettings.isAuditor && selectedSection === 3">
                    Добавить ссылку
                  </b-button>
                </div>
              </div>
            </b-col>
            <b-col cols="12" md="6">
              <b-row v-if="discipline.status_rpd">
                <b-col cols="12" md="8">
                  <b-card-text>
                    Ссылка на скачивание:
                    <b-link
                        style="color: blue;"
                        :href="`/ulsu/rpd/make.php?type=${getProjectFileType}&rpd_id=${discipline.rpd_id}&edu_plan=${discipline.edu_plan_number}&discipline=${discipline.discipline_number}`"
                        target="_blank">
                      {{getProjectFile}}
                    </b-link>
                  </b-card-text>
                </b-col>
              </b-row>
              <b-row class="p-0 mt-10">
                <b-col cols="12" md="8">
                  <b-card-text>
                    Комментарий:&nbsp;
                    <b-link
                        v-if="getSettings.isAuditor"
                        @click="showModalNotes(discipline)"
                    >
                      <b-icon icon="pencil-square"></b-icon>
                    </b-link>
                  </b-card-text>
                  <b-card-text>
                    {{ discipline.notes }}
                  </b-card-text>
                </b-col>
              </b-row>
              <b-row>
                <b-col cols="12" md="8">
                  <b-card-text>
                    Ответственный за разработку: {{ discipline.developer }}
                  </b-card-text>
                </b-col>
              </b-row>
              <b-row>
                <b-col>
                  <b-btn @click="openModalAgreed(discipline)">Утверждение</b-btn>
                </b-col>
              </b-row>
            </b-col>
          </b-row>

        </b-card>
      </b-row>
    </div>

    <b-modal v-model="showModalNote"
             title="Изменить комментарий"
             centered
             hide-footer
             v-if="getSettings.isAuditor">
      <b-form ref="formNotes" @submit.prevent="submitNotes" validated>
        <b-form-group
            id="note-group"
            label="Комментарий:"
            label-for="note"
        >
          <b-form-textarea
              v-model="formNotes.notes"
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

    <b-modal v-model="showModalDiscFile"
             title="Загрузка файла"
             centered
             hide-footer
    >
      <b-form ref="formDiscFile" @submit.prevent="submitDiscFile" validated>
        <b-form-group
            id="label-section-group"
            label="Категория:"
            label-for="label-section"
        >
          <span id="label-section">{{ selectedSectionName }}</span>
        </b-form-group>
        <b-form-group
            id="label-discipline-group"
            label="Дисциплина:"
            label-for="label-discipline"
        >
          <span id="label-discipline">{{ selectedDisciplineName }}</span>
        </b-form-group>
        <b-form-group
            id="input-discfile-program-group"
            label="Файл:"
            label-for="input-discfile-program"
            invalid-feedback="Поле должно быть заполнено"
        >
          <b-form-file
              v-model="formDiscFile.file"
              id="input-discfile-program"
              placeholder="Выберите файл"
              drop-placeholder="Перетащите файл сюда..."
              invalid-feedback="Поле должно быть заполнено"
              required
              :multiple="selectedSection === 3"
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

    <b-modal v-model="showModalLinks"
             title="Добавление/изменение ссылки"
             centered
             hide-footer
             v-if="!getSettings.isAuditor"
    >
      <b-form ref="formLink" @submit.prevent="submitLink" validated>
        <b-form-group
            id="name-link-group"
            label="Наименование:"
            label-for="name-link"
            invalid-feedback="Поле должно быть заполнено"
        >
          <b-form-input
              v-model="formLink.link_name"
              id="name-link"
              required
          ></b-form-input>
        </b-form-group>
        <b-form-group
            id="url-link-group"
            label="Ссылка:"
            label-for="url-link"
            invalid-feedback="Поле должно быть заполнено"
        >
          <b-form-input
              v-model="formLink.link_URL"
              id="url-link"
              required
          ></b-form-input>
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

    <b-modal v-model="showAgreedLinks"
             title="Утверждение РПД на заседании ученого совета подразделения"
             centered
             hide-footer
             v-if="!getSettings.isAuditor"
    >
      <div>
        <b-form-group
            id="name-link-group"
            label="Укажите номер протокола"
            label-for="type-agreed-number"
            invalid-feedback="Поле должно быть заполнено"
        >
          <b-form-input v-model="agreedNumber" id="type-agreed-number" type="text"/>
        </b-form-group>

      </div>
      <div>
        <b-form-group
            id="name-link-group"
            label="Укажите дату протокола"
            label-for="type-agreed-date"
            invalid-feedback="Поле должно быть заполнено"
        >
          <b-form-input v-model="agreedDate" id="type-agreed-date" type="date"/>
        </b-form-group>
      </div>
      <div>
        <b-form-group
            id="name-link-group"
            label="Укажите подразделение:"
            label-for="type-agreed-structure"
            invalid-feedback="Поле должно быть заполнено"
        >
          <b-form-select v-model="agreedStructure" id="type-agreed-structure"
                         :options="getProgramData.structures"/>
        </b-form-group>
      </div>

      <div>
        <b-btn @click="saveAgreed" v-show="!showAgreedLoad"
               :disabled="!agreedStructure.length && !agreedNumber.length && !agreedDate.length">
          Сохранить
        </b-btn>
        <b-spinner label="Spinning" v-show="showAgreedLoad"></b-spinner>
      </div>
    </b-modal>
  </div>
</template>

<script>
import {mapActions, mapGetters} from "vuex";

import axios from "axios";

export default {
  name: "ProgramDiscipline",
  data() {
    return {
      showAgreedLoad: false,
      structures: [],
      agreedNumber: '',
      currentRPD: '',
      agreedStructure: '',
      agreedDate: '',
      showModalDiscFile: false,
      showModalNote: false,
      showModalLinks: false,
      showAgreedLinks: false,
      formDiscFile: {
        update_mode: 'new_file',
        discipline_id: '',
        section: null,
        doc_id: null,
        discipline_index: '',
        module_id: '',
        type: '',
      },
      formNotes: {
        notes: '',
        update_mode: '',
        discipline_id: '',
        discipline_index: '',
        module_id: '',
        type: '',
      },
      formLink: null,
      formAgreed: null,
      selectedSection: null,
      selectedBlock: null,
      filterDiscipline: '',
      sections: [
        {text: 'Программы дисциплин, модулей, практик, ГИА', value: 0},
        {text: 'Фонды оценочных средств', value: 1},
        {text: 'Аннотации', value: 2},
        {text: 'Методические указания', value: 3},
      ],
    }
  },
  created() {
    this.resetFormLink();
  },
  computed: {
    ...mapGetters('PROGRAM', [
      'getLoadingProgram',
      'getProgramData',
      'getSelectedProgram',
      'getSavingProgram'
    ]),
    ...mapGetters('APP', [
      'getSettings'
    ]),
    getProjectFile(){
      switch (this.selectedSection) {
        case 0:
          return 'Проект РПД';
        case 1:
          return 'Проект ФОС';
        case 2:
          return 'Проект Аннотации';
      }
    },
    getProjectFileType(){
      switch (this.selectedSection) {
        case 0:
          return 'rpd';
        case 1:
          return 'fos';
        case 2:
          return 'annotation';
      }
    },

    blocks() {
      return this.getProgramData.discipline_files.reduce((result, item) => {
        if (result.find(item2 => item2.value === item.block) === undefined)
          result.push({text: item.block, value: item.block})
        return result;
      }, [])
    },
    filteredDiscipline() {
      if (this.selectedSection !== null && this.selectedBlock !== null) {
        let filtered = this.getProgramData.discipline_files.filter(item => item.block === this.selectedBlock)
        if (this.filterDiscipline)
          filtered = filtered.filter(item => item.discipline_name.toLowerCase().indexOf(this.filterDiscipline.toLowerCase()) !== -1)
        filtered.map(item => {
          item.filteredFiles = item.files.filter(file => file.section.id === this.selectedSection);
          item.filteredLinks = this.selectedSection === 3
              ? this.getProgramData.web_links.filter(link => link.discipline_id === item.discipline_id
                  && link.discipline_index === item.discipline_index
                  && link.module_id === item.module_id
                  && link.type === item.type)
              : [];
        })
        return filtered;
      }
    },
    selectedSectionName() {
      return this.selectedSection !== null ? this.sections.find(item => item.value === this.selectedSection).text : ''
    },
    selectedDisciplineName() {
      const find = this.getProgramData.discipline_files.find(item => item.discipline_id === this.formDiscFile.discipline_id)
      return find ? find.discipline_name : ''
    },
  },
  methods: {
    ...mapActions('PROGRAM', [
      'putNotesProgram',
      'deleteDisciplineFile',
      'putDisciplineFile',
      'putLinkProgram',
      'deleteLinkProgram',
      'changeAgreedInfo'
    ]),
    openModalAgreed(discipline) {

      this.showAgreedLinks = true;
      this.currentRPD = discipline.rpd_id;
      this.agreedDate = discipline.agreed_date;
      this.agreedNumber = discipline.agreed_number;
      this.agreedStructure = discipline.agreed_structures;
    },
    async saveAgreed() {
      this.showAgreedLoad = true;
      let sendParameters = {
        rpd_id: this.currentRPD,
        date: this.agreedDate,
        number: this.agreedNumber,
        structure: this.agreedStructure
      }

      let result = await axios
          .post('/blocks/cdo_files_learning_plan/set_agreed_api.php', sendParameters)
          .then(response => (this.info = response));
      if (result.data.status) {
        this.showAgreedLinks = false;
        this.showAgreedLoad = false;
        this.changeAgreedInfo(sendParameters);
      }
    },
    async submitDiscFile() {
      this.formDiscFile.doc_id = this.getSelectedProgram;
      this.formDiscFile.section = this.selectedSection;
      await this.putDisciplineFile(this.formDiscFile);
      this.showModalDiscFile = false;
    },
    async submitNotes() {
      this.formNotes.doc_id = this.getSelectedProgram;
      await this.putNotesProgram(this.formNotes);
      this.showModalNote = false;
    },
    async submitLink() {
      this.formLink.doc_id = this.getSelectedProgram;
      await this.putLinkProgram(this.formLink);
      this.showModalLinks = false;
    },
    async submitAgreed() {
      this.formAgreed = 1;
      this.showAgreedLinks = false;
    },
    showModalDiscFiles(discipline_id, discipline_index, module_id, type) {
      this.showModalDiscFile = true;
      this.formDiscFile.discipline_id = discipline_id;
      this.formDiscFile.discipline_index = discipline_index;
      this.formDiscFile.module_id = module_id;
      this.formDiscFile.type = type;
    },
    showModalNotes({discipline_id, discipline_index, module_id, type}) {
      const find = this.getProgramData.discipline_files.find(item => item.discipline_id === discipline_id &&
          item.discipline_index === discipline_index &&
          item.module_id === module_id &&
          item.type === type
      );
      this.showModalNote = true;
      this.formNotes.notes = find !== undefined ? find.notes : '';
      this.formNotes.discipline_id = discipline_id;
      this.formNotes.discipline_index = discipline_index;
      this.formNotes.module_id = module_id;
      this.formNotes.type = type;
      this.formNotes.update_mode = 'update_notes';
    },
    showConfirmDelete(guidfile, discipline_id, discipline_index, module_id, type) {
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
              this.deleteDisciplineFile({
                guidfile: guidfile,
                discipline_id: discipline_id,
                discipline_index: discipline_index,
                module_id: module_id,
                type: type,
                doc_id: this.getSelectedProgram
              });
          })
    },
    showConfirmDeleteLink(link_guid, discipline_id) {
      this.$bvModal.msgBoxConfirm('Вы действительно хотите удалить ссылку?', {
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
              this.deleteLinkProgram({
                link_guid: link_guid,
                doc_id: this.getSelectedProgram,
                discipline_id: discipline_id
              });
          })
    },
    showModalLink(id, {discipline_id, discipline_index, module_id, type}) {
      this.showModalLinks = true;
      if (id === '') {
        this.resetFormLink();
        this.formLink.discipline_id = discipline_id;
        this.formLink.module_id = module_id;
        this.formLink.discipline_index = discipline_index;
        this.formLink.type = type;
      } else {
        this.formLink = {...this.getProgramData.web_links.find(item => item.link_guid === id)};
        this.formLink.update_mode = 'update_link';
      }
    },
    resetFormLink() {
      this.formLink = {
        update_mode: 'new_link',
        link_guid: '',
        link_name: '',
        link_URL: '',
        discipline_id: '',
        discipline_index: '',
        module_id: '',
        type: '',
        doc_id: null,
      }
    }
  }
}
</script>

<style scoped>

</style>