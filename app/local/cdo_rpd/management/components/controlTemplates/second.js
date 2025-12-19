const second = {
    props: {
        questionsForDiscipline: {
            type: Object,
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
            required: false
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
        coDev: {
            type: Boolean,
            default: false,
        }
    },
    data: () => ({
        selectCompetence: [],
        modalCourseAddTask: false,
        defaultTask: {
            id: '',
            competences: [],
            taskName: ''
        },
        editedTask: {
            id: '',
            competences: [],
            taskName: ''
        },

        modalCourseAddTheme: false,
        defaultTheme: {
            id: '',
            themeName: ''
        },
        editedTheme: {
            id: '',
            themeName: ''
        },

        modalCourseAddQuestion: false,
        defaultQuestion: {
            id: '',
            questionName: ''
        },
        editedQuestion: {
            id: '',
            questionName: ''
        },

        editedId: -1,

        postTaskLoader: false,
        postThemesLoader: false,
        postQuestionsLoader: false,

        showCriteriaModal: false,

        modalCourseImportTask: false,
        importTasksList: [],


        modalCourseImportTheme: false,
        importThemeList: [],

        modalCourseImportQuestion: false,
        importQuestionList: [],
    }),
    methods: {
        onChooseCompetence(competence, checkedC) {
            if (checkedC.target.checked) {
                this.editedTask.competences.push(competence);
            }
            if (!checkedC.target.checked) {
                this.editedTask.competences = this.editedTask.competences.filter((element) =>
                    competence.id !== element.id
                );
            }
        },
        competenceChosen(competenceListInQuestion, competence) {
            return competenceListInQuestion.some(
                value => {
                    return value.id === competence;
                }
            );
        },
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
            let promise = this.getBase64(file);
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

                    if (type == "second_themes") {
                        this_.importThemeList = response;
                    } else if (type == "second_questions") {
                        this_.importQuestionList = response;
                    } else if (type == "second_task") {
                        this_.importTasksList = response;
                    }
                    this_.$toast.open({
                        message: `Успшено импортировано!`,
                        type: "success",
                        duration: 5000,
                        dismissible: true
                    });
                }).fail(function (ex) {
                    notification.exception(ex);
                });
            });
        },
        importTasks(result) {
            let arrayQuestions = result.value.split("<li>");
            arrayQuestions.shift();
            arrayQuestions = arrayQuestions.map(item => item.replace("</li>", ""));
            arrayQuestions = arrayQuestions.map(item => item.replace("</ol>", ""));
            let summaryArray = [];
            arrayQuestions.forEach(item => {
                let question = {};
                question.taskName = item;
                question.id = uuidv4();
                question.competences = [];
                summaryArray.push(question);
            });
            // console.log(summaryArray);
            this.importTasksList = summaryArray;
        },
        importThemes(result) {
            let arrayQuestions = result.value.split("<li>");
            arrayQuestions.shift();
            arrayQuestions = arrayQuestions.map(item => item.replace("</li>", ""));
            arrayQuestions = arrayQuestions.map(item => item.replace("</ol>", ""));
            let summaryArray = [];
            arrayQuestions.forEach(item => {
                let question = {};
                question.themeName = item;
                question.id = uuidv4();
                summaryArray.push(question);
            });
            // console.log(summaryArray);
            this.importThemeList = summaryArray;
        },
        importQuestions(result){
            let arrayQuestions = result.value.split("<li>");
            arrayQuestions.shift();
            arrayQuestions = arrayQuestions.map(item => item.replace("</li>", ""));
            arrayQuestions = arrayQuestions.map(item => item.replace("</ol>", ""));
            let summaryArray = [];
            arrayQuestions.forEach(item => {
                let question = {};
                question.questionName  = item;
                question.id = uuidv4();
                summaryArray.push(question);
            });
            // console.log(summaryArray);
            this.importQuestionList = summaryArray;
        },
        readFileInputEventAsArrayBuffer(event, callback) {
            var file = event.target.files[0];

            var reader = new FileReader();

            reader.onload = function (loadEvent) {
                var arrayBuffer = loadEvent.target.result;
                callback(arrayBuffer);
            };

            reader.readAsArrayBuffer(file);
        },
        postTask(e) {
            console.log('postTaks');
            this.readFileInputEventAsArrayBuffer(e, (arrayBuffer) => {
                mammoth
                    .convertToHtml({arrayBuffer: arrayBuffer})
                    .then(this.importTasks)
                    .done();
            });
            this.modalCourseImportTask = true;
            e.target.value = '';
        },
        removeImportedTask(id) {
            this.importTasksList = this.importTasksList.filter(item => item.id !== id);
        },
        closeImportTaskModal() {
            this.modalCourseImportTask = false;
            this.importTasksList = [];
        },
        postThemes(e) {
            /*this.sendImport(e.target.files[0], 'second_themes');
            this.modalCourseImportTheme = true;
            e.target.value = '';*/
            console.log('postThemes');
            this.readFileInputEventAsArrayBuffer(e, (arrayBuffer) => {
                mammoth
                    .convertToHtml({arrayBuffer: arrayBuffer})
                    .then(this.importThemes)
                    .done();
            });
            this.modalCourseImportTheme = true;
            e.target.value = '';
        },
        removeImportedTheme(id) {
            this.importThemeList = this.importThemeList.filter(item => item.id !== id);
        },
        closeImportThemeModal() {
            this.modalCourseImportTheme = false;
            this.importThemeList = [];
        },
        postQuestions(e) {
            /*console.log(e.target.files);
            this.sendImport(e.target.files[0], 'second_questions');
            this.modalCourseImportQuestion = true;*/
            console.log('postQuestions');
            this.readFileInputEventAsArrayBuffer(e, (arrayBuffer) => {
                mammoth
                    .convertToHtml({arrayBuffer: arrayBuffer})
                    .then(this.importQuestions)
                    .done();
            });
            this.modalCourseImportQuestion = true;
            e.target.value = '';
        },
        removeImportedQuestion(id) {
            this.importQuestionList = this.importQuestionList.filter(item => item.id !== id);
        },
        closeImportQuestionModal() {
            this.modalCourseImportQuestion = false;
            this.importQuestionList = [];
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
        applyQuestion() {
            if (!this.editedQuestion.questionName.length) {
                this.$toast.open({
                    message: `Введите название`,
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
            this.modalCourseAddQuestion = true;
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
            this.modalCourseAddQuestion = false;
        },


        applyTheme() {
            if (!this.editedTheme.themeName.length) {
                this.$toast.open({
                    message: `Введите название`,
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
            this.modalCourseAddTheme = true;
        },
        deleteTheme(theme) {
            this.$dialog
                .confirm('Удалить тему?')
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
            this.modalCourseAddTheme = false;
        },

        applyTask() {
            if (!this.editedTask.competences.length || !this.editedTask.taskName.length) {
                this.$toast.open({
                    message: `Все поля обязательны`,
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
                return
            }

            if (this.editedId === -1) {
                this.editedTask.id = uuidv4();
            }

            this.$emit('add-from-unallocated-part', {
                item: _.cloneDeep(this.editedTask),
                type: 'tasks',
                tab: this.tab
            });

            this.closeTaskModal();
        },
        editTask(task) {
            this.editedId = task.id;
            this.editedTask = _.cloneDeep(task);
            this.modalCourseAddTask = true;
        },
        deleteTask(task) {
            this.$dialog
                .confirm('Удалить задание?')
                .then(dialog => {
                    this.$emit('delete-from-unallocated-part', {
                        item: _.cloneDeep(task),
                        type: 'tasks',
                        tab: this.tab
                    });
                }).catch(e => {
            })
        },
        closeTaskModal() {
            this.editedId = -1;
            this.editedTask = _.cloneDeep(this.defaultTask);
            this.modalCourseAddTask = false;
        },

    },
    template: `
      <div>
      <div class="controls-content">
        <accordion>
          <accordion-item class="accordion__trigger--white">
            <template slot="accordion-trigger">
              <div class="control-accordion__header">
                <p class="control-accordion__title">Задания</p>
                <div class="lds-ellipsis" v-show="postTaskLoader">
                  <div></div>
                  <div></div>
                  <div></div>
                  <div></div>
                </div>
                <form enctype="multipart/form-data" @click.stop class="d-flex fw-bold" v-show="!postTaskLoader">
                  <label class="btn-import" v-tooltip="{content: importTooltipText}">
                    импорт
                    <input type="file" @change="postTask">
                    <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                          d="M19.35 6.04C18.67 2.59 15.64 0 12 0C9.11 0 6.6 1.64 5.35 4.04C2.34 4.36 0 6.91 0 10C0 13.31 2.69 16 6 16H19C21.76 16 24 13.76 24 11C24 8.36 21.95 6.22 19.35 6.04ZM14 9V13H10V9H7L12 4L17 9H14Z"
                          fill="#2F80ED"/>
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
                        v-for="(task, i) in questionsForDiscipline[tab].tasks"
                        :key="task.id"
                        class="control-accordion__item"
                    >
                      <div class="control-accordion__item-title">
                        <span v-for="competence in task.competences">{{ competence.title }}; </span>
                      </div>
                      <div class="control-accordion__item-question">Вопрос {{ i + 1 }}. {{ task.taskName }}</div>

                      <div class="ml-auto">
                        <button
                            @click="deleteTask(task)"
                            class="btn-discard"
                        >
                          удалить
                        </button>
                        <button
                            @click="editTask(task)"
                            class="btn-confirm ml-auto"
                        >
                          править
                        </button>
                      </div>


                    </div>
                  </div>
                </div>
                <button class="btn-confirm" @click="modalCourseAddTask = true">добавить</button>
              </div>
            </template>
          </accordion-item>
        </accordion>

        <accordion>
          <accordion-item class="accordion__trigger--white">
            <template slot="accordion-trigger">
              <div class="control-accordion__header">
                <p class="control-accordion__title">Темы</p>
                <div class="lds-ellipsis" v-show="postThemesLoader">
                  <div></div>
                  <div></div>
                  <div></div>
                  <div></div>
                </div>
                <form enctype="multipart/form-data" @click.stop class="d-flex fw-bold" v-show="!postThemesLoader">
                  <label class="btn-import" v-tooltip="{content: importTooltipText}">
                    импорт тем
                    <input type="file" @change="postThemes">
                    <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                          d="M19.35 6.04C18.67 2.59 15.64 0 12 0C9.11 0 6.6 1.64 5.35 4.04C2.34 4.36 0 6.91 0 10C0 13.31 2.69 16 6 16H19C21.76 16 24 13.76 24 11C24 8.36 21.95 6.22 19.35 6.04ZM14 9V13H10V9H7L12 4L17 9H14Z"
                          fill="#2F80ED"/>
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
                      <div class="control-accordion__item-question">Тема {{ i + 1 }}. {{ theme.themeName }}</div>

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
                <button class="btn-confirm" @click="modalCourseAddTheme = true">добавить</button>
              </div>
            </template>
          </accordion-item>
        </accordion>

        <accordion>
          <accordion-item class="accordion__trigger--white">
            <template slot="accordion-trigger">
              <div class="control-accordion__header">
                <p class="control-accordion__title">вопросы</p>
                <div class="lds-ellipsis" v-show="postQuestionsLoader">
                  <div></div>
                  <div></div>
                  <div></div>
                  <div></div>
                </div>
                <form enctype="multipart/form-data" @click.stop class="d-flex fw-bold" v-show="!postQuestionsLoader">
                  <label class="btn-import" v-tooltip="{content: importTooltipText}">
                    импорт
                    <input type="file" @change="postQuestions">
                    <svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                          d="M19.35 6.04C18.67 2.59 15.64 0 12 0C9.11 0 6.6 1.64 5.35 4.04C2.34 4.36 0 6.91 0 10C0 13.31 2.69 16 6 16H19C21.76 16 24 13.76 24 11C24 8.36 21.95 6.22 19.35 6.04ZM14 9V13H10V9H7L12 4L17 9H14Z"
                          fill="#2F80ED"/>
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
                        v-for="(question,i) in questionsForDiscipline[tab].questions"
                        :key="question.id"
                        class="control-accordion__item"
                    >
                      <div class="control-accordion__item-question">Вопрос {{ i + 1 }}.{{ question.questionName }}
                      </div>

                      <div class="ml-auto">
                        <button
                            @click="deleteQuestion(question)"
                            class="btn-discard"
                        >
                          удалить
                        </button>
                        <button
                            @click="editQuestion(question)"
                            class="btn-confirm ml-auto"
                        >
                          править
                        </button>
                      </div>

                    </div>
                  </div>
                </div>
                <button class="btn-confirm" @click="modalCourseAddQuestion = true">добавить</button>
              </div>
            </template>
          </accordion-item>
        </accordion>
      </div>

      <!--добавление задания-->
      <div class="modal-controls" v-show="modalCourseAddTask">
        <svg @click="closeTaskModal" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30"
             fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
              d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z"
              fill="black" fill-opacity="0.54"/>
        </svg>
        <span class="edit-question-subtitle">Задание</span>
        <div class="task-modal-item">
        <div class="d-flex">
            <template class="" v-for="competence in competenceList">
                  <label class="rpd-checkbox-container pl-4 mr-2">
                      <span v-html="competence.short_code" :title="competence.title"> </span>
                      <input 
                        type="checkbox"
                        :value = "competenceChosen(editedTask.competences, competence.id)"
                        :checked = "competenceChosen(editedTask.competences, competence.id)"
                        @change="onChooseCompetence(competence, $event)"
                      > 
                      <span class="checkbox-checkmark"></span>
                </label>
            </template>
        </div>
          <!--<multiselect
              v-model="editedTask.competences"
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
          </multiselect>-->
        </div>
        <div class="task-modal-item">
          <fielded-input
              v-model.trim="editedTask.taskName"
              label="Добавить задание"
              placeholder="Введите текст"
          ></fielded-input>
        </div>
        <button class="btn-confirm btn--self-start" @click="applyTask">принять</button>
      </div>
      <!--импорт списка вопросов-->
      <div class="modal-controls" v-show="modalCourseImportTask">
        <svg @click="closeImportTaskModal" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30"
             fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
              d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z"
              fill="black" fill-opacity="0.54"/>
        </svg>
        <span class="edit-question-subtitle">Импорт</span>
        <div class="questions-list">
          <div class="d-flex align-items-center w-100 mb-35" v-for="question in importTasksList" :key="question.id">
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
                    v-model.trim="question.taskName"
                    label="Добавить задание"
                    placeholder="Введите текст"
                ></fielded-input>
              </div>
            </div>
            <svg @click="removeImportedTask(question.id)" width="12" height="12" viewBox="0 0 12 12" fill="none"
                 xmlns="http://www.w3.org/2000/svg" class="delete-question">
              <path
                  d="M12 1.20857L10.7914 0L6 4.79143L1.20857 0L0 1.20857L4.79143 6L0 10.7914L1.20857 12L6 7.20857L10.7914 12L12 10.7914L7.20857 6L12 1.20857Z"
                  fill="#FF3A40"></path>
            </svg>
          </div>
        </div>
        <button class="btn-confirm btn--self-start"
                @click="importList(importTasksList, 'tasks'); closeImportTaskModal()">принять
        </button>
      </div>


      <!--добавление тем-->
      <div class="modal-controls" v-show="modalCourseAddTheme">
        <svg @click="closeThemeModal" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30"
             fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
              d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z"
              fill="black" fill-opacity="0.54"/>
        </svg>
        <span class="edit-question-subtitle">Тема</span>
        <div class="task-modal-item">
          <fielded-input
              v-model.trim="editedTheme.themeName"
              label="Добавить тему"
              placeholder="Введите текст"
          ></fielded-input>
        </div>
        <button class="btn-confirm btn--self-start" @click="applyTheme">принять</button>
      </div>
      <!--импорт тем-->
      <div class="modal-controls" v-show="modalCourseImportTheme">
        <svg @click="closeImportThemeModal" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30"
             fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
              d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z"
              fill="black" fill-opacity="0.54"/>
        </svg>
        <span class="edit-question-subtitle">Импорт тем</span>
        <div class="questions-list">
          <div
              v-for="theme in importThemeList"
              :key="theme.id"
              class="task-modal-item d-flex align-items-center">
            <fielded-input
                v-model.trim="theme.themeName"
                label="Добавить тему"
                placeholder="Введите текст"
                class="w-100"
            ></fielded-input>
            <svg @click="removeImportedTheme(theme.id)" width="12" height="12" viewBox="0 0 12 12" fill="none"
                 xmlns="http://www.w3.org/2000/svg" class="delete-question">
              <path
                  d="M12 1.20857L10.7914 0L6 4.79143L1.20857 0L0 1.20857L4.79143 6L0 10.7914L1.20857 12L6 7.20857L10.7914 12L12 10.7914L7.20857 6L12 1.20857Z"
                  fill="#FF3A40"></path>
            </svg>
          </div>
        </div>
        <button class="btn-confirm btn--self-start"
                @click="importList(importThemeList, 'themes'); closeImportThemeModal()">принять
        </button>
      </div>


      <!--добавление вопросов-->
      <div class="modal-controls" v-show="modalCourseAddQuestion">
        <svg @click="closeQuestionModal" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30"
             fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
              d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z"
              fill="black" fill-opacity="0.54"/>
        </svg>
        <span class="edit-question-subtitle">Вопросы</span>
        <div class="task-modal-item">
          <fielded-input
              v-model="editedQuestion.questionName"
              label="Добавить вопрос"
              placeholder="Введите текст"
          ></fielded-input>
        </div>
        <button class="btn-confirm btn--self-start" @click="applyQuestion">принять</button>
      </div>

      <!--импорт вопросов-->
      <div class="modal-controls" v-show="modalCourseImportQuestion">
        <svg @click="closeImportQuestionModal" class="modal-controls__close" width="30" height="30" viewBox="0 0 30 30"
             fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
              d="M30 3.02143L26.9786 0L15 11.9786L3.02143 0L0 3.02143L11.9786 15L0 26.9786L3.02143 30L15 18.0214L26.9786 30L30 26.9786L18.0214 15L30 3.02143Z"
              fill="black" fill-opacity="0.54"/>
        </svg>
        <span class="edit-question-subtitle">Импорт</span>
        <div class="questions-list">
          <div
              v-for="question in importQuestionList"
              :key="question.id"
              class="task-modal-item d-flex align-items-center">
            <fielded-input
                v-model="question.questionName"
                label="Добавить вопрос"
                placeholder="Введите текст"
                class="w-100"
            ></fielded-input>
            <svg @click="removeImportedTheme(theme.id)" width="12" height="12" viewBox="0 0 12 12" fill="none"
                 xmlns="http://www.w3.org/2000/svg" class="delete-question">
              <path
                  d="M12 1.20857L10.7914 0L6 4.79143L1.20857 0L0 1.20857L4.79143 6L0 10.7914L1.20857 12L6 7.20857L10.7914 12L12 10.7914L7.20857 6L12 1.20857Z"
                  fill="#FF3A40"></path>
            </svg>
          </div>
        </div>
        <button class="btn-confirm btn--self-start"
                @click="importList(importQuestionList, 'questions'); closeImportQuestionModal()">принять
        </button>
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