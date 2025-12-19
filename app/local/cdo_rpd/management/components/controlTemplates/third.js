const third = {
    props: {
        questionsForDiscipline: {
            type: Object,
            required: true
        },
        parts: {
            type: Array,
            required: true
        },
        tab: {
            type: String,
            required: true
        },
        questionsList: {
            type: Array,
            required: true
        },
        competenceList: {
            type: Array,
            required: true
        },
        criteriaContent: {
            type: String,
            required: true,
            default: ''
        },
        importTooltipText: {
            type: String,
            required: false,
            default: ''
        },
    },
    data: () => ({
        selectCompetence: [],
        modalLabAddTheme: false,
        defaultTheme: {
            id: '',
            selectedTheme: '',
            name: '',
            target: '',
            content: '',
            result: '',
            link: '',
        },
        editedTheme: {
            id: '',
            selectedTheme: '',
            name: '',
            target: '',
            content: '',
            result: '',
            link: '',
        },

        modalLabAddQuestion: false,
        defaultQuestion: {
            id: '',
            competences: [],
            questionName: ''
        },
        editedQuestion: {
            id: '',
            competences: [],
            questionName: ''
        },

        editedId: -1,

        postQuestionsLoader: false,
        postThemesLoader: false,

        showCriteriaModal: false,

        modalCourseImportQuestion: false,
        importQuestionList: [],

        modalLabImportTheme: false,
        importThemeList: [],

    }),
    computed: {
        getThemesForSelect() {
            let allThemes = [];
            this.parts.forEach(part => {
                part.data.forEach(theme => {
                    allThemes = allThemes.concat(_.cloneDeep(theme));
                })
            });
            return allThemes.map(theme => ({
                id: theme.id,
                name_segment: theme.name_segment
            }));
        }
    },
    methods: {
        getBase64(file) {
            return new Promise(function (resolve, reject) {
                var reader = new FileReader();
                reader.onload = function () {
                    resolve(reader.result);
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        },
        async sendImport(file, type) {
            var promise = this.getBase64(file);
            let filebase64 = await promise;
            let this_ = this;
            require(['core/ajax', 'core/notification'], function (ajax, notification) {
                let promises = ajax.call([
                    {
                        methodname: 'read_import_file',
                        args: {
                            file: filebase64,
                            filename: file.name,
                            type: type
                        }
                    }
                ]);

                promises[0].done((response) => {

                    if (type == "third_questions") {
                        this_.importQuestionList = response;
                    }
                    this_.$toast.open({
                        message: `Успешно импортировано!`,
                        type: "success",
                        duration: 5000,
                        dismissible: true
                    });
                }).fail(function (ex) {
                    notification.exception(ex);
                });
            });
        },
        postQuestions(e) {
            this.sendImport(e.target.files[0], 'third_questions');
            e.target.value = '';
            this.modalCourseImportQuestion = true;
        },
        removeImportedQuestion(id) {
            this.importQuestionList = this.importQuestionList.filter(item => item.id !== id);
        },
        closeImportQuestionModal() {
            this.modalCourseImportQuestion = false;
            this.importQuestionList = [];
        },


        postThemes(e) {
            console.log(e.target.files);
            e.target.value = '';
            this.modalLabImportTheme = true;
            this.importThemeList = [{
                "id": "6c510161-bb93-4714-a2e7-bf252fcf3cd1",
                "selectedTheme": {"id": 1606460315059, "name_segment": "№2 _ Общая"},
                "name": "1",
                "target": "1",
                "content": "2",
                "result": "1",
                "link": "2"
            }, {
                "id": "533689d8-7a8b-4745-8344-b4c1f35d75a4",
                "selectedTheme": {"id": 21606460315059, "name_segment": "№1 _ Общая"},
                "name": "2",
                "target": "2",
                "content": "2",
                "result": "2",
                "link": "2"
            }];
        },
        removeImportedTheme(id) {
            this.importThemeList = this.importThemeList.filter(item => item.id !== id);
        },
        closeImportThemeModal() {
            this.modalLabImportTheme = false;
            this.importThemeList = [];
        },

        importList(items, type) {
            this.$emit('add-imported-list', {
                items: _.cloneDeep(items),
                type: type,
                tab: this.tab
            });
        },
        onCriteriaContentChange(e) {
            this.$emit('on-criteria-content-change', {
                text: e,
                tab: this.tab
            })
        },
        applyTheme() {
            if (!Object.values(this.editedTheme.selectedTheme).length) {
                this.$toast.open({
                    message: `Выберите тему<br> Если в выпадающем списке нет элементов, то в дисциплине нет ни одной темы`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            }
            if (!this.editedTheme.name.length) {
                this.$toast.open({
                    message: `Введите название`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            }
            if (!this.editedTheme.target.length) {
                this.$toast.open({
                    message: `Введите цель`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            }
            if (!this.editedTheme.content.length) {
                this.$toast.open({
                    message: `Введите содержание`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            }
            if (!this.editedTheme.result.length) {
                this.$toast.open({
                    message: `Введите результат`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            }
            if (!this.editedTheme.link.length) {
                this.$toast.open({
                    message: `Укажите ссылку`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            }

            if (this.editedId === -1) {
                this.editedTheme.id = uuidv4();
            }

            this.$emit('add-from-unallocated-part', {
                item: _.cloneDeep(this.editedTheme),
                type: 'themes',
                tab: this.tab
            });
            this.closeThemeModal();
        },
        editTheme(theme) {
            this.editedId = theme.id;
            this.editedTheme = _.cloneDeep(theme);
            this.modalLabAddTheme = true;
        },
        deleteTheme(theme) {
            this.$dialog
                .confirm('Удалить вопрос?')
                .then(dialog => {
                    this.$emit('delete-from-unallocated-part', {
                        item: _.cloneDeep(theme),
                        type: 'themes',
                        tab: this.tab
                    });
                }).catch(e => {
            })
        },
        closeThemeModal() {
            this.editedId = -1;
            this.editedTheme = _.cloneDeep(this.defaultTheme);
            this.modalLabAddTheme = false;
        },

        applyQuestion() {
            if (!this.editedQuestion.competences.length || !this.editedQuestion.questionName.length) {
                this.$toast.open({
                    message: `Оба поля обязательны`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            }

            if (this.editedId === -1) {
                this.editedQuestion.id = uuidv4();
            }

            this.$emit('add-from-unallocated-part', {
                item: _.cloneDeep(this.editedQuestion),
                type: 'questions',
                tab: this.tab
            });
            this.closeQuestionModal();
        },
        editQuestion(question) {
            this.editedId = question.id;
            this.editedQuestion = _.cloneDeep(question);
            this.modalLabAddQuestion = true;
        },
        deleteQuestion(question) {
            this.$dialog
                .confirm('Удалить вопрос?')
                .then(dialog => {
                    this.$emit('delete-from-unallocated-part', {
                        item: _.cloneDeep(question),
                        type: 'questions',
                        tab: this.tab
                    });
                }).catch(e => {
            })
        },
        closeQuestionModal() {
            this.editedId = -1;
            this.editedQuestion = _.cloneDeep(this.defaultQuestion);
            this.modalLabAddQuestion = false;
        },
        competenceChosen(competenceListInQuestion, competence) {
            return competenceListInQuestion.some(
                value => {
                    return value.id === competence;
                }
            );
        },
        onChooseCompetence(competence, checkedC) {
            if (checkedC.target.checked) {
                this.editedQuestion.competences.push(competence);
            }
            if (!checkedC.target.checked) {
                this.editedQuestion.competences = this.editedQuestion.competences.filter((element) =>
                    competence.id !== element.id
                );
            }
        },
    },
    template: `
    <div>
    
      <div class="controls-content">
        <accordion>
          <accordion-item class="accordion__trigger--white">
            <template slot="accordion-trigger">
              <div class="control-accordion__header">
                <p class="control-accordion__title">вопросы</p>
                <div class="lds-ellipsis" v-show="postQuestionsLoader"><div></div><div></div><div></div><div></div></div>
                <form enctype="multipart/form-data" @click.stop class="d-flex fw-bold" v-show="!postQuestionsLoader">
                  <label class="btn-import" v-tooltip="{content: importTooltipText}">
                    импорт вопросов
                    <input type="file" @change="postQuestions">
                    <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M19.35 6.04C18.67 2.59 15.64 0 12 0C9.11 0 6.6 1.64 5.35 4.04C2.34 4.36 0 6.91 0 10C0 13.31 2.69 16 6 16H19C21.76 16 24 13.76 24 11C24 8.36 21.95 6.22 19.35 6.04ZM14 9V13H10V9H7L12 4L17 9H14Z" fill="#2F80ED"/>
                    </svg>
                  </label>
                </form>
              </div>
            </template>
            <template slot="accordion-content">
              <div class="control-accordion__wrapper">
                <div class="control-accordion__content">
                  <div class="control-accordion__subheader">
                    <div class="control-accordion__subheader-item">Компетенция</div>
                    <div class="control-accordion__subheader-item">Задание</div>
                  </div>
                  <div class="control-accordion__list">
                    <div 
                      v-for="(question,i) in questionsForDiscipline[tab].questions"
                      :key="question.id"
                      class="control-accordion__item"
                     >
                      <div class="control-accordion__item-title">
                        <span v-for="competence in question.competences">{{competence.title}}; </span>
                      </div>
                      <div class="control-accordion__item-question">Вопрос {{i + 1}}. {{question.questionName}}</div>

                      <div class="ml-auto">
                        <button 
                          @click="deleteQuestion(question)"
                          class="btn-discard"
                        >
                          удалить
                        </button>
                        <button 
                          @click="editQuestion(question)" 
                          class="btn-confirm"
                        >
                          править
                        </button>
                      </div>
                      
                    </div>
                  </div>
                </div>
                <button class="btn-confirm" @click="modalLabAddQuestion = true">добавить</button>
              </div>
            </template>
          </accordion-item>
        </accordion>
        
        <accordion>
          <accordion-item class="accordion__trigger--white">
            <template slot="accordion-trigger">
              <div class="control-accordion__header">
                <p class="control-accordion__title">Темы</p>
                <div class="lds-ellipsis" v-show="postThemesLoader"><div></div><div></div><div></div><div></div></div>
                <form enctype="multipart/form-data" @click.stop class="d-flex fw-bold" v-show="!postThemesLoader">
                  <label class="btn-import" v-tooltip="{content: importTooltipText}">
                    импорт тем
                    <input type="file" @change="postThemes">
                    <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M19.35 6.04C18.67 2.59 15.64 0 12 0C9.11 0 6.6 1.64 5.35 4.04C2.34 4.36 0 6.91 0 10C0 13.31 2.69 16 6 16H19C21.76 16 24 13.76 24 11C24 8.36 21.95 6.22 19.35 6.04ZM14 9V13H10V9H7L12 4L17 9H14Z" fill="#2F80ED"/>
                    </svg>
                  </label>
                </form>
              </div>
            </template>
            <template slot="accordion-content">
              <div class="control-accordion__wrapper">
                <div class="control-accordion__content">
                  <div class="control-accordion__list">
                    <div 
                      v-for="(theme, i) in questionsForDiscipline[tab].themes"
                      :key="theme.id"
                      class="control-accordion__item"
                     >
                      <div class="control-accordion__item-question">Тема {{i + 1}}. {{theme.name}}</div>
                      
                      <div class="ml-auto">
                        <button
                          @click="deleteTheme(theme)"
                          class="btn-discard"
                        >
                          удалить
                        </button>
                        <button 
                          @click="editTheme(theme)"
                          class="btn-confirm"
                        >
                          править
                        </button>
                      </div>
                      
                    </div>
                  </div>
                </div>
                <button class="btn-confirm" @click="modalLabAddTheme = true">добавить</button>
              </div>
            </template>
          </accordion-item>
        </accordion>
      </div>
      
      <!-- добавление вопросов на лабораторную модалка-->
      <div class="modal-controls" v-show="modalLabAddQuestion">
      <svg @click="closeQuestionModal" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z" fill="black" fill-opacity="0.54"/>
      </svg>
      <span class="edit-question-subtitle">Вопросы</span>
      <div class="task-modal-item">
      <div class="d-flex">
            <template class="" v-for="competence in competenceList">
                  <label class="rpd-checkbox-container pl-4 mr-2">
                      <span v-html="competence.short_code" :title="competence.title"> </span>
                      <input 
                        type="checkbox"
                        :value = "competenceChosen(editedQuestion.competences, competence.id)"
                        :checked = "competenceChosen(editedQuestion.competences, competence.id)"
                        @change="onChooseCompetence(competence, $event)"
                      > 
                      <span class="checkbox-checkmark"></span>
                </label>
            </template>
        </div>
        <!--<multiselect 
          v-model="editedQuestion.competences"
          :options="competenceList" 
          :preserve-search="true"
          track-by="id" 
          label="title"
          multiple 
          :close-on-select="false"
          select-label="Выбрать" 
          selected-label="Выбрано" 
          placeholder="Выберите" 
          deselect-label="Удалить"
          select-group-label="Выбрать группу" 
          deselect-group-label="Удалить группу" 
          >
          <span slot="noResult">Ничего не найдено</span>
        </multiselect>-->
      </div>
      <div class="task-modal-item">
        <fielded-input
          v-model.trim="editedQuestion.questionName"
          label="Добавить задание"
          placeholder="Введите текст"
        ></fielded-input>
      </div>
      <button class="btn-confirm btn--self-start" @click="applyQuestion">принять</button>
    </div>
      <!--импорт вопросов-->
      <div class="modal-controls" v-show="modalCourseImportQuestion">
        <svg @click="closeImportQuestionModal" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z" fill="black" fill-opacity="0.54"/>
        </svg>
        <span class="edit-question-subtitle">Вопросы</span>
        <div class="questions-list">
          <div 
          class="d-flex align-items-center w-100 mb-35" 
          v-for="question in importQuestionList" 
          :key="question.id"
          >
          <div class="w-100">
            <div class="task-modal-item">
              <multiselect 
                v-model="question.competences"
                :options="competenceList" 
                :preserve-search="true"
                track-by="id" 
                label="title"
                multiple 
                :close-on-select="false"
                select-label="Выбрать" 
                selected-label="Выбрано" 
                placeholder="Выберите" 
                deselect-label="Удалить"
                select-group-label="Выбрать группу" 
                deselect-group-label="Удалить группу"
                :allow-empty="false"
                >
                <span slot="noResult">Ничего не найдено</span>
              </multiselect>
            </div>
            <div>
              <fielded-input
                v-model.trim="question.questionName"
                label="Добавить задание"
                placeholder="Введите текст"
              ></fielded-input>
            </div>
          </div>
          <svg @click="removeImportedTask(question.id)" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="delete-question"><path d="M12 1.20857L10.7914 0L6 4.79143L1.20857 0L0 1.20857L4.79143 6L0 10.7914L1.20857 12L6 7.20857L10.7914 12L12 10.7914L7.20857 6L12 1.20857Z" fill="#FF3A40"></path></svg>
        </div>
        </div>
        <button class="btn-confirm btn--self-start" 
                @click="importList(importQuestionList, 'questions'); closeImportQuestionModal()">принять</button>
      </div>
    
      <!-- добавление тем на лабораторную модалка-->
      <div class="modal-controls" v-show="modalLabAddTheme">
        <svg @click="modalLabAddTheme = false" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z" fill="black" fill-opacity="0.54"/>
        </svg>
        <span class="edit-question-subtitle">Тема</span>
        
        <div class="task-modal-item">
          <fielded-select
            v-model="editedTheme.selectedTheme"
            item-name="name_segment"
            :items="getThemesForSelect"
            label="Тема"
          ></fielded-select>
        </div>
        <div class="task-modal-item">
          <fielded-input
            v-model.trim="editedTheme.name"
            label="Название лабораторной работы"
            placeholder="Введите текст"
          ></fielded-input>
        </div>
        <div class="task-modal-item">
          <fielded-input
            v-model.trim="editedTheme.target"
            label="Цели"
            placeholder="Введите текст"
          ></fielded-input>
        </div>
        <div class="task-modal-item">
          <fielded-textarea
            v-model.trim="editedTheme.content"
            label="Содержание"
            placeholder="Введите текст"
          ></fielded-textarea>
        </div>
        <div class="task-modal-item">
          <fielded-input
            v-model.trim="editedTheme.result"
            label="Результаты"
            placeholder="Введите текст"
          ></fielded-input>
        </div>
        <div class="task-modal-item">
          <fielded-input
            v-model.trim="editedTheme.link"
            label="Ссылка на документ"
            placeholder="Введите текст"
          ></fielded-input>
        </div>
        <button class="btn-confirm btn--self-start" @click="applyTheme">принять</button>
      </div>
      <!-- импорт тем на лабораторную модалка-->
      <div class="modal-controls" v-show="modalLabImportTheme">
        <svg @click="closeImportThemeModal" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z" fill="black" fill-opacity="0.54"/>
        </svg>
        <span class="edit-question-subtitle">Импорт тем</span>
        <div class="questions-list">
          <div
          v-for="theme in importThemeList" 
          :key="theme.id"
          class="d-flex align-items-center mb-35">
          <div class="w-100">
            <div class="task-modal-item">
              <fielded-select
                v-model="theme.selectedTheme"
                item-name="name_segment"
                :items="getThemesForSelect"
                label="Тема"
              ></fielded-select>
            </div>
            <div class="task-modal-item">
              <fielded-input
                v-model.trim="theme.name"
                label="Название лабараторной работы"
                placeholder="Введите текст"
              ></fielded-input>
            </div>
            <div class="task-modal-item">
              <fielded-input
                v-model.trim="theme.target"
                label="Цели"
                placeholder="Введите текст"
              ></fielded-input>
            </div>
            <div class="task-modal-item">
              <fielded-textarea
                v-model.trim="theme.content"
                label="Содержание"
                placeholder="Введите текст"
              ></fielded-textarea>
            </div>
            <div class="task-modal-item">
              <fielded-input
                v-model.trim="theme.result"
                label="Результаты"
                placeholder="Введите текст"
              ></fielded-input>
            </div>
            <div class="task-modal-item">
              <fielded-input
                v-model.trim="theme.link"
                label="Ссылка на документ"
                placeholder="Введите текст"
              ></fielded-input>
            </div>
          </div>
          <svg @click="removeImportedTheme(theme.id)" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="delete-question"><path d="M12 1.20857L10.7914 0L6 4.79143L1.20857 0L0 1.20857L4.79143 6L0 10.7914L1.20857 12L6 7.20857L10.7914 12L12 10.7914L7.20857 6L12 1.20857Z" fill="#FF3A40"></path></svg>
        </div>
        </div>
        <button class="btn-confirm btn--self-start" @click="importList(importThemeList, 'themes'); closeImportThemeModal()">принять</button>
      </div>
      
      
      
      
      
      <div class="criteria">
        <span class="criteria__text">Критерии и шкала оценивания</span>
        <button class="btn-confirm" @click="showCriteriaModal = true">редактировать</button>
      </div>
      
      <criteria-modal 
        :is-open="showCriteriaModal" 
        :content="criteriaContent"
        @on-criteria-content-change="onCriteriaContentChange"
        @close-criteria-modal="showCriteriaModal = false"
      ></criteria-modal>
    </div> 
  `
}